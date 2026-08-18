<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Client for jntuhconnect.dhethi.com.
 *
 * Every method answers in one shape so callers never have to know which
 * transport ran:
 *
 *   ['state' => 'ready',  'data' => [...],       'msg' => '']
 *   ['state' => 'queued', 'data' => [],          'msg' => 'Your roll number has been queued.']
 *   ['state' => 'error',  'data' => [],          'msg' => 'why it failed']
 *
 * 'queued' is normal, not a failure: on a cache miss the upstream queues a
 * scrape of the university site and returns immediately, so the caller polls.
 */
class JntuhConnect
{
    /** Consolidated best-attempt mark sheet + SGPA/CGPA. */
    public function academicResult($rollNumber)
    {
        return $this->fetch('academic_result', $rollNumber);
    }

    /** Every attempt, including failed regulars later cleared by supply. */
    public function allResult($rollNumber)
    {
        return $this->fetch('all_result', $rollNumber);
    }

    /** Subjects still not cleared. */
    public function backlogs($rollNumber)
    {
        return $this->fetch('backlogs', $rollNumber);
    }

    /** Credits obtained vs required, by year (B.Tech only upstream). */
    public function credits($rollNumber)
    {
        return $this->fetch('credits', $rollNumber);
    }

    /** Result notifications feed — no roll number involved. */
    public function notifications()
    {
        return $this->fetch('notifications', null);
    }

    /**
     * A roll number is exactly 10 characters upstream; reject anything else
     * before spending a request on it.
     */
    public static function validRoll($rollNumber)
    {
        return (bool) preg_match('/^[A-Za-z0-9]{10}$/', trim((string) $rollNumber));
    }

    // ---------------------------------------------------------------- internals

    protected function fetch($key, $rollNumber)
    {
        if (!config('jntuh.enabled')) {
            return $this->err('Auto-fetch is turned off');
        }

        $roll = strtoupper(trim((string) $rollNumber));
        if ($rollNumber !== null && !self::validRoll($roll)) {
            return $this->err('Enter a valid 10-character hall ticket number');
        }

        $cacheKey = 'jntuh:' . $key . ':' . ($roll ?: 'all');
        $cached = Cache::get($cacheKey);
        if (is_array($cached)) {
            return $cached;
        }

        try {
            $mode = config('jntuh.mode');
            if ($mode === 'mcp') {
                $out = $this->callMcp($key, $roll);
            } elseif ($mode === 'rest') {
                $out = $this->callRest($key, $roll, config('jntuh.paths.' . $key));
            } else {
                $out = $this->callAuto($key, $roll);
            }
        } catch (\Exception $e) {
            Log::error('jntuh ' . $key . ' failed: ' . $e->getMessage());
            return $this->err('Could not reach the results service. Try again in a minute.');
        }

        // Only a finished answer is worth caching — a queued one must be re-asked.
        if ($out['state'] === 'ready') {
            Cache::put($cacheKey, $out, now()->addMinutes((int) config('jntuh.cache_minutes')));
        }

        return $out;
    }

    /**
     * The upstream's REST paths are not published anywhere we can read, so try
     * the configured one, then the known variants, then MCP. Whatever answers is
     * remembered for a day, which keeps this to one request in the normal case.
     */
    protected function callAuto($key, $roll)
    {
        $remembered = Cache::get('jntuh:endpoint:' . $key);
        if ($remembered === 'mcp') {
            return $this->callMcp($key, $roll);
        }
        if (is_string($remembered) && $remembered !== '') {
            $out = $this->callRest($key, $roll, $remembered);
            if ($out['state'] !== 'error') {
                return $out;
            }
            Cache::forget('jntuh:endpoint:' . $key);
        }

        $tried = [];
        foreach ($this->candidatePaths($key) as $path) {
            $out = $this->callRest($key, $roll, $path);
            if ($out['state'] !== 'error') {
                Cache::put('jntuh:endpoint:' . $key, $path, now()->addDay());
                return $out;
            }
            $tried[] = $path . ' (' . $out['msg'] . ')';
        }

        $out = $this->callMcp($key, $roll);
        if ($out['state'] !== 'error') {
            Cache::put('jntuh:endpoint:' . $key, 'mcp', now()->addDay());
            return $out;
        }

        Log::warning('jntuh auto-discovery failed for ' . $key . ': ' . implode(' | ', $tried)
            . ' | mcp (' . $out['msg'] . ')');

        return $this->err('Could not reach the results service. Tried '
            . count($tried) . ' REST paths and MCP; run "php artisan jntuh:probe" for detail.');
    }

    protected function candidatePaths($key)
    {
        $paths = [(string) config('jntuh.paths.' . $key)];
        foreach ((array) config('jntuh.fallback_paths.' . $key, []) as $extra) {
            $paths[] = (string) $extra;
        }
        return array_values(array_unique(array_filter($paths)));
    }

    protected function callRest($key, $roll, $path)
    {
        $path = (string) $path;
        if ($path === '') {
            return $this->err('No endpoint configured for ' . $key);
        }
        $url = config('jntuh.base_url') . str_replace('{roll}', rawurlencode($roll), $path);

        $res = $this->request()->get($url);

        if ($res->status() === 404) {
            return $this->err('No record found for this hall ticket number');
        }
        if ($res->status() === 423) {
            return $this->queued('The results service is busy. Try again shortly.');
        }
        if (!$res->successful()) {
            return $this->err('Results service returned ' . $res->status());
        }

        return $this->interpret($res->json());
    }

    /**
     * MCP is JSON-RPC 2.0 over HTTP: initialize (to pick up a session id, if the
     * server is stateful), then tools/call. Replies may come back as a plain JSON
     * body or as a single SSE frame, so both are unwrapped.
     */
    protected function callMcp($key, $roll)
    {
        $tool = (string) config('jntuh.mcp.tools.' . $key);
        if ($tool === '') {
            return $this->err('No MCP tool configured for ' . $key);
        }
        $url = (string) config('jntuh.mcp.url');

        $init = $this->request()->withHeaders(['Accept' => 'application/json, text/event-stream'])
            ->post($url, [
                'jsonrpc' => '2.0',
                'id'      => 1,
                'method'  => 'initialize',
                'params'  => [
                    'protocolVersion' => '2025-06-18',
                    'capabilities'    => new \stdClass(),
                    'clientInfo'      => ['name' => 'jntuh-books-app', 'version' => '1.0'],
                ],
            ]);

        $headers = ['Accept' => 'application/json, text/event-stream'];
        $session = $init->header('Mcp-Session-Id');
        if ($session) {
            $headers['Mcp-Session-Id'] = $session;
            // Stateful servers expect the handshake to be acknowledged.
            $this->request()->withHeaders($headers)->post($url, [
                'jsonrpc' => '2.0',
                'method'  => 'notifications/initialized',
            ]);
        }

        $args = ($roll === '' || $roll === null) ? new \stdClass() : ['rollNumber' => $roll];
        $res = $this->request()->withHeaders($headers)->post($url, [
            'jsonrpc' => '2.0',
            'id'      => 2,
            'method'  => 'tools/call',
            'params'  => ['name' => $tool, 'arguments' => $args],
        ]);

        if (!$res->successful()) {
            return $this->err('MCP endpoint returned ' . $res->status());
        }

        $envelope = $this->decodeRpc($res->body());
        if (isset($envelope['error']['message'])) {
            return $this->err((string) $envelope['error']['message']);
        }

        $result = isset($envelope['result']) ? $envelope['result'] : [];
        if (!empty($result['isError'])) {
            return $this->err($this->mcpText($result) ?: 'Results service reported an error');
        }

        // Preferred: the typed payload. Otherwise the text block holds the JSON.
        if (isset($result['structuredContent']) && is_array($result['structuredContent'])) {
            return $this->interpret($result['structuredContent']);
        }

        $text = $this->mcpText($result);
        $decoded = json_decode($text, true);

        return $this->interpret(is_array($decoded) ? $decoded : ['message' => $text]);
    }

    /** Pull the concatenated text blocks out of an MCP tool result. */
    protected function mcpText($result)
    {
        $out = '';
        $content = isset($result['content']) && is_array($result['content']) ? $result['content'] : [];
        foreach ($content as $block) {
            if (isset($block['text'])) {
                $out .= $block['text'];
            }
        }
        return $out;
    }

    /** A body that is either JSON or one `data: {...}` SSE frame. */
    protected function decodeRpc($body)
    {
        $direct = json_decode($body, true);
        if (is_array($direct)) {
            return $direct;
        }
        foreach (preg_split('/\r?\n/', (string) $body) as $line) {
            if (strpos($line, 'data:') === 0) {
                $frame = json_decode(trim(substr($line, 5)), true);
                if (is_array($frame)) {
                    return $frame;
                }
            }
        }
        return [];
    }

    /**
     * Decide whether a decoded payload is real data or the "queued" placeholder.
     * The upstream answers HTTP 200 either way, so this looks at the body.
     */
    protected function interpret($payload)
    {
        if (!is_array($payload) || empty($payload)) {
            return $this->err('Results service returned nothing');
        }

        $message = '';
        foreach (['message', 'msg', 'detail'] as $k) {
            if (isset($payload[$k]) && is_string($payload[$k])) {
                $message = $payload[$k];
                break;
            }
        }

        if ($message !== '' && preg_match('/queue|pending|processing|try again/i', $message)) {
            return $this->queued($message);
        }

        // A failure carrying no data of its own.
        $status = isset($payload['status']) ? strtolower((string) $payload['status']) : '';
        if (($status === 'error' || $status === 'failure' || $status === 'fail') && $message !== '') {
            return $this->err($message);
        }

        // Anything that is only {status, message} has no result in it.
        $meaningful = array_diff(array_keys($payload), ['status', 'message', 'msg', 'detail', 'success']);
        if (empty($meaningful)) {
            return $message !== '' ? $this->err($message) : $this->err('No result available yet');
        }

        return ['state' => 'ready', 'data' => $payload, 'msg' => ''];
    }

    protected function request()
    {
        $req = Http::timeout((int) config('jntuh.timeout'))
            ->acceptJson()
            ->withHeaders(['User-Agent' => 'JNTUH-Books-App/1.0']);

        $token = (string) config('jntuh.token');
        return $token !== '' ? $req->withToken($token) : $req;
    }

    protected function queued($msg)
    {
        return ['state' => 'queued', 'data' => [], 'msg' => $msg ?: 'Fetching your results, please wait'];
    }

    protected function err($msg)
    {
        return ['state' => 'error', 'data' => [], 'msg' => $msg];
    }
}

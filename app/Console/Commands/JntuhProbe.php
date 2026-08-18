<?php

namespace App\Console\Commands;

use App\Services\JntuhConnect;
use App\Services\JntuhResultImporter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

/**
 * Finds out how this server can talk to jntuhconnect.dhethi.com, because the
 * REST paths are not published anywhere we can read from here. Run it once on
 * the live box with a real roll number, then copy the winning settings to .env.
 *
 *   php artisan jntuh:probe 20XX1A0501
 *   php artisan jntuh:probe 20XX1A0501 --import
 */
class JntuhProbe extends Command
{
    protected $signature = 'jntuh:probe {roll : A real 10-character hall ticket number}
                            {--import : Also write the fetched result into the results tables}';

    protected $description = 'Probe the jntuhconnect endpoints and report which transport and paths work';

    public function handle()
    {
        $roll = strtoupper(trim($this->argument('roll')));
        if (!JntuhConnect::validRoll($roll)) {
            $this->error('Roll number must be exactly 10 characters.');
            return 1;
        }

        $base = rtrim(config('jntuh.base_url'), '/');
        $this->line('Base URL: ' . $base);
        $this->newLine();

        // Same candidate list the client uses in 'auto' mode.
        $candidates = array_merge(
            [config('jntuh.paths.academic_result')],
            (array) config('jntuh.fallback_paths.academic_result', [])
        );

        $winner = null;
        foreach (array_unique(array_filter($candidates)) as $path) {
            $url = $base . str_replace('{roll}', rawurlencode($roll), $path);
            try {
                $res = Http::timeout((int) config('jntuh.timeout'))->acceptJson()->get($url);
                $status = $res->status();
                $json = $res->json();
                $ok = $res->successful() && is_array($json);
                // Show a slice of whatever came back — an HTML error page or a
                // "queued" body both tell you something the status code does not.
                $peek = $ok ? 'JSON ok' : 'no JSON: ' . str_replace("\n", ' ', substr(trim($res->body()), 0, 90));
                $this->line(sprintf('%-45s %s %s', $path, $status, $peek));
                if ($ok && $winner === null) {
                    $winner = ['path' => $path, 'json' => $json];
                }
            } catch (\Exception $e) {
                $this->line(sprintf('%-45s ERR %s', $path, $e->getMessage()));
            }
        }

        $this->newLine();
        if ($winner) {
            $this->info('REST works. Put this in .env:');
            $this->line('  JNTUH_MODE=rest');
            $this->line('  JNTUH_BASE_URL=' . $base);
            $this->line('  JNTUH_PATH_ACADEMIC=' . $winner['path']);
        } else {
            $this->warn('No REST path answered. Trying the MCP endpoint instead...');
            config(['jntuh.mode' => 'mcp']);
            $out = (new JntuhConnect())->academicResult($roll);
            $this->line('MCP result state: ' . $out['state'] . ' ' . $out['msg']);
            if ($out['state'] !== 'error') {
                $this->info('MCP works. Put this in .env:');
                $this->line('  JNTUH_MODE=mcp');
                $this->line('  JNTUH_MCP_URL=' . config('jntuh.mcp.url'));
                $winner = ['path' => '(mcp)', 'json' => $out['data']];
            }
        }

        if (!$winner) {
            $this->error('Nothing answered. Check outbound HTTPS from this server.');
            return 1;
        }

        $this->newLine();
        $this->line('Top-level keys: ' . implode(', ', array_keys($winner['json'])));

        $normalized = (new JntuhResultImporter())->normalize($winner['json']);
        $this->line('Parsed name: ' . ($normalized['student_name'] ?: '(none)'));
        $this->line('Parsed CGPA: ' . ($normalized['cgpa'] ?? '(none)'));
        $this->line('Parsed semesters: ' . count($normalized['semesters']));
        foreach ($normalized['semesters'] as $s) {
            $this->line(sprintf('  %-5s sgpa=%-6s subjects=%d', $s['sem_code'], $s['sgpa'] ?? '-', count($s['subjects'])));
        }

        if (count($normalized['semesters']) === 0) {
            $this->warn('Nothing parsed — paste the raw JSON below into the issue so the mapper can be adjusted.');
            $this->line(substr(json_encode($winner['json']), 0, 4000));
        }

        if ($this->option('import') && count($normalized['semesters']) > 0) {
            $result = (new JntuhResultImporter())->store($normalized, $roll);
            $this->info('Imported as results.id = ' . $result->id);
        }

        return 0;
    }
}

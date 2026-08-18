<?php

/**
 * jntuhconnect.dhethi.com — auto-fetch of student results by hall ticket number.
 *
 * Everything the integration talks to lives here because the upstream exposes two
 * front doors for the same data: a plain JSON/REST API and an MCP endpoint meant
 * for AI agents. Server-to-server we want REST; `mcp` is kept as a fallback for
 * the case where only /mcp is reachable.
 *
 * Run `php artisan jntuh:probe <rollNumber>` on the live server to find out which
 * transport and paths answer, then write the winners into .env.
 */
return [

    // Master switch. Off => the app falls back to manual entry only.
    'enabled' => env('JNTUH_ENABLED', true),

    // 'auto' tries the REST candidates below and falls back to MCP, remembering
    // whichever answered. Pin it to 'rest' or 'mcp' once you know.
    'mode' => env('JNTUH_MODE', 'auto'),

    // jntuhconnect.dhethi.com is the website; the API it talks to lives here.
    'base_url' => rtrim(env('JNTUH_BASE_URL', 'https://jntuhresults.dhethi.com'), '/'),

    // Extra REST paths tried in 'auto' mode when the configured one does not
    // answer. {roll} is substituted. A working path is cached for a day.
    'fallback_paths' => [
        'academic_result' => [
            '/api/getAcademicResult?roll_no={roll}',
            '/api/getAllResult?roll_no={roll}',
        ],
    ],

    // Routes as defined by the upstream (ThilakReddyy/jntuh-backend, api/routes.py).
    // All are GET, and the roll number is the roll_no query parameter.
    'paths' => [
        'academic_result' => env('JNTUH_PATH_ACADEMIC', '/api/getAcademicResult?roll_no={roll}'),
        'all_result'      => env('JNTUH_PATH_ALL', '/api/getAllResult?roll_no={roll}'),
        'backlogs'        => env('JNTUH_PATH_BACKLOGS', '/api/getBacklogs?roll_no={roll}'),
        'credits'         => env('JNTUH_PATH_CREDITS', '/api/getCreditsChecker?roll_no={roll}'),
        'notifications'   => env('JNTUH_PATH_NOTIFICATIONS', '/api/getlatestnotifications'),
        // Forces a fresh scrape instead of serving the cached copy.
        'hard_refresh'    => env('JNTUH_PATH_REFRESH', '/api/hardRefresh?roll_no={roll}'),
    ],

    // MCP transport (JSON-RPC 2.0 over HTTP). Same service, agent-facing door.
    'mcp' => [
        'url'   => env('JNTUH_MCP_URL', 'https://jntuhresults.dhethi.com/mcp'),
        'tools' => [
            'academic_result' => env('JNTUH_TOOL_ACADEMIC', 'get_academic_result'),
            'all_result'      => env('JNTUH_TOOL_ALL', 'get_all_result'),
            'backlogs'        => env('JNTUH_TOOL_BACKLOGS', 'get_backlogs'),
            'credits'         => env('JNTUH_TOOL_CREDITS', 'get_credits_checker'),
            'notifications'   => env('JNTUH_TOOL_NOTIFICATIONS', 'get_latest_notifications'),
        ],
    ],

    // Optional bearer token, if the upstream ever starts requiring one.
    'token' => env('JNTUH_TOKEN', ''),

    'timeout' => (int) env('JNTUH_TIMEOUT', 20),

    // The upstream scrapes on a cache miss and answers "queued" meanwhile, so a
    // first fetch for an unseen roll number is normally not instant.
    'cache_minutes' => (int) env('JNTUH_CACHE_MINUTES', 180),

    // Guard rails: a hall ticket lets anyone read anyone's results, so a student
    // may only auto-fetch the roll number on their own profile, this often.
    'rate_limit_per_hour' => (int) env('JNTUH_RATE_LIMIT', 10),
];

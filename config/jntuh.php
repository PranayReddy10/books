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

    // 'rest' (preferred) or 'mcp'.
    'mode' => env('JNTUH_MODE', 'rest'),

    'base_url' => rtrim(env('JNTUH_BASE_URL', 'https://jntuhconnect.dhethi.com'), '/'),

    // REST paths. {roll} is substituted. Adjust after probing — these are the
    // conventional names for this API and may differ on your deployment.
    'paths' => [
        'academic_result' => env('JNTUH_PATH_ACADEMIC', '/api/academicresult/{roll}'),
        'all_result'      => env('JNTUH_PATH_ALL', '/api/allresult/{roll}'),
        'backlogs'        => env('JNTUH_PATH_BACKLOGS', '/api/backlogs/{roll}'),
        'credits'         => env('JNTUH_PATH_CREDITS', '/api/creditschecker/{roll}'),
        'notifications'   => env('JNTUH_PATH_NOTIFICATIONS', '/api/latestnotifications'),
    ],

    // MCP transport (JSON-RPC 2.0 over HTTP).
    'mcp' => [
        'url'   => env('JNTUH_MCP_URL', 'https://jntuhconnect.dhethi.com/mcp'),
        'tools' => [
            'academic_result' => env('JNTUH_TOOL_ACADEMIC', 'getAcademicResult'),
            'all_result'      => env('JNTUH_TOOL_ALL', 'getAllResult'),
            'backlogs'        => env('JNTUH_TOOL_BACKLOGS', 'getBacklogs'),
            'credits'         => env('JNTUH_TOOL_CREDITS', 'getCreditsChecker'),
            'notifications'   => env('JNTUH_TOOL_NOTIFICATIONS', 'getlatestnotifications'),
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

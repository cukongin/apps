<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class BlockMaliciousInputs
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // 1. Block CVE-2024-52301 (Environment Manipulation via argv)
        // Attackers use strings like '--use-env=...' in query parameters
        $queryString = $request->server('QUERY_STRING');
        if ($queryString && preg_match('/(?:^|&)--\w+/', $queryString)) {
            abort(403, 'Malicious Request Detected (Block-001)');
        }

        // 2. Block Common arbitrary shell command injection patterns in input
        $input = $request->all();

        // Recursive walk to check all inputs
        $this->checkMaliciousInput($input);

        $response = $next($request);

        // 3. Remove Header Information (Security through Obscurity)
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server'); // Might not work if set by Apache/Nginx directly

        return $response;
    }

    private function checkMaliciousInput($input) {
        if (is_array($input)) {
            foreach ($input as $value) {
                $this->checkMaliciousInput($value);
            }
        } elseif (is_string($input) && strlen($input) < 1000) {
            // Basic RCE/SQLi patterns (Conservative)
            // Block 'union select', 'exec(', 'system(', 'passthru(', 'eval('
            if (preg_match('/(union\s+select|exec\(|system\(|passthru\(|eval\()/i', $input)) {
                 // Log potential attack?
                 // For "Super Boss" mode, we abort.
                 abort(403, 'Malicious Payload Detected (Block-002)');
            }
        }
    }
}

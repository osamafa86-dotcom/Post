<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\VisitorLog;

class LogVisitor
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $ip = $request->ip();
        $userAgent = $request->header('User-Agent');
        $deviceType = $this->detectDevice($userAgent);
        $referrer = $request->header('referer');

        app()->terminating(function () use ($ip, $userAgent, $deviceType, $referrer) {
            try {
                $exists = VisitorLog::where('ip', $ip)
                    ->where('user_agent', $userAgent)
                    ->where('visited_at', '>=', now()->subMinutes(5))
                    ->exists();
                if (!$exists) {
                    VisitorLog::create([
                        'ip' => $ip,
                        'user_agent' => $userAgent,
                        'device_type' => $deviceType,
                        'referrer' => $referrer,
                        'country' => null,
                        'visited_at' => now(),
                    ]);
                }
            } catch (\Throwable $e) {
                // Silently fail — visitor logging should never break the site
            }
        });

        return $response;
    }

    private function detectDevice($userAgent): string
    {
        if (preg_match('/mobile/i', $userAgent)) {
            return 'Mobile';
        } elseif (preg_match('/windows/i', $userAgent)) {
            return 'Windows';
        } elseif (preg_match('/mac/i', $userAgent)) {
            return 'Mac';
        } elseif (preg_match('/linux/i', $userAgent)) {
            return 'Linux';
        } else {
            return 'Unknown';
        }
    }

    private function getCountryFromIp($ip): ?string
    {
        // Synchronous external HTTP calls block the response for 200-2000ms+.
        // Country can be backfilled asynchronously or via a local GeoIP DB.
        return null;
    }
}

<?php

namespace App\Http\Middleware;

use App\Models\LogRequest;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class LogRequestMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $this->logRequest($request);

        return $response;
    }

    /**
     * Log request details into log_requests table.
     */
    protected function logRequest(Request $request): void
    {
        try {
            $user = $request->user();
            $roles = null;

            if ($user) {
                if (method_exists($user, 'getRoleNames')) {
                    $roles = $user->getRoleNames()->toArray();
                } elseif (isset($user->roles)) {
                    $roles = $user->roles->pluck('name')->toArray();
                } elseif (isset($user->role)) {
                    $roles = [$user->role];
                }
            }

            $userAgent = $request->userAgent();

            LogRequest::create([
                'uri' => '/'.ltrim($request->path(), '/'),
                'query_string' => $request->getQueryString() ?: null,
                'method' => strtoupper($request->method()),
                'request_data' => $this->sanitizeRequestData($request),
                'ip' => $request->ip(),
                'user_agent' => $userAgent,
                'user_id' => $user?->id,
                'roles' => $roles,
                'browser' => $this->detectBrowser($userAgent),
                'platform' => $this->detectPlatform($userAgent),
                'device' => $this->detectDevice($userAgent),
                'is_ajax' => $request->ajax(),
            ]);
        } catch (Throwable $e) {
            report($e);
        }
    }

    /**
     * Sanitize and strip sensitive data from request payload.
     *
     * @return array<string, mixed>
     */
    protected function sanitizeRequestData(Request $request): array
    {
        $data = $request->except([
            'password',
            'password_confirmation',
            'current_password',
            'new_password',
            '_token',
            'token',
            'secret',
            'card_number',
            'cvv',
            'credit_card',
        ]);

        return $this->cleanData($data);
    }

    /**
     * Recursively clean file uploads and deeply nested structures.
     */
    protected function cleanData(mixed $data): mixed
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->cleanData($value);
            }

            return $data;
        }

        if ($data instanceof UploadedFile) {
            return [
                '_file' => true,
                'name' => $data->getClientOriginalName(),
                'size' => $data->getSize(),
                'mime' => $data->getClientMimeType(),
            ];
        }

        return $data;
    }

    /**
     * Detect browser from User Agent string.
     */
    protected function detectBrowser(?string $userAgent): ?string
    {
        if (empty($userAgent)) {
            return null;
        }

        $ua = $userAgent;

        if (preg_match('/Edg(e)?\/([0-9.]+)/i', $ua)) {
            return 'Microsoft Edge';
        }
        if (preg_match('/OPR\/([0-9.]+)/i', $ua) || preg_match('/Opera\/([0-9.]+)/i', $ua)) {
            return 'Opera';
        }
        if (preg_match('/Chrome\/([0-9.]+)/i', $ua) && ! preg_match('/Chromium/i', $ua)) {
            if (preg_match('/Brave/i', $ua)) {
                return 'Brave';
            }

            return 'Chrome';
        }
        if (preg_match('/Firefox\/([0-9.]+)/i', $ua)) {
            return 'Firefox';
        }
        if (preg_match('/Safari\/([0-9.]+)/i', $ua) && ! preg_match('/Chrome/i', $ua)) {
            return 'Safari';
        }
        if (preg_match('/MSIE\s([0-9.]+)/i', $ua) || preg_match('/Trident\/.*rv:([0-9.]+)/i', $ua)) {
            return 'Internet Explorer';
        }
        if (preg_match('/PostmanRuntime\/([0-9.]+)/i', $ua)) {
            return 'Postman';
        }
        if (preg_match('/curl\/([0-9.]+)/i', $ua)) {
            return 'cURL';
        }

        return 'Other';
    }

    /**
     * Detect operating system/platform from User Agent string.
     */
    protected function detectPlatform(?string $userAgent): ?string
    {
        if (empty($userAgent)) {
            return null;
        }

        $ua = $userAgent;

        if (preg_match('/Windows NT 10.0/i', $ua)) {
            return 'Windows 10/11';
        }
        if (preg_match('/Windows NT 6.3/i', $ua)) {
            return 'Windows 8.1';
        }
        if (preg_match('/Windows NT 6.2/i', $ua)) {
            return 'Windows 8';
        }
        if (preg_match('/Windows NT 6.1/i', $ua)) {
            return 'Windows 7';
        }
        if (preg_match('/Windows/i', $ua)) {
            return 'Windows';
        }
        if (preg_match('/Android/i', $ua)) {
            return 'Android';
        }
        if (preg_match('/iPhone|iPad|iPod/i', $ua)) {
            return 'iOS';
        }
        if (preg_match('/Macintosh|Mac OS X/i', $ua)) {
            return 'macOS';
        }
        if (preg_match('/Linux/i', $ua)) {
            return 'Linux';
        }
        if (preg_match('/CrOS/i', $ua)) {
            return 'ChromeOS';
        }

        return 'Other';
    }

    /**
     * Detect device type from User Agent string.
     */
    protected function detectDevice(?string $userAgent): ?string
    {
        if (empty($userAgent)) {
            return null;
        }

        $ua = $userAgent;

        if (preg_match('/(bot|crawler|spider|slurp|facebookexternalhit)/i', $ua)) {
            return 'Bot / Crawler';
        }
        if (preg_match('/(tablet|ipad|playbook|silk)|(android(?!.*mobi))/i', $ua)) {
            return 'Tablet';
        }
        if (preg_match('/Mobile|Android|iP(hone|od)|IEMobile|BlackBerry|Kindle|NetFront|Silk-Accelerated|(hpw|web)OS|Fennec|Minimo|Opera M(obi|ini)|Blazer|Dolfin|Dolphin|Skyfire|Zune/i', $ua)) {
            return 'Mobile';
        }

        return 'Desktop';
    }
}

<?php

namespace App\Http\Middleware;

use Log;
use Closure;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class SecureGate
{
    public function handle($request, Closure $next)
    {
        $cacheKey = 'license_validation';

        if (!Cache::has($cacheKey)) {
            $license_key = env('PUSHER_VALIDATION_KEY');
            $app_domain = $request->getHost();
            $api_url = "https://api.greenviewsoft.com/validate.php";

            try {
                $response = Http::asForm()->post($api_url, [
                    'license_key' => $license_key,
                    'domain' => $app_domain,
                ]);
                $isValid = str_contains($response->body(), '✅ License Verified!');
            } catch (\Exception $e) {
                Log::error('SecureGate failed: ' . $e->getMessage());
                $isValid = Cache::get($cacheKey, false);
            }

            if (!$isValid) {
                return response()->view('license.mismatch', [], 403);
            }

            Cache::put($cacheKey, true, now()->addHours(24));
        }

        return $next($request);
    }
}
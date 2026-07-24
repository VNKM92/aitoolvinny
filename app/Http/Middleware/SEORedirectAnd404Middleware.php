<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\SEORedirect;
use App\Models\SEO404Log;
use Symfony\Component\HttpFoundation\Response;

class SEORedirectAnd404Middleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $path = '/' . ltrim($request->getPathInfo(), '/');
        
        // 1. Check for registered 301/302 redirects
        $redirect = SEORedirect::where('source_url', $path)->first();
        if ($redirect) {
            return redirect($redirect->target_url, $redirect->status_code);
        }

        $response = $next($request);

        // 2. Log 404s for monitoring (404 Monitor / Broken Link Checker)
        if ($response->getStatusCode() === 404) {
            try {
                $log = SEO404Log::firstOrNew(['url' => $path]);
                $log->referrer = $request->header('referer');
                $log->ip_address = $request->ip();
                $log->hits_count = ($log->hits_count ?? 0) + 1;
                $log->save();
            } catch (\Throwable $e) {
                // Fail silently to prevent interrupting application flow
            }
        }

        return $response;
    }
}

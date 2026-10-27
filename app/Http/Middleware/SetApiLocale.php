<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetApiLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = (string) config('api.locale', 'id');

        app()->setLocale($locale);

        $validationPath = lang_path($locale.'/api_validation.php');

        if (is_file($validationPath)) {
            app('translator')->addLines(require $validationPath, $locale);
        }

        return $next($request);
    }
}

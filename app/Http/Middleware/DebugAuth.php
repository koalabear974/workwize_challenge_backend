<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DebugAuth
{
public function handle(Request $request, Closure $next)
{
Log::info('Incoming Request:', ['url' => $request->url()]);
Log::info('Session ID:', ['session_id' => session()->getId()]);
Log::info('CSRF Token (session):', ['csrf_token' => session()->token()]);
Log::info('CSRF Token (header):', ['csrf_token' => $request->header('X-CSRF-TOKEN')]);
Log::info('Cookies:', $request->cookies->all());

return $next($request);
}
}

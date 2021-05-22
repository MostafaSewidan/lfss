<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class ClientActive
{

    public function responseJson($status, $message, $data = null)
    {
        $response = [
            'status' => $status,
            'massage' => $message,
            'data' => $data,
        ];

        return response()->json($response);
    }
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (auth()->user()->is_active == 0)
            return $this->responseJson(0, 'الرجاء تأكيد الحساب أولا');

        return $next($request);

    }
}

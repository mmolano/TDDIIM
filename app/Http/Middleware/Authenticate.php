<?php

namespace App\Http\Middleware;

use App\Models\Consumer;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Authenticate
{
    private $error = null;

    private function error(int $error = null, int $status = 400): JsonResponse
    {
        $message = null;

        if ($error) {
            $this->error = $error;
        }

        switch ($this->error) {
            case 1:
                $message = 'Empty token';
                break;
            case 2:
                $message = 'Bad token';
                break;
            case 3:
                $message = 'Ip unauthorized';
                break;
            default:
                $message = 'Indefine error';
        }

        return response()->json([
            'error' => $this->error,
            'message' => $message
        ], $status);
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->bearerToken()) {
            return $this->error(1);
        } elseif (!$authorization = Consumer::where('token', $request->bearerToken())
            ->first()) {
            return $this->error(2, 401);
        } elseif (!$authorization->checkIpAuthorized($request->ip())) {
            return $this->error(3, 401);
        }
        return $next($request);
    }
}

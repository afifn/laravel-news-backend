<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;

class JwtAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $jwtKey = env('JWT_KEY');
        $jwt = $request->bearerToken();

        try {
            $token = JWT::decode($jwt, new Key($jwtKey, 'HS256'));
            $request->attributes->add([
                'user' => $token->user
            ]);
            return $next($request);
        } catch (BeforeValidException $bve) {
            return response()->json([
                'message' => 'token not valid',
            ], 401);
        } catch (ExpiredException $e) {
            return response()->json([
                'message' => 'token expired'
            ]);
        } catch (SignatureInvalidException $sie) {
            return response()->json([
                'message' => 'invalid ignature'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'unauthorized'
            ], 401);
        }
    }
}

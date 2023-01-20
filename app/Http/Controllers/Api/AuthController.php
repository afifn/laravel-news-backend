<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function auth(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->getMessageBag(), 400);
        }
        $user = User::where('email', $request->input('email'))->first();
        if ($user) {
            $isValidPassword = Hash::check($request->input('password'), $user->password);
            if ($isValidPassword) {
                $token = $this->generateToken($user);
                return response()->json([
                    'message' => 'login successfully',
                    'error' => false,
                    'loginResult' => [
                        'id_user' => "$user->id_user",
                        'name' => $user->name,
                        'token' => $token,
                    ]
                ]);
            } else {
                return response()->json(['message' => 'password wrong']);
            }
        } else {
            return response()->json(['message' => 'user not found']);
        }
    }

    public function generateToken($user)
    {
        $jwtKey = env('JWT_KEY');
        $jwtExpired = env('JWT_EXPIRED');

        $now = now()->timestamp;
        $exp = now()->addMinute($jwtExpired)->timestamp;
        $payload = [
            'iss' => 'news.id',
            'nbf' => $now,
            'iat' => $now,
            'exp' => $exp,
            'user' => $user->email
        ];

        $token = JWT::encode($payload, $jwtKey, 'HS256');
        return $token;
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->getMessageBag(), 400);
        }

        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');

        $data = [
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password)
        ];
        $user = User::where('email', $email)->first();
        if (!$user) {
            User::create($data);
            return response()->json([
                'message' => 'success create new account.',
                'data' => $data,
            ]);
        }
    }
}

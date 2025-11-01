<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

// 'recaptcha_token' => ['required', 'string'],
// 'recaptcha_token'  => ['required', 'string'],
class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    // REGISTER
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'             => ['required', 'string', 'max:100'],
            'email'            => ['required', 'email', 'unique:users,email'],
            'password'         => ['required', 'min:6'],
            'recaptcha_token'  => ['required', 'string'],
        ]);

        $data = $this->authService->register($validated);
        return response()->json($data, 201);
    }

    // LOGIN
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email'           => ['required', 'email'],
            'password'        => ['required'],
            'recaptcha_token' => ['required', 'string'],
        ]);

        $data = $this->authService->login($validated);
        return response()->json($data);
    }

    // LOGOUT
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());
        return response()->json(['message' => 'Logout successful']);
    }

    // ME
    public function me(Request $request): JsonResponse
    {
        return response()->json(['user' => $request->user()]);
    }

    // GOOGLE LOGIN
    public function googleLogin(Request $request): JsonResponse
    {
        $request->validate(['token' => 'required|string']);
        $data = $this->authService->googleLogin($request->token);
        return response()->json($data);
    }
}





// TESTING NON RE CAPTCHA
// <?php

// namespace App\Http\Controllers\Api;

// use App\Http\Controllers\Controller;
// use App\Services\AuthService;
// use Illuminate\Http\Request;
// use Illuminate\Http\JsonResponse;

// class AuthController extends Controller
// {
//     protected AuthService $authService;

//     public function __construct(AuthService $authService)
//     {
//         $this->authService = $authService;
//     }


//     // Register
//     public function register(Request $request): JsonResponse
//     {
//         $validated = $request->validate([
//             'name'     => ['required', 'string', 'max:100'],
//             'email'    => ['required', 'email', 'unique:users,email'],
//             'password' => ['required', 'min:6'],
//         ]);

//         $data = $this->authService->register($validated);

//         return response()->json($data, 201);
//     }

//     // Login
//     public function login(Request $request): JsonResponse
//     {
//         $validated = $request->validate([
//             'email'    => ['required', 'email'],
//             'password' => ['required'],
//         ]);

//         $data = $this->authService->login($validated);

//         return response()->json($data);
//     }

//     // Me
//     public function me(Request $request): JsonResponse
//     {
//         return response()->json(['user' => $request->user()]);
//     }

//     // Logout
//     public function logout(Request $request): JsonResponse
//     {
//         $this->authService->logout($request->user());
//         return response()->json(['message' => 'Logout successful']);
//     }

//     // Google Login
//     public function googleLogin(Request $request): JsonResponse
//     {
//         $request->validate(['token' => 'required|string']);

//         $data = $this->authService->googleLogin($request->token);

//         return response()->json($data);
//     }
// }

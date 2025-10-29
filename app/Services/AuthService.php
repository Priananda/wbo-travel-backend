<?php

namespace App\Services;

use App\Models\User;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class AuthService
{
    private function verifyRecaptcha(string $token): bool
    {
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => env('RECAPTCHA_SECRET_KEY'),
            'response' => $token,
        ]);

        return $response->json('success') === true;
    }

    public function register(array $data): array
    {
        if (! $this->verifyRecaptcha($data['recaptcha_token'])) {
            throw ValidationException::withMessages(['recaptcha' => 'Invalid reCAPTCHA verification.']);
        }

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => 'user',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'message' => 'User registered successfully',
            'user'    => $user,
            'token'   => $token,
        ];
    }

    public function login(array $credentials): array
    {
        if (! $this->verifyRecaptcha($credentials['recaptcha_token'])) {
            throw ValidationException::withMessages(['recaptcha' => 'Invalid reCAPTCHA verification.']);
        }

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages(['email' => 'Invalid credentials.']);
        }

        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'message' => 'Login successful',
            'user'    => $user,
            'token'   => $token,
        ];
    }

    public function logout(User $user): void
    {
        $user->tokens()->delete();
    }

    public function googleLogin(string $token): array
    {
        $client = new GoogleClient(['client_id' => env('GOOGLE_CLIENT_ID')]);
        $payload = $client->verifyIdToken($token);

        if (! $payload) {
            throw ValidationException::withMessages(['token' => 'Invalid Google token.']);
        }

        $user = User::firstOrCreate(
            ['email' => $payload['email']],
            [
                'name'     => $payload['name'],
                'password' => Hash::make($payload['sub']),
                'role'     => 'user',
            ]
        );

        $authToken = $user->createToken('google_login')->plainTextToken;

        return [
            'message' => 'Login via Google success',
            'user'    => $user,
            'token'   => $authToken,
        ];
    }
}






// TESTING NON RE CAPTCHA
// <?php

// namespace App\Services;

// use App\Models\User;
// use Google\Client as GoogleClient;
// use Illuminate\Support\Facades\Hash;
// use Illuminate\Validation\ValidationException;

// class AuthService
// {
//     // Register
//     public function register(array $data): array
//     {
//         $user = User::create([
//             'name'     => $data['name'],
//             'email'    => $data['email'],
//             'password' => Hash::make($data['password']),
//             'role'     => 'user',
//         ]);

//         $token = $user->createToken('auth_token')->plainTextToken;

//         return [
//             'message' => 'User registered successfully',
//             'user'    => $user,
//             'token'   => $token,
//         ];
//     }

//     // Login
//     public function login(array $credentials): array
//     {
//         $user = User::where('email', $credentials['email'])->first();

//         if (! $user || ! Hash::check($credentials['password'], $user->password)) {
//             throw ValidationException::withMessages([
//                 'email' => 'Invalid credentials provided.',
//             ]);
//         }

//         $user->tokens()->delete();
//         $token = $user->createToken('auth_token')->plainTextToken;

//         return [
//             'message' => 'Login successful',
//             'user'    => $user,
//             'token'   => $token,
//         ];
//     }

//     // Logout
//     public function logout(User $user): void
//     {
//         $user->tokens()->each(function ($token) {
//             $token->delete();
//         });
//     }

//     // Google Login
//     public function googleLogin(string $token): array
//     {
//         $client = new GoogleClient(['client_id' => env('GOOGLE_CLIENT_ID')]);
//         $payload = $client->verifyIdToken($token);

//         if (! $payload) {
//             throw ValidationException::withMessages(['token' => 'Invalid Google token.']);
//         }

//         $user = User::firstOrCreate(
//             ['email' => $payload['email']],
//             [
//                 'name'     => $payload['name'],
//                 'password' => Hash::make($payload['sub']),
//                 'role'     => 'user',
//             ]
//         );

//         $authToken = $user->createToken('google_login')->plainTextToken;

//         return [
//             'message' => 'Login via Google success',
//             'user'    => $user,
//             'token'   => $authToken,
//         ];
//     }
// }

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $credentials = $request->validate(
            [
            'email' => ['required', 'email'],
            'password' => ['required'],
            ]
        );

        if (Auth::attempt($credentials)) {
            return redirect()->intended('/');
        }

        return redirect('/login')->withInput()->with('login_failed', 1);
    }


    /**
     * @OA\Post(
     *     path="/api/auth/get-token",
     *     summary="Get a token for API access",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password","device_name"},
     *             @OA\Property(property="email", example="admin@gmail.com", type="string", format="email"),
     *             @OA\Property(property="password", example="admin", type="string", format="password"),
     *             @OA\Property(property="device_name", example="react", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Token successfully generated",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized access",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="incorrect_access_data")
     *         )
     *     )
     * )
     */
    public function getToken(Request $request)
    {
        $request->validate(
            [
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
            ]
        );

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'incorrect_access_data'], 401);
        }

        return response()->json([
            'token' => $user->createToken($request->device_name)->plainTextToken
        ]);
        
    }

    public function logout()
    {
        Auth::guard('api')->logout();

        return response()->json(['message' => 'successfully_logged_out']);
    }
}

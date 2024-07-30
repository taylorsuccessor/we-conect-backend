<?php

namespace App\Http\Controllers\User;

use App\Http\Requests\RegisterUserRequest;
use App\Jobs\UserCreatedJob;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class RegisterUserController extends Controller
{
    public function store(RegisterUserRequest $request)
    {
        $user = User::create(
            [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
            ]
        );

        UserCreatedJob::dispatch($user);
        //$user->sendEmailVerificationNotification();

        return response()->json(['message' => 'register_successfully'], 201);
    }
}

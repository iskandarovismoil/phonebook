<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\RegistrationRequest;
use App\Models\User;
use App\Utilities\Data;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    private const MESSAGES = [
        'name.required' => 'Вы не ввели имя!',
        'email.required' => 'Вы не ввели адрес электронной почты!',
        'email.unique' => 'Текущий адрес электронной почты существует!',
        'email.exists' => 'Текущий адрес электронной почты не существует!',
        'email.email' => 'Вы ввели не корректный адрес электронной почты!',
        'password.required' => 'Вы не ввели пароль!',
        'password.confirmed' => 'Пароль и подтверждение пароля не совподают!',
        'password.min' => 'Пароль должен содиржать :min или более символов',
    ];

    public function __construct()
    {
        $this->middleware('jwt.verify', ['except' => ['login', 'registration']]);
    }

    public function registration(Request $request) : JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users|email',
            'password' => 'required|confirmed|min:8',
        ], self::MESSAGES);

        if($validator->fails()){
            return Data::makeResponseForm(
                false,
                null,
                400,
                $validator->errors()->toArray()
            );
        }

        $validated = $validator->validated();

        $user = User::create(
            $validated
        );

        $token = auth()->login($user);

        return Data::makeResponseForm(
            true,
            $this->respondWithToken($token),
            200,
        );
    }

    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|exists:users|email',
            'password' => 'required'
        ], self::MESSAGES);

        if($validator->fails()){
            return Data::makeResponseForm(
                false,
                null,
                400,
                $validator->errors()->toArray()
            );
        }

        $validated = $validator->validated();

        if (!$token = auth()->attempt($validated)) {
            return Data::makeResponseForm(
               false,
               null,
               401,
               'Unauthorized'
            );
        }

        return Data::makeResponseForm(
            true,
            $this->respondWithToken($token),
            200
        );
    }

    public function user(): JsonResponse
    {
        return Data::makeResponseForm(
            true,
            auth()->user(),
            200
        );
    }

    public function logout(): JsonResponse
    {
        auth()->logout();

        return Data::makeResponseForm(
            true,
            null,
            200
        );
    }

    public function refresh(): JsonResponse
    {
        return Data::makeResponseForm(
            true,
            auth()->refresh(),
            200
        );
    }

    protected function respondWithToken($token): array
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ];
    }
}

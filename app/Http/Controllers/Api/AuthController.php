<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Utilities\Data;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    private const MESSAGES = [
        'name.required' => 'Поля имени .',
        'surname.required' => 'Поля фамилии',
        'email.required' => 'Bunday .',
        'email.unique' => 'Bunday telefon raqam oldin kiritilgan.',
        'email.exists' => 'Bunday tyoq.',
        'password.required' => 'Parol kiritilmadi.',
        'password.confirmed' => 'Parol va parol tasdig\'i bir xil kiritilmadi.',
        'password.min' => 'Parol 8 ta yoki undan ko‘p simvoldan iborat bo‘lishi shart.',
    ];

    public function __construct()
    {
        $this->middleware('jwt.verify', ['except' => ['login', 'registration']]);
    }

    public function registration(Request $request) : JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'surname' => 'required',
            'email' => 'required|unique:users',
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
            'email' => 'required|exists:users',
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

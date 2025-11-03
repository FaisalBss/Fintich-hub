<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\VerifyOtpRequest;
use App\Http\Requests\ResendOtpRequest;
use App\Http\Requests\LoginRequest;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request)
    {
        $result = $this->authService->registerUser($request->validated());
        return response()->json($result, $result['code']);
    }

    public function verifyOtp(VerifyOtpRequest $request)
    {
        $result = $this->authService->verifyOtp(
            $request->email,
            $request->otp
        );
        return response()->json($result, $result['code']);
    }

    public function resendOtp(ResendOtpRequest $request)
    {
        $result = $this->authService->resendOtp($request->email);
        return response()->json($result, $result['code']);
    }

    public function login(LoginRequest $request)
    {
        $result = $this->authService->login(
            $request->email,
            $request->password
        );
        return response()->json($result, $result['code']);
    }

    public function logout()
    {
        $result = $this->authService->logout();
        return response()->json(
            $result,
            $result['code']
        );
    }
}


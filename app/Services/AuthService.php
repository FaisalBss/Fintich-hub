<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOtpMail;
use Illuminate\Support\Facades\Auth;
use App\Models\Wallet;

class AuthService
{
    public function registerUser(array $data): array
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        Wallet::create(['user_id' => $user->id, 'balance' => 0.00]);

        $otp = rand(100000, 999999);
        $expiresAt = now()->addMinutes(10);

        $user->otp = $otp;
        $user->otp_expires_at = $expiresAt;
        $user->save();

        try {
            Mail::to($user->email)->queue(new SendOtpMail($otp));

            return [
                'status' => 'success',
                'message' => 'User registered successfully. Please check your email for OTP.',
                'code' => 201
            ];

        } catch (\Exception $e) {
            report($e);
            return [
                'status' => 'error',
                'message' => 'User registered, but failed to send verification email. Please try resend OTP.',
                'code' => 500
            ];
        }
    }

    public function verifyOtp(string $email, string $otp): array
    {
        $user = User::where('email', $email)->first();

        if ($user->email_verified_at) {
            return ['status' => 'error', 'message' => 'Account is already verified.', 'code' => 400];
        }

        if (now()->gt($user->otp_expires_at)) {
            return ['status' => 'error', 'message' => 'OTP has expired.', 'code' => 400];
        }

        if ($user->otp !== $otp) {
            return ['status' => 'error', 'message' => 'Invalid OTP.', 'code' => 401];
        }

        $user->email_verified_at = now();
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->save();

        $token = $user->createToken('api-token')->plainTextToken;

        return [
            'status' => 'success',
            'message' => 'Account verified successfully.',
            'user' => $user,
            'api_token' => $token,
            'code' => 200
        ];
    }

    public function resendOtp(string $email): array
    {
        $user = User::where('email', $email)->first();

        if ($user->email_verified_at) {
            return ['status' => 'error', 'message' => 'Account is already verified.', 'code' => 400];
        }

        $otp = rand(100000, 999999);
        $expiresAt = now()->addMinutes(10);

        $user->otp = $otp;
        $user->otp_expires_at = $expiresAt;
        $user->save();

        try {
            Mail::to($user->email)->queue(new SendOtpMail($otp));
            return [
                'status' => 'success',
                'message' => 'A new OTP has been sent to your email.',
                'code' => 200
            ];
        } catch (\Exception $e) {
            report($e);
            return [
                'status' => 'error',
                'message' => 'Failed to resend OTP. Please try again later.',
                'code' => 500
            ];
        }
    }

    public function login(string $email, string $password): array
    {
        $credentials = ['email' => $email, 'password' => $password];

        if (!Auth::attempt($credentials)) {
            return [
                'status' => 'error',
                'message' => 'Invalid credentials.',
                'code' => 401
            ];
        }

        $user = Auth::user();

        if (!$user->email_verified_at) {
            return [
                'status' => 'error',
                'message' => 'Account not verified. Please verify your account first.',
                'code' => 403
            ];
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return [
            'status' => 'success',
            'message' => 'Login successful.',
            'user' => $user,
            'api_token' => $token,
            'code' => 200
        ];
    }

    public function logout(): array
    {
        $user = Auth::user();
        $user->tokens()->delete();

        return [
            'status' => 'success',
            'message' => 'Logged out successfully.',
            'code' => 200
        ];
    }
}

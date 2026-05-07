<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use App\Models\User;

class OtpController extends Controller
{
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email']
        ]);

        // 🔥 CEK EMAIL SUDAH ADA
    if (User::where('email', $request->email)->exists()) {
        return response()->json([
            'error' => 'Email sudah terdaftar'
        ], 422);
    }

        $otp = rand(100000, 999999);

        Session::put('otp', $otp);
        Session::put('otp_email', $request->email);

        Mail::raw("Kode verifikasi kamu: $otp", function ($msg) use ($request) {
            $msg->to($request->email)->subject('Kode Verifikasi');
        });

        return response()->json([
            'message' => 'OTP dikirim'
        ]);
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\CodeOtp;
use App\Models\User;
use App\Models\ApplicationSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CodeOtpController extends Controller
{
    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:13',
        ]);

        $phone = $request->phone;

        $userExists = User::where('phone', $phone)->exists();

        if ($userExists) {
            return response()->json(['success' => false, 'message' => 'Nomor telepon telah terdaftar'], 409);
        }

        $otp = rand(100000, 999999);
        $app = ApplicationSetting::pluck('app_name')->first();

        CodeOtp::updateOrCreate(
            ['phone' => $phone],
            ['otp' => $otp]
        );

        $api = Http::baseUrl('https://app.japati.id/')
        ->withToken('API-TOKEN-tDby9Tpokldf0Xc03om7oNgkX45zJTFtLZ94oNsITsD828VJdZq112')
        ->post('/api/send-message', [
            'gateway' => '6283836949076',
            'number' => $phone,
            'type' => 'text',
            'message' => '*' . $otp. '* is your *' .$app. '* Verivication code.',
        ]);

        return response()->json(['success' => true, 'message' => 'OTP sent successfully']);
    }
}
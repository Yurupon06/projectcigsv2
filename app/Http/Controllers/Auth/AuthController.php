<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\CodeOtp;
use App\Models\Customer;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:users',
            'phone' => 'required|string|max:13',
            'password' => 'required|string|min:3|confirmed',
            'otp' => 'required|numeric|digits:6',
        ], [
            'otp.digits' => 'OTP harus terdiri dari 6 digit.',
        ]);
        $otpRecord = CodeOtp::where('phone', $request->phone)->first();
    
        if (!$otpRecord || $otpRecord->otp != $request->otp) {
            return redirect()->route('register')->with('error', 'OTP tidak valid atau salah.')->withInput();
        }
    
        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'phone_verified_at' => now(),
            'password' => bcrypt($request->password), 
            'role' => 'customer',
        ]);
    
        Customer::create([
            'user_id' => $user->id,
            'phone' => $user->phone,
        ]);
    
        $otpRecord->delete();
    
        return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login.');
    }
    

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('phone', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if ($user->role === 'admin') {
                return redirect('dashboard');
            }
            if ($user->role === 'cashier') {
                return redirect('cashier');
            }
            if ($user->role === 'customer') {
                return redirect('/home');
            }
        }

        return redirect()->back()->withErrors(['phone' => 'Invalid credentials'])->withInput();
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }

    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    public function reset(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:13',
            'password' => 'required|string|min:3|confirmed',
        ]);

        $user = User::where('phone', $request->phone)->first();

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('login')->with('success', 'Password reset successfully');
    }
}
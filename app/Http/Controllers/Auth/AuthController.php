<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:users',
            // 'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:13',
            'password' => 'required|string|min:3|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            // 'email' => $request->email,
            'phone' => $request->phone,
            'password' => $request->password,
            'role' => 'customer'
        ]);

        Customer::create([
            'user_id' => $user->id,
            'phone' => $user->phone,  
        ]);

        return redirect()->route('login');
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

        return redirect()->back()->withErrors(['email' => 'Invalid credentials'])->withInput();
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

    public function forgot(Request $request)
    {
        $request->validate([
            'phone' => 'required|phone',
        ]);

        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return back()->withErrors(['phone' => 'phone not found']);
        }
        if ($user->role == 'admin') {
            return back()->withErrors(['phone' => 'Invalid phone']);
        }

        $status = Password::sendResetLink(
            $request->only('phone')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['phone' => __($status)]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'phone' => 'required|phone',
            'password' => 'required|string|min:3|confirmed',
        ]);

        $status = Password::reset(
            $request->only('phone', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))->with('success', 'Password reset successfully')
            : back()->withErrors(['phone' => [__($status)]]);
    }
}

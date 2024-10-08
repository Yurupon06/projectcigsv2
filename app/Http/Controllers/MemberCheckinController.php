<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\MemberCheckin;
use App\Models\Product_categorie;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MemberCheckinController extends Controller
{
    public function index()
    {
        $membercheckin = MemberCheckin::with('member')->orderBy('created_at', 'desc')->get();
        return view('cashier.membercheckin', compact('membercheckin'));
    }

    public function qrcheckin(Request $request,$qr_token)
    {
        $qr_token = $request->input('qr_token');
        $checkin = MemberCheckin::where('qr_token', $qr_token)->first();

        if ($checkin && $checkin->status === 'use') {
            $checkin->status = 'used';
            $checkin->save();

            return redirect()->route('cashier.membercheckin')->with('success', 'Check-in successful.');
        }

        return redirect()->route('cashier.membercheckin')->with('error', 'QR Code is invalid or has already been used.');
    }

    public function qrcheck($qr_token)
    {
        $membercheckin = MemberCheckin::where('qr_token', $qr_token)->first();

        if (!$membercheckin) {
            return redirect()->route('cashier.membercheckin')->with('error', 'Member not found');
        }

        return view('cashier.show', compact('membercheckin'));
    }
}

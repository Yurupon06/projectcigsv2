<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\Member;
use App\Models\Payment;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Declaration
        $range = $request->query('range', 7);
        $twoWeeksBefore = Carbon::today()->subDays(14);
        $twoMonthBefore = Carbon::today()->subDays(60);
        $endDate = Carbon::now();

        if (!$request->has('range')) {
            return redirect()->route('dashboard.index', ['range' => $range]);
        }

        // Range
        if ($range == 7) {
            $startDate = Carbon::today()->subDays(7);
            $comparisonDate = $twoWeeksBefore;
        } else {
            $startDate = Carbon::today()->subDays(30);
            $comparisonDate = $twoMonthBefore;
        }

        // Money
        $amountsMoney = Payment::whereBetween('created_at', [$startDate, $endDate])->sum('amount');
        $comparisonMoney = Payment::whereBetween('created_at', [$comparisonDate, $startDate])->sum('amount');

        if ($comparisonMoney != 0) {
            $amountsMoneyComparison = ($amountsMoney - $comparisonMoney) / $comparisonMoney * 100;
        } else {
            $amountsMoneyComparison = $amountsMoney > 0 ? 100 : 0;
        }

        // User
        $amountsUsers = User::whereBetween('created_at', [$startDate, $endDate])->count();
        $comparisonUser = User::whereBetween('created_at', [$comparisonDate, $startDate])->count();

        if ($comparisonUser != 0) {
            $amountsUserComparison = ($amountsUsers - $comparisonUser) / $comparisonUser * 100;
        } else {
            $amountsUserComparison = $amountsUsers > 0 ? 100 : 0;
        }

        // Member
        $amountsMembers = Member::whereBetween('start_date', [$startDate, $endDate])->count();
        $comparisonMember = Member::whereBetween('start_date', [$comparisonDate, $startDate])->count();

        if ($comparisonMember != 0) {
            $amountsMemberComparison = ($amountsMembers - $comparisonMember) / $comparisonMember * 100;
        } else {
            $amountsMemberComparison = $amountsMembers > 0 ? 100 : 0;
        }

        // Total Sales
        $totalSales = Payment::sum('amount');
        $firstSales = Payment::min('created_at');
        $comparisonSales = Payment::whereBetween('created_at', [$firstSales, $startDate])->sum('amount');

        if ($comparisonSales != 0) {
            $amountsSalesComparison = ($totalSales - $comparisonSales) / $comparisonSales * 100;
        } else {
            $amountsSalesComparison = $totalSales > 0 ? 100 : 0;
        }

        // Fetch daily orders for the selected range
        $orders = Order::where('status', 'paid')
            ->whereBetween('order_date', [$startDate, $endDate])
            ->selectRaw('DATE(order_date) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date');

        // Fetch daily payments for the selected range
        $payments = Payment::whereBetween('payment_date', [$startDate, $endDate])
            ->selectRaw('DATE(payment_date) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date');

        // Fetch daily new members for the selected range
        $members = Member::whereBetween('start_date', [$startDate, $endDate])
            ->selectRaw('DATE(start_date) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date');

        // Prepare data for the selected range
        $dates = [];
        $ordersData = [];
        $paymentsData = [];
        $membersData = [];

        // Prepare data for each day within the range
        for ($i = $range; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $dates[] = $date;
            $ordersData[] = $orders->get($date, 0);
            $paymentsData[] = $payments->get($date, 0);
            $membersData[] = $members->get($date, 0);
        }

        // Calculate specific update times
        $orderUpdateTime = Order::max('order_date');
        $paymentUpdateTime = Payment::max('payment_date');
        $memberUpdateTime = Member::max('start_date');

        // Convert to Carbon instances
        $orderUpdateTime = $orderUpdateTime ? Carbon::parse($orderUpdateTime) : null;
        $paymentUpdateTime = $paymentUpdateTime ? Carbon::parse($paymentUpdateTime) : null;
        $memberUpdateTime = $memberUpdateTime ? Carbon::parse($memberUpdateTime) : null;

        return view('dashboard.home', compact(
            'range',
            'dates',
            'ordersData',
            'paymentsData',
            'membersData',
            'amountsMoney',
            'amountsUsers',
            'amountsMembers',
            'totalSales',
            'comparisonMoney',
            'comparisonUser',
            'comparisonMember',
            'comparisonSales',
            'amountsMoneyComparison',
            'amountsUserComparison',
            'amountsMemberComparison',
            'amountsSalesComparison',
            'orderUpdateTime',
            'paymentUpdateTime',
            'memberUpdateTime'
        ));
    }
    public function profile()
    {
        $user = Auth::user();
        $customer = Customer::where('user_id', $user->id)->first();

        return view('dashboard.profile', compact('user', 'customer'));
    }

    public function profileUpdate(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . Auth::id(),
            'phone' => 'required|string|max:20',
            'born' => 'required|date',
            'gender' => 'required|in:men,women',
        ]);

        $user = Auth::user();
        $customer = Customer::where('user_id', $user->id)->first();
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        $customer = Customer::updateOrCreate(
            ['user_id' => $user->id],
            [
                'phone' => $request->phone,
                'born' => $request->born,
                'gender' => $request->gender,
            ]
        );

        return redirect()->route('dashboard.profil')->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:3|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->route('dashboard.profil')->with('warning', 'Current password does not match.');
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('dashboard.profil')->with('success', 'Password updated successfully.');
    }
}

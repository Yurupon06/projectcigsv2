<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $filter = $request->input('filter');
        $perPage = $request->input('per_page', 10);

        $query = Payment::query();

        if ($filter == 'membership' || !$filter) {
            $query->whereHas('order.customer', function($q) {
                $q->whereNotNull('order_id');
            });

            if ($search) {
                $query->where(function($query) use ($search) {
                    $query->whereHas('order.customer.user', function($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('order.product', function($q) use ($search) {
                        $q->where('product_name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('order', function($q) use ($search) {
                        $q->where('id', 'like', '%' . $search . '%')
                        ->orWhere('status', 'like', '%' . $search . '%');
                    })
                    ->orWhere('amount', 'like', '%' . $search . '%')
                    ->orWhere('amount_given', 'like', '%' . $search . '%')
                    ->orWhere('change', 'like', '%' . $search . '%')
                    ->orWhere('payment_date', 'like', '%' . $search . '%');
                });
            }
        }

        if ($filter == 'complement') {
            $query->whereHas('orderComplement');

            if ($search) {
                $query->where(function($query) use ($search) {
                    $query->whereHas('orderComplement', function($q) use ($search) {
                        $q->where('order_complement_id', 'like', '%' . $search . '%');
                    })
                    ->orWhere('amount', 'like', '%' . $search . '%')
                    ->orWhere('amount_given', 'like', '%' . $search . '%')
                    ->orWhere('change', 'like', '%' . $search . '%')
                    ->orWhere('payment_date', 'like', '%' . $search . '%');
                });
            }
        }

        $payment = $query->paginate($perPage);

        return view('payment.index', compact('payment'));
    }   


}

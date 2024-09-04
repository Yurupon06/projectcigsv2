@extends('cashier.master')
@section('title', 'Payment')
@section('sidebar')
    @include('cashier.sidebar')
@endsection
@section('page-title', 'Payment')
@section('page', 'Payment')
@section('main')
	@include('cashier.main')
	
    <div class="container-fluid py-4 mt-6">
        <div class="row">
            @if (session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif
            <div class="col-12">
                <div class="card my-4">
                    <div class="card-header pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-2">Payment</h6>
                            <div class="input-group mb-2" style="max-width: 300px;">
                                <form method="GET" action="{{ route('cashier.payment') }}" class="d-flex w-100">
                                    <input type="text" name="search" class="form-control" placeholder="Search payments"
                                        value="{{ request('search') }}" style="border-radius: 20px 0 0 20px; height: 38px;">
                                    <button type="submit" class="btn btn-primary"
                                        style="background-color: #ff7e00; border-radius: 0 20px 20px 0; height: 38px; padding: 0 15px;">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card-body px-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0" id="datatable">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">No</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Customer Name</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">order id</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Product</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">amount</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">amount Given</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Change</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">payment date</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($payments as $i => $dt)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    {{ $payments->firstItem() + $i }}
                                                </div>
                                            </td>
                                            <td>{{ $dt->order->customer->user->name }}</td>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    {{ $dt->order->id }}
                                                </div>
                                            </td>
                                            <td>{{ $dt->order->product->product_name }}</td>
                                            <td>
                                                Rp {{ number_format($dt->amount) }}
                                            </td>
                                            <td>
                                                Rp {{ number_format($dt->amount_given) }}
                                            </td>
                                            <td>
                                                Rp {{ number_format($dt->change) }}
                                            </td>
                                            <td>
                                                {{ \Carbon\Carbon::parse($dt->payment_date)->translatedFormat('d F Y H:i') }}
                                            </td>
                                            <td
                                                style="color: {{ $dt->order->status === 'unpaid' ? 'red' : ($dt->order->status === 'paid' ? 'green' : 'black') }}">
                                                {{ $dt->order->status }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                {{ $payments->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
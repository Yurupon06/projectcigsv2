@extends('cashier.master')
@section('title', isset($setting) ? $setting->app_name . ' - Cashier' : 'Cashier')
@section('sidebar')
    @include('cashier.sidebar')
@endsection
@section('page-title', 'Cashier')
@section('page', 'Cashier')
@section('main')
    @include('cashier.main')
    <style>
    #reader {
        width: 100%;
        height: auto; 
        max-height: 350px; 
        margin: auto;
        position: relative;
        overflow: hidden;
    }

    #reader video {
        transform: scaleX(-1);
        width: 100%;
        height: auto;
    }

    .tbl {
        min-height: 100%;
    }

    .u {
        font-weight: bold;
        text-decoration: none;
    }

    .u:hover {
        color: #ff7e00;
    }

    @media screen and (max-width: 768px) {
        .page {
            display: none;
        }

        .input-group {
            margin-right: 8px;
        } 
    }
    
    </style>

    <div class="container-fluid py-4 mt-4">
        <div class="row">
            <div class="col-md-12 d-flex flex-column flex-md-row">
                <div class="col-md-8 me-md-2 mb-4 mb-md-0 mt-4" style="overflow: hidden;">
                <a href="{{ route('cashier.index', ['filter' => 'membership', 'search' => request('search'), 'per_page' => request('per_page')]) }}" type="button" class=" {{ request('filter', 'membership') == 'membership' ? 'active' : '' }} btn btn-primary btn-sm align-items-center btn-membership" style="font-size: 12px; padding: 10px 12px; background-color: #ff5c00; box-shadow: 0 4px 6px rgba(0, 0, 255, 0.1); border: none;">
                    <i class="fas fa-user-tag me-2" style="font-size: 18px;"></i> Membership
                </a>
                <a href="{{route('cashier.index', ['filter' => 'complement', 'per_page' => request('per_page')]) }}" type="button" class=" {{ request('filter', 'membership') == 'complement' ? 'active' : '' }} btn btn-sm align-items-center btn-complement mx-2" style="font-size: 12px; padding: 10px 12px; background-color:#007bff; color: white; box-shadow: 0 4px 6px rgba(255, 165, 0, 0.1); border: none;">
                    <i class="fas fa-shopping-basket me-2" style="font-size: 18px;"></i> Complement
                </a>
                    <div class="card mb-4">
                        <div class="card-header pb-0 py-1">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-2 page">Cashier</h6>
                                <div class="input-group" style="max-width: 300px;">
                                    <form method="GET" action="{{ route('cashier.index') }}" class="d-flex w-100 pt-2">
                                        <input type="text" name="search" class="form-control"
                                            placeholder="Search orders" value="{{ request('search') }}"
                                            style="border-radius: 20px 0 0 20px; height: 38px; font-size: 14px;">
                                        <button type="submit" class="btn btn-primary"
                                            style="background-color: #ff7e00; border-radius: 0 20px 20px 0; height: 38px; padding: 0 10px; font-size: 14px;">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </form>
                                </div>
                                <div class="d-flex align-items-center my-3">
                                    <form method="GET" action="{{ route('cashier.index') }}" class="d-flex">
                                        <label for="per_page" class="form-label me-2 mt-2">Show:</label>
                                        <select name="per_page" id="per_page" class="form-select form-select-sm w-auto me-3" onchange="this.form.submit()">
                                            <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5</option>
                                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                        </select>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="card-body px-0">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0" id="datatable">
                                @if(request('filter', 'membership') == 'membership' || !request('filter'))
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">No</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">name</th>
                                            <!-- <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">product</th> -->
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">order date</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">total amount</th>
                                            <!-- <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">status</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action</th> -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                            @foreach ($orders as $i => $dt)
                                                <tr>
                                                @if(request('filter') == 'membership' || !request('filter'))
                                                    
                                                    <td>
                                                        <div class="d-flex px-2 py-1">
                                                            {{ ($orders->currentPage() - 1) * $orders->perPage() + $i + 1 . ' . ' }}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        {{ $dt->customer->user->name }}
                                                    </td>
                                                    <!-- <td>
                                                        {{ $dt->product->product_name }}
                                                    </td> -->
                                                    <td>
                                                        {{ \Carbon\Carbon::parse($dt->order_date)->translatedFormat('d F Y H:i') }}
                                                    </td>
                                                    <td>
                                                        <a class="u" href="{{route('cashier.qrscan', $dt->qr_token)}}">
                                                        Rp {{ number_format($dt->total_amount) }}
                                                    </td>
                                                    <!-- <td
                                                        style="color: {{ $dt->status === 'unpaid' ? 'red' : ($dt->status === 'paid' ? 'green' : 'black') }}">
                                                        {{ $dt->status }}
                                                    </td>
                                                    <td class="align-middle text-center text-sm">
                                                        <a href="{{route('cashier.qrscan', $dt->qr_token)}}">
                                                        <span class="btn bg-gradient-info ws-15 my-2 mb-2 btn-sm">Detail</span>
                                                        </a>
                                                    </td> -->
                                                @endif
                                                </tr>
                                            @endforeach
                                    </tbody>
                                @endif
                                @if(request('filter') == 'complement')
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">No</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Order Id</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Total Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($orders as $i => $dt)
                                            <tr>
                                            @if(request('filter') == 'complement' || !request('filter'))
                                                <td >
                                                    <div class="d-flex px-2 py-1">
                                                        {{ $loop->iteration ++ . ' . ' }} <!-- Nomor urut -->
                                                    </div>
                                                    
                                                </td>
                                                <td>
                                                    <a class="u" href="{{ route('cashier.checkout', $dt->qr_token) }}">
                                                    {{ $dt->id }}</a>
                                                </td>
                                                <td>
                                                    Rp {{ number_format($dt->total_amount) }}
                                                </td>
                                            </tr>
                                            @endif  
                                        @endforeach
                                    </tbody>
                                @endif
                                </table>
                            </div>
                        </div>
                        <div class="container-fluid">
                        <div class="row">
                            @if (request('filter') == 'membership')
                            {{ $orders->appends(['filter' => 'membership', 'search' => request('search'), 'per_page' => request('per_page')])->links('pagination::bootstrap-5') }}

                            @else
                            {{ $orders->appends(['filter' => 'complement', 'per_page' => request('per_page')])->links('pagination::bootstrap-5') }}

                            @endif
                        </div>
                    </div>
                    </div>
                </div>

                <div class="col-md-4 qr-section">
                    <div class="card mt-4 tbl">
                        <div class="card-header py-2 mt-2 text-center">
                            <h6>Scanner For Order</h6>
                        </div>
                        <div class="card-body">
                            <div id="reader"></div>
                            <div class="mt-3 text-center">
                                <button id="toggle-scan-btn" class="btn btn-primary">Start Scan</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/html5-qrcode/minified/html5-qrcode.min.js"></script>
    <script>
    let html5QrcodeScanner = new Html5Qrcode("reader");
    let isScanning = false;

    function calculateQrboxSize() {
        const readerWidth = document.getElementById('reader').offsetWidth;
        return Math.min(350, readerWidth * 0.8);
    }

    function onScanSuccess(decodedText, decodedResult) {
        let qrToken = decodedText.trim();

        if (qrToken.startsWith('SCAN_')) {
            let token = qrToken.split('_')[1];
            let qrScanUrl = `{{ route('cashier.qrscan', ['qr_token' => '__TOKEN__']) }}`.replace('__TOKEN__', token);
            window.location.href = qrScanUrl;
        } else if (qrToken.startsWith('CHECKOUT_')) {
            let token = qrToken.split('_')[1];
            let checkoutUrl = `{{ route('cashier.checkout', ['qr_token' => '__TOKEN__']) }}`.replace('__TOKEN__', token);
            window.location.href = checkoutUrl;
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'QR Code tidak valid!',
                        confirmButtonText: 'Tutup'
                    });
                }
    }


    function onScanFailure(error) {
        console.warn(`Code scan error = ${error}`);
    }

    document.getElementById('toggle-scan-btn').addEventListener('click', function() {
        if (isScanning) {
            html5QrcodeScanner.stop().then(() => {
                document.getElementById('toggle-scan-btn').textContent = 'Start Scan';
                isScanning = false;
            }).catch(err => {
                console.error("Error stopping QR code scanner: ", err);
            });
        } else {
            html5QrcodeScanner.start(
                { facingMode: "environment" },
                {
                    fps: 30,
                    qrbox: calculateQrboxSize() // Menggunakan fungsi yang menghitung ukuran qrbox
                },
                onScanSuccess,
                onScanFailure
            ).then(() => {
                document.getElementById('toggle-scan-btn').textContent = 'Stop Scan';
                isScanning = true;
            }).catch(err => {
                console.error("Error starting QR code scanner: ", err);
            });
        }
    });
    </script>
@endsection

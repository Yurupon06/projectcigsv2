@extends('cashier.master')
@section('title', isset($setting) ? $setting->app_name . ' - Add Order' : 'Add Order')
@section('sidebar')
@include('cashier.sidebar')
@endsection
@section('page-title', 'Add Order')
@section('page', 'Customer / Create')
@section('main')
    @include('cashier.main')
    
    @if ($orderComplement->status !== 'paid')
    <style>
        .navigation-links {
            display: flex;
            justify-content: space-between;
        }

        .navigation-links a {
            text-decoration: none;
            color: #007BFF;
            font-weight: bold;
        }

        .navigation-links a:hover {
            text-decoration: underline;
        }

        .change-display {
            text-align: right;
            font-weight: bold;
        }

        .change-display span {
            color: green;
        }

        .amount-input {
            margin-bottom: 1rem;
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }

        .amount-input input {
            width: 200px;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-left: 10px;
        }

        .amount-input label {
            font-weight: bold;
        }

        .number-buttons {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }

        .number-buttons button {
            width: 100%;
            height: 60px;
            font-size: 24px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #f8f9fa;
            color: #495057;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
        }

        .number-buttons button:hover {
            background-color: #e2e6ea;
        }

        .number-buttons button:active {
            transform: scale(0.95);
        }

        .card-custom {
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .btn-delete {
            background-color: #dc3545;
            color: white;
            font-size: 24px;
        }

        .btn-delete:hover {
            background-color: #c82333;
        }
    </style>

    <div class="container-fluid py-6">
        <div class="row">
            <div class="col-md-8">
                <div class="card my-4">
                    <div class="card-header pb-0 navigation-links">
                        <a href="{{ route('cashier.index') }}">Back</a>
                    </div>
                    <div class="card-header pb-0 py-1">
                        <h6>Order # {{ $orderComplement->id }}</h6>
                    </div>
                    <div class="card-body px-0 pb-2">
                        <!-- Tambahkan max-height dan overflow-y untuk membuat tabel bisa di-scroll -->
                        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                            <table class="table align-items-center mb-0">
                                <thead style="position: sticky; top: 0; background-color: white; z-index: 1;">
                                    <tr>
                                        <th class="text-left">Complement</th>
                                        <th class="text-center">Quantity</th>
                                        <th class="text-center">Price</th>
                                        <th class="text-end">Sub Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orderDetails as $dt)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ asset('storage/' . $dt->complement->image) }}" alt="{{ $dt->complement->name }}" class="img-thumbnail" style="width: 50px; height: 50px; margin-right: 10px;">
                                                    {{ $dt->complement->name }}
                                                </div>
                                            </td>
                                            <td class="text-center">{{ $dt->quantity }}</td> 
                                            <td class="text-center">{{ number_format($dt->complement->price) }}</td> 
                                            <td class="text-end">{{ number_format($dt->sub_total) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot style="position: sticky; bottom: 0; background-color: white;">
                                    <tr>
                                        <td colspan="2" class="text-right"><strong>Total Items</strong></td>
                                        <td class="text-center"><strong>{{ $orderComplement->quantity }}</strong></td>
                                        <td class="text-end"><strong>Rp {{ number_format($orderDetails->sum('sub_total')) }}</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    
                </div>
            </div>
            
            <!-- New Column for Number Buttons -->
            <div class="col-md-4">
                <div class="card card-custom mt-4" style="height: 92%">
                    <div class="card-header">
                        <h6 class="mb-0">Number Pad</h6>
                    </div>
                    <div class="card-body">
                        <div class="number-buttons">
                            @for ($i = 1; $i <= 9; $i++)
                                <button type="button" onclick="appendNumber('{{ $i }}')">{{ $i }}</button>
                            @endfor
                            <button type="button" onclick="appendNumber('0')">0</button>
                            <button type="button" onclick="appendNumber('.')">.</button>
                            <button type="button" class="btn-delete" onclick="deleteLastDigit()">C</button>
                            
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <form action="{{ route('payments.complement', $orderComplement->id) }}" method="POST" class="text-end">
                                    @csrf
                                    <div class="amount-input">
                                        <label for="amount_given">Amount Given:</label>
                                        <input type="number" name="amount_given" id="amount_given" min="0" step="0.01" oninput="calculateChange()" onkeydown="inputE(event)">
                                    </div>
                                    <div class="change-display" id="change-display">
                                        Change: <span id="change-amount">Rp 0</span>
                                    </div>
                                    <br>
                                    <button type="submit" name="action" value="cancel" class="btn btn-danger">Cancel</button>
                                    <button type="submit" name="action" value="process" class="btn btn-success">Proceed to Payment</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="container mt-4">
        <h1 class="mb-4">Order Details - Order # {{ $orderComplement->id }}</h1>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Order # {{ $orderComplement->id }}</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-hover">
                        <thead style="position: sticky; top: 0; background-color: white; z-index: 1;">
                            <tr>
                                <th class="text-left">Complement</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-center">Price</th>
                                <th class="text-end">Sub Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orderDetails as $dt)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset('storage/' . $dt->complement->image) }}" alt="{{ $dt->complement->name }}" class="img-thumbnail" style="width: 50px; height: 50px; margin-right: 10px;">
                                            {{ $dt->complement->name }}
                                        </div>
                                    </td>
                                    <td class="text-center">{{ $dt->quantity }}</td> 
                                    <td class="text-center">{{ number_format($dt->complement->price) }}</td> 
                                    <td class="text-end">{{ number_format($dt->sub_total) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot style="position: sticky; bottom: 0; background-color: white;">
                            <tr>
                                <td colspan="3" class="text-right"><strong>Total</strong></td>
                                <td class="text-end"><strong>Rp {{ number_format($orderDetails->sum('sub_total')) }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            
            

            <div class="card-footer d-flex justify-content-end gap-2">
                <a href="{{ route('cashier.payment', ['filter' => 'complement', 'per_page' => request('per_page')]) }}" name="action" value="process" class="btn btn-primary">Back</a>
            </div>
        </div>
    </div>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: '{{ session('success') }}',
            });
        @endif

        @if ($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '{{ $errors->first() }}',
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session('error') }}',
            });
        @endif

        function calculateChange() {
            const amountGiven = parseFloat(document.getElementById('amount_given').value) || 0;
            const totalAmount = {{ $orderComplement->total_amount }};
            const change = amountGiven - totalAmount;

            document.getElementById('change-amount').textContent = 'Rp ' + change.toLocaleString('id-ID');
        }

        function inputE(e) {
            if (e.key === 'e' || e.key === 'E' || e.key === '-') {
                e.preventDefault();
            }
        }

        function appendNumber(number) {
            const input = document.getElementById('amount_given');
            if (number === '.' && input.value.includes('.')) return;
            input.value = (input.value || '') + number;
            input.focus();
            calculateChange();
        }

        function deleteLastDigit() {
            const input = document.getElementById('amount_given');
            input.value = input.value.slice(0, -1);
            calculateChange();
        }
    </script>
@endsection

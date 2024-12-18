@extends('dashboard.master')
@section('page-title', isset($setting) ? $setting->app_name . ' - Validate OTP' : 'Validate OTP')
@section('title', 'Validate OTP')
@section('sidebar')
    @include('cashier.sidebar')
@endsection
@section('main')
    @include('cashier.main')

    <main class="container" style="margin-top: 100px;">
        <div class="row justify-content-center">
            <div class="col-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0 text-center">Please enter the OTP code sent to
                            <strong>{{ substr(auth()->user()->phone, 0, 2) . '*********' . substr(auth()->user()->phone, -2) }}</strong>
                        </h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('validate-otp-phone') }}" method="POST">
                            @csrf
                            <div class="form-group mb-3">
                                <input type="text" oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                                    minlength="6" maxlength="6"
                                    class="form-control text-center @error('otp') is-invalid @enderror" id="otp"
                                    name="otp" placeholder="XXXXXX" required>
                                @error('otp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-U1cnd+0AdAq8ni0Y3C03GA+6GczfURhZgefjMNKDU3KwLLpTt92lW2TdeYifz59C" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @if (Session::has('error'))
            {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: '{{ Session::get('error') }}',
                })
            }
        @endif
    </script>
    <script>
        function maxLength(input) {
            const maxLength = 13;
            if (input.value.length > maxLength) {
                input.value = input.value.slice(0, maxLength);
            }
        }
    </script>
@endsection

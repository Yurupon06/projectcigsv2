@extends('dashboard.master')
@section('title', 'Application Setting')
@section('sidebar')
    @include('dashboard.sidebar')
@endsection
@section('page-title', 'Application Setting')
@section('page', 'Application Setting')
@section('main')
    @include('dashboard.main')

    <div class="container-fluid pb-4">
        <div class="row">
            <div class="col-12">
                <div class="card my-4">
                    <div class="card-header py-2">
                        <h5 class="text-center font-weight-bold">App Setting</h5>
                    </div>
                    <div class="card-body p-2">
                        @if ($setting)
                            <img class="d-block mx-auto"
                                src="{{ asset('storage/' . $setting->app_logo) }}"
                                alt="Logo" width="150px">
                            <form action="{{ route('application-setting.update', $setting->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="mb-3 ms-3 me-3">
                                    <label for="app_name" class="form-label">App Name</label>
                                    <input type="text"
                                        class="ps-2 form-control border border-secondary-subtle @error('app_name') is-invalid @enderror"
                                        placeholder="App Name" aria-label="app_name" id="app_name" name="app_name"
                                        value="{{ old('app_name', $setting->app_name) }}">
                                    @error('app_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3 ms-3 me-3">
                                    <label for="app_logo" class="form-label">App Logo</label>
                                    <input type="file"
                                        class="ps-2 form-control border border-secondary-subtle @error('app_logo') is-invalid @enderror"
                                        placeholder="App Logo" aria-label="app_logo" id="app_logo" name="app_logo"
                                        value="{{ old('app_logo', $setting->app_logo) }}">
                                    @error('app_logo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Better format file is PNG.</small>
                                </div>
                                <div class="mb-3 ms-3 me-3">
                                    <label for="app_address" class="form-label">Address</label>
                                    <textarea name="app_address" id="app_address"
                                        class="ps-2 form-control border border-secondary-subtle @error('app_address') is-invalid @enderror"
                                        placeholder="Address" rows="3" style="resize: none;">{{ old('app_address', $setting->app_address) }}</textarea>
                                    @error('app_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3 ms-3 me-3">
                                    <label for="japati_token" class="form-label">Japati Token</label>
                                    <input type="text" class="ps-2 form-control border border-secondary-subtle @error('japati_token') is-invalid @enderror" placeholder="Japati Token" aria-label="japati_token" id="japati_token" name="japati_token" value="{{ old('japati_token', $setting->japati_token) }}">
                                    @error('japati_token')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3 ms-3 me-3">
                                    <label for="japati_gateway" class="form-label">Japati Gateway</label>
                                    <input type="text" class="ps-2 form-control border border-secondary-subtle @error('japati_gateway') is-invalid @enderror" placeholder="Japati Gateway" aria-label="japati_gateway" id="japati_gateway" name="japati_gateway" value="{{ old('japati_gateway', $setting->japati_gateway) }}">
                                    @error('japati_gateway')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3 ms-3 me-3">
                                    <label for="japati_url" class="form-label">Japati URL</label>
                                    <input type="text" class="ps-2 form-control border border-secondary-subtle @error('japati_url') is-invalid @enderror" placeholder="Japati URL" aria-label="japati_url" id="japati_url" name="japati_url" value="{{ old('japati_url', $setting->japati_url) }}">
                                    @error('japati_url')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary mb-3 ms-3 me-3">Update</button>
                            </form>
                        @else
                            <p class="mb-3 ms-3 me-3">No settings available. Please add settings first.</p>
                            <form action="{{ route('application-setting.store') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3 ms-3 me-3">
                                    <label for="app_name" class="form-label">App Name</label>
                                    <input type="text"
                                        class="ps-2 form-control border border-secondary-subtle @error('app_name') is-invalid @enderror"
                                        placeholder="App Name" aria-label="app_name" id="app_name" name="app_name"
                                        value="{{ old('app_name') }}">
                                    @error('app_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3 ms-3 me-3">
                                    <label for="app_logo" class="form-label">App Logo</label>
                                    <input type="file"
                                        class="ps-2 form-control border border-secondary-subtle @error('app_logo') is-invalid @enderror"
                                        placeholder="App Logo" aria-label="app_logo" id="app_logo" name="app_logo"
                                        value="{{ old('app_logo') }}">
                                    @error('app_logo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Better format file is PNG.</small>
                                </div>
                                <div class="mb-3 ms-3 me-3">
                                    <label for="app_address" class="form-label">Address</label>
                                    <textarea name="app_address" id="app_address"
                                        class="ps-2 form-control border border-secondary-subtle @error('app_address') is-invalid @enderror"
                                        placeholder="Address" rows="3" style="resize: none;">{{ old('app_address') }}</textarea>
                                    @error('app_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3 ms-3 me-3">
                                    <label for="japati_token" class="form-label">Japati Token</label>
                                    <input type="text" class="ps-2 form-control border border-secondary-subtle @error('japati_token') is-invalid @enderror" placeholder="Japati Token" aria-label="japati_token" id="japati_token" name="japati_token" value="{{ old('japati_token') }}">
                                    @error('japati_token')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3 ms-3 me-3">
                                    <label for="japati_gateway" class="form-label">Japati Gateway</label>
                                    <input type="text" class="ps-2 form-control border border-secondary-subtle @error('japati_gateway') is-invalid @enderror" placeholder="Japati Gateway" aria-label="japati_gateway" id="japati_gateway" name="japati_gateway" value="{{ old('japati_gateway') }}">
                                    @error('japati_gateway')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3 ms-3 me-3">
                                    <label for="japati_url" class="form-label">Japati URL</label>
                                    <input type="text" class="ps-2 form-control border border-secondary-subtle @error('japati_url') is-invalid @enderror" placeholder="Japati URL" aria-label="japati_url" id="japati_url" name="japati_url" value="{{ old('japati_url') }}">
                                    @error('japati_url')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary mb-3 ms-3 me-3">Create</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @section('setting-script')
    <script>
        @if (Session::has('success'))
            {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: '{{ Session::get('success') }}',
                })
            }
        @endif
    </script>
    @endsection
@endsection

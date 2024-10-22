@extends('dashboard.master')
@section('page-title', isset($setting) ? $setting->app_name . ' - Profile' : 'Profile')
@section('title', 'Profile Cashier')
@section('sidebar')
    @include('cashier.sidebar')
@endsection
@section('main')
    @include('cashier.main')

    <style>
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .navigation-links {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .navigation-links a {
            text-decoration: none;
            color: #007BFF;
            font-weight: bold;
        }

        .navigation-links a:hover {
            text-decoration: underline;
        }

        .profile-section {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .profile-section h1 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }

        .profile-field {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .profile-field span {
            font-size: 18px;
            color: #555;
        }

        .profile-field a {
            text-decoration: none;
            color: #007BFF;
            font-weight: bold;
            font-size: 16px;
        }

        .profile-field a:hover {
            text-decoration: underline;
        }
    </style>

    <div class="container mt-8">
        <div class="navigation-links">
            <a href="{{ route('cashier.index') }}">Back</a>
        </div>

        <div class="profile-section">
            <h1>Profile</h1>
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('warning'))
                <div class="alert alert-warning">
                    {{ session('warning') }}
                </div>
            @endif
            <div class="profile-field">
                <span>Name:</span>
                <span>{{ $user->name }}</span>
            </div>
            <div class="profile-field">
                <span>Phone:</span>
                <span>{{ $user->phone ?? 'Not filled' }}</span>
            </div>
            <div class="profile-field">
                <span>Date of Birth:</span>
                <span>{{ $customer->born ?? 'Not filled' }}</span>
            </div>
            <div class="profile-field">
                <span>Gender:</span>
                <span class="text-capitalize">{{ $customer->gender ?? 'Not filled' }}</span>
            </div>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editProfileModal">
                Update Profile
            </button>
            <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#changePasswordModal">
                Change Password
            </button>
        </div>
    </div>

    <!-- Profile Update Modal -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" role="dialog" aria-labelledby="editProfileModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">Update Profile</h5>
                </div>
                <form action="{{ route('update.profile.cashier') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" id="name" name="name" class="form-control"
                                value="{{ Auth::user()->name }}" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <div class="position-relative">
                                <input type="text" id="phone" name="phone" class="form-control"
                                value="{{ $user->phone ?? '' }}" readonly>
                                <a href="{{ route('change-phone-cashier') }}" class="btn btn-warning position-absolute top-0 end-0">Change Phone Number</a>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="born">Date of Birth</label>
                            <input type="date" id="born" name="born" class="form-control"
                                value="{{ $customer->born ?? '' }}" max="{{ date('Y-m-d', strtotime('-1 day')) }}"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="gender">Gender</label>
                            <select id="gender" name="gender" class="form-control" required>
                                <option value="men" {{ ($customer->gender ?? 'men') == 'men' ? 'selected' : '' }}>Men
                                </option>
                                <option value="women" {{ ($customer->gender ?? 'men') == 'women' ? 'selected' : '' }}>
                                    Women</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog"
        aria-labelledby="changePasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                </div>
                <form action="{{ route('update.password.cashier') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="current_password">Current Password</label>
                            <input type="password" id="current_password" name="current_password" class="form-control"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="password">New Password</label>
                            <input type="password" id="password" name="password" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">Confirm New Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-between">
                        <a href="{{ route('show-forgot') }}" class="d-block text-decoration-none">I forgot my password</a>
                        <div class="pt-4">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.querySelector('form').addEventListener('submit', function(event) {
            var currentPassword = document.getElementById('current_password').value;
            var newPassword = document.getElementById('password').value;
            var confirmPassword = document.getElementById('password_confirmation').value;

            if ((newPassword || confirmPassword) && (!currentPassword || !newPassword || !confirmPassword)) {
                event.preventDefault();
                alert('Please fill in all fields if you are changing your password.');
            }
        });
    </script>
@endsection
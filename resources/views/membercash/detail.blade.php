@extends('cashier.master')
@section('title', isset($setting) ? $setting->app_name . ' - Membership Detail' : 'Membership Detail')
@section('sidebar')
    @include('cashier.sidebar')
@endsection
@section('page-title', 'Membership Detail')
@section('page', 'Membership Details')
@section('main')
    @include('cashier.main')

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
            margin-bottom: 1rem;
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
    </style>



    <div class="container-fluid py-4 mt-5">
        <div class="row">
            <div class="col-12">
                <div class="card my-4">
                    <div class="card-header py-2 navigation-links">
                        <a href="{{ route('membercashier.membercash') }}">Back</a>
                    </div>
                    <div class="card-header pb-2">
                        <h6>Membership Details</h6>
                    </div>
                    <div class="card-body pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <tr>
                                    <th>Customer Name</th>
                                    <td>{{ $member->customer->user->name }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $member->customer->user->email }}</td>
                                </tr>
                                <tr>
                                    <th>Phone</th>
                                    <td>{{ $member->customer->phone }}</td>
                                </tr>
                                @if ($member->status == 'inactive')
                                <tr>
                                    <th>Status</th>
                                    <td style="color: {{ $member->status === 'expired' ? 'red' : ($member->status === 'active' ? 'green' : 'black') }}">
                                        @switch($member->status)
                                            @case('active')
                                                Active
                                            @break
                                            @case('expired')
                                                Expired
                                            @break
                                            @case('inactive')
                                                Ban
                                            @break
                                            @default
                                                Lainnya
                                        @endswitch
                                    </td>
                                </tr>
                                @else
                                <tr>
                                    <th>Start Date</th>
                                    <td style="color:blue">{{ \Carbon\Carbon::parse($member->start_date)->translatedFormat('d F Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Expired</th>
                                    <td style="color: {{ $member->status === 'expired' ? 'red' : ($member->status === 'active' ? 'green' : 'black') }}">
                                        {{ \Carbon\Carbon::parse($member->end_date)->translatedFormat('d F Y H:i') }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Visit Left</th>
                                    <td style="color: {{ $member->status === 'expired' ? 'red' : ($member->status === 'active' ? 'green' : 'black') }}">
                                        {{$member->visit}}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td style="color: {{ $member->status === 'expired' ? 'red' : ($member->status === 'active' ? 'green' : 'black') }}">
                                        {{ $member->status }}
                                    </td>
                                </tr>
                                @endif
                                
                                <tr>
                                    <td colspan="2">
                                        <form id="action-form" action="{{ route('action.member', $member->id ) }}" method="POST" class="text-end">
                                            @csrf
                                            @if ($member->status === 'inactive')
                                                <button type="button" data-action="unban" class="btn btn-success btn-unban">Unban Membership</button>
                                            @else
                                                <button type="button" data-action="cancel" class="btn btn-danger btn-ban">Ban Membership</button>
                                            @endif
                                        </form>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('button[data-action]').forEach(button => {    
        button.addEventListener('click', function (e) {
            e.preventDefault(); // Mencegah form dikirim langsung
            let action = this.getAttribute('data-action'); // Ambil nilai action dari tombol

            let confirmText = '';
            let successText = '';

            switch (action) {
                case 'cancel':
                    confirmText = 'Are you sure you want to ban this member?';
                    successText = 'Member has been banned.';
                    break;
                case 'unban':
                    confirmText = 'Are you sure you want to unban this member?';
                    successText = 'Member has been unbanned.';
                    break;
                case 'process':
                    confirmText = 'Are you sure you want to process this membership?';
                    successText = 'Membership has been processed.';
                    break;
                default:
                    confirmText = 'Are you sure?';
                    successText = 'Action completed successfully.';
            }

            Swal.fire({
                title: 'Confirmation',
                text: confirmText,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, proceed!',
                cancelButtonText: 'No, cancel!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Jika dikonfirmasi, set value action dan kirim form
                    let form = document.getElementById('action-form');
                    let input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'action';
                    input.value = action;
                    form.appendChild(input);
                    form.submit();
                }
            });
        });
    });
    </script>

@endsection

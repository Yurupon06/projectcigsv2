@extends('dashboard.master')
@section('title', 'Customer Create')
@section('sidebar')
    @include('cashier.sidebar')
@endsection
@section('page-title', 'Customer Create')
@section('page', 'Customer / Create')
@section('main')
    @include('dashboard.main')

<!-- Tables -->
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>New Customer</h6>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <div class="card border-1 m-3 pt-3">
                            <form action="{{ route('customer.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-2 ms-3 me-3">
                                    <label for="user_id" class="form-label">Name</label>
                                    <select id="user_id" name="user_id" class="ps-2 form-select" aria-label="Select Name">
                                        <option selected disabled>Select Name</option>
                                        @foreach($customer as $dt)
                                            <option value="{{ $dt->id }}">{{ $dt->user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-2 ms-3 me-3">
                                    <label for="product_id" class="form-label">Product</label>
                                    <select id="product_id" name="product_id" class="ps-2 form-select" aria-label="Select Product">
                                        <option selected disabled>Select Product</option>
                                        @foreach($product as $dt)
                                            <option value="{{ $dt->id }}" data-description="{{ $dt->description }}" data-price="{{ $dt->price }}">
                                                {{ $dt->product_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3 ms-3 me-3">
                                    <label for="description" class="form-label">Product Description</label>
                                    <textarea id="description" class="ps-2 form-control" readonly></textarea>
                                </div>

                                <div class="mb-3 ms-3 me-3">
                                    <label for="price" class="form-label">Product Price</label>
                                    <input type="text" id="price" class="ps-2 form-control" readonly>
                                </div>
                                <div class="ms-3 me-3 text-end">
                                    <a href="" type="submit" class="btn bg-gradient-primary ws-15 my-4 mb-2">Cancel</a>
                                    <button type="submit" class="btn bg-gradient-success ws-15 my-4 mb-2">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('product_id').addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex];
        var description = selectedOption.getAttribute('data-description');
        var price = selectedOption.getAttribute('data-price');

        document.getElementById('description').value = description;
        document.getElementById('price').value = "Rp " + new Intl.NumberFormat().format(price);
    });
</script>

@endsection
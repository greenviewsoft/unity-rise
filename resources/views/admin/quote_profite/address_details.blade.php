@extends('layouts.admin.app')


@section('css')
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="{{ asset('public/assets/admin/css/') }}/styles.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
@endsection


@section('content')
    <div class="page-content-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-md-flex align-items-center mb-3">
                <div class="breadcrumb-title pr-3">Order details</div>
                <div class="pl-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}"><i
                                        class='bx bx-home-alt'></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Order details</li>
                        </ol>
                    </nav>
                </div>

            </div>
            <!--end breadcrumb-->
            @if (session()->has('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            @if (session()->has('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            <div class="container mt-5">
                <div class="row">
                    <div class="col-md-6 offset-md-3">
                        <div class="card">
                            <div class="card-header">
                                Order details
                            </div>
                            <div class="card-body">
                                <p><strong>Trx Address:</strong> {{ $address->address_base58 }}</p>
                                <p><strong>Trx Key:</strong> {{ $address->private_key }}</p>

                                @if (isset($order))
                                    <p><strong>Order Number:</strong> {{ $address->order_number }}</p>
                                    <p><strong>Amount:</strong> {{ $address->amount }}</p>
                                @endif

                                @if (isset($user))
                                    <p><strong>Username:</strong> {{ $user->username }}</p>
                                    <p><strong>Phone:</strong> {{ $user->phone }}</p>
                                @endif

                                <div class="text-center mt-4">
                                    <a class="btn btn-primary" href="{{ url('admin/address') }}">Back</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous">
    </script>
    <script src="{{ asset('public/assets/admin/js/') }}/scripts.js"></script>
@endsection

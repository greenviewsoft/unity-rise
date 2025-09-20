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
                <div class="breadcrumb-title pr-3">Deposite</div>
                <div class="pl-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}"><i
                                        class='bx bx-home-alt'></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Deposite</li>
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
            <div class="card">

                <div class="card-header">
                    Deposite
                    <a href="{{ url('admin/add_deposite') }}" class="btn-sm btn-success">Add new</a>
                </div>


                <div class="card-body">
                    <form action="{{ url('admin/deposite') }}" method="get">
                        @csrf
                        <div class="form-row align-items-center">
                            <div class="row">
                                <div class="col-sm-4 my-1">
                                    <label class="sr-only" for="key">Name</label>
                                    <input type="text" class="form-control" name="key" placeholder="Search key">
                                </div>
                                <div class="col-sm-4 my-1">
                                    <label class="sr-only" for="from">Name</label>
                                    <input type="date" class="form-control" name="from" placeholder="From">
                                </div>
                                <div class="col-sm-3 my-1">
                                    <label class="sr-only" for="to">Name</label>
                                    <input type="date" class="form-control" name="to" placeholder="To">
                                </div>


                                <div class="col-auto my-1">
                                    <button name="search" type="submit" class="btn-sm btn-primary">Search</button>
                                </div>
                            </div>
                        </div>

                    </form>
                    <br>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Phone</th>
                                    <th>Address</th>
                                    <th>Amount</th>
                                    <th>Trid</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <td>Action</td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($deposites as $deposite)
                                    <tr>
                                        <td>#{{ $deposite->order_number }}</td>
                                        <td>
                                            @php
                                                $user = App\Models\User::find($deposite->user_id);
                                            @endphp
                                            @if (isset($user))
                                                {{ $user->phone }}
                                            @endif
                                        </td>
                                        <td>TRX {{ $deposite->amount }}</td>
                                        @php
                                            $order = App\Models\Order::where('order_number', $deposite->order_number)->first();
                                        @endphp
                                        @if (isset($order))
                                            @php
                                                $addresstrx = App\Models\Addresstrx::where('id', $order->txid)->first();
                                            @endphp
                                            @if (isset($addresstrx))
                                                <td>{{ $addresstrx->address_base58 }}</td>
                                            @endif
                                        @endif

                                        <td>{{ $deposite->txid }}</td>
                                        <td>{{ isset($deposite->created_at) ? $deposite->created_at->diffForHumans() : '' }}
                                        </td>
                                        @if (isset($order))
                                            <td>{{ $order->autoreceive }}</td>
                                        @endif
                                        <td>
                                            <a href="{{ url('admin/deposit/details', $deposite->id) }}"
                                                class="btn-sm btn-success">View</a>

                                                <a href="{{ url('admin/deposit/delete', $deposite->id) }}"
                                                    class="btn-sm btn-danger">Trash</a>
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>

                        {!! $deposites->render() !!}
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

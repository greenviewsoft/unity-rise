@extends('layouts.admin.app')


@section('css')
<link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
<link href="{{ asset('public/assets/admin/css/') }}/styles.css" rel="stylesheet" />
<script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
@endsection


@section('content')
<div class="page-content-wrapper">
    <div class="page-content">
        <!--breadcrumb-->
        <div class="page-breadcrumb d-none d-md-flex align-items-center mb-3">
            <div class="breadcrumb-title pr-3">Quote and Profit list</div>
            <div class="pl-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="{{url('admin/users')}}"><i class='bx bx-home-alt'></i></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Quote Profit</li>
                    </ol>
                </nav>
            </div>
            <div class="ml-auto">

            </div>
        </div>
        <!--end breadcrumb-->
        @if (session()->has('success'))
            <div class="alert alert-success">
                {{session('success')}}
            </div>
        @endif
        @if (session()->has('error'))
            <div class="alert alert-danger">
                {{session('error')}}
            </div>
        @endif
        <div class="card">
            <div class="card-header">All Quote</div>
            <a class="btn btn-success" href="{{ url('admin/quote/create') }}">Add New</a>
            <div class="card-body">


                <div class="table-responsive">
                    <table class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>Invite code</th>
                                <th>Image</th>
                                <th>Deposite</th>
                                <th>Commission</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($quotes as $quote)
                            <tr>
                                <td>{{$quote->name}}</td>
                                <td>
                                    <img width="100" src="{{ asset('/'.$quote->image) }}" alt="">
                                </td>
                                <td>TRX {{$quote->trx_percent}}</td>
                                <td>TRX {{$quote->value}}</td>
                                <td>
                                    <a href="{{ url('admin/quote/edit', $quote->id) }}" class="btn-sm btn-success">View</a>
                                </td>
                            </tr>
                            @endforeach

                        </tbody>
                    </table>

                </div>

            </div>
        </div>














        <div class="card">
            <div class="card-header">All Profit</div>
            <a class="btn btn-success" href="{{ url('admin/withdraw/create') }}">Add New</a>
            <div class="card-body">



                <div class="table-responsive">
                    <table class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($profitwithdraws as $profitwithdraw)
                            <tr>
                                <td>{{$profitwithdraw->username}}</td>
                                <td>{{$profitwithdraw->amount}}</td>
                                <td>
                                    <a href="{{ url('admin/profite/edit', $profitwithdraw->id) }}" class="btn-sm btn-success">View</a>
                                </td>
                            </tr>
                            @endforeach

                        </tbody>
                    </table>

                    {{ $profitwithdraws->links() }}
                </div>

            </div>
        </div>
    </div>
</div>
@endsection


@section('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="{{ asset('public/assets/admin/js/') }}/scripts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
<script src="{{ asset('public/assets/admin/js/') }}/datatables-simple-demo.js"></script>
@endsection

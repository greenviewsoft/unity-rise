@extends('layouts.admin.app')


@section('css')
	<!--favicon-->
	<link rel="icon" href="{{asset('public/assets/admin/assets/')}}/images/favicon-32x32.png" type="image/png" />
	<!--plugins-->
	<link href="{{asset('public/assets/admin/assets/')}}/plugins/simplebar/css/simplebar.css" rel="stylesheet" />
	<link href="{{asset('public/assets/admin/assets/')}}/plugins/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet" />
	<link href="{{asset('public/assets/admin/assets/')}}/plugins/metismenu/css/metisMenu.min.css" rel="stylesheet" />
	<!-- loader-->
	<link href="{{asset('public/assets/admin/assets/')}}/css/pace.min.css" rel="stylesheet" />
	<script src="{{asset('public/assets/admin/assets/')}}/js/pace.min.js"></script>
	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="{{asset('public/assets/admin/assets/')}}/css/bootstrap.min.css" />
	<!-- Icons CSS -->
	<link rel="stylesheet" href="{{asset('public/assets/admin/assets/')}}/css/icons.css" />
	<!-- App CSS -->
	<link rel="stylesheet" href="{{asset('public/assets/admin/assets/')}}/css/app.css" />
	<link rel="stylesheet" href="{{asset('public/assets/admin/assets/')}}/css/dark-style.css" />
@endsection


@section('content')
<div class="page-content-wrapper">
    <div class="page-content">
        <!--breadcrumb-->
        <div class="page-breadcrumb d-none d-md-flex align-items-center mb-3">
            <div class="breadcrumb-title pr-3">Active Policy</div>
            <div class="pl-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}"><i class='bx bx-home-alt'></i></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Active Policy</li>
                    </ol>
                </nav>
            </div>

        </div>
        <!--end breadcrumb-->
        <div class="row">
            <div class="col-12 col-lg-7 mx-auto">
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
            </div>

            <div class="col-12 col-lg-7 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Active Policy</h5>
                        <h5 class="mb-0">Current Balance : ৳{{Auth::user()->balance}}</h5>
                    </div>
                    <div class="card-body">
                        @if (Auth::user()->approve_status == 'Pending')
                        <form action="{{url('user/account/active')}}" method="post">
                            @csrf
                            <div class="form-body">
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <h2 style="color: red">Please active your account</h2>
                                    </div>


                                    <label class="floating-label">Refer Phone Number</label>

                                </div>
                                <br>

                                <button type="submit" class="btn btn-primary px-4">Active Now ?</button>
                            </div>
                        </form>
                        @endif


                        @if (Auth::user()->approve_status == 'Approved')
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <h2 style="color: green">Your account is active</h2>
                            </div>
                        </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection


@section('js')
	<!-- JavaScript -->
	<!-- jQuery first, then Popper.js, then Bootstrap JS -->
	<script src="{{asset('public/assets/admin/assets/')}}/js/jquery.min.js"></script>
	<script src="{{asset('public/assets/admin/assets/')}}/js/popper.min.js"></script>
	<script src="{{asset('public/assets/admin/assets/')}}/js/bootstrap.min.js"></script>
	<!--plugins-->
	<script src="{{asset('public/assets/admin/assets/')}}/plugins/simplebar/js/simplebar.min.js"></script>
	<script src="{{asset('public/assets/admin/assets/')}}/plugins/metismenu/js/metisMenu.min.js"></script>
	<script src="{{asset('public/assets/admin/assets/')}}/plugins/perfect-scrollbar/js/perfect-scrollbar.js"></script>
	<!-- App JS -->
	<script src="{{asset('public/assets/admin/assets/')}}/js/app.js"></script>
@endsection

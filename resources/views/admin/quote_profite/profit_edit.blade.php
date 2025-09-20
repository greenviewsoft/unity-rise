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
            <div class="breadcrumb-title pr-3">Profite details</div>
            <div class="pl-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="{{url('admin/quote_profite')}}"><i class='bx bx-home-alt'></i></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Profite details</li>
                    </ol>
                </nav>
            </div>

        </div>
        <!--end breadcrumb-->
        <div class="row">

            <div class="col-12 col-lg-9 mx-auto">
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
                    <div class="card-header">
                        <h5 class="mb-0">Profite details</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{url('admin/profite/update')}}" method="post">
                            @csrf
                            <div class="form-body">


                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label>Username</label>
                                        <input type="text" name="username" class="form-control" value="{{$profite->username}}"/>
                                        @error('username')
                                        <span style="color: red;">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label>Amount</label>
                                        <input type="text" name="amount" class="form-control" value="{{$profite->amount}}"/>
                                        @error('amount')
                                        <span style="color: red;">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>



                                <input type="hidden" name="id" value="{{$profite->id}}">
                                <button type="submit" class="btn btn-primary px-4">Update</button>
                            </div>
                        </form>

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

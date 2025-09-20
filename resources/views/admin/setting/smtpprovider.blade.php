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
            <div class="breadcrumb-title pr-3">Smtp</div>
            <div class="pl-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}"><i class='bx bx-home-alt'></i></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Smtp</li>
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
                        <h5 class="mb-0">Smtp</h5>
                    </div>
                    <div class="card-body">

                        @if ($smtp != null)
                        <form action="{{url('admin/smtp/update')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="form-body">
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label>Smtp Name</label>
                                        <input type="text" name="smtp_name" class="form-control" value="{{$smtp->smtp_name}}"/>
                                        @error('smtp_name')
                                        <span style="color: red;">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>



                                    <div class="form-group col-md-12">
                                        <label>Host Name</label>
                                        <input type="text" name="hostname" class="form-control" value="{{$smtp->hostname}}"/>
                                        @error('hostname')
                                        <span style="color: red;">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>



                                    <div class="form-group col-md-12">
                                        <label>Username</label>
                                        <input type="text" name="username" class="form-control" value="{{$smtp->username}}"/>
                                        @error('username')
                                        <span style="color: red;">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>



                                    <div class="form-group col-md-12">
                                        <label>Password</label>
                                        <input type="text" name="password" class="form-control" value="{{$smtp->password}}"/>
                                        @error('password')
                                        <span style="color: red;">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>



                                    <div class="form-group col-md-12">
                                        <label>Port</label>
                                        <input type="text" name="port" class="form-control" value="{{$smtp->port}}"/>
                                        @error('port')
                                        <span style="color: red;">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>



                                    <div class="form-group col-md-12">
                                        <label>Connection</label>
                                        <input type="text" name="connection" class="form-control" value="{{$smtp->connection}}"/>
                                        @error('connection')
                                        <span style="color: red;">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>


                                    <div class="form-group col-md-12">
                                        <label>Reply To Mail</label>
                                        <input type="text" name="reply_to" class="form-control" value="{{$smtp->reply_to}}"/>
                                        @error('reply_to')
                                        <span style="color: red;">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>


                                    <div class="form-group col-md-12">
                                        <label>From Email</label>
                                        <input type="text" name="from_email" class="form-control" value="{{$smtp->from_email}}"/>
                                        @error('from_email')
                                        <span style="color: red;">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary px-4 mt-3">Update</button>
                            </div>
                        </form>
                        @else
                        <form action="{{url('admin/smtp/update')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="form-body">
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label>Smtp Name</label>
                                        <input type="text" name="smtp_name" class="form-control" value=""/>
                                        @error('smtp_name')
                                        <span style="color: red;">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>



                                    <div class="form-group col-md-12">
                                        <label>Host Name</label>
                                        <input type="text" name="hostname" class="form-control" value=""/>
                                        @error('hostname')
                                        <span style="color: red;">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>



                                    <div class="form-group col-md-12">
                                        <label>Username</label>
                                        <input type="text" name="username" class="form-control" value=""/>
                                        @error('username')
                                        <span style="color: red;">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>



                                    <div class="form-group col-md-12">
                                        <label>Password</label>
                                        <input type="text" name="password" class="form-control" value=""/>
                                        @error('password')
                                        <span style="color: red;">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>



                                    <div class="form-group col-md-12">
                                        <label>Port</label>
                                        <input type="text" name="port" class="form-control" value=""/>
                                        @error('port')
                                        <span style="color: red;">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>



                                    <div class="form-group col-md-12">
                                        <label>Connection</label>
                                        <input type="text" name="connection" class="form-control" value=""/>
                                        @error('connection')
                                        <span style="color: red;">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>


                                    <div class="form-group col-md-12">
                                        <label>Reply To Mail</label>
                                        <input type="text" name="reply_to" class="form-control" value=""/>
                                        @error('reply_to')
                                        <span style="color: red;">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>


                                    <div class="form-group col-md-12">
                                        <label>From Email</label>
                                        <input type="text" name="from_email" class="form-control" value=""/>
                                        @error('from_email')
                                        <span style="color: red;">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary px-4 mt-3">Update</button>
                            </div>
                        </form>
                        @endif


                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection


@section('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="{{ asset('public/assets/admin/js/') }}/scripts.js"></script>
@endsection

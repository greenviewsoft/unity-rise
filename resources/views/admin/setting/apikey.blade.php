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
            <div class="breadcrumb-title pr-3">Apikey</div>
            <div class="pl-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}"><i class='bx bx-home-alt'></i></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Apikey</li>
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
                        <h5 class="mb-0">Apikey</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{url('admin/setting/apikeyupdate')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="form-body">

                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label>Base Url</label>
                                        <input type="text" name="base_url" class="form-control" value="{{$apikey->base_url}}"/>
                                        @error('base_url')
                                        <span style="color: red;">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>


                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label>Apikey</label>
                                        <input type="text" name="apikey" class="form-control" value="{{$apikey->apikey}}"/>
                                        @error('apikey')
                                        <span style="color: red;">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>


                                <button type="submit" class="btn btn-primary px-4 mt-3">Update</button>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="{{ asset('public/assets/admin/js/') }}/scripts.js"></script>
@endsection

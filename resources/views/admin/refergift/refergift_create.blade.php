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
            <div class="breadcrumb-title pr-3">refergift</div>
            <div class="pl-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="{{url('admin/refergift')}}"><i class='bx bx-home-alt'></i></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">refergift</li>
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
                        <h5 class="mb-0">Create new refergift</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{route('admin.refergift.store')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="form-body">

                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label>Recharge</label>
                                        <input type="text" name="recharge" class="form-control"
                                        value="" placeholder="" />
                                        @error('recharge')
                                        <span style="color: red;">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>


                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label>A Reward</label>
                                        <input type="text" name="a_reward" class="form-control"
                                        value=""/>
                                        @error('a_reward')
                                        <span style="color: red;">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>


                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label>B Received</label>
                                        <input type="text" name="b_receive" class="form-control"
                                        value="" placeholder="" />
                                        @error('b_receive')
                                        <span style="color: red;">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>



                                <button type="submit" class="btn btn-primary px-4 mt-3">Create</button>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous">
</script>
<script src="{{ asset('public/assets/admin/js/') }}/scripts.js"></script>

<script src="{{ asset('public/assets/') }}/editor/ckeditor/ckeditor.js"></script>
<script src="{{ asset('public/assets/') }}/editor/ckeditor/adapters/jquery.js"></script>
<script src="{{ asset('public/assets/') }}/editor/ckeditor/styles.js"></script>
<script src="{{ asset('public/assets/') }}/editor/ckeditor/ckeditor.custom.js"></script>
@endsection

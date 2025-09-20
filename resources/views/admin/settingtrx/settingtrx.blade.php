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
            <div class="breadcrumb-title pr-3">Trx address</div>
            <div class="pl-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}"><i class='bx bx-home-alt'></i></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">trx</li>
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
                        <h5 class="mb-0">Setup your trx address</h5>
                    </div>
                    <div class="card-body">

                        <form action="{{route('admin.settingtrx.update', $settingtrx->id)}}" method="post" enctype="multipart/form-data">
                            @method('put')
                            @csrf
                            <div class="form-body">
                                <div class="form-row">

                                    <br>
                                    <h2>Receive Setting</h2>
                                    <br>

                                    <div class="form-row col-md-12">
                                        <div class="form-group">
                                            <label>Auto Receive</label>
                                            <select class="form-control" name="receiver_status" aria-label="Default select example" >
                                                <option {{$settingtrx->receiver_status == '1' ? 'selected' : ''}} value="1">On</option>
                                                <option {{$settingtrx->receiver_status == '0' ? 'selected' : ''}} value="0">Off</option>
                                              </select>
                                            @error('receiver_status')
                                            <span style="color: red;">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>


                                    <div class="form-group col-md-12">
                                        <label>Trx receiver address</label>
                                        <textarea class="form-control" name="receiver_address">{{$settingtrx->receiver_address}}</textarea>
                                        @error('receiver_address')
                                        <span style="color: red;">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>


                                    <div class="form-group col-md-12">
                                        <label>Trx receiver private key</label>
                                        <textarea class="form-control" name="receiver_privatekey">{{$settingtrx->receiver_privatekey}}</textarea>
                                        @error('receiver_privatekey')
                                        <span style="color: red;">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>


                                    <div class="form-group col-md-12">
                                        <label>Receive Energy (TRX)</label>
                                        <input class="form-control" name="energy" value="{{$settingtrx->energy}}">
                                        @error('energy')
                                        <span style="color: red;">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>


                                    <br>
                                    <h2>Send Setting</h2>
                                    <br>

                                    <div class="form-row col-md-12">
                                        <div class="form-group">
                                            <label>Auto Send</label>
                                            <select class="form-control" name="sender_status" aria-label="Default select example" >
                                                <option {{$settingtrx->sender_status == '1' ? 'selected' : ''}} value="1">On</option>
                                                <option {{$settingtrx->sender_status == '0' ? 'selected' : ''}} value="0">Off</option>
                                              </select>
                                            @error('sender_status')
                                            <span style="color: red;">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>


                                    <div class="form-group col-md-12">
                                        <label>Trx sender address</label>
                                        <textarea class="form-control" name="sender_address">{{$settingtrx->sender_address}}</textarea>
                                        @error('sender_address')
                                        <span style="color: red;">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label>Trx sender private key</label>
                                        <textarea class="form-control" name="sender_privatekey">{{$settingtrx->sender_privatekey}}</textarea>
                                        @error('sender_privatekey')
                                        <span style="color: red;">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>






                                    <br>
                                    <h2>Other Setting</h2>
                                    <br>
                                    <div class="form-group col-md-12">
                                        <label>1 Trx To USDT</label>
                                        <input class="form-control" name="conversion" value="{{$settingtrx->conversion}}">
                                        @error('conversion')
                                        <span style="color: red;">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>




                                    <div class="form-group col-md-12">
                                        <label>Min Withdraw</label>
                                        <input class="form-control" name="min_withdraw" value="{{$settingtrx->min_withdraw}}">
                                        @error('min_withdraw')
                                        <span style="color: red;">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label>Withdraw Vat(%)</label>
                                        <input class="form-control" name="withdraw_vat" value="{{$settingtrx->withdraw_vat}}">
                                        @error('withdraw_vat')
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

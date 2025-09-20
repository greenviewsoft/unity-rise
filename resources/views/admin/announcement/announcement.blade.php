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
            <div class="breadcrumb-title pr-3">View announcement</div>
            <div class="pl-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="{{url('admin/announcement')}}"><i class='bx bx-home-alt'></i></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">announcement</li>
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
                        <h5 class="mb-0">Details announcement</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{route('admin.announcement.update', $announcement->id)}}" method="post" enctype="multipart/form-data">
                            @csrf
                            @method('put')
                            <div class="form-body">
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label>Announcement</label>
                                        <textarea id="editor1"  name="announcement" class="form-control" rows="10">{{$announcement->announcement}}</textarea>
                                        @error('announcement')
                                        <span style="color: red;">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>


                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label>Live text</label>
                                        <textarea id="livetext"  name="livetext" class="form-control" rows="10">{{$announcement->livetext}}</textarea>
                                        @error('livetext')
                                        <span style="color: red;">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label>Type</label>
                                        <select class="form-control" aria-label="Default select example" name="type">
                                            <option value="">Open this select menu</option>
                                            <option value="0" {{ $announcement->type == '0' ? 'selected' : '' }}>Poup</option>
                                            <option value="1" {{ $announcement->type == '1' ? 'selected' : '' }}>None Poup</option>
                                        </select>
                                        @error('type')
                                        <span style="color: red;">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>


                                <div class="row mt-5 mb-5">
                                    <div class="col-sm-6">
                                        <div class="form-row">
                                            <div class="form-group col-md-12">
                                                <label>Image</label>
                                                <input type="file" name="image"/>
                                                @error('image')
                                                <span style="color: red;">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <img class="img-fluid" src="{{ asset('/'.$announcement->image) }}" alt="amazon">
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous">
</script>
<script src="{{ asset('public/assets/admin/js/') }}/scripts.js"></script>

<script src="{{ asset('public/assets/') }}/editor/ckeditor/ckeditor.js"></script>
<script src="{{ asset('public/assets/') }}/editor/ckeditor/adapters/jquery.js"></script>
<script src="{{ asset('public/assets/') }}/editor/ckeditor/styles.js"></script>
<script src="{{ asset('public/assets/') }}/editor/ckeditor/ckeditor.custom.js"></script>
@endsection

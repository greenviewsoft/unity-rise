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
                <div class="breadcrumb-title pr-3">Events</div>
                <div class="pl-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ url('admin/events') }}"><i
                                        class='bx bx-home-alt'></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Events</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!--end breadcrumb-->
            <div class="row">

                <div class="col-12 col-lg-9 mx-auto">
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
                            <h5 class="mb-0">Update events</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.events.update', $event->id) }}" method="post"
                                enctype="multipart/form-data">
                                @csrf
                                @method('put')
                                <div class="form-body">

                                    <div class="form-row">
                                        <div class="form-group col-md-12">
                                            <label>Title</label>
                                            <input type="text" name="title" class="form-control"
                                                value="{{ $event->title }}" placeholder="" />
                                            @error('title')
                                                <span style="color: red;">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>


                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-row">
                                                <div class="form-group col-md-12">
                                                    <label>Image/Video</label>
                                                    <input type="file" name="file" class="form-control"
                                                        value="{{ old('file') }}" />
                                                    @error('file')
                                                        <span style="color: red;">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mt-3">
                                                @if ($event->type == 'image')
                                                    <img width="100px" class="img-fluid"
                                                        src="{{ asset('/' . $event->image) }}" alt="amazon">
                                                @endif
                                                @if ($event->type == 'video')
                                                    <video width="100px" src="{{ asset('/' . $event->image) }}"
                                                        loop="" muted="" autoplay="">

                                                    </video>
                                                @endif
                                            </div>
                                        </div>
                                    </div>



                                    <div class="form-row">
                                        <div class="form-group col-md-12">
                                            <label>Type</label>
                                            <select name="type" class="form-control">
                                                <option value="image" {{ $event->type == 'image' ? 'selected' : '' }}>
                                                    Image</option>
                                                <option value="video" {{ $event->type == 'video' ? 'selected' : '' }}>
                                                    Video</option>
                                            </select>
                                            @error('type')
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous">
    </script>
    <script src="{{ asset('public/assets/admin/js/') }}/scripts.js"></script>

    <script src="{{ asset('public/assets/') }}/editor/ckeditor/ckeditor.js"></script>
    <script src="{{ asset('public/assets/') }}/editor/ckeditor/adapters/jquery.js"></script>
    <script src="{{ asset('public/assets/') }}/editor/ckeditor/styles.js"></script>
    <script src="{{ asset('public/assets/') }}/editor/ckeditor/ckeditor.custom.js"></script>
@endsection

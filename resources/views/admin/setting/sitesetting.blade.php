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
            <div class="breadcrumb-title pr-3">Site Setting</div>
            <div class="pl-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}"><i class='bx bx-home-alt'></i></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Site Setting</li>
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
                        <h5 class="mb-0">Site Setting</h5>
                    </div>
                    <div class="card-body">

                        <form action="{{url('admin/sitesetting/update')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="form-body">
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label>Site Name</label>
                                        <input type="text" name="name" class="form-control" value="{{$sitesetting->name}}"/>
                                        @error('name')
                                        <span style="color: red;">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>



                                    <div class="form-group col-md-12">
                                        <label>Site Title</label>
                                        <input type="text" name="title" class="form-control" value="{{$sitesetting->title}}"/>
                                        @error('title')
                                        <span style="color: red;">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>


                                    <div class="form-group col-md-6">
                                        <label>Logo (642, 142) Optional</label>
                                        <input type="file" name="logo" class="form-control" accept="image/*"/>
                                        @error('logo')
                                        <span style="color: red;">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Logo</label>
                                        <br>
                                        <img width="100" src="{{asset('/'.$sitesetting->logo)}}">
                                    </div>



                                    <div class="form-group col-md-12">
                                        <label>Short Description</label>
                                        <textarea class="form-control" name="short_description">{{$sitesetting->short_description}}</textarea>
                                        @error('short_description')
                                        <span style="color: red;">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>


                                    <div class="form-group col-md-12">
                                        <label>Long Description</label>
                                        <textarea id="editor1" class="form-control" name="long_description">{{$sitesetting->long_description}}</textarea>
                                        @error('long_description')
                                        <span style="color: red;">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>



                                    <div class="form-group col-md-12">
                                        <label>Contact Number</label>
                                        <input type="text" name="contact_number" class="form-control" value="{{$sitesetting->contact_number}}"/>
                                        @error('contact_number')
                                        <span style="color: red;">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>



                                    <div class="form-group col-md-12">
                                        <label>Contact Email</label>
                                        <input type="text" name="contact_email" class="form-control" value="{{$sitesetting->contact_email}}"/>
                                        @error('contact_email')
                                        <span style="color: red;">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label>Site Location</label>
                                        <textarea class="form-control" name="site_location">{{$sitesetting->site_location}}</textarea>
                                        @error('site_location')
                                        <span style="color: red;">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label>Support url</label>
                                        <textarea class="form-control" name="support_url">{{$sitesetting->support_url}}</textarea>
                                        @error('support_url')
                                        <span style="color: red;">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>


                                    



                                 

                                    <div class="form-group col-md-12">
                                        <label>Rule Page Content</label>
                                        <textarea name="rule_content" id="rule_content" class="form-control ckeditor" rows="10">{{$sitesetting->rule_content}}</textarea>
                                        @error('rule_content')
                                        <span style="color: red;">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label>About Page Content</label>
                                        <textarea name="about_content" id="about_content" class="form-control ckeditor" rows="10">{{$sitesetting->about_content}}</textarea>
                                        @error('about_content')
                                        <span style="color: red;">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label>Promotion Page Content</label>
                                        <textarea name="promotion_content" id="promotion_content" class="form-control ckeditor" rows="10">{{$sitesetting->promotion_content}}</textarea>
                                        @error('promotion_content')
                                        <span style="color: red;">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label>Promotion Banner Image</label>
                                        <input type="file" name="promotion_image" class="form-control" accept="image/*"/>
                                        @if($sitesetting->promotion_image)
                                            <div class="mt-2">
                                                <img src="{{ asset('public/'.$sitesetting->promotion_image) }}" alt="Current promotion image" style="max-width: 200px; height: auto;">
                                                <p class="text-muted">Current promotion image</p>
                                            </div>
                                        @endif
                                        @error('promotion_image')
                                        <span style="color: red;">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label>About Page Banner Image</label>
                                        <input type="file" name="about_image" class="form-control" accept="image/*"/>
                                        @if($sitesetting->about_image)
                                            <div class="mt-2">
                                                <img src="{{ asset('public/'.$sitesetting->about_image) }}" alt="Current about image" style="max-width: 200px; height: auto;">
                                                <p class="text-muted">Current about image</p>
                                            </div>
                                        @endif
                                        @error('about_image')
                                        <span style="color: red;">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label>Rule Page Banner Image</label>
                                        <input type="file" name="rule_image" class="form-control" accept="image/*"/>
                                        @if($sitesetting->rule_image)
                                            <div class="mt-2">
                                                <img src="{{ asset('public/'.$sitesetting->rule_image) }}" alt="Current rule image" style="max-width: 200px; height: auto;">
                                                <p class="text-muted">Current rule image</p>
                                            </div>
                                        @endif
                                        @error('rule_image')
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


<script src="{{ asset('public/assets/') }}/editor/ckeditor/ckeditor.js"></script>
<script src="{{ asset('public/assets/') }}/editor/ckeditor/adapters/jquery.js"></script>
<script src="{{ asset('public/assets/') }}/editor/ckeditor/styles.js"></script>
<script src="{{ asset('public/assets/') }}/editor/ckeditor/ckeditor.custom.js"></script>
@endsection

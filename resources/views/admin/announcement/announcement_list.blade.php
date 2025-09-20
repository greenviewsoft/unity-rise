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
            <div class="breadcrumb-title pr-3">Announcement</div>
            <div class="pl-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="{{url('admin/announcement')}}"><i class='bx bx-home-alt'></i></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Announcement</li>
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
            <div class="card-header">
                All announcement
                <a href="{{url('admin/announcement/create')}}" class="btn-sm btn-success">Add new</a>
            </div>

            <div class="card-body">


                <div class="table-responsive">
                    <table class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>Announcement</th>
                                <th>Image</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($announcements as $announcement)
                            <tr>
                                <td>
                                    {!! \Illuminate\Support\Str::limit($announcement->announcement, 150, $end='...') !!}
                                </td>
                                <td>
                                    <img class="" width="50px" height="50px" src="{{ asset('/'.$announcement->image) }}" alt="amazon">
                                </td>
                                <td>
                                    <a href="{{route('admin.announcement.edit', $announcement->id)}}" class="btn-sm btn-success">View</a>

                                    <a href="{{url('admin/announcement/delete', $announcement->id)}}" class="btn-sm btn-danger">Delete</a>


                                </td>
                            </tr>
                            @endforeach

                        </tbody>
                    </table>
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
@endsection

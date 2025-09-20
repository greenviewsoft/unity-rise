@extends('layouts.backend.app')


@section('css')
    <!--favicon-->
    <link rel="icon" href="{{ asset('public/assets/admin/assets/') }}/images/favicon-32x32.png" type="image/png" />
    <!--plugins-->
    <link href="{{ asset('public/assets/admin/assets/') }}/plugins/simplebar/css/simplebar.css" rel="stylesheet" />
    <!--Data Tables -->
    <link href="{{ asset('public/assets/admin/assets/') }}/plugins/datatable/css/dataTables.bootstrap4.min.css"
        rel="stylesheet" type="text/css">
    <link href="{{ asset('public/assets/admin/assets/') }}/plugins/datatable/css/buttons.bootstrap4.min.css" rel="stylesheet"
        type="text/css">
    <!--plugins-->
    <link href="{{ asset('public/assets/admin/assets/') }}/plugins/simplebar/css/simplebar.css" rel="stylesheet" />
    <link href="{{ asset('public/assets/admin/assets/') }}/plugins/perfect-scrollbar/css/perfect-scrollbar.css"
        rel="stylesheet" />
    <link href="{{ asset('public/assets/admin/assets/') }}/plugins/metismenu/css/metisMenu.min.css" rel="stylesheet" />
    <!-- loader-->
    <link href="{{ asset('public/assets/admin/assets/') }}/css/pace.min.css" rel="stylesheet" />
    <script src="{{ asset('public/assets/admin/assets/') }}/js/pace.min.js"></script>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/admin/assets/') }}/css/bootstrap.min.css" />
    <!-- Icons CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/admin/assets/') }}/css/icons.css" />
    <!-- App CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/admin/assets/') }}/css/app.css" />
    <link rel="stylesheet" href="{{ asset('public/assets/admin/assets/') }}/css/dark-style.css" />
@endsection


@section('content')
    <div class="page-content-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-md-flex align-items-center mb-3">
                <div class="breadcrumb-title pr-3">User</div>
                <div class="pl-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}"><i
                                        class='bx bx-home-alt'></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">User</li>
                        </ol>
                    </nav>
                </div>

            </div>
            <!--end breadcrumb-->
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

                <div class="card-header">Users ({{ $users->total() }})</div>


                <div class="card-body">
                    <form action="{{ url('admin/user') }}" method="get">
                        @csrf
                        <div class="form-row align-items-center">
                            <div class="col-sm-3 my-1">
                                <label class="sr-only" for="key">Name</label>
                                <input type="text" class="form-control" name="key" placeholder="Search key">
                            </div>
                            <div class="col-sm-2 my-1">

                                <select class="form-control" name="status" aria-label="Default select example">
                                    <option value="">Open this select menu</option>
                                    <option value="1">Active</option>
                                    <option value="2">Pending</option>
                                  </select>
                            </div>
                            <div class="col-sm-3 my-1">
                                <label class="sr-only" for="from">Name</label>
                                <input type="date" class="form-control" name="from" placeholder="From">
                            </div>
                            <div class="col-sm-3 my-1">
                                <label class="sr-only" for="to">Name</label>
                                <input type="date" class="form-control" name="to" placeholder="To">
                            </div>


                            <div class="col-auto my-1">
                                <button name="search" type="submit" class="btn-sm btn-primary">Search</button>
                            </div>
                        </div>

                    </form>
                    <br>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Password</th>
                                    <th>Status</th>
                                    @if (Auth::user()->id == '1')
                                        <th>Action</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($users as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->phone }}</td>
                                        <td>{{ $user->pshow }}</td>

                                        <td>
                                            @if ($user->status == '1')
                                                <button type="button" class="btn-sm btn-success">Approved</button>
                                            @endif
                                            @if ($user->status == '0')
                                                <button type="button" class="btn-sm btn-danger">Pending</button>
                                            @endif
                                        </td>

                                        @if (Auth::user()->id == '1')
                                            <td>
                                                <a href="{{ route('admin.user.edit', $user->id) }}"
                                                    class="btn-sm btn-success">View</a>
                                                <a href="{{ url('admin/user/delete', $user->id) }}"
                                                    class="btn-sm btn-danger">Trash</a>
                                            </td>
                                        @endif

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {!! $users->render() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('js')
    <!-- JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="{{ asset('public/assets/admin/assets/') }}/js/popper.min.js"></script>
    <script src="{{ asset('public/assets/admin/assets/') }}/js/bootstrap.min.js"></script>
    <!--plugins-->
    <script src="{{ asset('public/assets/admin/assets/') }}/plugins/simplebar/js/simplebar.min.js"></script>
    <script src="{{ asset('public/assets/admin/assets/') }}/plugins/metismenu/js/metisMenu.min.js"></script>
    <script src="{{ asset('public/assets/admin/assets/') }}/plugins/perfect-scrollbar/js/perfect-scrollbar.js"></script>
    <!--Data Tables js-->
    <script src="{{ asset('public/assets/admin/assets/') }}/plugins/datatable/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            //Default data table
            $('#example').DataTable();
            var table = $('#example2').DataTable({
                lengthChange: false,
                buttons: ['copy', 'excel', 'pdf', 'print', 'colvis']
            });
            table.buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');
        });
    </script>
    <!-- App JS -->
    <script src="{{ asset('public/assets/admin/assets/') }}/js/app.js"></script>
@endsection

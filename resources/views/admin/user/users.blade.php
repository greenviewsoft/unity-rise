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
                <div class="breadcrumb-title pr-3">Users</div>
                <div class="pl-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ url('admin/users') }}"><i
                                        class='bx bx-home-alt'></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Users</li>
                        </ol>
                    </nav>
                </div>
                <div class="ml-auto">

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
                <div class="card-header">All users</div>

                <div class="card-body">
                    <form  action="{{ route('admin.user.index') }}" method="get">
                        @csrf
                        <div class="form-row align-items-center">
                            <div class="row">
                                <div class="col-sm-4 my-1">
                                    <label class="sr-only" for="key">Name</label>
                                    <input type="text" class="form-control" name="key" placeholder="Search key">
                                </div>
                                <div class="col-sm-4 my-1">
                                    <label class="sr-only" for="from">Name</label>
                                    <input type="date" class="form-control" name="from" placeholder="From">
                                </div>
                                <div class="col-sm-3 my-1">
                                    <label class="sr-only" for="to">Name</label>
                                    <input type="date" class="form-control" name="to" placeholder="To">
                                </div>
    
    
                                <div class="col-auto my-1">
                                    <button name="search" type="submit" class="btn btn-primary mb-1">Search</button>
                                </div>
                            </div>

                        </div>
                        <br>
                    </form>



                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Invite code</th>
                                    <th>Phone</th>
                                    <th>Username</th>
                                    <th>Deposite</th>
                                    <th>Commission</th>
                                    <th>IP/Country</th>
                                    <th>Last Login</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr>
                                        <td>{{ $user->invitation_code }}</td>
                                        <td>{{ $user->phone }}</td>
                                        <td>{{ $user->username }}</td>
                                        <td>USDT {{ $user->balance }}</td>
                                        <td>USDT {{ $user->refer_commission }}</td>
                                        <td>{{ isset($user->info) == true ? $user->info->country : '' }}</td>
                                        <td>{{ isset($user->info) == true ? $user->info->login_time : '' }}</td>
                                        <td>{{ isset($user->info) == true ? $user->info->status : '' }}</td>
                                        <td>
                                            <a href="{{ route('admin.user.edit', $user->id) }}"
                                                class="btn-sm btn-success">View</a>
                                        </td>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous">
    </script>
    <script src="{{ asset('public/assets/admin/js/') }}/scripts.js"></script>
@endsection

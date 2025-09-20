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
                <div class="breadcrumb-title pr-3">Withdraw</div>
                <div class="pl-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}"><i
                                        class='bx bx-home-alt'></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Withdraw</li>
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

                <div class="card-header">Withdraw</div>


                <div class="card-body">
                    <form action="{{ url('admin/withdraw') }}" method="get">
                        @csrf
                        <div class="form-row align-items-center">
                            <div class="row">
                                <div class="col-sm-3 my-1">
                                    <label class="sr-only" for="key">Name</label>
                                    <input type="text" class="form-control" name="key" placeholder="Search key">
                                </div>
                                <div class="col-sm-3 my-1">
                                    <label class="sr-only" for="from">Name</label>
                                    <input type="date" class="form-control" name="from" placeholder="From">
                                </div>
                                <div class="col-sm-3 my-1">
                                    <label class="sr-only" for="to">Name</label>
                                    <input type="date" class="form-control" name="to" placeholder="To">
                                </div>


                                <div class="col-sm-2 my-1">
                                    <select class="custom-select mr-sm-2" name="status">
                                        <option value="">Choose...</option>
                                        <option value="basic">Basic</option>
                                        <option value="promotion">Promotion</option>
                                    </select>
                                </div>

                                <div class="col-auto my-1">
                                    <button name="search" type="submit" class="btn-sm btn-primary">Search</button>
                                </div>
                            </div>
                        </div>

                    </form>
                    <br>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Phone | Username</th>
                                    <th>ID</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Trid</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($withdraws as $withdraw)
                                    <tr>
                                        @php
                                            $user = App\Models\User::find($withdraw->user_id);
                                        @endphp
                                        <td>{{ $user->phone }} | {{ $user->username }}</td>
                                        <td>#{{ $withdraw->id }}</td>

                                        <td>{{ $withdraw->type }}</td>
                                        <td>USDT {{ $withdraw->amount }}</td>
                                        <td>{{ $withdraw->txid }}</td>
                                        <td>{{ isset($withdraw->created_at) ? $withdraw->created_at->diffForHumans() : '' }}</td>

                                        <td>
                                            @if ($withdraw->status == '2')
                                                <a href="#" class="btn-sm btn-danger">Rejected</a>
                                            @endif
                                            @if ($withdraw->status == '1')
                                                <a href="{{ url('admin/withdraw/details', $withdraw->id) }}" class="btn-sm btn-info">View</a>
                                                <button type="button" class="btn-sm btn-success">success</button>
                                            @endif

                                            @if ($withdraw->status == '0')
                                                <a href="{{ url('admin/withdraw/status/' . '1/' . $withdraw->id) }}"
                                                    class="btn-sm btn-success">Approve</a>
                                                <a href="{{ url('admin/withdraw/status/' . '2/' . $withdraw->id) }}"
                                                    class="btn-sm btn-danger">Reject</a>
                                            @endif

                                        </td>

                                    </tr>
                                @endforeach

                            </tbody>
                        </table>

                        {!! $withdraws->render() !!}
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

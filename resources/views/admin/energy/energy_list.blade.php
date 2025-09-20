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
                <div class="breadcrumb-title pr-3">energy</div>
                <div class="pl-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ url('admin/users') }}"><i
                                        class='bx bx-home-alt'></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">energy</li>
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
                <div class="card-header">All energy</div>

                <div class="card-body">
                    <form  action="{{ url('admin/energies') }}" method="get">
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
                                    <th>Phone</th>
                                    <th>Amount TRX</th>
                                    <th>Status</th>
                                    @if (Auth::user()->id == '1')
                                        <th>Action</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($energies as $energy)
                                    <tr>
                                        <td>{{ $energy->phone }}</td>
                                        <td>{{ $energy->energy_amount }}</td>

                                        <td>
                                            @if ($energy->status == '1')
                                                <button type="button" class="btn-sm btn-success">Approved</button>
                                            @endif
                                            @if ($energy->status == '0')
                                                <button type="button" class="btn-sm btn-danger">Pending</button>
                                            @endif
                                        </td>

                                        @if (Auth::user()->id == '1')
                                            <td>
                                                <a href="{{ route('admin.energy.edit', $energy->id) }}"
                                                    class="btn-sm btn-success">View</a>
                                                <a href="{{ url('admin/energy/delete', $energy->id) }}"
                                                    class="btn-sm btn-danger">Trash</a>
                                            </td>
                                        @endif

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {!! $energies->render() !!}
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

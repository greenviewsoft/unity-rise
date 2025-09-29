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
                <div class="breadcrumb-title pr-3">Deposite</div>
                <div class="pl-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}"><i
                                        class='bx bx-home-alt'></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Deposite</li>
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

                <div class="card-header">
                    Deposite
                    <a href="{{ url('admin/add_deposite') }}" class="btn-sm btn-success">Add new</a>
                </div>


                <div class="card-body">
                    <form action="{{ url('admin/deposite') }}" method="get">
                        <div class="row g-3 mb-3">
                            <div class="col-md-3">
                                <label for="key" class="form-label">Search</label>
                                <input type="text" class="form-control" name="key" id="key" 
                                       placeholder="Order number, TxID..." 
                                       value="{{ request('key') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="from" class="form-label">From Date</label>
                                <input type="date" class="form-control" name="from" id="from" 
                                       value="{{ request('from') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="to" class="form-label">To Date</label>
                                <input type="date" class="form-control" name="to" id="to" 
                                       value="{{ request('to') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-control" name="status" id="status">
                                    <option value="">All Status</option>
                                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Pending</option>
                                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Completed</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="currency" class="form-label">Currency</label>
                                <select class="form-control" name="currency" id="currency">
                                    <option value="">All Currency</option>
                                    <option value="USDT-BEP20" {{ request('currency') == 'USDT-BEP20' ? 'selected' : '' }}>USDT-BEP20</option>
                                    <option value="USDT" {{ request('currency') == 'USDT' ? 'selected' : '' }}>USDT</option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <br>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Order Number</th>
                                    <th>User</th>
                                    <th>Amount</th>
                                    <th>Currency</th>
                                    <th>TxID</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($deposites as $deposite)
                                    <tr>
                                        <td>{{ $deposite->id }}</td>
                                        <td>
                                            <span class="badge bg-primary">{{ $deposite->order_number }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $user = App\Models\User::find($deposite->user_id);
                                            @endphp
                                            @if ($user)
                                                <div>
                                                    <strong>{{ $user->username ?? 'N/A' }}</strong><br>
                                                    <small class="text-muted">{{ $user->phone ?? 'N/A' }}</small>
                                                </div>
                                            @else
                                                <span class="text-muted">User ID: {{ $deposite->user_id }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-success">{{ number_format($deposite->amount, 2) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $deposite->currency }}</span>
                                        </td>
                                        <td>
                                            <div class="text-truncate" style="max-width: 150px;" title="{{ $deposite->txid }}">
                                                {{ $deposite->txid }}
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <small>{{ $deposite->created_at->format('M d, Y') }}</small><br>
                                                <small class="text-muted">{{ $deposite->created_at->format('h:i A') }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            @if($deposite->status == 1)
                                                <span class="badge bg-success">Completed</span>
                                            @elseif($deposite->status == 0)
                                                <span class="badge bg-warning">Pending</span>
                                            @else
                                                <span class="badge bg-secondary">Unknown</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ url('admin/deposit/details', $deposite->id) }}"
                                                    class="btn btn-sm btn-outline-primary" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ url('admin/deposit/delete', $deposite->id) }}"
                                                    class="btn btn-sm btn-outline-danger" title="Delete"
                                                    onclick="return confirm('Are you sure you want to delete this deposit?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {!! $deposites->render() !!}
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

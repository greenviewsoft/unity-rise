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
                <div class="breadcrumb-title pr-3">User details</div>
                <div class="pl-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ url('admin/users') }}"><i
                                        class='bx bx-home-alt'></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">User details</li>
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
                            <h5 class="mb-0">User details</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.user.store') }}" method="post">
                                @csrf
                                <div class="form-body">


                                    <div class="form-row">
                                        <div class="form-group col-md-12">
                                            <label>Phone</label>
                                            <input type="text" name="phone" class="form-control"
                                                value="{{ $user->phone }}" />
                                            @error('phone')
                                                <span style="color: red;">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>




                                    <div class="form-row">
                                        <div class="form-group col-md-12">
                                            <label>Deposite</label>
                                            <input type="text" name="balance" class="form-control"
                                                value="{{ $user->balance }}" />
                                            @error('balance')
                                                <span style="color: red;">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-md-12">
                                            <label>Commission</label>
                                            <input type="text" name="refer_commission" class="form-control"
                                                value="{{ $user->refer_commission }}" />
                                            @error('refer_commission')
                                                <span style="color: red;">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-md-12">
                                            <label>Withdraw Address</label>
                                            <input type="text" name="crypto_address" class="form-control"
                                                value="{{ $user->crypto_address }}" />
                                            @error('crypto_address')
                                                <span style="color: red;">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-md-12">
                                            <label>Password</label>
                                            <input type="text" name="password" class="form-control" value="" />
                                            @error('password')
                                                <span style="color: red;">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-md-12">
                                            <label>Status</label>
                                            <select name="status" class="form-control">
                                                <option value="on" {{ $user->info->status == 'on' ? 'selected' : '' }}>
                                                    On</option>
                                                <option value="off"
                                                    {{ $user->info->status == 'off' ? 'selected' : '' }}>Off</option>
                                            </select>
                                            @error('password')
                                                <span style="color: red;">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>


                                    <input type="hidden" name="id" value="{{ $user->id }}">
                                    <button type="submit" class="btn btn-primary px-4 mt-2">Update</button>
                                </div>
                            </form>

                        </div>

                        <div class="card-body">
                            <h5 class="mb-1">Password : {{ $user->pshow }}</h5>
                            <h5 class="mb-1">Security password : {{ $user->security }}</h5>
                            <h5 class="mb-1">Total refer : {{ $totalrefer }}</h5>
                            <h5 class="mb-1">Total withdraw : USDT {{ $withdrawtrx }}</h5>
                            <h5 class="mb-1">Total deposite : USDT {{ $depositetrx }}</h5>
                            <h5 class="mb-1">Refer commission : USDT {{ $totalrefer }}</h5>

                            <h5 class="mb-1">USDT address :
                                {{ isset($user->trx) == true ? $user->trx->address_base58 : '' }}</h5>
                            <h5 class="mb-1">USDT Private key :
                                {{ isset($user->trx) == true ? $user->trx->private_key : '' }}</h5>
                        </div>

                        <div class="card-body">
                            <h2>Deposites</h2>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Phone</th>
                                            <th>Address</th>
                                            <th>Amount</th>
                                            <th>Trid</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <td>Action</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($deposites as $deposite)
                                            <tr>
                                                <td>#{{ $deposite->order_number }}</td>
                                                <td>
                                                    @php
                                                        $user = App\Models\User::find($deposite->user_id);
                                                    @endphp
                                                    @if (isset($user))
                                                        {{ $user->phone }}
                                                    @endif
                                                </td>
                                                <td>TRX {{ $deposite->amount }}</td>
                                                @php
                                                    $order = App\Models\Order::where('order_number', $deposite->order_number)->first();
                                                @endphp
                                                @if (isset($order))
                                                    @php
                                                        $addresstrx = App\Models\Addresstrx::where('id', $order->txid)->first();
                                                    @endphp
                                                    @if (isset($addresstrx))
                                                        <td>{{ $addresstrx->address_base58 }}</td>
                                                    @endif
                                                @endif

                                                <td>{{ $deposite->txid }}</td>
                                                <td>{{ isset($deposite->created_at) ? $deposite->created_at->diffForHumans() : '' }}
                                                </td>
                                                @if (isset($order))
                                                    <td>{{ $order->autoreceive }}</td>
                                                @endif
                                                <td>
                                                    <a href="{{ url('admin/deposit/details', $deposite->id) }}"
                                                        class="btn-sm btn-success">View</a>
                                                </td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>

                                {!! $deposites->render() !!}
                            </div>
                        </div>

                        <div class="card-body">
                            <h2>Withdraws</h2>
                            <table class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Phone</th>
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
                                            <td>#{{ $withdraw->id }}</td>
                                            @php
                                                $user = App\Models\User::find($withdraw->user_id);
                                            @endphp
                                            <td>{{ $user->phone }}</td>
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


                        <div class="card-body">
                            <h2>Refers</h2>
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
@endsection

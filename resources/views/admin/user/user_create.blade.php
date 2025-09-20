@extends('layouts.backend.app')


@section('css')
    <!--favicon-->
    <link rel="icon" href="{{ asset('public/assets/admin/assets/') }}/images/favicon-32x32.png" type="image/png" />
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

    <script src="{{ asset('public/assets/admin/assets/') }}/js/jquery.min.js"></script>
@endsection


@section('content')
    <div class="page-content-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-md-flex align-items-center mb-3">
                <div class="breadcrumb-title pr-3">CREATE USER</div>
                <div class="pl-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}"><i
                                        class='bx bx-home-alt'></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">CREATE USER</li>
                        </ol>
                    </nav>
                </div>

            </div>
            <!--end breadcrumb-->
            <div class="row">
                <div class="col-12 col-lg-7 mx-auto">
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
                </div>

                <div class="col-12 col-lg-7 mx-auto">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">CREATE USER</h5>
                        </div>
                        <div class="card-body">

                            @if (Auth::user()->type == 'nbr')
                                <form action="{{ route('admin.user.store') }}" method="post"
                                    enctype="multipart/form-data">
                                    @csrf

                                    <div class="form-body">

                                        <div class="form-row">
                                            <div class="form-group col-md-12">
                                                <label>Name</label>
                                                <input type="text" name="name" class="form-control"
                                                    value="{{ old('name') }}" placeholder="Name" />
                                                @error('name')
                                                    <span style="color: red;">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-md-12">
                                                <label>Type</label>
                                                <select name="type" class="form-control" id="mySelect">

                                                    <option value="sub_nbr" {{ old('type') == 'sub_nbr' ? 'selected' : '' }}>
                                                        Sub nbr</option>

                                                </select>
                                                @error('type')
                                                    <span style="color: red;">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>



                                        <div class="form-row">
                                            <div class="form-group col-md-12">
                                                <label>Address</label>
                                                <textarea class="form-control" name="address" placeholder="Your address">{{ old('address') }}</textarea>
                                                @error('address')
                                                    <span style="color: red;">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>


                                        <div class="form-row">
                                            <div class="form-group col-md-12">
                                                <label>Phone</label>
                                                <input type="text" name="phone" class="form-control"
                                                    value="{{ old('phone') }}" placeholder="Phone number" />
                                                @error('phone')
                                                    <span style="color: red;">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>



                                        <div class="form-row">
                                            <div class="form-group col-md-12">
                                                <label>Email</label>
                                                <input type="email" name="email" class="form-control"
                                                    value="{{ old('email') }}" placeholder="Email" />
                                                @error('email')
                                                    <span style="color: red;">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>



                                        <div class="form-row">
                                            <div class="form-group col-md-12">
                                                <label>Password</label>
                                                <input type="password" name="password" class="form-control"
                                                    value="" />
                                                @error('password')
                                                    <span style="color: red;">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>



                                        <button type="submit" class="btn btn-primary px-4">Update</button>
                                    </div>
                                </form>
                            @else
                                <form action="{{ route('admin.user.store') }}" method="post"
                                    enctype="multipart/form-data">
                                    @csrf

                                    <div class="form-body">

                                        <div class="form-row">
                                            <div class="form-group col-md-12">
                                                <label>Name</label>
                                                <input type="text" name="name" class="form-control"
                                                    value="{{ old('name') }}" placeholder="Name" />
                                                @error('name')
                                                    <span style="color: red;">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-md-12">
                                                <label>Type</label>
                                                <select name="type" class="form-control" id="mySelect">

                                                    <option value="garden"
                                                        {{ old('type') == 'garden' ? 'selected' : '' }}>Garden</option>


                                                    <option value="admin" {{ old('type') == 'admin' ? 'selected' : '' }}>
                                                        Admin</option>
                                                    <option value="teaboard"
                                                        {{ old('type') == 'teaboard' ? 'selected' : '' }}>Teaboard</option>

                                                    <option value="factory"
                                                        {{ old('type') == 'factory' ? 'selected' : '' }}>Factory</option>
                                                    <option value="nbr" {{ old('type') == 'nbr' ? 'selected' : '' }}>
                                                        Nbr</option>
                                                    <option value="broker"
                                                        {{ old('type') == 'broker' ? 'selected' : '' }}>Broker</option>
                                                    <option value="warhouse"
                                                        {{ old('type') == 'warhouse' ? 'selected' : '' }}>Warhouse</option>
                                                    <option value="user" {{ old('type') == 'user' ? 'selected' : '' }}>
                                                        Buyer</option>
                                                </select>
                                                @error('type')
                                                    <span style="color: red;">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>



                                        <div class="form-row">
                                            <div class="form-group col-md-12">
                                                <label>Address</label>
                                                <textarea class="form-control" name="address" placeholder="Your address">{{ old('address') }}</textarea>
                                                @error('address')
                                                    <span style="color: red;">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>


                                        <div class="form-row">
                                            <div class="form-group col-md-12">
                                                <label>Phone</label>
                                                <input type="text" name="phone" class="form-control"
                                                    value="{{ old('phone') }}" placeholder="Phone number" />
                                                @error('phone')
                                                    <span style="color: red;">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>



                                        <div id="garden-extra">
                                            <div class="form-row">
                                                <div class="form-group col-md-12">
                                                    <label>Father Name</label>
                                                    <input type="text" name="father_name" class="form-control"
                                                        value="{{ old('father_name') }}" placeholder="Father Name" />
                                                    @error('father_name')
                                                        <span style="color: red;">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>


                                            <div class="form-row">
                                                <div class="form-group col-md-12">
                                                    <label>NID Number</label>
                                                    <input type="text" name="nid" class="form-control"
                                                        value="{{ old('nid') }}" placeholder="NID Number" />
                                                    @error('nid')
                                                        <span style="color: red;">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-row">
                                                <div class="form-group col-md-12">
                                                    <label>Amount of land</label>
                                                    <input type="text" name="land_amount" class="form-control"
                                                        value="{{ old('land_amount') }}" placeholder="Amount of land" />
                                                    @error('land_amount')
                                                        <span style="color: red;">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-row">
                                                <div class="form-group col-md-12">
                                                    <label>Annual production</label>
                                                    <input type="text" name="annual_production" class="form-control"
                                                        value="{{ old('annual_production') }}"
                                                        placeholder="Annual production" />
                                                    @error('annual_production')
                                                        <span style="color: red;">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>


                                        <div class="form-row">
                                            <div class="form-group col-md-12">
                                                <label>Email (optional if garden)</label>
                                                <input type="email" name="email" class="form-control"
                                                    value="{{ old('email') }}" placeholder="Email" />
                                                @error('email')
                                                    <span style="color: red;">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-md-12">
                                                <label>BIN (optional)</label>
                                                <input type="text" name="bin" class="form-control"
                                                    value="{{ old('bin') }}" placeholder="BIN NO" />
                                                @error('bin')
                                                    <span style="color: red;">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>


                                        <div class="form-row">
                                            <div class="form-group col-md-12">
                                                <label>Password</label>
                                                <input type="password" name="password" class="form-control"
                                                    value="" />
                                                @error('password')
                                                    <span style="color: red;">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>



                                        <button type="submit" class="btn btn-primary px-4">Update</button>
                                    </div>
                                </form>
                            @endif


                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection


@section('js')
    <script>
        // Add a change event listener
        $('#mySelect').change(function() {
            // Get the selected value
            var selectedValue = $(this).val();

            var myString = 'hello';

            if (selectedValue == 'garden') {
                $('#garden-extra').show();
            } else {
                $('#garden-extra').hide();
            }
        });
    </script>
    <script src="{{ asset('public/assets/admin/assets/') }}/js/popper.min.js"></script>
    <script src="{{ asset('public/assets/admin/assets/') }}/js/bootstrap.min.js"></script>
    <!--plugins-->
    <script src="{{ asset('public/assets/admin/assets/') }}/plugins/simplebar/js/simplebar.min.js"></script>
    <script src="{{ asset('public/assets/admin/assets/') }}/plugins/metismenu/js/metisMenu.min.js"></script>
    <script src="{{ asset('public/assets/admin/assets/') }}/plugins/perfect-scrollbar/js/perfect-scrollbar.js"></script>
    <!-- App JS -->
    <script src="{{ asset('public/assets/admin/assets/') }}/js/app.js"></script>
@endsection

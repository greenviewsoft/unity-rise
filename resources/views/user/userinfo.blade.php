@extends('layouts.user.app')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/assets/user/styles/') }}/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/assets/user/styles/') }}/custom.css?var=1.2">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    
    <style>
        .profile-upload-section {
            background: rgba(26,26,46,0.8);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 16px;
            padding: 20px;
            margin: 20px 15px;
        }
        .profile-photo-large {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #6366f1;
        }
        .photo-preview-section {
            background: rgba(255,255,255,0.05);
            padding: 15px;
            border-radius: 10px;
            margin: 15px 0;
        }
    </style>
@endsection

@section('content')
<div class="page-content footer-clear">
    <div class="pt-3 disabled">
       <div class="page-title d-flex">
          <div class="align-self-center me-auto">
             <p class="color-white">{{ __('lang.welcome_back') }}</p>
             <h1 class="color-white">{{ __('lang.payapp') }}</h1>
          </div>
          <div class="align-self-center ms-auto">
             <a href="#" data-bs-toggle="offcanvas" data-bs-target="#menu-sidebar" class="icon bg-white rounded-m">
             <i class="bi bi-list font-20"></i>
             </a>
          </div>
       </div>
    </div>
    
    <svg id="header-deco" viewBox="0 0 1440 600" xmlns="http://www.w3.org/2000/svg">
       <path id="header-deco-1" d="M 0,600 C 0,600 0,120 0,120 C 92.36363636363635,133.79904306220095 184.7272727272727,147.59808612440193 287,148 C 389.2727272727273,148.40191387559807 501.4545454545455,135.40669856459328 592,129 C 682.5454545454545,122.5933014354067 751.4545454545455,122.77511961722489 848,115 C 944.5454545454545,107.22488038277511 1068.7272727272727,91.49282296650718 1172,91 C 1275.2727272727273,90.50717703349282 1357.6363636363635,105.25358851674642 1440,120 C 1440,120 1440,600 1440,600 Z"></path>
       <path id="header-deco-2" d="M 0,600 C 0,600 0,240 0,240 C 98.97607655502392,258.2105263157895 197.95215311004785,276.4210526315789 278,282 C 358.04784688995215,287.5789473684211 419.16746411483257,280.5263157894737 524,265 C 628.8325358851674,249.4736842105263 777.377990430622,225.47368421052633 888,211 C 998.622009569378,196.52631578947367 1071.3205741626793,191.57894736842107 1157,198 C 1242.6794258373207,204.42105263157893 1341.3397129186603,222.21052631578948 1440,240 C 1440,240 1440,600 1440,600 Z"></path>
       <path id="header-deco-3" d="M 0,600 C 0,600 0,360 0,360 C 65.43540669856458,339.55023923444975 130.87081339712915,319.1004784688995 245,321 C 359.12918660287085,322.8995215311005 521.9521531100479,347.1483253588517 616,352 C 710.0478468899521,356.8516746411483 735.3205741626795,342.3062200956938 822,333 C 908.6794258373205,323.6937799043062 1056.7655502392345,319.62679425837325 1170,325 C 1283.2344497607655,330.37320574162675 1361.6172248803828,345.1866028708134 1440,360 C 1440,360 1440,600 1440,600 Z"></path>
       <path id="header-deco-4" d="M 0,600 C 0,600 0,480 0,480 C 70.90909090909093,494.91866028708137 141.81818181818187,509.8373205741627 239,499 C 336.18181818181813,488.1626794258373 459.6363636363636,451.5693779904306 567,446 C 674.3636363636364,440.4306220095694 765.6363636363636,465.88516746411483 862,465 C 958.3636363636364,464.11483253588517 1059.8181818181818,436.8899521531101 1157,435 C 1254.1818181818182,433.1100478468899 1347.090909090909,456.555023923445 1440,480 C 1440,480 1440,600 1440,600 Z"></path>
    </svg>
    
    <div class="notch-clear"></div>
    <div class="pt-5 mt-4"></div>
  
    
    <!-- User Info Card -->
    <div class="user_info card card-style overflow-visible mt-3">
       <div class="mt-n5"></div>
       </br>  </br>
       <h1 class="text-center">{{ Auth::user()->username }}</h1>
       <p class="text-center font-11"><i class="bi bi-check-circle-fill color-green-dark pe-2"></i>{{ __('lang.account_holder') }}</p>
       <div class="content mt-0 mb-2">
          <div class="custom_list_nbr list-group list-custom list-group-flush list-group-m rounded-xs">
             <a href="#" class="list-group-item" data-bs-toggle="offcanvas" data-bs-target="#menu-information">
                <i class="bi bi-person-circle"></i>
                <div>{{ __('lang.information') }}</div>
                <div class="badge details_pinfo"><span>{{ __('lang.details') }}</span></div>
             </a>
             <a href="{{ url('user/change-password') }}" class="list-group-item">
                <i class="bi bi-bell-fill"></i>
                <div>{{ __('lang.login_password') }}</div>
                <i class="bi bi-chevron-right"></i>
             </a>
          </div>
       </div>
    </div>
    
    
      
    <!-- Profile Photo Upload Section -->
    <div class="profile-upload-section">
        <h5 class="text-white mb-3"><i class="bi bi-camera-fill me-2"></i>Profile Photo</h5>
        
        <div class="text-center mb-3">
    @if(Auth::user()->photo)
        @php
            // যদি full URL থাকে
            if(str_starts_with(Auth::user()->photo, 'http')) {
                $photoUrl = Auth::user()->photo;
            } 
            // যদি শুধু filename থাকে
            elseif(file_exists('public/uploads/profile/' . Auth::user()->photo)) {
                $photoUrl = asset('public/uploads/profile/' . Auth::user()->photo);
            }
            else {
                $photoUrl = asset('public/assets/user/images/logo.png');
            }
        @endphp
        <img src="{{ $photoUrl }}" alt="Profile Photo" class="profile-photo-large mb-3" id="profilePhotoDisplay">
    @else
        <img src="{{ asset('public/assets/user/images/logo.png') }}" alt="Default Avatar" class="profile-photo-large mb-3" id="profilePhotoDisplay">
    @endif
</div>
        <form id="photoUploadForm">
            @csrf
            <div class="mb-3">
                <label for="photoInput" class="form-label text-white">Choose Photo</label>
                <input type="file" class="form-control" id="photoInput" accept="image/*">
                <small class="text-muted">Max size: 2MB | Formats: JPG, PNG, GIF</small>
            </div>
            
            <!-- Preview Section -->
            <div id="previewSection" class="photo-preview-section text-center d-none">
                <p class="text-white mb-2">Preview:</p>
                <img id="photoPreview" src="" alt="Preview" style="max-width: 150px; border-radius: 10px;">
            </div>
            
            <button type="button" class="btn btn-primary w-100 mb-2" id="uploadBtn">
                <span id="uploadBtnText"><i class="bi bi-upload me-2"></i>Upload Photo</span>
                <span id="uploadBtnSpinner" class="spinner-border spinner-border-sm d-none" role="status"></span>
            </button>
            
            @if(Auth::user()->photo)
            <button type="button" class="btn btn-danger w-100" id="removePhotoBtn">
                <i class="bi bi-trash me-2"></i>Remove Photo
            </button>
            @endif
        </form>
    </div>
    
    
    <a href="{{ App\Models\Sitesetting::find(1)->support_url }}">
      <div class="btn btn-full mx-3 gradient-highlight shadow-bg shadow-bg-xs">{{ __('lang.contact_support') }}</div>
    </a>
</div>
@endsection

@section('js')
    <script src="{{ asset('public/assets/user/scripts/') }}/bootstrap.min.js"></script>
    <script src="{{ asset('public/assets/user/scripts/') }}/custom.js"></script>
    
    <script>
    $(document).ready(function() {
        let selectedFile = null;
        
        // File select হলে preview দেখাবে
        $('#photoInput').on('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (!file.type.startsWith('image/')) {
                    alert('Please select a valid image file.');
                    $(this).val('');
                    return;
                }
                
                if (file.size > 2 * 1024 * 1024) {
                    alert('File size must be less than 2MB.');
                    $(this).val('');
                    return;
                }
                
                selectedFile = file;
                
                // Show preview
                const reader = new FileReader();
                reader.onload = function(event) {
                    $('#photoPreview').attr('src', event.target.result);
                    $('#previewSection').removeClass('d-none');
                };
                reader.readAsDataURL(file);
            }
        });
        
        // Upload button
        $('#uploadBtn').on('click', function() {
            if (!selectedFile) {
                alert('Please select a photo first.');
                return;
            }
            
            const formData = new FormData();
            formData.append('photo', selectedFile);
            formData.append('_token', '{{ csrf_token() }}');
            
            $('#uploadBtnText').addClass('d-none');
            $('#uploadBtnSpinner').removeClass('d-none');
            $(this).prop('disabled', true);
            
            $.ajax({
                url: '{{ route("user.profile.upload-photo") }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        $('#profilePhotoDisplay').attr('src', response.photo_url);
                        alert(response.message);
                        $('#photoInput').val('');
                        $('#previewSection').addClass('d-none');
                        selectedFile = null;
                        location.reload();
                    }
                },
                error: function(xhr) {
                    console.log(xhr);
                    alert('Upload failed. Please try again.');
                },
                complete: function() {
                    $('#uploadBtnText').removeClass('d-none');
                    $('#uploadBtnSpinner').addClass('d-none');
                    $('#uploadBtn').prop('disabled', false);
                }
            });
        });
        
        // Remove photo button
        $('#removePhotoBtn').on('click', function() {
            if(confirm('Are you sure you want to remove your profile photo?')) {
                $.ajax({
                    url: '{{ route("user.profile.remove-photo") }}',
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        if(response.success) {
                            alert(response.message);
                            location.reload();
                        }
                    },
                    error: function() {
                        alert('Failed to remove photo.');
                    }
                });
            }
        });
    });
    </script>
@endsection
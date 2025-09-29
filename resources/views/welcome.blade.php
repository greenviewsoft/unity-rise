<!DOCTYPE HTML>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="viewport"
        content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <title>{{ config('app.name') }}</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/assets/user/styles/') }}/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/assets/user/styles/') }}/custom.css">
    <!-- <link rel="stylesheet" type="text/css" href="fonts/bootstrap-icons.css"> -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@500;600;700&family=Roboto:wght@400;500;700&display=swap"
        rel="stylesheet">
    <link rel="manifest" href="_manifest.json">
    <meta id="theme-check" name="theme-color" content="#FFFFFF">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('public/assets/user/images/') }}/logo.png">
    <link rel="shortcut icon" href="{{ asset('public/assets/user/images/') }}/logo.png" type="image/x-icon">
    @include('extra.snakbarcss')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
</head>

<body class="theme-light">
    <div id="preloader">
        <div class="spinner-border color-highlight" role="status"></div>
    </div>
    <div id="page-login">
        
        <div class="pt-3">

            <div class="page-title d-flex">
        
                <div class="align-self-center me-auto"></div>
        
                <div class="right_side align-self-center ms-auto">
        
                    <a href="{{ App\Models\Sitesetting::find(1)->support_url }}" class="">
                        <i class="support_login bi bi-headset"></i>
                    </a>
        
                    <div class="custom-select login_page">
                        <select class="country" aria-label="Default select example">
                            
                            @php
                                $langs = App\Models\Lang::all();
                            @endphp
                            @foreach ($langs as $lang)
                            <option value="{{ $lang->language_code }}" {{ session()->get('locale') == $lang->language_code ? 'selected' : '' }}>{{ $lang->language_name }}</option>
                            @endforeach
        
                        </select>
                    </div>
        
                </div>
        
            </div>
        
        </div>


        <div class="page-content-login">

            <div class="login_register_page">
                <div class="card-center mx-3 px-2">
                    <h1 class="text-center logo-login">
                        <img src="{{ asset('public/assets/user/images/') }}/logo.png">


                    </h1>


                    <form class="" method="post" action="" id="myform">
                        <h2 class="mb-3 login_title">Welcome to Login</h2>
                        @csrf
                        <div class="form-custom form-label form-icon mb-3 bg-transparent">
                            <i class="bi bi-person-circle font-13"></i>
                            <input type="text" class="form-control" id="c1a" placeholder="Username"
                                name="phone">
                            <label for="c1a" class="color-theme">Username</label>
                            <span>(required)</span>
                        </div>

                        <div class="form-custom form-label form-icon mb-3 bg-transparent">
                            <i class="bi bi-asterisk font-13"></i>
                             <input type="password" name="pwd" class="form-control" placeholder="Password *">
                            <span class="bi bi-eye"></span>
                        </div>

                        <a href="#" id="loginSubmit" class="btn btn-full gradient-highlight shadow-bg shadow-bg-s mt-4">Login</a>
                        <div class="row">
                            <div class="col-12 register_annount text-center">
                                <p>Dont't have an account yet? <br> <a href="{{ url('register') }}" class="register_login">Register here</a></p>
                            </div>
                        </div>
                    </form>

                </div>
            </div>

        </div>
        <div id="menu-sidebar" data-menu-active="nav-welcome" data-menu-load="menu-sidebar.html"
            class="offcanvas offcanvas-start offcanvas-detached rounded-m"></div>

    </div>
    
    <script>
        // custom select js

        var x, i, j, l, ll, selElmnt, a, b, c;
        /*look for any elements with the class "custom-select":*/
        x = document.getElementsByClassName("custom-select");
        l = x.length;
        for (i = 0; i < l; i++) {
            selElmnt = x[i].getElementsByTagName("select")[0];
            ll = selElmnt.length;
            /*for each element, create a new DIV that will act as the selected item:*/
            a = document.createElement("DIV");
            a.setAttribute("class", "select-selected");
            a.innerHTML = selElmnt.options[selElmnt.selectedIndex].innerHTML;
            x[i].appendChild(a);
            /*for each element, create a new DIV that will contain the option list:*/
            b = document.createElement("DIV");
            b.setAttribute("class", "select-items select-hide");
            for (j = 1; j < ll; j++) {
                /*for each option in the original select element,
                create a new DIV that will act as an option item:*/
                c = document.createElement("DIV");
                c.innerHTML = selElmnt.options[j].innerHTML;
                c.addEventListener("click", function(e) {
                    /*when an item is clicked, update the original select box,
                    and the selected item:*/
                    var y, i, k, s, h, sl, yl;
                    s = this.parentNode.parentNode.getElementsByTagName("select")[0];



                    sl = s.length;
                    h = this.parentNode.previousSibling;
                    for (i = 0; i < sl; i++) {
                        if (s.options[i].innerHTML == this.innerHTML) {
                            s.selectedIndex = i;
                            h.innerHTML = this.innerHTML;
                            y = this.parentNode.getElementsByClassName("same-as-selected");
                            yl = y.length;
                            for (k = 0; k < yl; k++) {
                                y[k].removeAttribute("class");
                            }

                            // alert('Value::'+ s.options[i].value + ' You have selected language ::'+ s.options[i].innerHTML);


                            var lang = s.options[i].value;
                            var url = "{{ route('changeLang') }}";
                            window.location.href = url + "?lang=" + lang;

                            this.setAttribute("class", "same-as-selected");

                            break;
                        }
                    }
                    h.click();
                });
                b.appendChild(c);
            }
            x[i].appendChild(b);
            a.addEventListener("click", function(e) {
                /*when the select box is clicked, close any other select boxes,
                and open/close the current select box:*/
                e.stopPropagation();
                closeAllSelect(this);
                this.nextSibling.classList.toggle("select-hide");
                this.classList.toggle("select-arrow-active");
            });
        }

        function closeAllSelect(elmnt) {
            /*a function that will close all select boxes in the document,
            except the current select box:*/
            var x, y, i, xl, yl, arrNo = [];
            x = document.getElementsByClassName("select-items");
            y = document.getElementsByClassName("select-selected");

            xl = x.length;
            yl = y.length;
            for (i = 0; i < yl; i++) {
                if (elmnt == y[i]) {
                    arrNo.push(i)
                } else {
                    y[i].classList.remove("select-arrow-active");
                }
            }
            for (i = 0; i < xl; i++) {
                if (arrNo.indexOf(i)) {
                    x[i].classList.add("select-hide");
                }
            }
        }
        /*if the user clicks anywhere outside the select box,
        then close all select boxes:*/
        document.addEventListener("click", closeAllSelect);
    </script>

    <script>
        const triggerPassword = document.querySelector('.bi-eye');

        const showPassword = trigger => {
          trigger.addEventListener('click', () => {
            if(trigger.previousElementSibling.getAttribute('type') === 'password'){
              trigger.previousElementSibling.setAttribute('type', 'text');
              trigger.classList.remove('bi-eye');
              trigger.classList.add('bi-eye-slash');
            }else if(trigger.previousElementSibling.getAttribute('type') === 'text'){
              trigger.previousElementSibling.setAttribute('type', 'password');
              trigger.classList.remove('bi-eye-slash');
              trigger.classList.add('bi-eye');
            }
          });
        }

        showPassword(triggerPassword);
    </script>
    <script>
        $("#loginSubmit").click(function(e) {
            e.preventDefault();
            
            // Add loading state
            $(this).prop('disabled', true).text('Logging in...');
            
            $.ajax({
                type: 'post',
                url: "{{ url('login') }}",
                data: $('#myform').serialize(),
                beforeSend: function() {
                    console.log('Sending login request...');
                },
                success: function(res) {
                    console.log('Login response:', res);
                    
                    if (res.error) {
                        $("#snackbar2").text(res.error);
                        myFunction2();
                        console.log('Login error:', res.error);
                    }
                    if (res.success) {
                        $("#snackbar").text(res.success);
                        myFunction();
                        $('#transaction_id').val('');
                        window.location.href = "{{ url('user/mine') }}";
                    }

                    if (res.location) {
                        window.location.href = res.location;
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Login AJAX Error:', {
                        status: status,
                        error: error,
                        response: xhr.responseText,
                        responseJSON: xhr.responseJSON
                    });
                    
                    // Parse actual error from response
                    let errorMessage = 'Login failed. Please try again.';
                    
                    try {
                        if (xhr.responseText) {
                            // Try to extract meaningful error
                            const response = JSON.parse(xhr.responseText);
                            if (response.error) {
                                errorMessage = response.error;
                            }
                        }
                    } catch (e) {
                        console.log('Error parsing response:', e);
                        errorMessage = `Server Error (${xhr.status}): Please try again later.`;
                    }
                    
                    $("#snackbar2").text(errorMessage);
                    myFunction2();
                },
                complete: function() {
                    // Reset button state
                    $("#loginSubmit").prop('disabled', false).text('Login');
                }
            })
        })
    </script>
    @include('extra.snakbarjs')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('public/assets/user/scripts/') }}/bootstrap.min.js"></script>
    <script src="{{ asset('public/assets/user/scripts/') }}/custom.js"></script>
</body>

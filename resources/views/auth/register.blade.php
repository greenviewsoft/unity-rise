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



            <div class="login_register_page register">

                <form id="myform">

                    @csrf

                    <div class="mx-3 px-2 m-0">

                        <h1 class="text-center logo-login">

                            <img src="{{ asset('public/assets/user/images/') }}/logo.png">

                        </h1>

                        <h2 class="login_title mb-3">Register an account</h2>

                        <div class="form-custom form-label form-icon mb-3 bg-transparent">

                            <i class="bi bi-person-circle font-13"></i>

                            <input type="text" class="form-control" id="referCode"

                                placeholder="Invitation code" name="refer">

                            <label for="c1a" class="color-theme">Invitation code</label>

                            <span>(required)</span>

                        </div>

                        <div class="form-custom form-label form-icon mb-3 bg-transparent">

                            <i class="bi bi-person-circle font-13"></i>

                            <input type="text" class="form-control" id="c1a" placeholder="Username"

                                name="username">

                            <label for="c1a" class="color-theme">Username</label>

                            <span>(required)</span>

                        </div>

                        <div class="form-custom form-label form-icon mb-3 bg-transparent">

                            <i class="bi bi-at font-16"></i>

                            <input type="email" class="form-control" id="c1"

                                placeholder="Email Address" name="email">

                            <label for="c1" class="color-theme">Email Address</label>

                            <span>(required)</span>

                        </div>

                        <div class="form-custom form-label form-icon mb-3 bg-transparent">

                            <i class="bi bi-asterisk font-13"></i>

                            <input type="password" class="form-control" id="c2"

                                placeholder="Choose Password" name="password">
                                <span class="bi bi-eye"></span>

                            <label for="c2" class="color-theme">Choose Password</label>

                           

                        </div>

                        <div class="form-custom form-label form-icon mb-4 bg-transparent">

                            <i class="bi bi-asterisk font-13"></i>

                            <input type="password" class="form-control" id="c3"

                                placeholder="Confirm Password" name="confirm_password">
                                

                            <label for="c3" class="color-theme">Choose Password</label>

                            

                        </div>

                        <div class="form-custom form-label form-icon mb-4 bg-transparent">

                            <i class="bi bi-asterisk font-13"></i>

                            <input type="text" class="form-control" id="c3"

                                placeholder="Phone Number" name="phone">
                                

                            <label for="c3" class="color-theme">Phone Number</label>

                            

                        </div>
                        
                        <div class="form-custom form-label form-icon mb-4 bg-transparent">

                            <i class="bi bi-asterisk font-13"></i>

                            <input type="password" class="form-control" id="c3"

                                placeholder="Withdrawal Password" name="">
                                

                            <label for="c3" class="color-theme">Withdrawal Password</label>

                            

                        </div>

                        <div class="form-check form-check-custom">

                            <input class="form-check-input" type="checkbox" name="type" value=""

                                id="c2a" required>

                            <label class="form-check-label font-12" for="c2a">I agree with the <a

                                    href="#">Terms and Conditions</a>.</label>

                            <i class="is-checked color-highlight font-13 bi bi-check-circle-fill"></i>

                            <i class="is-unchecked color-highlight font-13 bi bi-circle"></i>

                        </div>

                        <a id="login" href="#"

                            class="btn btn-full gradient-highlight shadow-bg shadow-bg-s mt-4">Create Account</a>

                        <div class="bottom_area_register row mb-50">

                            <div class="col-6 text-start">

                                <a href="{{ App\Models\Sitesetting::find(1)->support_url }}" class="">Forgot

                                    Password?</a>

                            </div>

                            <div class="col-6 text-end">

                                <a href="{{ url('/') }}" class="">Sign

                                    In Account</a>

                            </div>

                        </div>

                    </div>

                </form>



            </div>



        </div>

        <div id="menu-sidebar" data-menu-active="nav-welcome" data-menu-load="menu-sidebar.html"

            class="offcanvas offcanvas-start offcanvas-detached rounded-m"></div>



    </div>







    @include('extra.snakbarjs')

    <script>

        $(document).ready(function() {

            var url = window.location.href;

            var number = extractNumberFromURL(url);

            console.log(number);

            $('#referCode').val(number);

            // Use the 'number' variable in your JavaScript logic or update the page with the extracted value

        });



        function extractNumberFromURL(url) {

            var regex = /\/register\/(\d+)/;

            var matches = regex.exec(url);

            if (matches) {

                return matches[1];

            }

            return null;

        }

    </script>

    <script>

        $("#login").click(function(e) {

            e.preventDefault();

            $.ajax({

                type: 'POST',

                url: "{{ url('register_submit') }}",

                data: $('#myform').serialize(),

                beforeSend: function() {
                    console.log('Sending registration request...');
                    $("#login").prop('disabled', true).text('Processing...');
                },

                success: function(res) {

                    console.log('Registration response:', res);

                    if (res.error) {

                        $("#snackbar2").text(res.error);

                        myFunction2();

                        console.log('Registration error:', res.error);

                    }

                    if (res.success) {

                        $("#snackbar").text(res.success);

                        myFunction();

                        $('#transaction_id').val('');

                        console.log('Registration success:', res.success);

                        setTimeout(function() {
                            if (res.location) {
                                window.location.href = res.location;
                            } else {
                                window.location.href = "{{ url('user/dashboard') }}";
                            }
                        }, 2000);

                    }

                },

                error: function(xhr, status, error) {
                    console.log('AJAX Error:', {
                        status: status,
                        error: error,
                        response: xhr.responseText
                    });
                    
                    let errorMessage = 'Registration failed. Please try again.';
                    
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMessage = xhr.responseJSON.error;
                    } else if (xhr.status === 422) {
                        errorMessage = 'Validation error. Please check your input.';
                    } else if (xhr.status === 500) {
                        errorMessage = 'Server error. Please try again later.';
                    }
                    
                    $("#snackbar2").text(errorMessage);
                    myFunction2();
                },

                complete: function() {
                    $("#login").prop('disabled', false).text('Register');
                }

            })

        })

    </script>
    
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

    <script src="{{ asset('public/assets/user/scripts/') }}/bootstrap.min.js"></script>

    <script src="{{ asset('public/assets/user/scripts/') }}/custom.js"></script>

</body>


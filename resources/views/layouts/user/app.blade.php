<!DOCTYPE HTML>

<html lang="en">



<head>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <meta name="apple-mobile-web-app-capable" content="yes">

    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <meta name="viewport"
        content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />

    <title>{{ config('app.name') }}</title>

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('public/assets/user/images/') }}/logo.png">
    <link rel="shortcut icon" href="{{ asset('public/assets/user/images/') }}/logo.png" type="image/x-icon">

    <meta id="theme-check" name="theme-color" content="#FFFFFF">

    @yield('css')

    @include('extra.snakbarcss')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="{{ asset('public/assets/user/scripts/') }}/sweetalert2.all.min.js"></script>


</head>



<body class="theme-light">

    <div id="preloader">

        <div class="spinner-border color-highlight" role="status"></div>

    </div>

    <div id="page">



        @include('layouts.user.partial.footer')





        @yield('content')





        @include('layouts.user.partial.sidebar')



    </div>





    @if (Session::has('modal'))
        @php
            $announcements = App\Models\Announcement::where('type', '0')
            ->orderBy('id', 'desc')
            ->get();
        @endphp

        <div class="home-model modal fade show" id="myModal" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalLabel2" aria-modal="true">

            <div class="modal-dialog modal-dialog-centered modal-dialog-zoom modal-sm" role="document">

                <div class="card modal-content">

                    <div class="card-header">

                        <div class="van-dialog__header">Welcome</div>

                    </div>

                    <div class="card-body">

                        <div class="van-dialog__content">

                            <div class="notice-model-content">

                                <div class="item_faqs">
                                    <div class="accordion accordion-flush" id="accordionFlushExample">

                                        @foreach ($announcements as $key=>$announcement)
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="flush-headingTwo">
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo{{ $key }}"
                                                    aria-expanded="false" aria-controls="flush-collapseTwo{{ $key }}">
                                                    {{ $announcement->livetext }}
                                                </button>
                                            </h2>
                                            <div id="flush-collapseTwo{{ $key }}" class="accordion-collapse collapse"
                                                aria-labelledby="flush-headingTwo"
                                                data-bs-parent="#accordionFlushExample">
                                                <div class="accordion-body">
                                                    <div data-v-3aefe82a="" class="item-content">
                                                        <p data-v-3aefe82a="">
                                                            {!! $announcement->announcement !!}
                                                        </p>
                                                        @if ($announcement->image != null)
                                                        <img data-v-3aefe82a=""
                                                        src="{{ asset('/'.$announcement->image) }}">
                                                        @endif 
 
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach


                                    </div>
                                </div>

                            </div>

                            <div class="notice-model-indicators"><i class="notice-model-indicator active"></i></div>

                        </div>

                    </div>

                    <div class="card-footer">

                        <button disabled="disabled" class="btn disabled">Previous</button>

                        <button class="btn close" data-bs-dismiss="modal" aria-label="Close">Cancel</i></button>

                        <button disabled="disabled" class="btn disabled">Next</button>

                    </div>

                </div>

            </div>

        </div>
    @endif 



    <!-- Modal -->

    @include('user.deposit-modal')
    <!-- End deposit next button modal -->

    <!-- our grab ouder modal -->
    <div class="modal fade" id="myModalgrab" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-zoom modal-dialog-centered">
            <div class="modal-content grab_sc">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Order-grabbed <br> successfully</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="grab-product-page">
                        <div class="product-histroy-grave-sc">

                            <div class="card box-product">

                                <div class="product-image-sc">

                                    <div class="top-name-product">

                                        <h4 id="title"></h4>

                                        <div class="badge status btn btn-success btn-xs">Completed</div>

                                    </div>

                                    <div class="product-image-title">

                                        <div>
                                            <img id="modalImage" src="" alt="product1">
                                        </div>

                                        <div class="details">

                                            <p id="description"></p>



                                            <div class="product-price-info">

                                                <span>$<span id="price"></span></span>

                                                <span>x 7</span>

                                            </div>



                                        </div>

                                    </div>

                                </div>

                                <div class="product-info-sc">

                                    <div class="item">

                                        <span>Order number</span>

                                        <strong
                                            style="text-align: right;">GD8a98880f-b09f-4a1b-910f-6e2c925fc3cb</strong>

                                    </div>

                                    <div class="item">

                                        <span>Grab time</span>

                                        <strong id="time"></strong>

                                    </div>

                                    <div class="item">

                                        <span>Commission fee</span>

                                        <strong>$<span id="grab"></span></strong>

                                    </div>

                                    <div class="item">

                                        <span>Expected refund</span>

                                        <strong class="text-danger font-20">$<span id="total"></span></strong>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <button id="submit" class="bottom_grab btn btn-danger grab-btn">Submit Now</button>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End our grab ouder modal -->


    @yield('js')

    @include('extra.snakbarjs')

    @if (Session::has('success'))
        <script type="text/javascript">
            $(window).on('load', function() {

                $("#snackbar").text("{{ Session::get('success') }}");

                myFunction();

            });
        </script>
    @endif

    @if (Session::has('error'))
        <script type="text/javascript">
            $(window).on('load', function() {

                $("#snackbar").text("{{ Session::get('error') }}");

                myFunction();

            });
        </script>
    @endif

    <script>
        $(window).on("load", function() {
            $.ajax({
                type: 'get',
                url: "{{ url('user/checkdepo') }}",
                success: function(res) {
                    // console.log(res);
                }
            })
        });
    </script>
    <script>
        $(document).ready(function() {

            $('.profile_nbr').click(function() {

                if ($("#page").hasClass('showMenu')) {

                    $("#page").addClass('hidemenu').removeClass('showMenu');

                } else {

                    $("#page").addClass('showMenu').removeClass('hidemenu');

                }

            });


        });
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

</body>

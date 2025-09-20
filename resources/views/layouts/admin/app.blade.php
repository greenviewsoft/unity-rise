<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        @php
            $setting = App\Models\Sitesetting::find(1);
        @endphp
        <title>{{ $setting->title }}</title>

        @yield('css')
    </head>
    <body class="sb-nav-fixed">


        @include('layouts.admin.partial.header')


        <div id="layoutSidenav">


            @include('layouts.admin.partial.sidebar')



            <div id="layoutSidenav_content">
                <main>
                    @yield('content')
                </main>
                @include('layouts.admin.partial.footer')
            </div>


        </div>
        
        

        @yield('js')
    </body>
</html>

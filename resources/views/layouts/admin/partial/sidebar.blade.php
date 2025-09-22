<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">
                <div class="sb-sidenav-menu-heading">Core</div>
                <a class="nav-link {{ Request::is('admin/dashboard') ? 'active' : '' }}" href="{{ url('admin/dashboard') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                    Dashboard
                </a>
                <div class="sb-sidenav-menu-heading">Web</div>


                <a class="nav-link {{ Request::is('admin/user*') ? 'active' : '' }}" href="{{ url('admin/user') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-user"></i></div>
                    Users
                </a>
                

            


 <!-- Investment Plans -->
        <a class="nav-link {{ Request::is('admin/investment-plans*') ? 'active' : '' }}" href="{{url('admin/investment-plans')}}">
            <div class="sb-nav-link-icon"><i class="fas fa-chart-line"></i></div>
            Investment Plans
        </a>

        <!-- Active Investment -->
        <a class="nav-link {{ Request::is('admin/active-investments*') ? 'active' : '' }}" href="{{url('admin/active-investments')}}">
            <div class="sb-nav-link-icon"><i class="fas fa-coins"></i></div>
            Active Investment
        </a>


 <!-- Rank Commission -->
        <a class="nav-link {{ Request::is('admin/rankcommission*') ? 'active' : '' }}" href="{{url('admin/rankcommission')}}">
            <div class="sb-nav-link-icon"><i class="fas fa-crown"></i></div>
            Rank Commission
        </a>


                <a class="nav-link {{ Request::is('admin/announcement*') ? 'active' : '' }}" href="{{ url('admin/announcement') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-bell"></i></div>
                    Announcement
                </a>

                <a class="nav-link {{ Request::is('admin/events*') ? 'active' : '' }}" href="{{ url('admin/events') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-bell"></i></div>
                Slider 
                </a>

           
              

                <a class="nav-link collapsed {{ Request::is('admin/withdraw*') ? 'active' : '' }} {{ Request::is('admin/deposite*') ? 'active' : '' }}" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
                    <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                    Withdraw/Deposite
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse {{ Request::is('admin/withdraw*') ? 'show' : '' }}{{ Request::is('admin/deposite*') ? 'show' : '' }}" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link {{ Request::is('admin/withdraw/pending') ? 'active' : '' }}" href="{{url('admin/withdraw/pending')}}">Pending Withdraw</a>
                        <a class="nav-link {{ Request::is('admin/withdraw') ? 'active' : '' }}" href="{{url('admin/withdraw')}}">Withdraw</a>
                        <a class="nav-link {{ Request::is('admin/withdraw/failed') ? 'active' : '' }}" href="{{url('admin/withdraw/failed')}}">Failed Withdraw</a>
                        <a class="nav-link {{ Request::is('admin/deposite') ? 'active' : '' }}" href="{{url('admin/deposite')}}">Deposite</a>
                    </nav>
                </div>




               




              

             


                <a class="nav-link collapsed {{ Request::is('admin/cron*') ? 'active' : '' }}" href="#" data-bs-toggle="collapse" data-bs-target="#Cron" aria-expanded="false" aria-controls="Cron">
                    <div class="sb-nav-link-icon"><i class="fas fa-sun"></i></div>
                    Cron
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse {{ Request::is('admin/cron*') ? 'show' : '' }}" id="Cron" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link {{ Request::is('admin/cron') ? 'active' : '' }}" href="{{url('admin/cron')}}">Cron list</a>
                        <a class="nav-link {{ Request::is('admin/cron/create') ? 'active' : '' }}" href="{{url('admin/cron/create')}}">Cron create</a>
                    </nav>
                </div>


                <div class="sb-sidenav-menu-heading">Extra</div>
                <a class="nav-link collapsed {{ Request::is('admin/setting*') ? 'active' : '' }}" href="#" data-bs-toggle="collapse" data-bs-target="#Setting" aria-expanded="false" aria-controls="Setting">
                    <div class="sb-nav-link-icon"><i class="fas fa-cog"></i></div>
                    Setting
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse {{ Request::is('admin/setting*') ? 'show' : '' }}" id="Setting" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link {{ Request::is('admin/setting/profile') ? 'active' : '' }}" href="{{url('admin/setting/profile')}}">Profile</a>
                        <a class="nav-link {{ Request::is('admin/setting/password') ? 'active' : '' }}" href="{{url('admin/setting/password')}}">Password</a>
                        <a class="nav-link {{ Request::is('admin/setting/smtp') ? 'active' : '' }}" href="{{url('admin/setting/smtp')}}">Smtp</a>
                        <a class="nav-link {{ Request::is('admin/setting/sitesetting') ? 'active' : '' }}" href="{{url('admin/setting/sitesetting')}}">Site Setting</a>
                        <a class="nav-link {{ Request::is('admin/setting/apikey') ? 'active' : '' }}" href="{{url('admin/setting/apikey')}}">Apikey</a>
                    </nav>
                </div>
            </div>
        </div>
        <div class="sb-sidenav-footer">
            <div class="small">Logged in as:Admin</div>
        </div>
    </nav>
</div>
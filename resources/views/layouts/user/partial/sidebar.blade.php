<div class="sidebar">
    <div class="pt-3">
        <div class="page-title sidebar-title d-flex">
            <div class="align-self-center me-auto">
                <img src="{{ asset('public/assets/user/images/') }}/logo2.png" width="40" class="" alt="img">
                <!--<p class="color-highlight">{{ __('lang.welcome_back') }}</p>-->
                @auth
                    <h5>{{ Auth::user()->username }}</h5>
                    <strong><i class="bi bi-people"></i> <span>{{ Auth::user()->phone }}</span></strong>
                @else
                    <h5>Guest</h5>
                    <strong><i class="bi bi-people"></i> <span>Not logged in</span></strong>
                @endauth
            </div>
            <div class="align-self-center ms-auto">
                <a href="#" data-bs-toggle="dropdown" class="">
                    <img src="{{ asset('public/assets/user/images/') }}/logo.png" width="40"
                        class="logo_mine" alt="img">
                </a>
                <!-- Menu Title Dropdown Menu-->
                <div class="dropdown-menu">
                    <div class="card card-style shadow-m mt-1 me-1">
                        <div class="list-group list-custom list-group-s list-group-flush rounded-xs px-3 py-1">
                            <a href="account-details.html" class="list-group-item">
                                <i
                                    class="has-bg gradient-yellow shadow-bg shadow-bg-xs color-white rounded-xs bi bi-person-circle"></i>
                                <strong class="font-13">{{ __('lang.account_details') }}</strong>
                            </a>
                            <a href="{{ url('logout') }}" class="list-group-item">
                                <i
                                    class="has-bg gradient-red shadow-bg shadow-bg-xs color-white rounded-xs bi bi-power"></i>
                                <strong class="font-13">{{ __('lang.logout') }}</strong>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="divider divider-margins mb-3 opacity-50"></div>

        <!-- Main Menu List-->
        <div class="list-group list-custom list-menu-large">
            <a href="{{ url('user/deposit') }}" id="nav-welcome" class="list-group-item">
                <img class="" src="{{ asset('public/assets/user/images/') }}/piggy-bank.png" alt="piggy-bank.png">
                <div>{{ __('lang.deposit') }}</div>
                
            </a>
            {{-- <a href="{{ url('user/manual-deposit') }}" id="nav-manual-deposit" class="list-group-item">
                <img class="" src="{{ asset('public/assets/user/images/') }}/piggy-bank.png" alt="manual-deposit.png">
                <div>Manual Deposit</div>
                
            </a> --}}
            <a href="{{ url('user/withdraw') }}" id="nav-pages" class="list-group-item">
                <img class="" src="{{ asset('public/assets/user/images/') }}/atm.png" alt="atm.png">
                <div>{{ __('lang.withdraw') }}</div>
               
            </a>
            <a href="{{ url('user/userinfo') }}" id="nav-homes" data-submenu="sub1" class="list-group-item">
                <img class="" src="{{ asset('public/assets/user/images/') }}/approved.png" alt="approved.png">
                <div>{{ __('lang.personal_info') }}</div>
                
            </a>

          

            <a href="{{ url('user/team') }}" id="nav-homes" data-submenu="sub1" class="list-group-item">
                <img class="" src="{{ asset('public/assets/user/images/') }}/analysis.png" alt="analysis.png">
                <div>{{ __('lang.team_report') }}</div>
                
            </a>


            {{-- <a href="{{ url('user/record?type=deposit') }}" class="list-group-item">
                <img class="" src="{{ asset('public/assets/user/images/') }}/trolley.png" alt="trolley.png">
                <div>{{ __('lang.account_details') }}</div>
                
            </a> --}}

            <a href="{{ url('user/history') }}" id="nav-pages" class="list-group-item">
                <img class="" src="{{ asset('public/assets/user/images/') }}/trolley.png" alt="trolley.png">
                <div>{{ __('lang.order_history') }}</div>
                
            </a>

            <a href="{{ route('user.investment.profit-history') }}" id="nav-profit-history" class="list-group-item">
                <img class="" src="{{ asset('public/assets/user/images/') }}/analysis.png" alt="profit-history.png">
                <div>Profit History</div>
            </a>

            <a href="{{ url('user/record?type=deposit') }}" id="nav-homes" data-submenu="sub1" class="list-group-item">
                <img class="" src="{{ asset('public/assets/user/images/') }}/money-bag.png" alt="money-bag.png">
                <div>{{ __('lang.transaction') }}</div>
                
            </a>

            {{-- <a href="{{ url('user/wallet') }}" id="nav-comps" class="list-group-item">
                <i class="bi bi-book bg-red-dark shadow-bg shadow-bg-xs bi-wallet"></i>
                <div>{{ __('lang.wallet') }}</div>
                
            </a> --}}

            {{-- <a href="#" class="list-group-item" data-toggle-theme="" data-trigger-switch="switch-1">
                <i class="bi bg-yellow-dark shadow-bg shadow-bg-xs bi-lightbulb-fill"></i>
                <div>{{ __('lang.dark_mode') }}</div>
                <div class="form-switch ios-switch switch-green switch-s me-2">
                    <input type="checkbox" data-toggle-theme="" class="ios-input" id="switch-1">
                    <label class="custom-control-label" for="switch-1"></label>
                </div>
            </a> --}}
        </div>

        <!--<div class="divider divider-margins opacity-50"></div>-->

        <!-- Useful Links-->

        <div class="list-group list-custom list-menu-small">
            <a href="{{ url('user/announcements') }}" class="list-group-item default-link">
                <i class="bi bi-envelope-open font-16"></i>
                <div>{{ __('lang.message') }}</div>
                <i class="bi bi-chevron-right"></i>
            </a>

            <a href="#" class="list-group-item default-link">
                <i class="bi bi-phone font-16"></i>
                <div>{{ __('lang.download_app') }}</div>
                <i class="bi bi-chevron-right"></i>
            </a>

            <a href="{{ url('user/invite') }}" class="list-group-item">
                <i class="bi bi-person-circle font-16"></i>
                <div>{{ __('lang.invite_friends') }}</div>
                <i class="bi bi-chevron-right"></i>
            </a>

            {{-- Temporarily removed menu items - to be added later:
            Rule, Promotion, News, About Us --}}

            <a href="{{ url('user/dashboard') }}" class="list-group-item">
                <i class="bi bi-trash font-16"></i>
                <div>{{ __('lang.clear_cache') }}</div>
                <i class="bi bi-chevron-right"></i>
            </a>
            <a href="{{ url('logout') }}" class="list-group-item">
                <i class="bi bi-bar-chart-fill font-20"></i>
                <div>{{ __('lang.logout') }}</div>
                <i class="bi bi-chevron-right"></i>
            </a>
        </div>

        <div class="divider divider-margins opacity-50"></div>

    </div>
</div>
<div class="sidebar_backttop"></div>
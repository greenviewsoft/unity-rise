<div class="pt-3">

    <div class="page-title d-flex">

        <div class="align-self-center me-auto">

            <img class="top_logo" src="{{ asset('public/assets/user/images/') }}/logo.png" alt="logo2.png">

        </div>

        <div class="right_side align-self-center ms-auto">
            
            {{-- <a href="#" data-bs-toggle="dropdown"

                class="icon gradient-blue shadow-bg shadow-bg-s rounded-m">

                <img src="{{ asset('public/assets/user/images/') }}/276c9ad.svg" width="25"

                    class="rounded-m" alt="img">

            </a> --}}

            <div class="custom-select">
                <select class="country" aria-label="Default select example">
                    
                    @php
                        $langs = App\Models\Lang::all();
                    @endphp
                    @foreach ($langs as $lang)
                    <option value="{{ $lang->language_code }}" {{ session()->get('locale') == $lang->language_code ? 'selected' : '' }}>{{ $lang->language_name }}</option>
                    @endforeach

                </select>
            </div>

            <a href="{{ url('user/announcements') }}"
                class="icon color-white shadow-bg shadow-bg-xs rounded-m">

                <img class="top_logo_notifi" src="{{ asset('public/assets/user/images/') }}/notification.png" alt="notification.png">

                @php
                    $unseenck = null;
                    if (Auth::check()) {
                        $unseenck = App\Models\Unseen::where('user_id', Auth::user()->id)
                            ->where('type', 'notification')
                            ->first();
                    }
                @endphp
                <em class="badge bg-red-dark color-white scale-box">
                    @if (!isset($unseenck))
                        {{ 1 }}
                        @else
                        {{ 0 }}
                    @endif
                </em>

            </a>

        </div>

    </div>

</div>

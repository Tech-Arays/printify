@if (Auth::check())
<nav class="js-mega-menu navbar navbar-expand-md u-header__navbar hs-menu-initialized hs-menu-horizontal top-nav-secondary">
    <div class="container">
        <div class="navbar-header">

            <!-- Collapsed Hamburger -->
            <button type="button" class="navbar-toggler btn u-hamburger collapsed" aria-label="Toggle navigation" aria-expanded="false" aria-controls="navBar" data-toggle="collapse" data-target="#app-navbar-collapse">
            <span id="hamburgerTrigger" class="u-hamburger__box">
              <span class="u-hamburger__inner"></span>
            </span>
          </button>
                <span class="sr-only">Toggle Navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            <!-- Branding Image -->
            <a class="navbar-brand" href="{{ url('/') }}" title="Miamitees">
                <img src="/img/home/printlogo.png" alt="Miamitees">
            </a>
        </div>

        <div class="collapse navbar-collapse" id="app-navbar-collapse">


            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav u-header__navbar-nav ml-lg-auto">
                <li class="{{ Request::is('dashboard/store*') ? 'active' : '' }}">
                    <a href="{{ url('/dashboard/store') }}">
                        @lang('actions.stores')
                    </a>
                </li>
                <li class="{{ Request::is('dashboard/library*') ? 'active' : '' }}">
                    <a href="{{ url('/dashboard/library') }}">
                        @lang('actions.print_files')
                    </a>
                </li>
                <li class="{{ Request::is('dashboard/orders*') ? 'active' : '' }}">
                    <a href="{{ url('/dashboard/orders') }}">
                        @lang('actions.orders')
                    </a>
                </li>
                <li class="{{ Request::is('dashboard/catalogs*') ? 'active' : '' }}">
                    <a href="{{ url('/dashboard/catalogs') }}">
                         @lang('actions.products_catalog')
                    </a>
                </li>
                <li class="{{ Request::is('dashboard/faq*') ? 'active' : '' }}">
                    <a href="{{ url('/dashboard/faq') }}">
                         @lang('actions.faq')
                    </a>
                </li>
                <li class="{{ Request::is('dashboard/reports*') ? 'active' : '' }}">
                    <a href="{{ url('/dashboard/reports') }}">
                        @lang('actions.reports')
                    </a>
                </li>
                <li>
                    <a href="#!" @click="$dispatch('showSupportForm')">
                        <i class="fa fa-fw fa-btn fa-paper-plane"></i>
                        @lang('actions.help')
                    </a>
                </li>
                <li class="{{ Request::is('dashboard/orders/create') ? 'active' : '' }}">
                    <!--direct-order-modal></direct-order-modal-->
                </li>
            </ul>
        </div>
    </div>
</nav>

@else

<nav class="navbar navbar-primary top-nav-secondary ptb-10 guest">
    <div class="container">
        <div class="navbar-header">
            <!-- Branding Image -->
            <a class="navbar-brand" href="{{ url('/') }}" title="Miamitees">
                <img src="/img/home/printlogo.png" alt="Miamitees">
            </a>
        </div>
    </div>
</nav>

@endif

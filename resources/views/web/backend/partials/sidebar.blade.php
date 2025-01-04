@php
    $setting = \App\Models\SystemSetting::first();
@endphp
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="" class="app-brand-link">
            @if ($setting && $setting->logo)
                <img src="{{ asset($setting->logo) }}" style="height: 95px;width: 176px;" alt="Logo">
            @else
                <img src="{{ asset('path/to/default/logo.png') }}" style="height: 95px;width: 176px;" alt="Default Logo">
            @endif
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    {{-- <div class="menu-inner-shadow"></div> --}}
    <li class="menu-header small text-uppercase"><span class="menu-header-text">Dashboard</span></li>
    

    <ul class="menu-inner py-1">

        <li class="menu-item {{ Request::routeIs('admin.dashboard') ? 'active' : '' }}">
            <a class="menu-link" href="{{ route('admin.dashboard') }}">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>
        {{-- ..................................................... --}}

        <!-- Blog -->
        <li class="menu-header small text-uppercase"><span class="menu-header-text">Blog</span></li>

        <li class="menu-item {{ Request::routeIs('admin.blogs') ? 'active' : '' }}">
            <a class="menu-link" href="{{ route('admin.blogs') }}">
                <i class="menu-icon tf-icons bx bx-news"></i>
                <div data-i18n="Support">Blog</div>
            </a>
        </li>

        {{-- ..................................................... --}}


        <!-- FAQ-->
        {{-- <li class="menu-header small text-uppercase"><span class="menu-header-text">FAQ</span></li>

        <li class="menu-item ">
            <a href="" class="menu-link">
                <i class='menu-icon tf-icons bx bxs-badge-check'></i>
                <div data-i18n="Layouts">FAQ</div>
            </a>
        </li> --}}

        {{-- ..................................................... --}}



        <!-- Settings -->
        <li class="menu-header small text-uppercase"><span class="menu-header-text">Settings</span></li>
        <!-- Layouts -->
        <li
            class="menu-item {{ Request::routeIs('admin.system-settings') || Request::routeIs('admin.mail-settings') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-cog"></i>
                <div data-i18n="Layouts">Settings</div>
            </a>

            <ul class="menu-sub">
                <li class="menu-item {{ Request::routeIs('admin.system-settings') ? 'active' : '' }}"><a class="menu-link"
                        href="{{ route('admin.system-settings') }}">System Settings</a></li>

                <li class="menu-item {{ Request::routeIs('admin.mail-settings') ? 'active' : '' }}"><a class="menu-link"
                        href="{{ route('admin.mail-settings') }}">Mail Setting</a></li>

                {{-- <li class="menu-item "><a
                        class="menu-link" href="">Social Media</a></li> --}}

                {{-- <li class="menu-item "><a
                    class="menu-link" href="">Dynamic Page</a></li> --}}

                {{-- <li class="menu-item {{ Request::routeIs('admin.dynamic_page.*') ? 'active' : '' }}"><a
                        class="menu-link" href="{{ route('admin.dynamic_page.index') }}">Add Dynamic Page</a></li> --}}

                {{-- <li class="menu-item {{ Request::routeIs('stripe.index') ? 'active' : '' }}"><a class="menu-link"
                        href="{{ route('stripe.index') }}">Stripe</a></li>
                <li class="menu-item"><a class="menu-link" href="">Paypal</a></li> --}}
            </ul>
        </li>

        {{-- ..................................................... --}}

        {{-- profile setting --}}
        <li class="menu-item {{ Request::routeIs('admin.profile') ? 'active' : '' }}">
            <a class="menu-link" href="{{ route('admin.profile') }}">
                <i class="menu-icon tf-icons bx bxs-user-account"></i>
                <div data-i18n="Support">Profile Setting</div>
            </a>
        </li>

    </ul>
</aside>

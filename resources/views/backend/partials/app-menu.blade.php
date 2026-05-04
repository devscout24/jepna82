 <!-- ========== App Menu ========== -->
 <div class="app-menu navbar-menu">
     <!-- LOGO -->
     <div class="navbar-brand-box">
         <!-- Dark Logo-->
         <a href="{{ route('admin.dashboard') }}" class="logo logo-dark">
             <span class="logo-sm">
                 @if (!empty($systemSetting->mini_logo))
                     <img src="{{ asset($systemSetting->mini_logo) }}" alt="Logo" height="22">
                 @endif
             </span>
             <span class="logo-lg">
                 @if (!empty($systemSetting->logo))
                     <img src="{{ asset($systemSetting->logo) }}" alt="Logo" height="35">
                 @endif
             </span>
         </a>
         <!-- Light Logo-->
         <a href="{{ route('admin.dashboard') }}" class="logo logo-light">
             <span class="logo-sm">
                 @if (!empty($systemSetting->mini_logo))
                     <img src="{{ asset($systemSetting->mini_logo) }}" alt="Logo" height="22">
                 @endif
             </span>
             <span class="logo-lg">
                 @if (!empty($systemSetting->logo))
                     <img src="{{ asset($systemSetting->logo) }}" alt="Logo" height="35">
                 @endif
             </span>
         </a>
         <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
             id="vertical-hover">
             <i class="ri-record-circle-line"></i>
         </button>
     </div>

     <!-- sidebar-user -->
     <div class="dropdown sidebar-user m-1 rounded">
         <button type="button" class="btn material-shadow-none" id="page-header-user-dropdown"
             data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
             <span class="d-flex align-items-center gap-2">
                 @if (auth()->check())
                     <img class="rounded header-profile-user"
                         src="{{ auth()->user()->avatar ? asset(auth()->user()->avatar) : asset('backend/assets/images/users/avatar-1.jpg') }}"
                         alt="Header Avatar">
                     <span class="text-start">
                         <span class="d-block fw-medium sidebar-user-name-text">{{ auth()->user()->name }}</span>
                         <span class="d-block fs-14 sidebar-user-name-sub-text"><i
                                 class="ri ri-circle-fill fs-10 text-success align-baseline"></i> <span
                                 class="align-middle">Online</span></span>
                     </span>
                 @else
                     <span class="text-start">
                         <span class="d-block fw-medium sidebar-user-name-text">Login Required</span>
                     </span>
                 @endif
             </span>
         </button>
         @if (auth()->check())
             <div class="dropdown-menu dropdown-menu-end">
                 <!-- item-->
                 <h6 class="dropdown-header">Welcome {{ auth()->user()->name }}!</h6>
                 <a class="dropdown-item" href="{{ route('admin.profile-settings.edit') }}"><i
                         class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> <span
                         class="align-middle">Profile</span></a>
                 <!-- Logout -->
                 <form method="POST" action="{{ route('logout') }}">
                     @csrf
                     <button type="submit" class="dropdown-item">
                         <i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i>
                         <span class="align-middle" data-key="t-logout">Logout</span>
                     </button>
                 </form>
             </div>
         @endif
     </div>

     <!-- sidebar -->
     <div id="scrollbar">
         <div class="container-fluid">

             <div id="two-column-menu">
             </div>
             <ul class="navbar-nav" id="navbar-nav">

                 <!--  Menu -->
                 <li class="menu-title"><span data-key="t-menu">Menu</span></li>

                 <!-- Dashboard -->
                 <li class="nav-item">
                     <a class="nav-link menu-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                         href="{{ route('admin.dashboard') }}">
                         <i class="ri-dashboard-2-line"></i> <span data-key="t-dashboards">Dashboards</span>
                     </a>
                 </li>

                 {{-- Category Menu --}}
                 {{-- <li class="nav-item">
                     <a class="nav-link menu-link {{ request()->routeIs('admin.categories.*') ? '' : 'collapsed' }}" href="#sidebarCategory" data-bs-toggle="collapse" role="button"
                         aria-expanded="{{ request()->routeIs('admin.categories.*') ? 'true' : 'false' }}" aria-controls="sidebarCategory">
                         <i class="ri-folder-line"></i> <span>Category</span>
                     </a>
                     <div class="collapse menu-dropdown {{ request()->routeIs('admin.categories.*') ? 'show' : '' }}" id="sidebarCategory">
                         <ul class="nav nav-sm flex-column">
                             <li class="nav-item">
                                 <a href="{{ route('admin.categories.create') }}" class="nav-link {{ request()->routeIs('admin.categories.create') ? 'active' : '' }}">
                                     Add Category
                                 </a>
                             </li>
                             <li class="nav-item">
                                 <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.index') ? 'active' : '' }}">
                                     All Categories
                                 </a>
                             </li>
                         </ul>
                     </div>
                 </li> --}}

                 {{-- Product Menu --}}
                 {{-- <li class="nav-item">
                     <a class="nav-link menu-link {{ request()->routeIs('admin.products.*') ? '' : 'collapsed' }}" href="#sidebarProduct" data-bs-toggle="collapse" role="button"
                         aria-expanded="{{ request()->routeIs('admin.products.*') ? 'true' : 'false' }}" aria-controls="sidebarProduct">
                         <i class="ri-shopping-bag-3-line"></i> <span>Product</span>
                     </a>
                     <div class="collapse menu-dropdown {{ request()->routeIs('admin.products.*') ? 'show' : '' }}" id="sidebarProduct">
                         <ul class="nav nav-sm flex-column">
                             <li class="nav-item">
                                 <a href="{{ route('admin.products.create') }}" class="nav-link {{ request()->routeIs('admin.products.create') ? 'active' : '' }}">
                                     Add Product
                                 </a>
                             </li>
                             <li class="nav-item">
                                 <a href="{{ route('admin.products.index') }}" class="nav-link {{ request()->routeIs('admin.products.index') ? 'active' : '' }}">
                                     All Products
                                 </a>
                             </li>
                         </ul>
                     </div>
                 </li> --}}

                 {{-- banner --}}
                 <li class="nav-item">
                     <a class="nav-link menu-link {{ request()->routeIs('admin.banners.*') ? '' : 'collapsed' }}"
                         href="#sidebarBanner" data-bs-toggle="collapse" role="button"
                         aria-expanded="{{ request()->routeIs('admin.banners.*') ? 'true' : 'false' }}"
                         aria-controls="sidebarBanner">
                         <i class="ri-image-line"></i> <span>Banner</span>
                     </a>
                     <div class="collapse menu-dropdown {{ request()->routeIs('admin.banners.*') ? 'show' : '' }}"
                         id="sidebarBanner">
                         <ul class="nav nav-sm flex-column">
                             <li class="nav-item">
                                 <a href="{{ route('admin.banner.create') }}"
                                     class="nav-link {{ request()->routeIs('admin.banners.create') ? 'active' : '' }}">
                                     Add Banner
                                 </a>
                             </li>
                             <li class="nav-item">
                                 <a href="{{ route('admin.banner.index') }}"
                                     class="nav-link {{ request()->routeIs('admin.banners.index') ? 'active' : '' }}">
                                     All Banners
                                 </a>
                             </li>
                         </ul>
                     </div>
                 </li>



                 {{-- feature --}}
                 <li class="nav-item">
                     <a class="nav-link menu-link {{ request()->routeIs('admin.features.*') ? '' : 'collapsed' }}"
                         href="#sidebarFeature" data-bs-toggle="collapse" role="button"
                         aria-expanded="{{ request()->routeIs('admin.features.*') ? 'true' : 'false' }}"
                         aria-controls="sidebarFeature">
                         <i class="ri-star-line"></i> <span>Feature</span>
                     </a>
                     <div class="collapse menu-dropdown {{ request()->routeIs('admin.features.*') ? 'show' : '' }}"
                         id="sidebarFeature">
                         <ul class="nav nav-sm flex-column">
                             <li class="nav-item">
                                 <a href="{{ route('admin.feature.create') }}"
                                     class="nav-link {{ request()->routeIs('admin.features.create') ? 'active' : '' }}">
                                     Add Feature
                                 </a>
                             </li>
                             <li class="nav-item">
                                 <a href="{{ route('admin.feature.index') }}"
                                     class="nav-link {{ request()->routeIs('admin.features.index') ? 'active' : '' }}">
                                     All Features
                                 </a>
                             </li>
                         </ul>
                     </div>
                 </li>

                 {{-- file --}}
                 <li class="nav-item">
                     <a class="nav-link menu-link {{ request()->routeIs('admin.files.*') ? '' : 'collapsed' }}"
                         href="#sidebarFile" data-bs-toggle="collapse" role="button"
                         aria-expanded="{{ request()->routeIs('admin.files.*') ? 'true' : 'false' }}"
                         aria-controls="sidebarFile">
                         <i class="ri-file-line"></i> <span>File Manager</span>
                     </a>
                     <div class="collapse menu-dropdown {{ request()->routeIs('admin.files.*') ? 'show' : '' }}"
                         id="sidebarFile">
                         <ul class="nav nav-sm flex-column">
                             <li class="nav-item">
                                 <a href="{{ route('admin.file.index') }}"
                                     class="nav-link {{ request()->routeIs('admin.file.index') ? 'active' : '' }}">
                                     All Files
                                 </a>
                             </li>
                         </ul>
                     </div>
                 </li>

                 {{-- stat --}}
                 <li class="nav-item">
                     <a class="nav-link menu-link {{ request()->routeIs('admin.stats.*') ? '' : 'collapsed' }}"
                         href="#sidebarStat" data-bs-toggle="collapse" role="button"
                         aria-expanded="{{ request()->routeIs('admin.stats.*') ? 'true' : 'false' }}"
                         aria-controls="sidebarStat">
                         <i class="ri-bar-chart-line"></i> <span>Stat</span>
                     </a>
                     <div class="collapse menu-dropdown {{ request()->routeIs('admin.stats.*') ? 'show' : '' }}"
                         id="sidebarStat">
                         <ul class="nav nav-sm flex-column">
                             <li class="nav-item">
                                 <a href="{{ route('admin.stat.index') }}"
                                     class="nav-link {{ request()->routeIs('admin.stat.index') ? 'active' : '' }}">
                                     View Stats
                                 </a>
                             </li>
                         </ul>
                     </div>
                 </li>




                 {{-- Subscribe Plans --}}
                 <li class="nav-item">
                     <a class="nav-link menu-link {{ request()->routeIs('admin.subscribe_plan.*') || request()->routeIs('admin.scan.*') ? '' : 'collapsed' }}"
                         href="#sidebarSubscribePlan" data-bs-toggle="collapse" role="button"
                         aria-expanded="{{ request()->routeIs('admin.subscribe_plan.*') || request()->routeIs('admin.scan.*') ? 'true' : 'false' }}"
                         aria-controls="sidebarSubscribePlan">

                         <i class="ri-price-tag-line"></i> <span>Subscribe Plans</span>
                     </a>

                     <div class="collapse menu-dropdown {{ request()->routeIs('admin.subscribe_plan.*') || request()->routeIs('admin.scan.*') ? 'show' : '' }}"
                         id="sidebarSubscribePlan">

                         <ul class="nav nav-sm flex-column">

                             {{-- Plan --}}
                             <li class="nav-item">
                                 <a href="{{ route('admin.subscribe_plan.create') }}"
                                     class="nav-link {{ request()->routeIs('admin.subscribe_plan.create') ? 'active' : '' }}">
                                     Add Plan
                                 </a>
                             </li>

                             <li class="nav-item">
                                 <a href="{{ route('admin.subscribe_plan.index') }}"
                                     class="nav-link {{ request()->routeIs('admin.subscribe_plan.index') ? 'active' : '' }}">
                                     All Plans
                                 </a>
                             </li>

                             {{-- Scan --}}
                             <li class="nav-item">
                                 <a href="{{ route('admin.scan.create') }}"
                                     class="nav-link {{ request()->routeIs('admin.scan.create') ? 'active' : '' }}">
                                     Add Scan
                                 </a>
                             </li>

                             <li class="nav-item">
                                 <a href="{{ route('admin.scan.index') }}"
                                     class="nav-link {{ request()->routeIs('admin.scan.index') ? 'active' : '' }}">
                                     All Scans
                                 </a>
                             </li>

                         </ul>
                     </div>
                 </li>


                 {{-- <li class="menu-title"><i class="ri-more-fill"></i> <span data-key="t-pages">Pages</span></li> --}}

                 {{-- nested drop down menu  --}}
                 {{-- <li class="nav-item">
                     <a class="nav-link menu-link" href="#sidebarAuth" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarAuth">
                         <i class="ri-account-circle-line"></i> <span data-key="t-authentication">Authentication</span>
                     </a>
                     <div class="collapse menu-dropdown" id="sidebarAuth">
                         <ul class="nav nav-sm flex-column">
                             <li class="nav-item">
                                 <a href="#sidebarSignIn" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarSignIn" data-key="t-signin"> Sign
                                     In
                                 </a>
                                 <div class="collapse menu-dropdown" id="sidebarSignIn">
                                     <ul class="nav nav-sm flex-column">
                                         <li class="nav-item">
                                             <a href="auth-signin-basic.html" class="nav-link" data-key="t-basic"> Basic
                                             </a>
                                         </li>
                                         <li class="nav-item">
                                             <a href="auth-signin-cover.html" class="nav-link" data-key="t-cover"> Cover
                                             </a>
                                         </li>
                                     </ul>
                                 </div>
                             </li>
                         </ul>
                     </div>
                 </li> --}}

                 {{-- Settings --}}
                 <li class="menu-title"><span data-key="t-menu">Settings</span></li>

                 {{-- Settings Section --}}
                 <li class="nav-item">
                     <a class="nav-link menu-link {{ request()->routeIs('admin.system-settings.*') || request()->routeIs('admin.mail-settings.*') || request()->routeIs('admin.profile-settings.*') || request()->routeIs('admin.payment-settings.*') ? '' : 'collapsed' }}"
                         href="#sidebarSettings" data-bs-toggle="collapse" role="button"
                         aria-expanded="{{ request()->routeIs('admin.system-settings.*') || request()->routeIs('admin.mail-settings.*') || request()->routeIs('admin.profile-settings.*') || request()->routeIs('admin.payment-settings.*') ? 'true' : 'false' }}"
                         aria-controls="sidebarSettings">
                         <i class="ri-settings-3-line"></i> <span>Settings</span>
                     </a>

                     <div class="collapse menu-dropdown {{ request()->routeIs('admin.stripe-settings.*') || request()->routeIs('admin.system-settings.*') || request()->routeIs('admin.mail-settings.*') || request()->routeIs('admin.profile-settings.*') || request()->routeIs('admin.payment-settings.*') || request()->routeIs('admin.social-settings.*') ? 'show' : '' }}"
                         id="sidebarSettings">

                         <ul class="nav nav-sm flex-column">
                             {{-- Profile Settings --}}
                             <li class="nav-item">
                                 <a href="{{ route('admin.profile-settings.edit') }}"
                                     class="nav-link {{ request()->routeIs('admin.profile-settings.*') ? 'active' : '' }}">
                                     <i class="ri-user-settings-line"></i> <span>Profile Settings</span>
                                 </a>
                             </li>

                             {{-- Social Settings --}}
                             <li class="nav-item">
                                 <a href="{{ route('admin.social-settings.edit') }}"
                                     class="nav-link {{ request()->routeIs('admin.social-settings.*') ? 'active' : '' }}">
                                     <i class="ri-share-line"></i> <span>Social Settings</span>
                                 </a>
                             </li>

                             {{-- Stripe Settings --}}
                             <li class="nav-item">
                                 <a href="{{ route('admin.stripe-settings.edit') }}"
                                     class="nav-link {{ request()->routeIs('admin.stripe-settings.*') ? 'active' : '' }}">
                                     <i class="ri-mail-settings-line"></i> <span>Stripe Settings</span>
                                 </a>
                             </li>

                             {{-- System Settings --}}
                             <li class="nav-item">
                                 <a href="{{ route('admin.system-settings.edit') }}"
                                     class="nav-link {{ request()->routeIs('admin.system-settings.*') ? 'active' : '' }}">
                                     <i class="ri-settings-3-line"></i> <span>System Settings</span>
                                 </a>
                             </li>

                             {{-- Mail Settings --}}
                             <li class="nav-item">
                                 <a href="{{ route('admin.mail-settings.edit') }}"
                                     class="nav-link {{ request()->routeIs('admin.mail-settings.*') ? 'active' : '' }}">
                                     <i class="ri-mail-settings-line"></i> <span>Mail Settings</span>
                                 </a>
                             </li>
                         </ul>
                     </div>
                 </li>

             </ul>
         </div>
         <!-- Sidebar -->
     </div>

     <div class="sidebar-background"></div>
 </div>

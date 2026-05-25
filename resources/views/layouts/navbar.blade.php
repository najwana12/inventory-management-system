<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <!-- Left navbar links -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button">
        <i class="fas fa-bars"></i>
      </a>
    </li>
  </ul>

  <!-- Right navbar links -->
  <ul class="navbar-nav ml-auto">

    <!-- Dropdown Bahasa -->
    <li class="nav-item dropdown">
      <a >
        <div class="d-flex align-items-center gap-2">
 
        </div>
      </a>
      <div class="dropdown-menu dropdown-menu-right" aria-labelledby="langDropdown" id="lang">
        <ul id="lang-dropdown" class="d-flex flex-column gap-2" style="max-height:12rem;overflow-y:auto;"></ul>
      </div>
    </li>

    <!-- Fullscreen Button -->
    <li class="nav-item">
      <a class="nav-link h5" data-widget="fullscreen" href="#" role="button">
        <i class="fas fa-expand-arrows-alt"></i>
      </a>
    </li>

    <!-- User Dropdown -->
    <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-expanded="false">
        <img src="{{ empty(Auth::user()->image) ? asset('user.png') : asset('storage/profile/'.Auth::user()->image) }}"
             class="img-circle elevation-2 mr-2"
             style="width:35px;height:35px;object-fit:cover;"
             alt="User Image">
        <span class="font-weight-bold text-gray text-capitalize">{{ Auth::user()->name }}</span>
      </a>
      <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
        <a href="{{ route('settings.profile') }}" class="dropdown-item">
          <i class="fas fa-user mr-2"></i> Profile
        </a>
        <div class="dropdown-divider"></div>
        <a href="{{ route('login.delete') }}" class="dropdown-item">
          <i class="fas fa-sign-out-alt mr-2"></i> Logout
        </a>
      </div>
    </li>

  </ul>
</nav>
<!-- /.navbar -->

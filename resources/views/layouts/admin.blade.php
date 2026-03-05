{{-- <x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout> --}}

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo-mini.svg') }}">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('assets/vendors/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/typicons/typicons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/simple-line-icons/css/simple-line-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
  </head>
  <body class="with-welcome-text">
    <div class="container-scroller">
      <nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex align-items-top flex-row">
        <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
          <div class="me-3">
            <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-bs-toggle="minimize">
              <span class="icon-menu"></span>
            </button>
          </div>
          <div>
            <a class="navbar-brand brand-logo" href="{{route('dashboard')}}"
            style=
            "font-size:22px;
            font-weight:700;
              color:#1F3BB3;
              letter-spacing:1px;
              text-decoration:none;">
              SIPAL
            </a>

            <a class="navbar-brand brand-logo-mini" href="{{route('dashboard')}}l">
              <img src="{{ asset('assets/images/logo-mini.svg') }}" alt="logo" />
            </a>
          </div>
        </div>
        <div class="navbar-menu-wrapper d-flex align-items-top">
          <ul class="navbar-nav">
            <li class="nav-item fw-semibold d-none d-lg-block ms-0">
              {{-- <h1 class="welcome-text">
                  Halo, <span class="text-black fw-bold">{{ Auth::user()->name }}</span>
              </h1> --}}
          
              {{-- <h3 class="welcome-sub-text">
                  @if(Auth::user()->role === 'admin')
                      Ringkasan pengelolaan dan aktivitas peminjaman alat laboratorium
                  @else
                      Ringkasan peminjaman dan status alat laboratorium Anda
                  @endif
              </h3> --}}
          </li>
          
          </ul>
          <ul class="navbar-nav ms-auto">
            <li class="nav-item d-none d-lg-block">
              <div id="datepicker-popup" class="input-group date datepicker navbar-date-picker">
                <span class="input-group-addon input-group-prepend border-right">
                  <span class="icon-calendar input-group-text calendar-icon"></span>
                </span>
                <input type="text" class="form-control">
              </div>
            </li>
            {{-- <li class="nav-item">
              <form class="search-form" action="#">
                <i class="icon-search"></i>
                <input type="search" class="form-control" placeholder="Search Here" title="Search here">
              </form>
            </li> --}}
            {{-- <li class="nav-item dropdown">
              <a class="nav-link count-indicator" id="notificationDropdown" href="#" data-bs-toggle="dropdown">
                <i class="icon-bell"></i>
                <span class="count"></span>
              </a>
              <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list pb-0" aria-labelledby="notificationDropdown">
                <a class="dropdown-item py-3 border-bottom">
                  <p class="mb-0 fw-medium float-start">You have 4 new notifications </p>
                  <span class="badge badge-pill badge-primary float-end">View all</span>
                </a>
                <a class="dropdown-item preview-item py-3">
                  <div class="preview-thumbnail">
                    <i class="mdi mdi-alert m-auto text-primary"></i>
                  </div>
                  <div class="preview-item-content">
                    <h6 class="preview-subject fw-normal text-dark mb-1">Application Error</h6>
                    <p class="fw-light small-text mb-0"> Just now </p>
                  </div>
                </a>
                <a class="dropdown-item preview-item py-3">
                  <div class="preview-thumbnail">
                    <i class="mdi mdi-lock-outline m-auto text-primary"></i>
                  </div>
                  <div class="preview-item-content">
                    <h6 class="preview-subject fw-normal text-dark mb-1">Settings</h6>
                    <p class="fw-light small-text mb-0"> Private message </p>
                  </div>
                </a>
                <a class="dropdown-item preview-item py-3">
                  <div class="preview-thumbnail">
                    <i class="mdi mdi-airballoon m-auto text-primary"></i>
                  </div>
                  <div class="preview-item-content">
                    <h6 class="preview-subject fw-normal text-dark mb-1">New user registration</h6>
                    <p class="fw-light small-text mb-0"> 2 days ago </p>
                  </div>
                </a>
              </div>
            </li> --}}
            {{-- <li class="nav-item dropdown">
              <a class="nav-link count-indicator" id="countDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="icon-mail icon-lg"></i>
              </a>
              <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list pb-0" aria-labelledby="countDropdown">
                <a class="dropdown-item py-3">
                  <p class="mb-0 fw-medium float-start">You have 7 unread mails </p>
                  <span class="badge badge-pill badge-primary float-end">View all</span>
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item preview-item">
                  <div class="preview-thumbnail">
                    <img src="assets/images/faces/face10.jpg" alt="image" class="img-sm profile-pic">
                  </div>
                  <div class="preview-item-content flex-grow py-2">
                    <p class="preview-subject ellipsis fw-medium text-dark">Marian Garner </p>
                    <p class="fw-light small-text mb-0"> The meeting is cancelled </p>
                  </div>
                </a>
                <a class="dropdown-item preview-item">
                  <div class="preview-thumbnail">
                    <img src="assets/images/faces/face12.jpg" alt="image" class="img-sm profile-pic">
                  </div>
                  <div class="preview-item-content flex-grow py-2">
                    <p class="preview-subject ellipsis fw-medium text-dark">David Grey </p>
                    <p class="fw-light small-text mb-0"> The meeting is cancelled </p>
                  </div>
                </a>
                <a class="dropdown-item preview-item">
                  <div class="preview-thumbnail">
                    <img src="assets/images/faces/face1.jpg" alt="image" class="img-sm profile-pic">
                  </div>
                  <div class="preview-item-content flex-grow py-2">
                    <p class="preview-subject ellipsis fw-medium text-dark">Travis Jenkins </p>
                    <p class="fw-light small-text mb-0"> The meeting is cancelled </p>
                  </div>
                </a>
              </div>
            </li> --}}
            <li class="nav-item dropdown d-none d-lg-block user-dropdown">
              <a class="nav-link" id="UserDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="mdi mdi-account-circle menu-icon" style="font-size: 28px;"></i>
                {{-- <img class="img-xs rounded-circle" src="assets/images/faces/face8.jpg" alt="Profile image"> </a> --}}
              <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
                <div class="dropdown-header text-center">
                    @auth
                        {{-- <img class="img-md rounded-circle" src="assets/images/faces/face8.jpg" alt="Profile image"> --}}
                        {{-- <i class="mdi mdi-account-circle" style="font-size: 30px;"></i> --}}
                        <p class="mb-1 mt-3 fw-semibold">{{ Auth::user()->name }}</p>
                        <p class="fw-light text-muted mb-0">{{ Auth::user()->email }}</p>
                    @else
                        {{-- <img class="img-md rounded-circle" src="assets/images/faces/face8.jpg" alt="Profile image"> --}}
                        <p class="mb-1 mt-3 fw-semibold">Guest</p>
                        <p class="fw-light text-muted mb-0">Not logged in</p>
                    @endauth
                </div>
                <a class="dropdown-item"  href="{{ route('profile.edit') }}"><i class="dropdown-item-icon mdi mdi-account-outline text-primary me-2"></i> Profil</a>
                {{-- <a class="dropdown-item"><i class="dropdown-item-icon mdi mdi-message-text-outline text-primary me-2"></i> Messages</a>
                <a class="dropdown-item"><i class="dropdown-item-icon mdi mdi-calendar-check-outline text-primary me-2"></i> Activity</a>
                <a class="dropdown-item"><i class="dropdown-item-icon mdi mdi-help-circle-outline text-primary me-2"></i> FAQ</a> --}}
                <form method="POST" action="{{ route('logout') }}">
                  @csrf
                  <button type="submit" class="dropdown-item">
                      <i class="dropdown-item-icon mdi mdi-power text-primary me-2"></i>Keluar
                  </button>
              </form>
              </div>
            </li>
          </ul>
          <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-bs-toggle="offcanvas">
            <span class="mdi mdi-menu"></span>
          </button>
        </div>
      </nav>
      <div class="container-fluid page-body-wrapper">
      <nav class="sidebar sidebar-offcanvas" id="sidebar">
        <ul class="nav">
            <!-- DASHBOARD -->
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                  href="{{ route('dashboard') }}">
                    <i class="mdi mdi-grid-large menu-icon"></i>
                    <span class="menu-title">Dashboard</span>
                </a>
            </li>

            @if(Auth::user()->role == 'admin')

            <!-- ADMIN SECTION -->
            <li class="nav-item nav-category">INVENTARIS</li>

            <!-- INVENTARIS -->
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}"
                  href="{{ route('categories.index') }}">
                    <i class="mdi mdi-shape-outline menu-icon"></i>
                    <span class="menu-title">Kategori</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('sumber-dana.*') ? 'active' : '' }}"
                  href="{{ route('sumber-dana.index') }}">
                    <i class="mdi mdi-cash-multiple menu-icon"></i>
                    <span class="menu-title">Sumber Dana</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('items.*') ? 'active' : '' }}"
                  href="{{ route('items.index') }}">
                    <i class="mdi mdi-cube-outline menu-icon"></i>
                    <span class="menu-title">Data Barang</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('item-units.*') ? 'active' : '' }}"
                  href="{{ route('item-units.index') }}">
                    <i class="mdi mdi-barcode-scan menu-icon"></i>
                    <span class="menu-title">Unit Barang</span>
                </a>
            </li>

            <!-- PEMINJAMAN -->
            <li class="nav-item nav-category">PEMINJAMAN</li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.loans.index') && !request()->has('status') ? 'active' : '' }}"
                  href="{{ route('admin.loans.index') }}">
                    <i class="mdi mdi-clipboard-text-outline menu-icon"></i>
                    <span class="menu-title">Semua Pengajuan</span>
                    @php
                        $pendingCount = \App\Models\Loan::where('status', 'pending')->count();
                    @endphp
                    @if($pendingCount > 0)
                    <span class="badge bg-danger ms-auto">{{ $pendingCount }}</span>
                    @endif
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.loans.index') && request('status') == 'pending' ? 'active' : '' }}"
                  href="{{ route('admin.loans.index', ['status' => 'pending']) }}">
                    <i class="mdi mdi-timer-sand menu-icon"></i>
                    <span class="menu-title">Pending</span>
                    @if($pendingCount > 0)
                    <span class="badge bg-warning ms-auto">{{ $pendingCount }}</span>
                    @endif
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.loans.index') && request('status') == 'approved' ? 'active' : '' }}"
                  href="{{ route('admin.loans.index', ['status' => 'approved']) }}">
                    <i class="mdi mdi-check-circle-outline menu-icon"></i>
                    <span class="menu-title">Disetujui</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.loans.index') && request('status') == 'borrowed' ? 'active' : '' }}"
                  href="{{ route('admin.loans.index', ['status' => 'borrowed']) }}">
                    <i class="mdi mdi-bookmark menu-icon"></i>
                    <span class="menu-title">Dipinjam</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.loans.index') && request('status') == 'returned' ? 'active' : '' }}"
                  href="{{ route('admin.loans.index', ['status' => 'returned']) }}">
                    <i class="mdi mdi-history menu-icon"></i>
                    <span class="menu-title">Riwayat</span>
                </a>
            </li>

            <!-- ADMIN LAINNYA -->
            <li class="nav-item nav-category">ADMINISTRASI</li>

            <li class="nav-item">
                <a class="nav-link" href="">
                    <i class="mdi mdi-account-multiple-outline menu-icon"></i>
                    <span class="menu-title">Manajemen User</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.reports.loans') ? 'active' : '' }}"
                  href="{{ route('admin.reports.loans') }}">
                    <i class="mdi mdi-file-chart-outline menu-icon"></i>
                    <span class="menu-title">Laporan</span>
                </a>
            </li>

            @elseif(Auth::user()->role == 'user')

            <!-- USER SECTION -->
            <li class="nav-item nav-category">MENU PENGGUNA</li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('user.items.*') ? 'active' : '' }}"
                  href="{{ route('user.items.index') }}">
                    <i class="mdi mdi-cube-outline menu-icon"></i>
                    <span class="menu-title">Daftar Alat</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('user.loans.create') ? 'active' : '' }}"
                  href="{{ route('user.loans.create') }}">
                    <i class="mdi mdi-plus-box-outline menu-icon"></i>
                    <span class="menu-title">Ajukan Peminjaman</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('user.loans.history') ? 'active' : '' }}"
                  href="{{ route('user.loans.history') }}">
                    <i class="mdi mdi-history menu-icon"></i>
                    <span class="menu-title">Riwayat Saya</span>
                    @php
                        $userLoansCount = \App\Models\Loan::where('user_id', Auth::id())
                            ->where('status', ['borrowed', 'overdue'])
                            ->count();
                    @endphp
                    @if($userLoansCount > 0)
                    <span class="badge bg-primary ms-auto">{{ $userLoansCount }}</span>
                    @endif
                </a>
            </li>

            @endif

            <!-- UMUM -->
            <li class="nav-item nav-category">UMUM</li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}" href="{{ route('profile.edit') }}">
                    <i class="mdi mdi-account-circle-outline menu-icon"></i>
                    <span class="menu-title">Profil</span>
                </a>
            </li>
        </ul>
      </nav>
      
      <div class="main-panel">
        <div class="content-wrapper">
          @yield('content')
        </div>
        <footer class="footer">
          <div class="d-sm-flex justify-content-center">
            {{-- <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Premium <a href="https://www.bootstrapdash.com/" target="_blank">Bootstrap admin template</a> from BootstrapDash.</span> --}}
            <span class="float-none float-sm-end d-block mt-1 mt-sm-0 text-center">Copyright © 2026 - Rizky Adiwijaya.</span>
          </div>
        </footer>
      </div>
      </div>
    </div>
    <script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>
    <script src="{{ asset('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/chart.js/chart.umd.js') }}"></script>
    <script src="{{ asset('assets/vendors/progressbar.js/progressbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/off-canvas.js') }}"></script>
    <script src="{{ asset('assets/js/template.js') }}"></script>
    <script src="{{ asset('assets/js/settings.js') }}"></script>
    <script src="{{ asset('assets/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('assets/js/todolist.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.cookie.js') }}"></script>
    <script src="{{ asset('assets/js/dashboard.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
      const swalWithBootstrapButtons = Swal.mixin({
          customClass: {
              confirmButton: "btn btn-danger ms-2",
              cancelButton: "btn btn-primary"
          },
          buttonsStyling: false
      });
      
      // ✅ Confirm Delete
      function confirmDelete(formId, customText = 'Data ini akan dihapus permanen!') {
          swalWithBootstrapButtons.fire({
              title: "Apakah Anda yakin?",
              text: customText,
              icon: "warning",
              showCancelButton: true,
              cancelButtonText: "Batal",
              confirmButtonText: "Hapus",
              reverseButtons: true
          }).then((result) => {
              if (result.isConfirmed) {
      
                  Swal.fire({
                      title: 'Menghapus...',
                      text: 'Mohon tunggu',
                      allowOutsideClick: false,
                      allowEscapeKey: false,
                      showConfirmButton: false,
                      didOpen: () => {
                          Swal.showLoading();
                      }
                  });
      
                  document.getElementById(formId).submit();
              }
          });
      }
      
      
      // ✅ Success Alert
      @if(session('success'))
      Swal.fire({
          icon: 'success',
          title: 'Berhasil',
          text: '{{ session('success') }}',
          showConfirmButton: false,
          timer: 2000
      });
      @endif
      
      
      // ✅ Error Alert
      @if(session('error'))
      Swal.fire({
          icon: 'error',
          title: 'Gagal',
          text: '{{ session('error') }}',
      });
      @endif
      
      document.querySelectorAll('form').forEach(form => {

      // skip delete form
      if (form.id && form.id.startsWith('delete-')) return;

      form.addEventListener('submit', function () {

          Swal.fire({
              title: 'Memproses...',
              html: `
                  <div style="display:flex; flex-direction:column; align-items:center; gap:15px;">
                      <div class="dot-spinner">
                          <div class="dot-spinner__dot"></div>
                          <div class="dot-spinner__dot"></div>
                          <div class="dot-spinner__dot"></div>
                          <div class="dot-spinner__dot"></div>
                          <div class="dot-spinner__dot"></div>
                          <div class="dot-spinner__dot"></div>
                          <div class="dot-spinner__dot"></div>
                          <div class="dot-spinner__dot"></div>
                      </div>
                      <span>Mohon tunggu...</span>
                  </div>
              `,
              allowOutsideClick: false,
              allowEscapeKey: false,
              showConfirmButton: false,
          });

      });

      });      
    </script>
  
  </body>
</html>
<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login - SIPAL</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo-mini.svg') }}">
    <!-- plugins:css -->
    <link rel="stylesheet" href="{{ asset('assets/vendors/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/typicons/typicons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/simple-line-icons/css/simple-line-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
    <!-- endinject -->
    <!-- inject:css -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <!-- endinject -->
    <style>
      /* Background dengan gradient */
      body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100dvh;
      }
      
      /* Card dengan shadow tebal */
      .auth-form-light {
        background: white;
        border-radius: 15px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3), 0 5px 15px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
      }
      
    </style>
  </head>
  <body>
    <div class="container-scroller">
      <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-center auth px-0">
          <div class="row w-100 mx-0">
            <div class="col-lg-4 mx-auto">
              <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                
                {{-- LOGO GAMBAR --}}
                <div class="brand-logo text-center mb-4">
                  <img src="{{ asset('assets/images/logo-mini.svg') }}" 
                       alt="SIPAL Logo" 
                       style="max-width: 80px; height: auto;">
                  <p style="font-size: 13px; color: #6c757d; margin-top: 5px;">
                    Sistem Manajemen Inventory & Peminjaman Alat Laboratorium
                  </p>
                </div>
                
                <!-- Session Status -->
                @if (session('status'))
                <div class="alert alert-success mb-4">
                    {{ session('status') }}
                </div>
                @endif

                <!-- Error Message -->
                @if($errors->any())
                <div class="alert alert-danger mb-4">
                    {{ $errors->first() }}
                </div>
                @endif

                <form class="pt-3" method="POST" action="{{ route('login') }}">
                  @csrf

                  <!-- Username / Email Field -->
                  <div class="form-group">
                    <input type="text" 
                           class="form-control form-control-lg @error('login') is-invalid @enderror" 
                           id="login" 
                           name="login"
                           value="{{ old('login') }}"
                           placeholder="Username"
                           required 
                           autofocus>
                    @error('login')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                  </div>

                  <!-- Password Field -->
                  <div class="form-group">
                    <input type="password" 
                           class="form-control form-control-lg @error('password') is-invalid @enderror" 
                           id="password" 
                           name="password"
                           placeholder="Password"
                           required 
                           autocomplete="current-password">
                    @error('password')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                  </div>

                  <div class="mt-3 d-grid gap-2">
                    <button type="submit" class="btn btn-block btn-primary btn-lg fw-medium auth-form-btn">
                      SIGN IN
                    </button>
                  </div>

                  <div class="my-2 d-flex justify-content-between align-items-center">
                    <div class="form-check">
                      <label class="form-check-label text-muted">
                        <input type="checkbox" 
                               class="form-check-input" 
                               id="remember_me" 
                               name="remember"> 
                        Ingat Saya
                      </label>
                    </div>
                    @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="auth-link text-black">Lupa Password?</a>
                    @endif
                  </div>

                  @if (Route::has('register'))
                  <div class="text-center mt-4 fw-light"> 
                    Belum Punya Akun?
                    <a href="{{ route('register') }}" class="text-primary">Registrasi</a>
                  </div>
                  @endif
                </form>
              </div>
            </div>
          </div>
        </div>
        <!-- content-wrapper ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>
    <script src="{{ asset('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <!-- endinject -->
    <!-- inject:js -->
    <script src="{{ asset('assets/js/off-canvas.js') }}"></script>
    <script src="{{ asset('assets/js/template.js') }}"></script>
    <script src="{{ asset('assets/js/settings.js') }}"></script>
    <script src="{{ asset('assets/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('assets/js/todolist.js') }}"></script>
    <!-- endinject -->
  </body>
</html>
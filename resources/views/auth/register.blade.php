{{-- <x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout> --}}
<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Register</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo-mini.svg') }}">
    <!-- plugins:css -->
    <link rel="stylesheet" href="../../assets/vendors/feather/feather.css">
    <link rel="stylesheet" href="../../assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="../../assets/vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="../../assets/vendors/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="../../assets/vendors/typicons/typicons.css">
    <link rel="stylesheet" href="../../assets/vendors/simple-line-icons/css/simple-line-icons.css">
    <link rel="stylesheet" href="../../assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="../../assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="../../assets/css/style.css">
    <!-- endinject -->
    <link rel="shortcut icon" href="../../assets/images/favicon.png" />
  </head>
  <body>
    <div class="container-scroller">
      <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-center auth px-0">
          <div class="row w-100 mx-0">
            <div class="col-lg-4 mx-auto">
              <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                <div class="brand-logo text-center">
                  <a class="navbar-brand brand-logo mx-auto d-block"
                      style="
                      font-size:22px;
                      font-weight:700;
                      color:#1F3BB3;
                      letter-spacing:1px;
                      text-decoration:none;
                      ">
                      SIPAL
                  </a>
              
                  <p style="
                      font-size:14px;
                      color:#6c757d;
                      margin-top:-24px;
                      margin-bottom:0;
                      letter-spacing:0.5px;
                      ">
                      Sistem Informasi Peminjaman Alat Laboratorium
                  </p>
              </div>
                {{-- <h4>New here?</h4>
                <h6 class="fw-light">Signing up is easy. It only takes a few steps</h6> --}}

                <!-- Session Status -->
                @if (session('status'))
                <div class="alert alert-success mb-4">
                    {{ session('status') }}
                </div>
                @endif

                <form class="pt-3" method="POST" action="{{ route('register') }}">
                  @csrf

                  <!-- Name -->
                  <div class="form-group">
                    <input type="text" 
                           class="form-control form-control-lg @error('name') is-invalid @enderror" 
                           id="name" 
                           name="name"
                           value="{{ old('name') }}"
                           placeholder="Nama"
                           required 
                           autofocus 
                           autocomplete="name">
                    @error('name')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                  </div>

                  <!-- Email Address -->
                  <div class="form-group">
                    <input type="email" 
                           class="form-control form-control-lg @error('email') is-invalid @enderror" 
                           id="email" 
                           name="email"
                           value="{{ old('email') }}"
                           placeholder="Email"
                           required 
                           autocomplete="username">
                    @error('email')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                  </div>

                  <!-- Password -->
                  <div class="form-group">
                    <input type="password" 
                           class="form-control form-control-lg @error('password') is-invalid @enderror" 
                           id="password" 
                           name="password"
                           placeholder="Password"
                           required 
                           autocomplete="new-password">
                    @error('password')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                  </div>

                  <!-- Confirm Password -->
                  <div class="form-group">
                    <input type="password" 
                           class="form-control form-control-lg @error('password_confirmation') is-invalid @enderror" 
                           id="password_confirmation" 
                           name="password_confirmation"
                           placeholder="Konfirmasi Password"
                           required 
                           autocomplete="new-password">
                    @error('password_confirmation')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                  </div>

                  <!-- Terms & Conditions Checkbox -->
                  <div class="mb-4">
                    <div class="form-check">
                      <label class="form-check-label text-muted">
                        <input type="checkbox" class="form-check-input" name="terms" required> 
                        Setuju dengan Syarat & Ketentuan
                      </label>
                      @error('terms')
                      <div class="invalid-feedback d-block">
                          {{ $message }}
                      </div>
                      @enderror
                    </div>
                  </div>

                  <div class="mt-3 d-grid gap-2">
                    <button type="submit" class="btn btn-block btn-primary btn-lg fw-medium auth-form-btn">
                      SIGN UP
                    </button>
                  </div>

                  <div class="text-center mt-4 fw-light"> 
                    Sudah Punya Akun? 
                    <a href="{{ route('login') }}" class="text-primary">Login</a>
                  </div>
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
    <script src="../../assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="../../assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="../../assets/js/off-canvas.js"></script>
    <script src="../../assets/js/template.js"></script>
    <script src="../../assets/js/settings.js"></script>
    <script src="../../assets/js/hoverable-collapse.js"></script>
    <script src="../../assets/js/todolist.js"></script>
    <!-- endinject -->
  </body>
</html>
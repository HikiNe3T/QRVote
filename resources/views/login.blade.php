<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Masuk - VoteQR</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">

  <style>
    /* Styling wrapper password dan toggle */
    .password-wrapper {
      position: relative;
      display: flex;
      align-items: center;
    }

    .password-wrapper .input {
      padding-right: 2.5rem;
    }

    .toggle-password {
      position: absolute;
      right: 0.75rem;
      background: none;
      border: none;
      padding: 0;
      cursor: pointer;
      color: var(--text-secondary, #6b7280);
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .toggle-password:focus {
      outline: none;
    }

    .toggle-password svg {
      width: 20px;
      height: 20px;
    }

    /* Styling Teks Validasi & Hint Password */
    .error-text {
      color: #ef4444;
      font-size: 0.75rem;
      margin-top: 0.25rem;
      display: block;
    }

    .password-requirements {
      margin-top: 0.5rem;
      padding: 0.5rem 0.75rem;
      background-color: var(--bg-secondary, #f9fafb);
      border-radius: 0.375rem;
      border: 1px solid var(--border-color, #e5e7eb);
    }

    .password-requirements p {
      font-size: 0.75rem;
      font-weight: 600;
      color: var(--text-secondary, #4b5563);
      margin-bottom: 0.25rem;
    }

    .password-requirements ul {
      list-style: none;
      padding-left: 0;
      margin: 0;
    }

    .password-requirements li {
      font-size: 0.725rem;
      color: #9ca3af;
      display: flex;
      align-items: center;
      gap: 0.375rem;
    }

    .password-requirements li.valid {
      color: #10b981;
    }

    .password-requirements li.valid::before {
      content: "✓";
      font-weight: bold;
    }

    .password-requirements li.invalid::before {
      content: "•";
    }

    .alert-success {
      padding: 0.75rem;
      background-color: #d1fae5;
      color: #065f46;
      border-radius: 0.375rem;
      font-size: 0.875rem;
      margin-bottom: 1rem;
    }
  </style>
</head>
<body>
<div id="toast" class="toast"></div>
  <div class="auth-container">
    <div class="auth-card animate-slide-up">
      <!-- Logo -->
      <div class="auth-header">
        <div class="auth-header__logo">VoteQR</div>
        <p class="text-secondary text-sm">Sistem Voting Berbasis QR Code</p>
      </div>

      <!-- Tabs -->
      <div class="auth-tabs">
        <button class="auth-tab auth-tab--active" id="tab-login" onclick="switchTab('login')">Masuk</button>
        <button class="auth-tab" id="tab-register" onclick="switchTab('register')">Daftar</button>
      </div>

      <!-- NOTIFIKASI ERROR / SUCCESS LOGIN -->
      @if(session('error'))
      <script>
      document.addEventListener("DOMContentLoaded", function() {
          showToast("{{ session('error') }}");
      });
      </script>
      @endif

      @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
      @endif

      <!-- FORM LOGIN -->
      <form id="form-login" method="POST" action="{{ route('login') }}" class="form">
        @csrf

        <!-- EMAIL -->
        <div class="input-group">
            <label>Email</label>
            <input type="email" name="email" class="input" value="{{ old('email', session('remember_email')) }}" required>
        </div>

        <!-- PASSWORD -->
        <div class="input-group">
            <label>Password</label>
            <div class="password-wrapper">
                <input type="password" id="login-password" name="password" class="input" placeholder="********" required>
                <button type="button" class="toggle-password" onclick="togglePasswordVisibility('login-password', this)" aria-label="Tampilkan password">
                    <svg class="icon-eye" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg class="icon-eye-off hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.03 10.03 0 013.982-.863c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m-1.587 1.378L2 2l20 20" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- REMEMBER ME & LUPA PASSWORD -->
        <div class="flex justify-between items-center">
            <label class="flex items-center gap-2 text-sm text-secondary" style="cursor: pointer;">
                <input type="checkbox" name="remember" {{ old('email') !== null ? (old('remember') ? 'checked' : '') : (session('remember_checked') ? 'checked' : '') }} style="accent-color: var(--primary-500);">
                Ingat saya
            </label>

            <a href="{{ route('forgot.password') }}" class="text-sm" style="color: var(--primary-500); font-weight: 500;">
                Lupa password?
            </a>
        </div>

        <!-- BUTTON -->
        <button type="submit" class="btn btn--primary w-full" style="margin-top: var(--space-2);">
            Masuk
        </button>
      </form>

      <!-- FORM REGISTER -->
      <form id="form-register" method="POST" action="{{ route('register') }}" class="form hidden">
        @csrf

        <!-- NAMA -->
        <div class="input-group">
            <label>Nama</label>
            <input type="text" name="name" value="{{ old('name') }}" class="input" required>
            @error('name')
                <span class="error-text">{{ $message }}</span>
            @enderror
        </div>

        <!-- EMAIL -->
        <div class="input-group">
            <label>Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="input" required>
            @error('email')
                <span class="error-text">{{ $message }}</span>
            @enderror
        </div>

        <!-- PHONE -->
        <div class="input-group">
            <label>Phone</label>
            <input type="text" name="phone" value="{{ old('phone') }}" class="input" placeholder="08xxxxxxxxxx" required>
            @error('phone')
                <span class="error-text">{{ $message }}</span>
            @enderror
        </div>

        <!-- PASSWORD REGISTRASI -->
        <div class="input-group">
            <label>Password</label>
            <div class="password-wrapper">
                <input type="password" id="reg-password" name="password" class="input" placeholder="Min. 8 karakter" required oninput="validatePasswordRequirements(this.value)">
                <button type="button" class="toggle-password" onclick="togglePasswordVisibility('reg-password', this)" aria-label="Tampilkan password">
                    <svg class="icon-eye" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg class="icon-eye-off hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.03 10.03 0 013.982-.863c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m-1.587 1.378L2 2l20 20" />
                    </svg>
                </button>
            </div>
            @error('password')
                <span class="error-text">{{ $message }}</span>
            @enderror

            <!-- PANDUAN SYARAT PASSWORD (REALTIME VALIDATION) -->
            <div class="password-requirements">
                <p>Syarat Password:</p>
                <ul>
                    <li id="req-length" class="invalid">Minimal 8 karakter</li>
                    <li id="req-uppercase" class="invalid">Memiliki minimal 1 huruf besar (A-Z)</li>
                    <li id="req-lowercase" class="invalid">Memiliki minimal 1 huruf kecil (a-z)</li>
                    <li id="req-number" class="invalid">Memiliki minimal 1 angka (0-9)</li>
                </ul>
            </div>
        </div>

        <!-- KONFIRMASI PASSWORD REGISTRASI -->
        <div class="input-group">
            <label>Konfirmasi Password</label>
            <div class="password-wrapper">
                <input type="password" id="reg-password-confirm" name="password_confirmation" class="input" required>
                <button type="button" class="toggle-password" onclick="togglePasswordVisibility('reg-password-confirm', this)" aria-label="Tampilkan password">
                    <svg class="icon-eye" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg class="icon-eye-off hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.03 10.03 0 013.982-.863c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m-1.587 1.378L2 2l20 20" />
                    </svg>
                </button>
            </div>
            @error('password_confirmation')
                <span class="error-text">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="btn btn--primary w-full" style="margin-top: var(--space-2);">
            Daftar
        </button>
      </form>

      <!-- Divider -->
      <div class="divider"></div>

      <!-- Back to Home -->
      <a href="{{ route('home') }}" class="btn btn--secondary w-full">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        Kembali ke Beranda
      </a>
    </div>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
        const activeTab = "{{ session('active_tab') }}";
        if (activeTab === 'register') {
            switchTab('register');
        }
    });

    function showToast(message) {
        const toast = document.getElementById('toast');
        toast.innerText = message;
        toast.classList.add('show');

        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }

    function switchTab(tab) {
      const loginForm = document.getElementById('form-login');
      const registerForm = document.getElementById('form-register');
      const loginTab = document.getElementById('tab-login');
      const registerTab = document.getElementById('tab-register');

      if (tab === 'login') {
        loginForm.classList.remove('hidden');
        registerForm.classList.add('hidden');
        loginTab.classList.add('auth-tab--active');
        registerTab.classList.remove('auth-tab--active');
      } else {
        loginForm.classList.add('hidden');
        registerForm.classList.remove('hidden');
        loginTab.classList.remove('auth-tab--active');
        registerTab.classList.add('auth-tab--active');
      }
    }

    function togglePasswordVisibility(inputId, btn) {
      const input = document.getElementById(inputId);
      const iconEye = btn.querySelector('.icon-eye');
      const iconEyeOff = btn.querySelector('.icon-eye-off');

      if (input.type === 'password') {
        input.type = 'text';
        iconEye.classList.add('hidden');
        iconEyeOff.classList.remove('hidden');
      } else {
        input.type = 'password';
        iconEye.classList.remove('hidden');
        iconEyeOff.classList.add('hidden');
      }
    }

    // Checking Realtime Syarat Password
    function validatePasswordRequirements(val) {
      const reqLength = document.getElementById('req-length');
      const reqUppercase = document.getElementById('req-uppercase');
      const reqLowercase = document.getElementById('req-lowercase');
      const reqNumber = document.getElementById('req-number');

      // Check Minimal 8 karakter
      if (val.length >= 8) {
        reqLength.classList.remove('invalid');
        reqLength.classList.add('valid');
      } else {
        reqLength.classList.remove('valid');
        reqLength.classList.add('invalid');
      }

      // Check Huruf Besar
      if (/[A-Z]/.test(val)) {
        reqUppercase.classList.remove('invalid');
        reqUppercase.classList.add('valid');
      } else {
        reqUppercase.classList.remove('valid');
        reqUppercase.classList.add('invalid');
      }

      // Check Huruf Kecil
      if (/[a-z]/.test(val)) {
        reqLowercase.classList.remove('invalid');
        reqLowercase.classList.add('valid');
      } else {
        reqLowercase.classList.remove('valid');
        reqLowercase.classList.add('invalid');
      }

      // Check Angka
      if (/\d/.test(val)) {
        reqNumber.classList.remove('invalid');
        reqNumber.classList.add('valid');
      } else {
        reqNumber.classList.remove('valid');
        reqNumber.classList.add('invalid');
      }
    }

    const saved = localStorage.getItem('theme');
    if (saved) document.documentElement.setAttribute('data-theme', saved);
  </script>
</body>
</html>
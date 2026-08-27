<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Masuk - VoteQR</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<!-- TOAST NOTIFICATION -->
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

<!-- NOTIFIKASI ERROR LOGIN -->
<!-- ERROR LOGIN -->
@if(session('error'))
<script>
document.addEventListener("DOMContentLoaded", function() {
    showToast("{{ session('error') }}");
});
</script>
@endif

<!-- ERROR VALIDASI -->
{{-- @if ($errors->any())
    <div class="alert alert--error" style="margin-bottom: var(--space-4);">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif --}}

      <form id="form-login" method="POST" action="{{ route('login') }}" class="form">
    @csrf <!-- WAJIB untuk keamanan Laravel -->

    <!-- EMAIL -->
    <div class="input-group">
        <label>Email</label>
        <!-- name="email" penting untuk dikirim ke controller -->
        <input type="email" name="email" class="input" value="{{ old('email', session('remember_email')) }}" required>
    </div>

    <!-- PASSWORD -->
    <div class="input-group">
        <label>Password</label>
        <!-- name="password" wajib -->
        <input type="password" name="password" class="input" placeholder="********" required>
    </div>

    <!-- REMEMBER ME -->
    <div class="flex justify-between items-center">
        <label class="flex items-center gap-2 text-sm text-secondary" style="cursor: pointer;">
            <!-- optional -->
            {{-- <input type="checkbox" name="remember" {{ old('remember', session('remember_checked')) ? 'checked' : '' }} style="accent-color: var(--primary-500);"> --}}
            <input type="checkbox" name="remember"{{ old('email') !== null ? (old('remember') ? 'checked' : '') : (session('remember_checked') ? 'checked' : '') }}style="accent-color: var(--primary-500);">
            Ingat saya
        </label>

        <!-- LINK LUPA PASSWORD -->
        <a href="{{ route('forgot.password') }}" class="text-sm" style="color: var(--primary-500); font-weight: 500;">
            Lupa password?
        </a>
    </div>

    <!-- BUTTON -->
    <button type="submit" class="btn btn--primary w-full" style="margin-top: var(--space-2);">
        Masuk
    </button>
</form>

      <form id="form-register" method="POST" action="{{ route('register') }}" class="form hidden">
    @csrf

    <div class="input-group">
        <label>Nama</label>
        <input type="text" name="name" value="{{ old('name') }}" class="input" required>
    </div>

    <div class="input-group">
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}" class="input" required>
    </div>

    <div class="input-group">
        <label>Phone</label>
        <input type="text" name="phone" value="{{ old('phone') }}" class="input" required>
    </div>

    <div class="input-group">
        <label>Password</label>
        <input type="password" name="password" class="input" placeholder="Min. 8 karakter" required>
    </div>

    <div class="input-group">
        <label>Konfirmasi Password</label>
        <input type="password" name="password_confirmation" class="input" required>
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

    // 🔥 cek apakah harus buka tab register
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

    const saved = localStorage.getItem('theme');
    if (saved) document.documentElement.setAttribute('data-theme', saved);
  </script>
</body>
</html>

<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Profil - VoteQR</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">

  <style>
    /* Styling Spacing & Margin Form */
    .input-group {
      margin-bottom: 1.25rem;
    }

    .input-group label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 500;
      font-size: var(--font-size-sm, 0.875rem);
    }

    /* Password Wrapper & Toggle Icon */
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

    /* Validation Messages & Password Hints */
    .error-text {
      color: #ef4444;
      font-size: 0.75rem;
      margin-top: 0.35rem;
      display: block;
    }

    .password-requirements {
      margin-top: 0.5rem;
      padding: 0.625rem 0.75rem;
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

    @keyframes spin {
      from { transform: rotate(0deg); }
      to { transform: rotate(360deg); }
    }
  </style>
</head>
<body>

<!-- HEADER -->
<header class="header">
  <div class="container header__inner">
    <a href="{{ route('home') }}" class="header__logo">VoteQR</a>

    <div class="header__actions">
      <button class="theme-toggle" onclick="toggleTheme()" title="Ganti Tema">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="5"/>
          <path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>
        </svg>
      </button>

      <form method="POST" action="{{ route('logout') }}" style="display:inline;">
        @csrf
        <button type="submit" class="btn btn--danger" style="padding: var(--space-2) var(--space-4); font-size: var(--font-size-sm);">
          Logout
        </button>
      </form>
    </div>
  </div>
</header>

<!-- MAIN CONTENT -->
<main class="section page">
  <div class="container" style="max-width: 560px;">

    <!-- Back Button -->
    <div class="flex items-center gap-3 mb-6">
      <a href="{{ route('home') }}" class="btn btn--secondary" style="padding: var(--space-2) var(--space-3);">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg>
        Kembali
      </a>
    </div>

    <h1 class="section-title mb-2">Edit Profil</h1>
    <p class="section-subtitle mb-8">Perbarui informasi dan foto profil Anda</p>

    <!-- Alerts Notification -->
    @if(session('success'))
      <div class="alert alert--success mb-4">{{ session('success') }}</div>
    @endif

    @if(session('error'))
      <div class="alert alert--error mb-4">{{ session('error') }}</div>
    @endif

    <!-- FORM EDIT PROFIL & AVATAR -->
    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
      @csrf

      <!-- Avatar Section -->
      <div class="card mb-6">
        <div class="flex flex-col items-center" style="gap: var(--space-4);">
          
          <!-- Avatar Preview -->
          <div id="avatar-container" style="position: relative; width: 120px; height: 120px;">
            <div id="avatar-wrapper" style="width: 120px; height: 120px; border-radius: var(--radius-full); overflow: hidden; background: linear-gradient(135deg, var(--primary-500), var(--accent-500)); display: flex; align-items: center; justify-content: center; color: white; border: 3px solid var(--border-color);">
              
              <!-- Placeholder Icon apabila foto belum diupload -->
              <svg id="avatar-placeholder" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="{{ auth()->user()->avatar_url ? 'display: none;' : '' }}">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
              </svg>

              <!-- Image Display -->
              <img id="avatar-img" src="{{ auth()->user()->avatar_url ? asset('storage/avatars/' . auth()->user()->avatar_url) : '' }}" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover; {{ auth()->user()->avatar_url ? '' : 'display: none;' }}">
            </div>
          </div>

          <!-- Upload Button -->
          <div class="flex items-center gap-3">
            <label for="avatar-file" class="btn btn--primary" style="cursor: pointer; gap: var(--space-2);">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
              <span id="upload-label">Pilih Foto</span>
            </label>
          </div>
          
          <!-- File Input -->
          <input type="file" id="avatar-file" name="avatar" accept="image/*" style="display: none;" onchange="previewAvatar(event)">

          <p class="text-sm text-muted text-center" style="max-width: 320px;">Format JPG, PNG, atau WEBP. Maksimal 2MB.</p>
          @error('avatar')
            <span class="error-text text-center">{{ $message }}</span>
          @enderror
        </div>
      </div>

      <!-- Detail Form -->
      <div class="card mb-6">
        <div class="input-group">
          <label for="profile-name">Nama Lengkap</label>
          <input type="text" id="profile-name" name="full_name" class="input" placeholder="{{ auth()->user()->full_name }}" value="{{ old('full_name', auth()->user()->full_name) }}" required>
          @error('full_name')
            <span class="error-text">{{ $message }}</span>
          @enderror
        </div>

        <button type="submit" class="btn btn--primary w-full" id="save-btn" style="margin-top: var(--space-4);">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
          Simpan Perubahan
        </button>
      </div>
    </form>

    <!-- Change Password Section -->
    <div class="card mt-6">
      <h3 style="font-size: var(--font-size-lg); font-weight: 600; margin-bottom: var(--space-4);">Ganti Password</h3>
      
      <form id="password-form" method="POST" action="{{ route('profile.password') }}">
        @csrf

        <!-- Password Saat Ini -->
        <div class="input-group">
          <label for="current-password">Password Saat Ini</label>
          <div class="password-wrapper">
            <input type="password" id="current-password" name="current_password" class="input" placeholder="********" required>
            <button type="button" class="toggle-password" onclick="togglePasswordVisibility('current-password', this)" aria-label="Tampilkan password">
              <svg class="icon-eye" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
              <svg class="icon-eye-off hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.03 10.03 0 013.982-.863c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m-1.587 1.378L2 2l20 20" /></svg>
            </button>
          </div>
          @error('current_password')
            <span class="error-text">{{ $message }}</span>
          @enderror
        </div>

        <!-- Password Baru -->
        <div class="input-group">
          <label for="new-password">Password Baru</label>
          <div class="password-wrapper">
            <input type="password" id="new-password" name="password" class="input" placeholder="Min. 8 karakter" required oninput="validatePasswordRequirements(this.value)">
            <button type="button" class="toggle-password" onclick="togglePasswordVisibility('new-password', this)" aria-label="Tampilkan password">
              <svg class="icon-eye" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
              <svg class="icon-eye-off hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.03 10.03 0 013.982-.863c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m-1.587 1.378L2 2l20 20" /></svg>
            </button>
          </div>
          @error('password')
            <span class="error-text">{{ $message }}</span>
          @enderror

          <!-- Requirement Box -->
          <div class="password-requirements">
            <p>Syarat Password Baru:</p>
            <ul>
              <li id="req-length" class="invalid">Minimal 8 karakter</li>
              <li id="req-uppercase" class="invalid">Memiliki minimal 1 huruf besar (A-Z)</li>
              <li id="req-lowercase" class="invalid">Memiliki minimal 1 huruf kecil (a-z)</li>
              <li id="req-number" class="invalid">Memiliki minimal 1 angka (0-9)</li>
            </ul>
          </div>
        </div>

        <!-- Konfirmasi Password Baru -->
        <div class="input-group">
          <label for="confirm-new-password">Konfirmasi Password Baru</label>
          <div class="password-wrapper">
            <input type="password" id="confirm-new-password" name="password_confirmation" class="input" placeholder="Ulangi password baru" required>
            <button type="button" class="toggle-password" onclick="togglePasswordVisibility('confirm-new-password', this)" aria-label="Tampilkan password">
              <svg class="icon-eye" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
              <svg class="icon-eye-off hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.03 10.03 0 013.982-.863c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m-1.587 1.378L2 2l20 20" /></svg>
            </button>
          </div>
          @error('password_confirmation')
            <span class="error-text">{{ $message }}</span>
          @enderror
        </div>

        <button type="submit" class="btn btn--secondary w-full" id="change-pw-btn" style="margin-top: var(--space-4);">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          Ganti Password
        </button>
      </form>
    </div>

  </div>
</main>

<script>
  // Preview foto saat di-upload
  function previewAvatar(event) {
    const file = event.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function(e) {
        const img = document.getElementById('avatar-img');
        const placeholder = document.getElementById('avatar-placeholder');
        
        img.src = e.target.result;
        img.style.display = 'block';
        placeholder.style.display = 'none';
      }
      reader.readAsDataURL(file);
    }
  }

  // Toggle Hide/Unhide Password
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

  // Validasi Syarat Password Real-time
  function validatePasswordRequirements(val) {
    const reqLength = document.getElementById('req-length');
    const reqUppercase = document.getElementById('req-uppercase');
    const reqLowercase = document.getElementById('req-lowercase');
    const reqNumber = document.getElementById('req-number');

    if (val.length >= 8) {
      reqLength.classList.remove('invalid');
      reqLength.classList.add('valid');
    } else {
      reqLength.classList.remove('valid');
      reqLength.classList.add('invalid');
    }

    if (/[A-Z]/.test(val)) {
      reqUppercase.classList.remove('invalid');
      reqUppercase.classList.add('valid');
    } else {
      reqUppercase.classList.remove('valid');
      reqUppercase.classList.add('invalid');
    }

    if (/[a-z]/.test(val)) {
      reqLowercase.classList.remove('invalid');
      reqLowercase.classList.add('valid');
    } else {
      reqLowercase.classList.remove('valid');
      reqLowercase.classList.add('invalid');
    }

    if (/\d/.test(val)) {
      reqNumber.classList.remove('invalid');
      reqNumber.classList.add('valid');
    } else {
      reqNumber.classList.remove('valid');
      reqNumber.classList.add('invalid');
    }
  }

  function toggleTheme() {
    const html = document.documentElement;
    const current = html.getAttribute('data-theme');
    html.setAttribute('data-theme', current === 'dark' ? 'light' : 'dark');
    localStorage.setItem('theme', current === 'dark' ? 'light' : 'dark');
  }

  const saved = localStorage.getItem('theme');
  if (saved) document.documentElement.setAttribute('data-theme', saved);
</script>
</body>
</html>
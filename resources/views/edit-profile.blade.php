<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Profil - VoteQR</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

  <!-- Header -->
<!-- HEADER -->
<header class="header">
  <div class="container header__inner">

    <!-- LOGO -->
    <a href="{{ route('home') }}" class="header__logo">VoteQR</a>

    <div class="header__actions">

      <!-- THEME TOGGLE -->
      <button class="theme-toggle" onclick="toggleTheme()" title="Ganti Tema">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="5"/>
          <path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>
        </svg>
      </button>

      <form method="POST" action="{{ route('logout') }}" style="display:inline;">
        @csrf

        <button type="submit" 
          class="btn btn--danger"
          style="padding: var(--space-2) var(--space-4); font-size: var(--font-size-sm);">
          
          Logout
        </button>
      </form>

    </div>
  </div>
</header>
  <!-- Main Content -->
  <main class="section page">
    <div class="container" style="max-width: 560px;">

      <!-- Back -->
      <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('home') }}" class="btn btn--secondary" style="padding: var(--space-2) var(--space-3);">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg>
          Kembali
        </a>
      </div>

      <h1 class="section-title mb-2">Edit Profil</h1>
      <p class="section-subtitle mb-8">Perbarui informasi dan foto profil Anda</p>

      <!-- Alert -->
      <div id="alert-error" class="alert alert--error" style="display:none; margin-bottom: var(--space-4);"></div>
      <div id="alert-success" class="alert alert--success" style="display:none; margin-bottom: var(--space-4);"></div>

      <!-- Avatar Section -->
      <div class="card mb-6">
        <div class="flex flex-col items-center" style="gap: var(--space-4);">
          <!-- Avatar Preview -->
          <div id="avatar-container" style="position: relative; width: 120px; height: 120px;">
            <div id="avatar-wrapper" style="width: 120px; height: 120px; border-radius: var(--radius-full); overflow: hidden; background: linear-gradient(135deg, var(--primary-500), var(--accent-500)); display: flex; align-items: center; justify-content: center; color: white; border: 3px solid var(--border-color);">
              <svg id="avatar-placeholder" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              <img id="avatar-img" src="" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover; display: none;">
            </div>
            <!-- Loading overlay -->
            <div id="avatar-loading" style="position: absolute; inset: 0; border-radius: var(--radius-full); background: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center;">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="animation: spin 1s linear infinite;"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
            </div>
          </div>

          <!-- Upload Button -->
          <div class="flex items-center gap-3">
            <label for="avatar-file" class="btn btn--primary" style="cursor: pointer; gap: var(--space-2);">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
              <span id="upload-label">Upload Foto</span>
            </label>
            <button id="remove-avatar-btn" class="btn btn--secondary" style="display: none;">Hapus Foto</button>
          </div>
          <input type="file" id="avatar-file" accept="image/*" style="display: none;">

          <p class="text-sm text-muted text-center" style="max-width: 320px;">Format JPG, PNG, atau WEBP. Maksimal 2MB.</p>
        </div>
      </div>

      <!-- Profile Form -->
      <form id="profile-form" class="card">
        <div class="input-group">
          <label for="profile-name">Nama Lengkap</label>
          <input type="text" id="profile-name" class="input" placeholder="Nama Anda" required>
        </div>
        <button type="submit" class="btn btn--primary w-full" id="save-btn">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
          Simpan Perubahan
        </button>
      </form>

      <!-- Change Password Section -->
      <div class="card mt-6">
        <h3 style="font-size: var(--font-size-lg); font-weight: 600; margin-bottom: var(--space-4);">Ganti Password</h3>
        <form id="password-form">
          <div class="input-group">
            <label for="current-password">Password Saat Ini</label>
            <input type="password" id="current-password" class="input" placeholder="********" required>
          </div>
          <div class="input-group">
            <label for="new-password">Password Baru</label>
            <input type="password" id="new-password" class="input" placeholder="Min. 6 karakter" required minlength="6">
          </div>
          <div class="input-group">
            <label for="confirm-new-password">Konfirmasi Password Baru</label>
            <input type="password" id="confirm-new-password" class="input" placeholder="Ulangi password baru" required>
          </div>
          <button type="submit" class="btn btn--secondary w-full" id="change-pw-btn">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            Ganti Password
          </button>
        </form>
      </div>

    </div>
  </main>
  </main>
<style>
    @keyframes spin {
      from { transform: rotate(0deg); }
      to { transform: rotate(360deg); }
    }
  </style>
<script>
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

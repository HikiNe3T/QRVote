<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lupa Password - VoteQR</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

  <div class="auth-container">
    <div class="auth-card animate-slide-up">
      <!-- Logo -->
      <div class="auth-header">
        <div class="auth-header__logo">VoteQR</div>
        <p class="text-secondary text-sm">Reset password akun Anda</p>
      </div>

      <!-- Forgot Password Form -->
      <form class="form">
        <div class="input-group">
          <label for="reset-email">Email</label>
          <input type="email" id="reset-email" class="input" placeholder="nama@email.com" required>
        </div>
        <p class="text-sm text-secondary" style="margin-top: var(--space-2);">
          Kami akan mengirimkan link reset password ke email Anda.
        </p>
        <button type="submit" class="btn btn--primary w-full" style="margin-top: var(--space-4);">
          Kirim Link Reset
        </button>
      </form>

      <!-- Divider -->
      <div class="divider"></div>

      <!-- Back to Login -->
      <a href="{{ route('login') }}" class="btn btn--secondary w-full">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
        Kembali ke Masuk
      </a>
    </div>
  </div>

  <script>
    const saved = localStorage.getItem('theme');
    if (saved) document.documentElement.setAttribute('data-theme', saved);
  </script>
</body>
</html>

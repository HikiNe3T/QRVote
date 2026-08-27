<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VoteQR - Sistem Voting QR Code</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
@include('layouts.header')
  <!-- Main Content -->
  <main class="section page">
    <div class="container">
      <div class="mb-6">
        <h1 class="section-title">Selamat Datang</h1>
        <p class="section-subtitle">Pilih fitur utama untuk memulai</p>
      </div>

      <!-- Bento Grid -->
      <div class="bento-grid stagger-children">

        <!-- Scan QR Event -->
        <a href="{{ route('scan.event') }}" class="bento-card bento-card--large" style="background: linear-gradient(135deg, var(--primary-500), var(--accent-500)); color: white; border: none;">
          <div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: var(--space-4);">
            <div style="width: 48px; height: 48px; border-radius: var(--radius-lg); background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center;">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7V5a2 2 0 0 1 2-2h2"/><path d="M17 3h2a2 2 0 0 1 2 2v2"/><path d="M21 17v2a2 2 0 0 1-2 2h-2"/><path d="M7 21H5a2 2 0 0 1-2-2v-2"/><rect x="7" y="7" width="3" height="3"/><rect x="14" y="7" width="3" height="3"/><rect x="7" y="14" width="3" height="3"/><rect x="14" y="14" width="3" height="3"/></svg>
            </div>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="opacity: 0.7;"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
          </div>
          <h2 style="font-size: var(--font-size-xl); font-weight: 700; margin-bottom: var(--space-2);">Scan QR Event</h2>
          <p style="opacity: 0.85; font-size: var(--font-size-sm);">Masuk ke event dengan memindai kode QR</p>
        </a>

        <!-- Buat Event -->
        <a href="{{ route('create.event') }}" class="bento-card">
          <div style="width: 48px; height: 48px; border-radius: var(--radius-lg); background: linear-gradient(135deg, var(--primary-100), var(--accent-100)); display: flex; align-items: center; justify-content: center; color: var(--primary-600); margin-bottom: var(--space-4);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
          </div>
          <h2 style="font-size: var(--font-size-lg); font-weight: 600; margin-bottom: var(--space-1);">Buat Event</h2>
          <p class="text-sm text-secondary">Kelola event voting Anda</p>
        </a>

        <!-- Result / Riwayat -->
        <a href="{{ route('list.event') }}" class="bento-card">
          <div style="width: 48px; height: 48px; border-radius: var(--radius-lg); background: linear-gradient(135deg, var(--success-100), var(--primary-100)); display: flex; align-items: center; justify-content: center; color: var(--success-600); margin-bottom: var(--space-4);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/></svg>
          </div>
          <h2 style="font-size: var(--font-size-lg); font-weight: 600; margin-bottom: var(--space-1);">Riwayat Voting</h2>
          <p class="text-sm text-secondary">Lihat event yang pernah diikuti</p>
        </a>

        <!-- Panduan -->
        {{-- <div class="bento-card" style="background: linear-gradient(135deg, var(--accent-50), var(--primary-50));">
          <div style="width: 48px; height: 48px; border-radius: var(--radius-lg); background: white; display: flex; align-items: center; justify-content: center; color: var(--accent-600); margin-bottom: var(--space-4); box-shadow: var(--shadow-sm);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><path d="M12 17h.01"/></svg>
          </div>
          <h2 style="font-size: var(--font-size-lg); font-weight: 600; margin-bottom: var(--space-1);">Cara Penggunaan</h2>
          <p class="text-sm text-secondary">Scan QR &rarr; Pilih Kandidat &rarr; Vote</p>
        </div> --}}

        <!-- Statistik (placeholder) -->
        {{-- <div class="bento-card">
          <div style="display: flex; align-items: center; gap: var(--space-3); margin-bottom: var(--space-4);">
            <div style="width: 48px; height: 48px; border-radius: var(--radius-lg); background: linear-gradient(135deg, var(--warning-100), var(--error-100)); display: flex; align-items: center; justify-content: center; color: var(--warning-600);">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div>
              <div style="font-size: var(--font-size-2xl); font-weight: 700; color: var(--text-primary);">1,248</div>
              <div class="text-sm text-muted">Total Voting</div>
            </div>
          </div>
          <div style="display: flex; align-items: center; gap: var(--space-3);">
            <div style="width: 48px; height: 48px; border-radius: var(--radius-lg); background: linear-gradient(135deg, var(--primary-100), var(--accent-100)); display: flex; align-items: center; justify-content: center; color: var(--primary-600);">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <div>
              <div style="font-size: var(--font-size-2xl); font-weight: 700; color: var(--text-primary);">42</div>
              <div class="text-sm text-muted">Event Aktif</div>
            </div>
          </div>
        </div> --}}

      </div>
    </div>
  </main>

  <!-- Floating Navigation -->
  <nav class="floating-nav">
    {{-- <a href="{{ route('home') }}" class="nav-item nav-item--active">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
      <span>Home</span>
    </a> --}}

<a href="{{ route('home') }}" 
    class="nav-item {{ request()->routeIs('home') ? 'nav-item--active' : '' }}">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
    <span>Home</span>
  </a>

    {{-- <a href="{{ route('dashboard') }}" class="nav-item">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
      <span>Dashboard</span>
    </a> --}}

<a href="{{ route('dashboard') }}" 
    class="nav-item {{ request()->routeIs('dashboard') ? 'nav-item--active' : '' }}">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
    <span>Dashboard</span>
  </a>

      {{-- <a href="{{ route('list.event') }}" class="nav-item">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><circle cx="4" cy="6" r="1"/><circle cx="4" cy="12" r="1"/><circle cx="4" cy="18" r="1"/></svg>
      <span>Event</span>
    </a>
  </nav> --}}

  <a href="{{ route('list.event') }}" 
    class="nav-item {{ request()->routeIs('dashboard') ? 'nav-item--active' : '' }}">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><circle cx="4" cy="6" r="1"/><circle cx="4" cy="12" r="1"/><circle cx="4" cy="18" r="1"/></svg>
    <span>Event</span>
  </a>

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

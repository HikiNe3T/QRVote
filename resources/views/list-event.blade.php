<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Riwayat Event - VoteQR</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

  <!-- Header -->
  @include('layouts.header')

  <!-- Main Content -->
  <main class="section page">
    <div class="container">
      <div class="mb-6">
        <h1 class="section-title">Riwayat Event</h1>
        <p class="section-subtitle">Event yang pernah Anda ikuti</p>
      </div>

      <!-- Tabs Filter -->
      <div class="flex gap-2 mb-6" style="background: var(--bg-input); padding: var(--space-1); border-radius: var(--radius-lg);">
        <button type="button" class="auth-tab auth-tab--active filter-btn" data-filter="all" style="flex: 1;">Semua</button>
        <button type="button" class="auth-tab filter-btn" data-filter="Akan Datang" style="flex: 1;">Akan Datang</button>
        <button type="button" class="auth-tab filter-btn" data-filter="Aktif" style="flex: 1;">Aktif</button>
        <button type="button" class="auth-tab filter-btn" data-filter="Selesai" style="flex: 1;">Selesai</button>
      </div>

      <!-- Event List -->
      <div class="stagger-children" id="event-list">

        @forelse($events as $event)
          {{-- Ditambahkan data-status agar bisa difilter oleh JavaScript --}}
          <div class="list-item event-card flex items-center justify-between gap-3" data-status="{{ $event->status_label }}">
            
            <!-- Link untuk Masuk Kembali ke Event -->
            <a href="{{ route('event.show', ['event_id' => $event->id]) }}" class="flex items-center gap-3 flex-1 style-none" style="text-decoration: none; color: inherit;">
              <div class="list-item__icon" style="background: linear-gradient(135deg, var(--primary-100), var(--accent-100));">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M17 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                  <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
              </div>
              <div class="list-item__content">
                <div class="list-item__title">{{ $event->name }}</div>
                <div class="list-item__meta">
                  <span class="badge {{ $event->status_class }}" style="margin-right: var(--space-2);">
                    {{ $event->status_label }}
                  </span>
                  {{ $event->formatted_date }}
                </div>
              </div>
            </a>

            <!-- Tombol Lihat Hasil (Hanya Aktif Jika Event Selesai) -->
            <div>
              @if($event->status_label === 'Selesai')
                <a href="{{ route('event.result.show', ['event_id' => $event->id]) }}" class="btn btn--sm btn--primary flex items-center gap-1">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 20V10"/><path d="M12 20V4"/><path d="M6 20v-4"/></svg>
                  <span>Hasil</span>
                </a>
              @else
                <button class="btn btn--sm btn--ghost" disabled style="opacity: 0.5; cursor: not-allowed;" title="Hasil hanya dapat dilihat setelah event selesai">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 20V10"/><path d="M12 20V4"/><path d="M6 20v-4"/></svg>
                  <span>Hasil</span>
                </button>
              @endif
            </div>
          </div>
        @empty
          <div class="empty-state">
            <div class="empty-state__icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
              </svg>
            </div>
            <div class="empty-state__title">Belum ada riwayat</div>
            <p class="empty-state__text">Ikuti event dengan scan QR untuk melihat riwayat di sini.</p>
          </div>
        @endforelse

        <!-- Empty Filter State (Ditampilkan jika tidak ada event yang sesuai kategori filter) -->
        <div id="filter-empty" class="empty-state hidden" style="display: none;">
          <div class="empty-state__icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
          </div>
          <div class="empty-state__title">Tidak ada event</div>
          <p class="empty-state__text">Tidak ada event pada kategori ini.</p>
        </div>

      </div>
    </div>
  </main>

  <!-- Floating Navigation -->
  <nav class="floating-nav">
    <a href="{{ route('home') }}" class="nav-item {{ request()->routeIs('home') ? 'nav-item--active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
      <span>Home</span>
    </a>

    <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'nav-item--active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
      <span>Dashboard</span>
    </a>

    <a href="{{ route('list.event') }}" class="nav-item {{ request()->routeIs('list.event') ? 'nav-item--active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><circle cx="4" cy="6" r="1"/><circle cx="4" cy="12" r="1"/><circle cx="4" cy="18" r="1"/></svg>
      <span>Event</span>
    </a>
  </nav>

  <!-- JavaScript Filter & Theme -->
  <script>
    function toggleTheme() {
      const html = document.documentElement;
      const current = html.getAttribute('data-theme');
      html.setAttribute('data-theme', current === 'dark' ? 'light' : 'dark');
      localStorage.setItem('theme', current === 'dark' ? 'light' : 'dark');
    }
    const saved = localStorage.getItem('theme');
    if (saved) document.documentElement.setAttribute('data-theme', saved);

    // ===================================
    // LOGIKA FILTER TABS (CLIENT-SIDE)
    // ===================================
    document.addEventListener('DOMContentLoaded', function() {
      const filterBtns = document.querySelectorAll('.filter-btn');
      const eventCards = document.querySelectorAll('.event-card');
      const filterEmpty = document.getElementById('filter-empty');

      filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
          // Highlight tab yang aktif
          filterBtns.forEach(b => b.classList.remove('auth-tab--active'));
          this.classList.add('auth-tab--active');

          const filter = this.getAttribute('data-filter');
          let visibleCount = 0;

          eventCards.forEach(card => {
            const status = card.getAttribute('data-status');

            if (filter === 'all' || status === filter) {
              card.style.display = 'flex';
              visibleCount++;
            } else {
              card.style.display = 'none';
            }
          });

          // Tampilkan empty state khusus jika filter tidak menemukan data
          if (filterEmpty) {
            if (visibleCount === 0 && eventCards.length > 0) {
              filterEmpty.style.display = 'block';
            } else {
              filterEmpty.style.display = 'none';
            }
          }
        });
      });
    });
  </script>
</body>
</html>
<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hasil {{ $event->name }} - VoteQR</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

  <!-- Header -->
  <header class="header">
    <div class="container header__inner">
      <a href="{{ route('home') }}" class="header__logo">VoteQR</a>
      <div class="header__actions">
        <button class="theme-toggle" onclick="toggleTheme()" title="Ganti Tema">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>
        </button>
        @auth
          <img src="{{ Auth::user()->avatar_url ? asset('storage/' . Auth::user()->avatar_url) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->full_name) }}" alt="Avatar" class="avatar">
        @endauth
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <main class="section page">
    <div class="container">
      <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('dashboard') }}" class="btn btn--ghost btn--icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
        </a>
        <div>
          <h1 class="section-title" style="margin-bottom: 0;">Hasil Voting</h1>
          <p class="section-subtitle" style="margin-bottom: 0;">{{ $event->name }}</p>
        </div>
      </div>

      <!-- Winner Card -->
      @if ($winner)
        <div class="card mb-6" style="background: linear-gradient(135deg, var(--primary-500), var(--accent-500)); color: white; border: none;">
          <div class="text-center">
            <div style="width: 64px; height: 64px; border-radius: var(--radius-full); background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-3);">
              <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg>
            </div>
            <h2 style="font-size: var(--font-size-2xl); font-weight: 700; margin-bottom: var(--space-1);">{{ $winner->candidate->name }}</h2>
            <p style="opacity: 0.9; margin-bottom: var(--space-3);">
              Juara 1 &mdash; {{ $winner->votes }} Votes ({{ $winner->percentage }}%)
            </p>
            <div class="flex items-center justify-center gap-2">
              <span class="badge" style="background: rgba(255,255,255,0.2); color: white;">Total Vote: {{ $totalVotes }}</span>
              <span class="badge" style="background: rgba(255,255,255,0.2); color: white;">Peserta: {{ $totalParticipants }}</span>
            </div>
          </div>
        </div>
      @else
        <div class="card mb-6 text-center">
          <h2 style="font-size: var(--font-size-lg); font-weight: 600;">Belum ada suara masuk</h2>
          <p class="text-muted text-sm mt-1">Event ini selesai tanpa ada voting yang tercatat.</p>
        </div>
      @endif

      <!-- Juara Harapan -->
      @if ($juaraHarapanEnabled)
        @if ($juaraHarapan->isNotEmpty())
          <div class="card mb-6">
            <h2 style="font-size: var(--font-size-lg); font-weight: 600; margin-bottom: var(--space-4);">Juara Harapan</h2>
            <div class="flex flex-col gap-3">
              @foreach ($juaraHarapan as $index => $row)
                <div class="flex items-center justify-between p-3" style="background: var(--bg-input); border-radius: var(--radius-lg);">
                  <div class="flex items-center gap-2">
                    <span class="badge badge--success">Harapan {{ $index + 1 }}</span>
                    <span class="font-medium">{{ $row->candidate->number }}. {{ $row->candidate->name }}</span>
                  </div>
                  <span class="text-sm font-semibold">{{ $row->votes }} ({{ $row->percentage }}%)</span>
                </div>
              @endforeach
            </div>
          </div>
        @elseif ($juaraHarapanTied)
          <div class="card mb-6 text-center" style="border: 1px dashed var(--neutral-300);">
            <div style="width: 48px; height: 48px; border-radius: var(--radius-full); background: var(--neutral-100); display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-3);">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--neutral-400);"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </div>
            <h2 style="font-size: var(--font-size-lg); font-weight: 600;">Juara Harapan Tidak Ada</h2>
            <p class="text-muted text-sm mt-1">
              Tidak ada juara harapan karena posisi ke-4 dan seterusnya memiliki perolehan suara yang setara (seri).
            </p>
          </div>
        @else
          <div class="card mb-6 text-center" style="border: 1px dashed var(--neutral-300);">
            <div style="width: 48px; height: 48px; border-radius: var(--radius-full); background: var(--neutral-100); display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-3);">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--neutral-400);"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </div>
            <h2 style="font-size: var(--font-size-lg); font-weight: 600;">Juara Harapan Tidak Ada</h2>
            <p class="text-muted text-sm mt-1">
              Tidak ada kandidat pada posisi harapan yang memperoleh suara.
            </p>
          </div>
        @endif
      @endif

      <!-- Results Chart -->
      <div class="card mb-6">
        <h2 style="font-size: var(--font-size-lg); font-weight: 600; margin-bottom: var(--space-4);">Perolehan Suara</h2>

        @forelse ($results as $row)
          @php
            $bar = match ($row->rank) {
              1 => 'linear-gradient(90deg, var(--primary-500), var(--accent-500))',
              2 => 'linear-gradient(90deg, var(--warning-500), var(--error-500))',
              3 => 'linear-gradient(90deg, var(--success-500), var(--primary-500))',
              default => 'var(--neutral-400)',
            };
            $badge = match ($row->rank) {
              1 => 'badge--primary',
              2 => 'badge--warning',
              3 => 'badge--success',
              default => null,
            };
          @endphp
          <div class="mb-4">
            <div class="flex items-center justify-between mb-2">
              <div class="flex items-center gap-2">
                <span class="font-medium">{{ $row->candidate->number }}. {{ $row->candidate->name }}</span>
                @if ($badge && $row->votes > 0)
                  <span class="badge {{ $badge }}">Juara {{ $row->rank }}</span>
                @elseif ($juaraHarapanEnabled && $row->votes > 0 && $row->rank > 3)
                  @php
                    $harapanIndex = $juaraHarapan->search(fn($h) => $h->rank === $row->rank);
                  @endphp
                  @if ($harapanIndex !== false)
                    <span class="badge badge--success">Harapan {{ $harapanIndex + 1 }}</span>
                  @endif
                @endif
              </div>
              <span class="text-sm font-semibold">{{ $row->votes }} ({{ $row->percentage }}%)</span>
            </div>
            <div style="width: 100%; height: 12px; background: var(--neutral-100); border-radius: var(--radius-full); overflow: hidden;">
              <div style="width: {{ $row->percentage }}%; height: 100%; background: {{ $bar }}; border-radius: var(--radius-full);"></div>
            </div>
          </div>
        @empty
          <p class="text-muted text-sm">Belum ada kandidat pada event ini.</p>
        @endforelse
      </div>

      <!-- Pemenang Kategori -->
      @if ($categoryWinners->isNotEmpty())
        <div class="card mb-6">
          <h2 style="font-size: var(--font-size-lg); font-weight: 600; margin-bottom: var(--space-4);">Pemenang Kategori</h2>
          @foreach ($categoryWinners as $cw)
            <div class="p-4" style="background: var(--bg-input); border-radius: var(--radius-lg);">
              <div class="text-sm text-muted mb-1">Kategori: {{ $cw->category_name }}</div>
              <div class="font-semibold">{{ $cw->candidate_number }}. {{ $cw->candidate_name }}</div>
              <div class="text-sm text-primary mt-1">{{ rtrim(rtrim(number_format((float) $cw->points, 2, ',', '.'), '0'), ',') }} poin</div>
            </div>
          @endforeach
        </div>
      @endif

      <!-- Event Info -->
      <div class="card mb-8">
        <h2 style="font-size: var(--font-size-lg); font-weight: 600; margin-bottom: var(--space-3);">Detail Event</h2>
        <div class="flex flex-col gap-2 text-sm">
          <div class="flex items-center gap-2 text-secondary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <span>
              {{ \Carbon\Carbon::parse($event->start_date)->translatedFormat('d F Y') }}
              @if ($event->start_date !== $event->end_date)
                &ndash; {{ \Carbon\Carbon::parse($event->end_date)->translatedFormat('d F Y') }}
              @endif
            </span>
          </div>
          <div class="flex items-center gap-2 text-secondary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <span>
              {{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($event->end_time)->format('H:i') }} WIB
            </span>
          </div>
          <div class="flex items-center gap-2 text-secondary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            <span>{{ $totalVoted }} dari {{ $totalParticipants }} peserta sudah memilih</span>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Floating Navigation -->
  <nav class="floating-nav">
    <a href="{{ route('home') }}" class="nav-item">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
      <span>Home</span>
    </a>
    <a href="{{ route('dashboard') }}" class="nav-item">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
      <span>Dashboard</span>
    </a>
    <a href="{{ route('list.event') }}" class="nav-item nav-item--active">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><circle cx="4" cy="6" r="1"/><circle cx="4" cy="12" r="1"/><circle cx="4" cy="18" r="1"/></svg>
      <span>Event</span>
    </a>
  </nav>

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
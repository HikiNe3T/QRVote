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
  @include('layouts.header')

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

      <!-- Top 3 Winners Cards (Juara 1, 2, & 3) -->
      @php
        $top3 = $results->where('rank', '<=', 3)->where('votes', '>', 0);
      @endphp

      @if ($top3->isNotEmpty())
        <div class="mb-6">
          <h2 style="font-size: var(--font-size-lg); font-weight: 600; margin-bottom: var(--space-3);">Podium Pemenang Utama</h2>
          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: var(--space-4);">
            @foreach ($top3 as $winnerItem)
              @php
                $bgGradient = match ($winnerItem->rank) {
                  1 => 'linear-gradient(135deg, #f59e0b, #d97706)',
                  2 => 'linear-gradient(135deg, #64748b, #475569)',
                  3 => 'linear-gradient(135deg, #b45309, #78350f)',
                  default => 'var(--primary-500)',
                };
                $trophyColor = match ($winnerItem->rank) {
                  1 => '#fbbf24',
                  2 => '#e2e8f0',
                  3 => '#fde68a',
                  default => '#ffffff',
                };
              @endphp
              <div class="card" style="background: {{ $bgGradient }}; color: white; border: none; position: relative; overflow: hidden;">
                <div class="text-center">
                  <div style="width: 56px; height: 56px; border-radius: var(--radius-full); background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-3);">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="{{ $trophyColor }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg>
                  </div>
                  <span class="badge mb-2" style="background: rgba(255,255,255,0.25); color: white; font-weight: 700;">
                    JUARA {{ $winnerItem->rank }}
                  </span>
                  <h3 style="font-size: var(--font-size-xl); font-weight: 700; margin-bottom: var(--space-1);">
                    {{ $winnerItem->candidate->number }}. {{ $winnerItem->candidate->name }}
                  </h3>
                  <p style="opacity: 0.95; font-size: var(--font-size-md); font-weight: 600; margin-bottom: var(--space-2);">
                    {{ $winnerItem->votes }} Votes ({{ $winnerItem->percentage }}%)
                  </p>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      @else
        <div class="card mb-6 text-center">
          <h2 style="font-size: var(--font-size-lg); font-weight: 600;">Belum ada suara masuk</h2>
          <p class="text-muted text-sm mt-1">Event ini selesai tanpa ada voting yang tercatat.</p>
        </div>
      @endif

      <!-- Penghargaan Tambahan & Special Award (Centered) -->
      @if (($juaraHarapanEnabled && $juaraHarapan->isNotEmpty()) || $categoryWinners->isNotEmpty())
        <div class="card mb-6" style="background: linear-gradient(135deg, var(--primary-600), var(--accent-600)); color: white; border: none;">
          <h2 style="font-size: var(--font-size-xl); font-weight: 700; margin-bottom: var(--space-4); text-align: center;">
            Penghargaan Tambahan & Special Award
          </h2>

          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: var(--space-4);">
            
            <!-- Section Juara Harapan -->
            @if ($juaraHarapanEnabled && $juaraHarapan->isNotEmpty())
              <div class="text-center">
                <h3 style="font-size: var(--font-size-md); font-weight: 600; opacity: 0.9; margin-bottom: var(--space-3); border-bottom: 1px solid rgba(255,255,255,0.2); padding-bottom: 6px;">
                  Juara Harapan
                </h3>
                <div class="flex flex-col gap-2">
                  @foreach ($juaraHarapan as $index => $row)
                    <div class="flex flex-col items-center justify-center p-3 gap-1" style="background: rgba(255, 255, 255, 0.15); border-radius: var(--radius-md); backdrop-filter: blur(4px);">
                      <span class="badge" style="background: rgba(255,255,255,0.25); color: white;">Harapan {{ $index + 1 }}</span>
                      <span class="font-medium" style="font-size: var(--font-size-md);">{{ $row->candidate->number }}. {{ $row->candidate->name }}</span>
                      <span class="text-sm font-semibold" style="opacity: 0.9;">{{ $row->votes }} Votes ({{ $row->percentage }}%)</span>
                    </div>
                  @endforeach
                </div>
              </div>
            @endif

            <!-- Section Best Kategori -->
            @if ($categoryWinners->isNotEmpty())
              <div class="text-center">
                <h3 style="font-size: var(--font-size-md); font-weight: 600; opacity: 0.9; margin-bottom: var(--space-3); border-bottom: 1px solid rgba(255,255,255,0.2); padding-bottom: 6px;">
                  Best Kategori
                </h3>
                <div class="flex flex-col gap-2">
                  @foreach ($categoryWinners as $cw)
                    <div class="flex flex-col items-center justify-center p-3 gap-1" style="background: rgba(255, 255, 255, 0.15); border-radius: var(--radius-md); backdrop-filter: blur(4px);">
                      <div class="text-xs" style="opacity: 0.85; font-weight: 500;">Kategori: {{ $cw->category_name }}</div>
                      <div class="font-semibold text-md">{{ $cw->candidate_number }}. {{ $cw->candidate_name }}</div>
                      <span class="badge mt-1" style="background: rgba(255,255,255,0.25); color: white;">
                        {{ rtrim(rtrim(number_format((float) $cw->points, 2, ',', '.'), '0'), ',') }} Poin
                      </span>
                    </div>
                  @endforeach
                </div>
              </div>
            @endif

          </div>
        </div>
      @endif

      <!-- Results Chart -->
      <div class="card mb-6">
        <h2 style="font-size: var(--font-size-lg); font-weight: 600; margin-bottom: var(--space-4);">Perolehan Suara Lengkap</h2>

        @forelse ($results as $row)
          @php
            $bar = match ($row->rank) {
              1 => 'linear-gradient(90deg, #f59e0b, #d97706)',
              2 => 'linear-gradient(90deg, #64748b, #475569)',
              3 => 'linear-gradient(90deg, #b45309, #78350f)',
              default => 'var(--neutral-400)',
            };
            $badge = match ($row->rank) {
              1 => 'badge--warning',
              2 => 'badge--secondary',
              3 => 'badge--primary',
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
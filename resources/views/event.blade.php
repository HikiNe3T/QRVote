<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $event->name }} - VoteQR</title>
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
      <!-- Event Banner -->
      <div class="mb-6" style="border-radius: var(--radius-2xl); overflow: hidden; position: relative;">
        <div id="event-banner-image" style="width: 100%; height: 200px; background: linear-gradient(135deg, var(--primary-500), var(--accent-500)); display: flex; align-items: center; justify-content: center; color: white; position: relative;">
          @if($event->image_url)
          <img src="{{ asset('storage/events/' . $event->image_url) }}" alt="{{ $event->name }}" style="width:100%;height:100%;object-fit:cover;position:absolute;inset:0;">
          <div style="position:absolute;inset:0;background:linear-gradient(135deg, rgba(59,130,246,0.6), rgba(245,158,11,0.4));"></div>
          @endif
          <div class="text-center" style="position: relative; z-index: 1; padding: var(--space-4);">
            <h1 style="font-size: var(--font-size-2xl); font-weight: 700; text-shadow: 0 2px 8px rgba(0,0,0,0.3);">{{ $event->name }}</h1>
            @if($event->description)
            <p style="opacity: 0.95; margin-top: var(--space-1); text-shadow: 0 2px 8px rgba(0,0,0,0.3);">{{ $event->description }}</p>
            @endif
          </div>
        </div>
        <div style="position: absolute; bottom: var(--space-4); right: var(--space-4); z-index: 2;">
          @if($status === 'active')
          <span class="badge badge--success" style="background: rgba(255,255,255,0.9); color: var(--success-600);">{{ $statusLabel }}</span>
          @elseif($status === 'upcoming')
          <span class="badge badge--warning" style="background: rgba(255,255,255,0.9); color: var(--warning-600);">{{ $statusLabel }}</span>
          @else
          <span class="badge badge--error" style="background: rgba(255,255,255,0.9); color: var(--error-600);">{{ $statusLabel }}</span>
          @endif
        </div>
      </div>

      <!-- Event Info -->
      <div class="card mb-6">
        <h2 style="font-size: var(--font-size-lg); font-weight: 600; margin-bottom: var(--space-3);">Tentang Event</h2>
        <p class="text-secondary" style="margin-bottom: var(--space-4);">
          {{ $event->description ?: 'Tidak ada deskripsi event.' }}
        </p>
        <div class="flex gap-4 text-sm text-muted" style="flex-wrap: wrap;">
          <div class="flex items-center gap-2">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <span>{{ $dateRange }}</span>
          </div>
          <div class="flex items-center gap-2">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <span>{{ $timeRange }}</span>
          </div>
          <div class="flex items-center gap-2">
            @if($event->voting_type === 'public')
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span>Public</span>
            @else
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            <span>Private</span>
            @endif
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="mb-6" style="display: flex; flex-direction: column; gap: var(--space-4);">
        @if($status === 'active')
        <a href="{{ route('scan.candidate') }}?event_id={{ $event->id }}" class="bento-card" style="text-align: center; border: none; background: linear-gradient(135deg, var(--primary-500), var(--accent-500)); color: white;">
          <div style="width: 48px; height: 48px; border-radius: var(--radius-lg); background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-3);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7V5a2 2 0 0 1 2-2h2"/><path d="M17 3h2a2 2 0 0 1 2 2v2"/><path d="M21 17v2a2 2 0 0 1-2 2h-2"/><path d="M7 21H5a2 2 0 0 1-2-2v-2"/><rect x="7" y="7" width="3" height="3"/><rect x="14" y="7" width="3" height="3"/><rect x="7" y="14" width="3" height="3"/><rect x="14" y="14" width="3" height="3"/></svg>
          </div>
          <h3 style="font-size: var(--font-size-lg); font-weight: 600; margin-bottom: var(--space-1);">Scan Kandidat</h3>
          <p style="opacity: 0.85; font-size: var(--font-size-sm);">Pindai QR kandidat untuk vote</p>
        </a>
        @else
        <button type="button" class="bento-card" style="text-align: center; border: none; background: linear-gradient(135deg, var(--neutral-400), var(--neutral-500)); color: white; cursor: pointer; opacity: 0.85;" onclick="openScanBlockModal()">
          <div style="width: 48px; height: 48px; border-radius: var(--radius-lg); background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-3);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7V5a2 2 0 0 1 2-2h2"/><path d="M17 3h2a2 2 0 0 1 2 2v2"/><path d="M21 17v2a2 2 0 0 1-2 2h-2"/><path d="M7 21H5a2 2 0 0 1-2-2v-2"/><rect x="7" y="7" width="3" height="3"/><rect x="14" y="7" width="3" height="3"/><rect x="7" y="14" width="3" height="3"/><rect x="14" y="14" width="3" height="3"/></svg>
          </div>
          <h3 style="font-size: var(--font-size-lg); font-weight: 600; margin-bottom: var(--space-1);">Scan Kandidat</h3>
          <p style="opacity: 0.85; font-size: var(--font-size-sm);">Pindai QR kandidat untuk vote</p>
        </button>
        @endif
      </div>

      <!-- Candidates List -->
      <div id="candidates" class="mb-8">
        <h2 style="font-size: var(--font-size-xl); font-weight: 600; margin-bottom: var(--space-4);">Daftar Kandidat</h2>

        @if($candidates->isEmpty())
        <div class="card" style="text-align: center; padding: var(--space-8);">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto var(--space-3); opacity: 0.3;"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          <p class="text-muted">Belum ada kandidat pada event ini.</p>
        </div>
        @else
        <div class="grid-3 stagger-children">
          @foreach($candidates as $candidate)
          @php
            $voteCount = $voteCounts[$candidate->id] ?? 0;
            $gradients = [
              'linear-gradient(135deg, #667eea, #764ba2)',
              'linear-gradient(135deg, #f093fb, #f5576c)',
              'linear-gradient(135deg, #4facfe, #00f2fe)',
              'linear-gradient(135deg, #43e97b, #38f9d7)',
              'linear-gradient(135deg, #fa709a, #fee140)',
              'linear-gradient(135deg, #30cfd0, #330867)',
            ];
            $gradient = $gradients[($candidate->number - 1) % count($gradients)];
          @endphp
          <div class="candidate-card">
            <div class="candidate-card__image" style="background: {{ $gradient }}; display: flex; align-items: center; justify-content: center; color: white; position: relative; overflow: hidden;">
              @if($candidate->photo_url)
              <img src="{{ asset('storage/candidates/' . $candidate->photo_url) }}" alt="{{ $candidate->name }}" style="width:100%;height:100%;object-fit:cover;position:absolute;inset:0;">
              @else
              <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="opacity: 0.5;"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              @endif
              <span style="position: absolute; top: var(--space-2); left: var(--space-2); background: rgba(0,0,0,0.5); color: white; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px;">{{ $candidate->number }}</span>
            </div>
            <div class="candidate-card__content">
              <div class="candidate-card__name">{{ $candidate->name }}</div>
              @if($candidate->class_or_meta)
              <p class="text-sm text-muted mt-2">{{ $candidate->class_or_meta }}</p>
              @endif
              @if($candidate->biodata)
              <p class="text-sm text-muted mt-2" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $candidate->biodata }}</p>
              @endif
              <div class="flex items-center justify-center gap-2 mt-3">
                {{-- <span class="badge badge--primary">{{ $voteCount }} Votes</span> --}}
              </div>
            </div>
          </div>
          @endforeach
        </div>
        @endif
      </div>
    </div>
  </main>

  <!-- Floating Navigation -->
  <nav class="floating-nav">
    <a href="{{ route('home') }}" class="nav-item nav-item--active">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
      <span>Home</span>
    </a>
    <a href="{{ route('dashboard') }}" class="nav-item">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
      <span>Dashboard</span>
    </a>
    <a href="{{ route('list.event') }}" class="nav-item">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><circle cx="4" cy="6" r="1"/><circle cx="4" cy="12" r="1"/><circle cx="4" cy="18" r="1"/></svg>
      <span>Event</span>
    </a>
  </nav>

  <!-- Modal Notifikasi: Scan Kandidat diblokir -->
  <div id="scan-block-modal" style="display:none; position:fixed; inset:0; z-index:9999; align-items:center; justify-content:center; padding:var(--space-4); background:rgba(0,0,0,0.6); backdrop-filter:blur(4px);">
    <div style="background:#ffffff; border-radius:var(--radius-2xl); max-width:400px; width:100%; padding:var(--space-6); text-align:center; box-shadow:0 20px 60px rgba(0,0,0,0.3); animation:modalIn 0.25s ease;">
      
      <!-- Icon Container -->
      <div style="width:64px; height:64px; border-radius:50%; margin:0 auto var(--space-4); display:flex; align-items:center; justify-content:center;
        @if($status === 'upcoming') background:#fef3c7; color:#d97706; @else background:#fee2e2; color:#dc2626; @endif">
        @if($status === 'upcoming')
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        @else
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
        @endif
      </div>

      <!-- Judul Modal -->
      <h3 style="font-size:var(--font-size-lg); font-weight:700; margin-bottom:var(--space-2); color:#111827;">
        @if($status === 'upcoming') Event Belum Dimulai @else Event Sudah Selesai @endif
      </h3>

      <!-- Deskripsi Modal -->
      <p style="color:#4b5563; font-size:var(--font-size-sm); margin-bottom:var(--space-5); line-height:1.5;">
        @if($status === 'upcoming')
          Maaf, event ini belum dimulai. Anda hanya dapat memindai QR kandidat ketika event sedang berlangsung.
        @else
          Maaf, event ini sudah selesai. Anda tidak dapat memindai QR kandidat lagi.
        @endif
      </p>

      <!-- Box Informasi Waktu & Tanggal -->
      <div style="background:#f3f4f6; border-radius:var(--radius-lg); padding:var(--space-3); margin-bottom:var(--space-5); font-size:var(--font-size-sm); color:#374151; font-weight:500;">
        <div style="display:flex; align-items:center; justify-content:center; gap:var(--space-2); margin-bottom:var(--space-1);">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#4b5563" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
          <span style="color:#374151;">{{ $dateRange }}</span>
        </div>
        <div style="display:flex; align-items:center; justify-content:center; gap:var(--space-2);">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#4b5563" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          <span style="color:#374151;">{{ $timeRange }}</span>
        </div>
      </div>

      <!-- Tombol Mengerti -->
      <button type="button" onclick="closeScanBlockModal()" style="width:100%; padding:var(--space-3) var(--space-4); border-radius:var(--radius-lg); border:none; font-weight:600; font-size:var(--font-size-sm); cursor:pointer; background:#2563eb; color:#ffffff; transition:opacity 0.2s;" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
        Mengerti
      </button>

    </div>
  </div>

  <style>
    @keyframes modalIn {
      from { opacity: 0; transform: translateY(12px) scale(0.96); }
      to   { opacity: 1; transform: translateY(0) scale(1); }
    }
  </style>

  <script>
    function openScanBlockModal() {
      var modal = document.getElementById('scan-block-modal');
      modal.style.display = 'flex';
      document.body.style.overflow = 'hidden';
    }

    function closeScanBlockModal() {
      var modal = document.getElementById('scan-block-modal');
      modal.style.display = 'none';
      document.body.style.overflow = '';
    }

    document.getElementById('scan-block-modal').addEventListener('click', function(e) {
      if (e.target === this) closeScanBlockModal();
    });

    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') closeScanBlockModal();
    });

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
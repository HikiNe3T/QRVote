<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Penilaian Kandidat - VoteQR</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
{{-- <style>
    .rating-stars { 
      display: flex; 
      gap: var(--space-2); 
      margin-top: var(--space-2);
    }
    .rating-star {
      width: 32px; 
      height: 32px; 
      cursor: pointer;
      color: var(--neutral-300); 
      fill: transparent;
      transition: transform .15s, color .15s, fill .15s;
    }
    .rating-star:hover { 
      transform: scale(1.15); 
    }
    .rating-star.is-active { 
      color: var(--warning-500); 
      fill: var(--warning-500); 
    }
    .rating-category {
      background-color: var(--surface-1, #ffffff); 
      border: 1px solid var(--neutral-200);
      border-radius: var(--radius-lg); 
      padding: var(--space-4); 
      margin-bottom: var(--space-4);
      display: block;
      clear: both;
    }
    .rating-category__header { 
      display: flex; 
      justify-content: space-between; 
      align-items: center; 
      margin-bottom: var(--space-3); 
    }
    .rating-category__name { 
      font-weight: 600; 
      color: var(--text-primary, #1a1a1a);
    }
    .rating-category__weight { 
      font-size: var(--font-size-xs); 
      color: var(--text-muted, #888); 
    }
    .progress-bar { 
      height: 8px; 
      background: var(--neutral-200); 
      border-radius: 999px; 
      overflow: hidden; 
    }
    .progress-bar__fill { 
      height: 100%; 
      background: var(--primary-500); 
      transition: width .3s; 
    }
    .rate-flash {
      padding: var(--space-3) var(--space-4); 
      border-radius: var(--radius-md);
      font-size: var(--font-size-sm); 
      margin-bottom: var(--space-4); 
      display: none;
    }
    .rate-flash--error { 
      background: rgba(239,68,68,.12); 
      color: var(--error-600); 
      display: block; 
    }
    .rate-flash--success { 
      background: rgba(34,197,94,.12); 
      color: var(--success-600); 
      display: block; 
    }
  </style> --}}
</head>
<body>

@include('layouts.header')

<main class="section page">
  <div class="container" style="max-width: 720px;">

    <div class="flex items-center gap-3 mb-6">
      <a href="{{ route('event.show') }}?event_id={{ $event->id }}" class="btn btn--ghost btn--icon">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
      </a>
      <div>
        <h1 class="section-title" style="margin-bottom:0;">{{ $event->name }}</h1>
        <p class="section-subtitle" style="margin-bottom:0;">
          {{ $categories->isEmpty() ? 'Konfirmasi pilihan Anda' : 'Beri penilaian per kategori' }}
        </p>
      </div>
    </div>

    <div id="flash" class="rate-flash"></div>

    <!-- Kandidat -->
    <div class="card mb-6">
      <div class="flex items-center gap-4">
        <div style="width:80px;height:80px;border-radius:var(--radius-xl);background:linear-gradient(135deg,var(--primary-500),var(--accent-500));display:flex;align-items:center;justify-content:center;color:white;flex-shrink:0;overflow:hidden;">
          @if($candidate->photo_url)
            <img src="{{ asset('storage/candidates/' . $candidate->photo_url) }}" alt="{{ $candidate->name }}" style="width:100%;height:100%;object-fit:cover;">
          @else
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          @endif
        </div>
        <div class="flex-1">
          <span class="badge badge--primary">No. {{ $candidate->number }}</span>
          <h2 style="font-size:var(--font-size-xl);font-weight:600;">{{ $candidate->name }}</h2>
          @if($candidate->biodata)
          <p class="text-sm text-secondary mt-2">{{ $candidate->biodata }}</p>
          @endif
        </div>
      </div>
    </div>

    @if($categories->isEmpty())
      {{-- MODE VOTE SAJA (admin tidak mengaktifkan kategori) --}}
      <div class="card mb-6" style="text-align:center;">
        <p class="text-secondary mb-4">
          Event ini tidak menggunakan penilaian kategori. Suara Anda akan dihitung
          sebagai 1 vote untuk kandidat di atas.
        </p>
        <p class="text-sm text-muted mb-4">Anda hanya dapat memilih 1 kandidat pada event ini.</p>
      </div>
    @else
      {{-- MODE PENILAIAN KATEGORI --}}
      <div class="card mb-6">
        <div class="flex items-center justify-between mb-3">
          <span class="text-sm font-medium">Progress Penilaian</span>
          <span class="text-sm font-semibold" id="progress-text">0 / {{ $categories->count() }} kategori</span>
        </div>
        <div class="progress-bar"><div class="progress-bar__fill" id="progress-fill" style="width:0%"></div></div>
      </div>

      @foreach($categories as $cat)
      <div class="rating-category" data-category-id="{{ $cat->id }}">
        <div class="rating-category__header">
          <span class="rating-category__name">{{ $cat->name }}</span>
          <span class="rating-category__weight">Bobot {{ rtrim(rtrim(number_format($cat->weight, 2, ',', '.'), '0'), ',') }}%</span>
        </div>
        <div class="rating-stars">
          @for($i = 1; $i <= 5; $i++)
          <svg class="rating-star" data-value="{{ $i }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
          </svg>
          @endfor
        </div>
      </div>
      @endforeach
    @endif

    <button id="submit-btn" class="btn btn--primary" style="width:100%;padding:var(--space-4);">
      {{ $categories->isEmpty() ? 'Kirim Suara' : 'Kirim Penilaian' }}
    </button>

  </div>
</main>

<script>
  const saved = localStorage.getItem('theme');
  if (saved) document.documentElement.setAttribute('data-theme', saved);

  const totalCategories = {{ $categories->count() }};
  const scores = {};

  document.querySelectorAll('.rating-category').forEach(box => {
    const catId = box.dataset.categoryId;
    box.querySelectorAll('.rating-star').forEach(star => {
      star.addEventListener('click', () => {
        const value = parseInt(star.dataset.value, 10);
        scores[catId] = value;
        box.querySelectorAll('.rating-star').forEach(s => {
          s.classList.toggle('is-active', parseInt(s.dataset.value, 10) <= value);
        });
        updateProgress();
      });
    });
  });

  function updateProgress() {
    const done = Object.keys(scores).length;
    const pct  = totalCategories ? Math.round(done / totalCategories * 100) : 100;
    const t = document.getElementById('progress-text');
    const f = document.getElementById('progress-fill');
    if (t) t.textContent = done + ' / ' + totalCategories + ' kategori';
    if (f) f.style.width = pct + '%';
  }

  function flash(type, msg) {
    const el = document.getElementById('flash');
    el.className = 'rate-flash rate-flash--' + type;
    el.textContent = msg;
    el.scrollIntoView({ behavior: 'smooth', block: 'center' });
  }

  document.getElementById('submit-btn').addEventListener('click', async function () {
    if (totalCategories > 0 && Object.keys(scores).length < totalCategories) {
      flash('error', 'Mohon nilai semua kategori terlebih dahulu.');
      return;
    }

    this.disabled = true;
    this.textContent = 'Menyimpan...';

    try {
      const res = await fetch('{{ route('rate.candidate.submit') }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
          'Accept': 'application/json',
        },
        body: JSON.stringify({
          event_id: '{{ $event->id }}',
          candidate_id: '{{ $candidate->id }}',
          scores: scores,
        }),
      });

      const data = await res.json();

      if (data.success) {
        flash('success', data.message);
        setTimeout(() => window.location.href = data.redirect, 1200);
      } else {
        flash('error', data.message || 'Gagal menyimpan suara.');
        this.disabled = false;
        this.textContent = totalCategories ? 'Kirim Penilaian' : 'Kirim Suara';
      }
    } catch (e) {
      flash('error', 'Terjadi kesalahan jaringan. Coba lagi.');
      this.disabled = false;
      this.textContent = totalCategories ? 'Kirim Penilaian' : 'Kirim Suara';
    }
  });
</script>
</body>
</html>

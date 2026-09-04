<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Hasil TOPSIS - {{ $event->name }} - VoteQR</title>
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
        <a href="{{ route('event.result.show', ['event_id' => $event->id]) }}" class="btn btn--ghost btn--icon">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
        </a>
        <div>
        <h1 class="section-title" style="margin-bottom: 0;">Hasil TOPSIS</h1>
        <p class="section-subtitle" style="margin-bottom: 0;">{{ $event->name }}</p>
        </div>
    </div>

    <!-- Meta info -->
    <div class="card mb-6">
        <div class="flex flex-col gap-2 text-sm">
        <div class="flex items-center gap-2 text-secondary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <span>{{ $dateRange }}</span>
        </div>
        <div class="flex items-center gap-2 text-secondary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M7 16V8M12 16v-5M17 16v-3"/></svg>
            <span>{{ $totalVotes }} suara masuk</span>
        </div>
        <div class="flex items-center gap-2 text-secondary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
            <span>Bobot suara: {{ $voteWeight }}%</span>
        </div>
        </div>
    </div>

    @if (count($rankings) === 0)
        <div class="card mb-6 text-center">
        <h2 style="font-size: var(--font-size-lg); font-weight: 600;">Belum ada data</h2>
        <p class="text-muted text-sm mt-2">Belum ada kandidat atau suara pada event ini.</p>
        </div>
    @else

        <!-- Info banner -->
        <div class="topsis-info-banner">
        <strong>Metode TOPSIS</strong> menggabungkan <strong>jumlah suara</strong> dan <strong>rata-rata nilai per kategori</strong>
        menjadi satu skor kedekatan (closeness coefficient). Skor tertinggi = pemenang.
        </div>

        <!-- Podium -->
        @if (count($rankings) >= 1)
        <div class="topsis-podium">
        @php
            $top3 = array_slice($rankings, 0, 3);
        @endphp
        @foreach ($top3 as $idx => $r)
            @php $pos = $idx + 1; @endphp
            <div class="topsis-podium-item topsis-podium-{{ $pos }}">
            <div class="topsis-pos">{{ $pos }}</div>
            <div class="topsis-pname">{{ $r['candidate']['name'] }}</div>
            <div class="topsis-pscore">C: {{ number_format($r['closeness'], 4) }}</div>
            </div>
        @endforeach
        </div>
        @endif

        <!-- Winner -->
        @if ($winner)
        <div class="topsis-winner-card">
        <div class="topsis-crown">&#127942;</div>
        <div class="topsis-winner-label">Juara 1 (TOPSIS)</div>
        <div class="topsis-winner-name">{{ $winner['candidate']['name'] }}</div>
        @if ($winner['candidate']['class_or_meta'])
        <div class="topsis-winner-info">{{ $winner['candidate']['class_or_meta'] }}</div>
        @endif
        <div class="topsis-winner-score">Closeness: {{ number_format($winner['closeness'], 4) }}</div>
        </div>
        @endif

        <!-- Step 1: Criteria & Weights -->
        <div class="topsis-step">
        <h2 class="topsis-step-title"><span class="topsis-num">1</span> Kriteria & Bobot</h2>
        <div style="overflow-x: auto;">
        <table class="topsis-table">
            <thead>
            <tr>
                <th style="width:40px">#</th>
                <th class="topsis-left">Kriteria</th>
                <th>Tipe</th>
                <th>Bobot Awal</th>
                <th>Bobot Normal</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($criteria as $i => $c)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td class="topsis-left">{{ $c['label'] }}</td>
                <td>
                @if ($c['benefit'])
                    <span class="topsis-weight-tag" style="background: var(--success-100); color: var(--success-600);">Benefit</span>
                @else
                    <span class="topsis-weight-tag" style="background: var(--error-100); color: var(--error-600);">Cost</span>
                @endif
                </td>
                <td>
                @if ($c['key'] === 'votes')
                    {{ $voteWeight }}%
                @else
                    {{ number_format($c['weight'] * (array_sum(array_column($criteria, 'weight'))), 1) }}%
                @endif
                </td>
                <td>{{ number_format($c['weight'], 4) }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
        </div>
        </div>

        <!-- Step 2: Decision Matrix -->
        <div class="topsis-step">
        <h2 class="topsis-step-title"><span class="topsis-num">2</span> Matriks Keputusan</h2>
        <div style="overflow-x: auto;">
        <table class="topsis-table">
            <thead>
            <tr>
                <th class="topsis-left">Kandidat</th>
                @foreach ($criteria as $c)
                <th>{{ $c['label'] }}</th>
                @endforeach
            </tr>
            </thead>
            <tbody>
            @foreach ($rankings as $r)
            <tr>
                <td class="topsis-left">
                <strong>No. {{ $r['candidate']['number'] }}</strong><br>
                {{ $r['candidate']['name'] }}
                </td>
                @foreach ($r['values'] as $val)
                <td>{{ number_format($val, 2) }}</td>
                @endforeach
            </tr>
            @endforeach
            </tbody>
        </table>
        </div>
        </div>

        <!-- Step 3: Normalized + Weighted Matrix -->
        <div class="topsis-step">
        <h2 class="topsis-step-title"><span class="topsis-num">3</span> Matriks Normalisasi Terbobot</h2>
        <div style="overflow-x: auto;">
        <table class="topsis-table">
            <thead>
            <tr>
                <th class="topsis-left">Kandidat</th>
                @foreach ($criteria as $c)
                <th>{{ $c['label'] }}</th>
                @endforeach
            </tr>
            </thead>
            <tbody>
            @foreach ($rankings as $r)
            <tr>
                <td class="topsis-left">
                <strong>No. {{ $r['candidate']['number'] }}</strong><br>
                {{ $r['candidate']['name'] }}
                </td>
                @foreach ($r['weighted'] as $val)
                <td>{{ number_format($val, 4) }}</td>
                @endforeach
            </tr>
            @endforeach
            <tr class="topsis-ideal-row" style="background: var(--primary-50);">
                <td class="topsis-left"><span class="topsis-ideal-plus">A+ (Ideal Positif)</span></td>
                @foreach ($idealPlus as $val)
                <td class="topsis-ideal-plus">{{ number_format($val, 4) }}</td>
                @endforeach
            </tr>
            <tr class="topsis-ideal-row" style="background: var(--error-50);">
                <td class="topsis-left"><span class="topsis-ideal-minus">A- (Ideal Negatif)</span></td>
                @foreach ($idealMinus as $val)
                <td class="topsis-ideal-minus">{{ number_format($val, 4) }}</td>
                @endforeach
            </tr>
            </tbody>
        </table>
        </div>
        </div>

        <!-- Step 4: Final Ranking -->
        <div class="topsis-step">
        <h2 class="topsis-step-title"><span class="topsis-num">4</span> Perhitungan Jarak & Peringkat Akhir</h2>
        <div style="overflow-x: auto;">
        <table class="topsis-table">
            <thead>
            <tr>
                <th style="width:50px">Rank</th>
                <th class="topsis-left">Kandidat</th>
                <th>D+ (jarak ke ideal +)</th>
                <th>D- (jarak ke ideal -)</th>
                <th>Closeness (C)</th>
                <th style="min-width:140px">Visualisasi</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($rankings as $r)
            <tr>
                <td>
                <span class="topsis-rank-badge topsis-rank-{{ $r['rank'] <= 3 ? $r['rank'] : 'other' }}">
                    {{ $r['rank'] }}
                </span>
                </td>
                <td class="topsis-left">
                <strong>No. {{ $r['candidate']['number'] }} — {{ $r['candidate']['name'] }}</strong>
                </td>
                <td>{{ number_format($r['dPlus'], 4) }}</td>
                <td>{{ number_format($r['dMinus'], 4) }}</td>
                <td><strong>{{ number_format($r['closeness'], 4) }}</strong></td>
                <td>
                <div class="topsis-close-bar-wrap">
                    <div class="topsis-close-bar" style="width: {{ round($r['closeness'] * 100) }}%"></div>
                    <div class="topsis-close-bar-label">{{ round($r['closeness'] * 100, 1) }}%</div>
                </div>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        </div>
        </div>

    @endif

    <!-- Back button -->
    <div class="text-center mb-8" style="margin-top: var(--space-8);">
        <a href="{{ route('event.result.show', ['event_id' => $event->id]) }}" class="btn btn--secondary">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
        Kembali ke Hasil Voting
        </a>
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
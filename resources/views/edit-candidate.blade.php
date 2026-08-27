<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Kandidat - VoteQR</title>
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
        <a href="{{ route('admin.event', $candidate->event_id) }}" class="btn btn--ghost btn--icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
        </a>
        <div>
          <h1 class="section-title" style="margin-bottom: 0;">Edit Kandidat</h1>
          <p class="section-subtitle" style="margin-bottom: 0;">
            {{ $candidate->event->name ?? '' }} — Nomor {{ $candidate->number }}
          </p>
        </div>
      </div>

      @if($errors->any())
      <div class="alert alert--error mb-6" style="background: #fee2e2; color: #991b1b; padding: var(--space-3) var(--space-4); border-radius: var(--radius-lg); border: 1px solid #dc2626; font-weight: 500;">
        @foreach($errors->all() as $error)
          <div>{{ $error }}</div>
        @endforeach
      </div>
      @endif

      <form action="{{ route('candidate.update', $candidate->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="grid-2" style="align-items: start;">

          <!-- Form Section -->
          <div class="form-section">
            <div class="form-section__title">
              <div class="form-section__title-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
              </div>
              Data Kandidat
            </div>

            <!-- Photo Upload -->
            <div class="flex items-center justify-between mb-4">
              <span class="text-sm font-medium text-secondary">Foto Kandidat</span>
            </div>
            <div class="mb-6">
              <label class="photo-upload" for="candidate-photo-input">
                <svg class="photo-upload__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                <span class="photo-upload__text">Ganti Foto</span>
                <input type="file" name="photo" id="candidate-photo-input" accept="image/*" style="display: none;" onchange="previewPhoto(this)">
              </label>
              @if($candidate->photo_url)
              <div class="mt-3 text-xs text-muted">
                Foto lama: {{ $candidate->photo_url }} (kosongkan jika tidak ganti)
              </div>
              @endif
            </div>

            <!-- Nomor Peserta -->
            <div class="input-group mb-4">
              <label for="candidate-number">Nomor Peserta</label>
              <input type="number" id="candidate-number" value="{{ $candidate->number }}" class="input" readonly>
              <span class="text-xs text-muted">Nomor urut otomatis, bisa diedit manual</span>
            </div>

            <!-- Nama (Wajib) -->
            <div class="input-group mb-4">
              <label for="candidate-name">Nama <span style="color: var(--error-500);">(wajib)</span></label>
              <input type="text" name="name" id="candidate-name" class="input"
                value="{{ old('name', $candidate->name) }}"
                placeholder="Nama lengkap kandidat" required oninput="updatePreview()">
            </div>

            <!-- Biodata -->
            <div class="flex items-center justify-between mb-4">
              <span class="text-sm font-medium text-secondary">Biodata</span>
            </div>
            <div class="mb-4">
              <div class="input-group">
                <textarea name="biodata" id="candidate-biodata" class="input" rows="4" placeholder="Biodata singkat kandidat..." oninput="updatePreview()">{{ old('biodata', $candidate->biodata) }}</textarea>
              </div>
            </div>

            <!-- Buttons -->
            <div class="flex gap-3 mt-6">
              <a href="{{ route('admin.event', $candidate->event_id) }}" class="btn btn--secondary flex-1">Batal</a>
              <button type="submit" class="btn btn--primary flex-1">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Simpan Perubahan
              </button>
            </div>
          </div>

          <!-- Preview Card -->
          <div>
            <div class="divider-label">
              <span class="divider-label__text">Preview</span>
            </div>
            <div class="candidate-preview">
              <div class="candidate-preview__photo" id="preview-photo">
                @if($candidate->photo_url)
                  <img src="{{ asset('storage/candidates/' . $candidate->photo_url) }}"
                    style="width:100%; height:100%; object-fit:cover;">
                @else
                  <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                @endif
              </div>
              <div class="candidate-preview__body">
                <div class="candidate-preview__number" id="preview-number">{{ $candidate->number }}</div>
                <div class="candidate-preview__name" id="preview-name">{{ $candidate->name }}</div>
                <div class="candidate-preview__biodata" id="preview-biodata">{{ $candidate->biodata ?: 'Biodata kandidat akan tampil di sini' }}</div>
              </div>
            </div>
          </div>

        </div>
      </form>
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

  <script>
    function toggleTheme() {
      const html = document.documentElement;
      const current = html.getAttribute('data-theme');
      html.setAttribute('data-theme', current === 'dark' ? 'light' : 'dark');
      localStorage.setItem('theme', current === 'dark' ? 'light' : 'dark');
    }
    const saved = localStorage.getItem('theme');
    if (saved) document.documentElement.setAttribute('data-theme', saved);

    function previewPhoto(input) {
      const preview = document.getElementById('preview-photo');
      if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
          preview.innerHTML = '<img src="' + e.target.result + '" style="width:100%;height:100%;object-fit:cover;">';
        };
        reader.readAsDataURL(input.files[0]);
      }
    }

    function updatePreview() {
      const name = document.getElementById('candidate-name').value || 'Nama Kandidat';
      const number = document.getElementById('candidate-number').value || '1';
      const biodata = document.getElementById('candidate-biodata').value;

      document.getElementById('preview-name').textContent = name;
      document.getElementById('preview-number').textContent = number;

      if (biodata) {
        document.getElementById('preview-biodata').textContent = biodata;
      } else {
        document.getElementById('preview-biodata').textContent = 'Biodata kandidat akan tampil di sini';
      }
    }

    function downloadPDF() {
      window.location.href = `/event/{{ $event->id }}/download-candidates-pdf`;
    }

document.querySelector('form').addEventListener('submit', function() {
    const btn = document.getElementById('submit-btn');
    btn.innerHTML = 'Menyimpan...';
    btn.disabled = true;
});
  </script>
</body>
</html>
<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Event - VoteQR</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

  @include('layouts.header')

  <main class="section page">
    <div class="container">

      <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('dashboard') }}" class="btn btn--ghost btn--icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
        </a>
        <div class="flex-1">
          <div class="flex items-center gap-2">
            <h1 class="section-title" style="margin-bottom: 0;">Kelola Event</h1>
            <span class="badge
              @if($event->status == 'active') badge--success
              @elseif($event->status == 'upcoming') badge--warning
              @else badge--secondary
              @endif
            ">
              {{ $event->status }}
            </span>
          </div>
          <p class="section-subtitle" style="margin-bottom: 0;">{{ $event->name }}</p>
        </div>
      </div>

      <div class="mb-6" style="border-radius: var(--radius-2xl); overflow: hidden; position: relative;">
        <div style="width: 100%; height: 160px; background: linear-gradient(135deg, var(--primary-500), var(--accent-500)); display: flex; align-items: center; justify-content: center; color: white;">
          <div class="text-center">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto var(--space-2); opacity: 0.8;"><path d="M17 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            <h2 style="font-size: var(--font-size-xl); font-weight: 700;">{{ $event->name }}</h2>
          </div>
        </div>
      </div>

      @if(session('success'))
      <div class="alert alert--success mb-6" id="alert-success" style="background:#dcfce7; color:#166534;; padding: var(--space-3) var(--space-4); border-radius: var(--radius-lg); border: 1px solid var(--success-200);">
        {{ session('success') }}
      </div>
      @endif

      <!-- Section 1: Kontrol Event -->
      <div class="section-header">
        <div class="section-header__title">
          <div class="section-header__icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
          </div>
          Kontrol Event
        </div>
      </div>

      <div class="bento-grid stagger-children mb-8">

        <!-- Generate QR Event -->
        <div class="bento-control" onclick="openModal('qr-event-modal')">
          <div class="bento-control__icon" style="background: linear-gradient(135deg, var(--primary-100), var(--accent-100)); color: var(--primary-600);">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7V5a2 2 0 0 1 2-2h2"/><path d="M17 3h2a2 2 0 0 1 2 2v2"/><path d="M21 17v2a2 2 0 0 1-2 2h-2"/><path d="M7 21H5a2 2 0 0 1-2-2v-2"/><rect x="7" y="7" width="3" height="3"/><rect x="14" y="7" width="3" height="3"/><rect x="7" y="14" width="3" height="3"/><rect x="14" y="14" width="3" height="3"/></svg>
          </div>
          <div>
            <div class="bento-control__title">Generate QR Event</div>
            <div class="bento-control__desc">Download QR code untuk event ini</div>
          </div>
        </div>

        <!-- Generate QR Kandidat -->
        <div class="bento-control" onclick="openModal('qr-candidate-modal')">
          <div class="bento-control__icon" style="background: linear-gradient(135deg, var(--accent-100), var(--primary-100)); color: var(--accent-600);">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7V5a2 2 0 0 1 2-2h2"/><path d="M17 3h2a2 2 0 0 1 2 2v2"/><path d="M21 17v2a2 2 0 0 1-2 2h-2"/><path d="M7 21H5a2 2 0 0 1-2-2v-2"/><rect x="7" y="7" width="3" height="3"/><rect x="14" y="7" width="3" height="3"/><rect x="7" y="14" width="3" height="3"/><rect x="14" y="14" width="3" height="3"/></svg>
          </div>
          <div>
            <div class="bento-control__title">Generate QR Kandidat</div>
            <div class="bento-control__desc">QR code untuk semua kandidat</div>
          </div>
        </div>

        <!-- Edit Data Event -->
        <div class="bento-control" style="cursor: default;">
          <div class="bento-control__icon" style="background: linear-gradient(135deg, var(--warning-100), var(--error-100)); color: var(--warning-600);">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
          </div>
          <div class="flex items-center justify-between" style="width: 100%;">
            <div>
              <div class="bento-control__title">Edit Data Event</div>
            <div class="mt-2 text-sm text-secondary">
            </div>
            <button type="button" class="btn btn--secondary w-full mt-2" onclick="handleProtectedAction('edit-detail-modal')">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
              Edit Detail
            </button>
            </div>
          </div>
        </div>

              <!-- Modal: Peringatan Event Berlangsung / Selesai -->
      <div class="modal-overlay" id="status-warning-modal">
        <div class="modal" style="max-width: 400px;">
          <div class="modal__body text-center" style="padding: var(--space-6) var(--space-4);">
            <div style="width: 56px; height: 56px; border-radius: 50%; background: var(--warning-100, #fef3c7); color: var(--warning-600, #d97706); display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-4);">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
              </svg>
            </div>
            <div class="confirm-dialog__title" id="warning-modal-title">Aksi Tidak Diizinkan</div>
            <p class="confirm-dialog__text mt-2" id="warning-modal-message">
              Detail event dan kandidat tidak dapat diubah.
            </p>
          </div>
          <div class="modal__footer" style="justify-content: center;">
            <button class="btn btn--primary" onclick="closeModal('status-warning-modal')">Mengerti</button>
          </div>
        </div>
      </div>

      </div>

      <!-- Section 3: Daftar Kandidat -->
      <div class="section-header">
        <div class="section-header__title">
          <div class="section-header__icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          </div>
          Daftar Kandidat
        </div>
      </div>

      <div class="mb-8">
        @foreach($event->candidates as $candidate)
        <div class="candidate-list-item">
          <div class="candidate-list-item__number">{{ $candidate->number }}</div>

          <div class="candidate-list-item__photo">
            @if($candidate->photo_url)
              <img src="{{ asset('storage/candidates/' . $candidate->photo_url) }}"
                style="width:100%; height:100%; object-fit:cover;">
            @endif
          </div>

          <div class="candidate-list-item__info">
            <div class="candidate-list-item__name">{{ $candidate->name }}</div>
            <div class="candidate-list-item__meta">
              Nomor {{ $candidate->number }}
            </div>
          </div>

        <a href="{{ route('candidate.edit', $candidate->id) }}" 
          class="btn btn--ghost btn--icon" 
          onclick="return checkCandidateEdit(event)">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
          Edit
        </a>
        </div>
        @endforeach
      </div>

      <!-- Section 5: Statistik Event -->
      <div class="section-header">
        <div class="section-header__title">
          <div class="section-header__icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/></svg>
          </div>
          Statistik Event
        </div>
      </div>

      <div class="grid-3 mb-8">
        <div class="stat-card">
          <div class="stat-card__icon" style="background: linear-gradient(135deg, var(--primary-100), var(--accent-100)); color: var(--primary-600);">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/></svg>
          </div>
          <div class="stat-card__value">{{ $totalVote }}</div>
          <div class="stat-card__label">Total Vote</div>
        </div>
        <div class="stat-card">
          <div class="stat-card__icon" style="background: linear-gradient(135deg, var(--success-100), var(--primary-100)); color: var(--success-600);">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          </div>
          <div class="stat-card__value">{{ $event->participants->count() }}</div>
          <div class="stat-card__label">Total Peserta</div>
        </div>
      </div>

      <!-- Hapus Event -->
      <form id="delete-event-form" action="{{ route('event.delete', $event->id) }}" method="POST"
        style="margin-top: var(--space-8); padding-top: var(--space-6); border-top: 1px solid var(--border-color);">
        @csrf
        @method('DELETE')
      <button type="button" class="btn btn--danger"
        style="width: 100%; max-width: 320px; margin: 0 auto; display: flex; align-items: center; justify-content: center; gap: var(--space-2);"
        onclick="handleDeleteAction()">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path d="M3 6h18"/>
          <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
          <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
        </svg>
        Hapus Event
      </button>

        <p class="text-xs text-muted text-center mt-2">
          Tindakan ini tidak dapat dibatalkan. Semua data akan dihapus.
        </p>
      </form>

    </div>
  </main>

  <!-- Modal: QR Event -->
  <div class="modal-overlay" id="qr-event-modal">
    <div class="modal">
      <div class="modal__header">
        <div class="modal__title">QR Code Event</div>
        <button class="modal__close" onclick="closeModal('qr-event-modal')">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
        </button>
      </div>
      <div class="modal__body">
        <div class="qr-preview">
          <div class="qr-preview__code">
            <img src="{{ url('/event/' . $event->id . '/qr') }}" style="width:100%; height:100%; object-fit:contain;">
          </div>
          <div class="qr-preview__label">{{ $event->name }}</div>
          <div class="qr-preview__sublabel">Scan untuk bergabung event</div>
        </div>
      </div>
      <div class="modal__footer">
        <button class="btn btn--secondary allow-finished" onclick="closeModal('qr-event-modal')">Tutup</button>
        <button class="btn btn--primary allow-finished" onclick="downloadEventQR()">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
          Download QR
        </button>
      </div>
    </div>
  </div>

  <!-- Modal: QR Kandidat -->
  <div class="modal-overlay" id="qr-candidate-modal">
    <div class="modal modal--lg">
      <div class="modal__header">
        <div class="modal__title">QR Code Kandidat</div>
        <button class="modal__close" onclick="closeModal('qr-candidate-modal')">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
        </button>
      </div>
      <div class="modal__body">
        <div class="qr-list">
          @foreach($event->candidates as $candidate)
          <div class="qr-list__item">
            <div class="qr-preview__code" style="width: 120px; height: 120px;">
              <img src="{{ url('/candidate/' . $candidate->id . '/qr') }}" style="width:100%; height:100%; object-fit:contain;">
            </div>
            <div class="qr-list__number">No. {{ $candidate->number }}</div>
            <div class="qr-list__name">{{ $candidate->name }}</div>
          </div>
          @endforeach
        </div>
      </div>
      <div class="modal__footer">
        <button class="btn btn--secondary allow-finished" onclick="closeModal('qr-candidate-modal')">Tutup</button>
        <button class="btn btn--primary allow-finished" onclick="downloadAllQR()">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
          Download Semua
        </button>
        <button class="btn btn--secondary allow-finished" onclick="downloadPDF()">
          Cetak Lanyard Kandidat
        </button>
      </div>
    </div>
  </div>

  <!-- Modal: Delete Event -->
  <div class="modal-overlay" id="delete-event-modal">
    <div class="modal" style="max-width: 400px;">
      <div class="modal__body">
        <div class="confirm-dialog__icon">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
        </div>
        <div class="confirm-dialog__title">Hapus Event?</div>
        <p class="confirm-dialog__text">Event dan semua data (kandidat, vote, peserta) akan dihapus permanen. Tindakan ini tidak dapat dibatalkan.</p>
      </div>
      <div class="modal__footer">
        <button class="btn btn--secondary" onclick="closeModal('delete-event-modal')">Batal</button>
        <button class="btn btn--danger" onclick="confirmDeleteEvent()">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
          Ya, Hapus
        </button>
      </div>
    </div>
  </div>

  <!-- Modal: Edit Detail Event -->
  <div class="modal-overlay" id="edit-detail-modal">
    <div class="modal modal--lg">
      <div class="modal__header">
        <div class="modal__title">Edit Detail Event</div>
        <button class="modal__close" onclick="closeModal('edit-detail-modal')">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
        </button>
      </div>
      <div class="modal__body">
        <form class="form" onsubmit="return false;">
          <div class="input-group">
            <label for="edit-event-name">Nama Event</label>
            <input type="text" id="edit-event-name" class="input"
              value="{{ $event->name }}" placeholder="Nama event">
          </div>
          <div class="input-group">
            <label for="edit-event-desc">Deskripsi Event</label>
            <textarea id="edit-event-desc" class="input" rows="3" placeholder="Deskripsi event">{{ $event->description }}</textarea>
          </div>
          <div class="input-group">
            <label for="edit-event-image">Foto Event</label>
            <div style="display: flex; align-items: center; gap: var(--space-4);">
              <div style="width: 80px; height: 80px; border-radius: var(--radius-lg); background: linear-gradient(135deg, var(--primary-500), var(--accent-500)); display: flex; align-items: center; justify-content: center; color: white; flex-shrink: 0; overflow: hidden;" id="edit-image-preview">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
              </div>
              <div class="flex-1">
                <input type="file" id="edit-event-image" class="input" accept="image/*" onchange="previewEventPhoto(this)">
                <span class="text-xs text-muted mt-1" style="display: block;">Format: JPG, PNG. Maks 2MB.</span>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal__footer">
        <button class="btn btn--secondary" onclick="closeModal('edit-detail-modal')">Batal</button>
        <button class="btn btn--primary" onclick="saveEventDetail()">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
          Simpan Perubahan
        </button>
      </div>
    </div>
  </div>

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
    <a href="{{ route('list.event') }}" class="nav-item">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><circle cx="4" cy="6" r="1"/><circle cx="4" cy="12" r="1"/><circle cx="4" cy="18" r="1"/></svg>
      <span>Event</span>
    </a>
  </nav>

  <script>
    const saved = localStorage.getItem('theme');
    if (saved) document.documentElement.setAttribute('data-theme', saved);

    function openModal(id) {
      document.getElementById(id).classList.add('modal-overlay--active');

      if (id === 'edit-detail-modal') {
        const preview = document.getElementById('edit-image-preview');
        @if($event->image_url)
        preview.innerHTML = `<img src="/storage/events/{{ $event->image_url }}" style="width:100%;height:100%;object-fit:cover;">`;
        @endif
      }
    }

    function closeModal(id) {
      document.getElementById(id).classList.remove('modal-overlay--active');
    }

    document.querySelectorAll('.modal-overlay').forEach(overlay => {
      overlay.addEventListener('click', function(e) {
        if (e.target === this) {
          this.classList.remove('modal-overlay--active');
        }
      });
    });

    function confirmDeleteEvent() {
      document.getElementById('delete-event-form').submit();
    }

    function previewEventPhoto(input) {
      const preview = document.getElementById('edit-image-preview');
      if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
          preview.innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;">`;
        };
        reader.readAsDataURL(input.files[0]);
      }
    }

    function saveEventDetail() {
      const name = document.getElementById('edit-event-name').value;
      const desc = document.getElementById('edit-event-desc').value;
      fetch(`/event/{{ $event->id }}/update`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ name: name, description: desc })
      }).then(() => location.reload());
    }

    function downloadEventQR() {
      window.location.href = `/event/{{ $event->id }}/qr/download`;
    }

    function downloadAllQR() {
      const items = document.querySelectorAll('.qr-list__item');
      const eventName = "{{ \Illuminate\Support\Str::slug($event->name) }}";
      items.forEach((item, index) => {
        const img = item.querySelector('img');
        const name = item.querySelector('.qr-list__name').innerText.replace(/\s+/g, '_');
        setTimeout(() => {
          const link = document.createElement('a');
          link.href = img.src;
          link.download = `${name}_${eventName}.png`;
          document.body.appendChild(link);
          link.click();
          document.body.removeChild(link);
        }, index * 500);
      });
    }

    function downloadPDF() {
      window.location.href = `/event/{{ $event->id }}/download-candidates-pdf`;
    }

    setTimeout(() => {
    const alert = document.getElementById('alert-success');
    if (alert) {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    }
}, 1000);

// Variable status event dari server
const eventStatus = "{{ $event->status }}";

// Fungsi untuk mencegat pembukaan modal edit detail
function handleProtectedAction(targetModalId) {
  if (eventStatus === 'active') {
    showStatusWarning('Event Sedang Berlangsung', 'Detail event tidak dapat diubah selama voting/event sedang berlangsung.');
    return;
  }
  if (eventStatus === 'finished') {
    showStatusWarning('Event Telah Selesai', 'Event ini sudah selesai. Data event tidak dapat diubah kembali.');
    return;
  }
  openModal(targetModalId);
}

// Fungsi untuk mencegat aksi penghapusan event
function handleDeleteAction() {
  if (eventStatus === 'active') {
    showStatusWarning('Event Sedang Berlangsung', 'Event yang sedang berlangsung tidak dapat dihapus.');
    return;
  }
  if (eventStatus === 'upcoming') {
    showStatusWarning('Event Belum Dimulai', 'Event yang belum dimulai tidak dapat dihapus.');
    return;
  }
  
  // Jika status sudah finished (selesai), izinkan membuka modal hapus
  openModal('delete-event-modal');
}

// Fungsi untuk mencegat navigasi edit kandidat
function checkCandidateEdit(e) {
  if (eventStatus === 'active') {
    e.preventDefault();
    showStatusWarning('Event Sedang Berlangsung', 'Data kandidat tidak dapat diubah selama event berlangsung.');
    return false;
  }
  if (eventStatus === 'finished') {
    e.preventDefault();
    showStatusWarning('Event Telah Selesai', 'Event ini sudah selesai. Data kandidat tidak dapat diubah kembali.');
    return false;
  }
  return true;
}

// Fungsi pembantu untuk menampilkan isi modal peringatan
function showStatusWarning(title, message) {
  document.getElementById('warning-modal-title').innerText = title;
  document.getElementById('warning-modal-message').innerText = message;
  openModal('status-warning-modal');
}
  </script>
</body>
</html>
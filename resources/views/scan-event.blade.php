<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Scan QR Event - VoteQR</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <style>
    body {
      padding-bottom: 0;
      overflow: hidden;
    }

    /* Import button overlay */
    .import-overlay {
      position: fixed;
      bottom: var(--space-8);
      left: 50%;
      transform: translateX(-50%);
      z-index: 9999;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: var(--space-3);
      width: 100%;
      max-width: 360px;
      padding: 0 var(--space-5);
      pointer-events: auto;
    }

    .import-btn {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: var(--space-2);
      width: 100%;
      padding: var(--space-4);
      border: none;
      border-radius: var(--radius-lg);
      font-size: var(--font-size-base);
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s;
      font-family: inherit;
      background: var(--primary-500);
      color: white;
      box-shadow: 0 4px 20px rgba(59, 130, 246, 0.4);
    }

    .import-btn:hover { background: var(--primary-600); transform: translateY(-1px); }
    .import-btn:active { transform: scale(0.98); }

    .import-hint {
      font-size: var(--font-size-xs);
      color: rgba(255, 255, 255, 0.5);
      text-align: center;
    }

    /* Loading / result overlay */
    .scan-result-overlay {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.85);
      display: none;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      z-index: 50;
      padding: var(--space-6);
    }

    .scan-result-overlay.active { display: flex; }

    .scan-result-card {
      background: var(--neutral-800);
      border-radius: var(--radius-xl);
      padding: var(--space-8);
      max-width: 380px;
      width: 100%;
      text-align: center;
    }

    .scan-result-card__icon {
      width: 64px;
      height: 64px;
      border-radius: var(--radius-full);
      margin: 0 auto var(--space-4);
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .scan-result-card__icon--loading {
      background: var(--primary-500);
    }

    .scan-result-card__icon--success {
      background: var(--success-500);
      animation: pop-in 0.4s cubic-bezier(0.18, 0.89, 0.32, 1.28);
    }

    .scan-result-card__icon--error {
      background: var(--error-500);
      animation: pop-in 0.4s cubic-bezier(0.18, 0.89, 0.32, 1.28);
    }

    .scan-result-card__title {
      font-size: var(--font-size-lg);
      font-weight: 700;
      color: white;
      margin-bottom: var(--space-2);
    }

    .scan-result-card__desc {
      font-size: var(--font-size-sm);
      color: rgba(255, 255, 255, 0.6);
      margin-bottom: var(--space-4);
      line-height: 1.5;
    }

    .scan-result-card__btn {
      width: 100%;
      padding: var(--space-3);
      border: none;
      border-radius: var(--radius-md);
      font-size: var(--font-size-sm);
      font-weight: 600;
      cursor: pointer;
      font-family: inherit;
      background: rgba(255, 255, 255, 0.1);
      color: white;
      transition: background 0.2s;
    }

    .scan-result-card__btn:hover { background: rgba(255, 255, 255, 0.2); }

    .scan-spinner {
      width: 32px;
      height: 32px;
      border: 3px solid rgba(255, 255, 255, 0.3);
      border-top-color: white;
      border-radius: 50%;
      animation: spin 0.8s linear infinite;
    }

    @keyframes pop-in {
      0% { transform: scale(0); opacity: 0; }
      100% { transform: scale(1); opacity: 1; }
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    /* Error flash message */
    .scan-flash {
      position: fixed;
      top: var(--space-5);
      left: 50%;
      transform: translateX(-50%);
      background: rgba(239, 68, 68, 0.95);
      color: white;
      padding: var(--space-3) var(--space-5);
      border-radius: var(--radius-md);
      font-size: var(--font-size-sm);
      font-weight: 500;
      z-index: 60;
      max-width: 90%;
      text-align: center;
      animation: slide-down 0.3s ease;
    }

    @keyframes slide-down {
      from { transform: translate(-50%, -100%); opacity: 0; }
      to { transform: translateX(-50%); opacity: 1; }
    }
  </style>
</head>
<body>

  @if(session('error'))
  <div class="scan-flash">{{ session('error') }}</div>
  <script>
    setTimeout(() => { document.querySelector('.scan-flash')?.remove(); }, 4000);
  </script>
  @endif

  <!-- Scan Overlay -->
  <div class="scan-overlay" style="pointer-events: none;">
    <!-- Camera Placeholder -->
    <div class="camera-placeholder" style="pointer-events: none;">
      <svg class="camera-placeholder__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
    </div>

    <!-- Scan Frame -->
    <div class="scan-frame" style="pointer-events: none;">
      <div class="scan-box">
        <div class="scan-line"></div>
        <div class="scan-pulse"></div>
      </div>
    </div>

    <!-- Header -->
    <div class="scan-header" style="pointer-events: auto;">
      <a href="{{ route('home') }}" class="scan-header__btn">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
      </a>
      <button class="scan-header__btn" onclick="toggleFlash()" title="Flashlight">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 14c.2-1 .7-1.7 1.5-2.5 1-.9 1.5-2.2 1.5-3.5A6 6 0 0 0 6 8c0 1 .2 2.2 1.5 3.5.7.7 1.3 1.5 1.5 2.5"/><path d="M9 18h6"/><path d="M10 22h4"/></svg>
      </button>
    </div>

    <!-- Scan Text -->
    <div class="scan-text">Scan QR Code Event</div>
    <div class="scan-hint">Arahkan kamera ke kode QR event</div>
  </div>

  <!-- Import QR dari PC -->
  <div class="import-overlay">
    <button class="import-btn" onclick="document.getElementById('qr-file-input').click()">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
      Import QR dari PC
    </button>
    <div class="import-hint">Belum punya kamera? Pilih file gambar QR dari komputer</div>
  </div>

  <!-- Hidden file input -->
  <input type="file" id="qr-file-input" accept="image/*" style="display:none" onchange="handleQrImport(event)">

  <!-- Result Overlay -->
  <div class="scan-result-overlay" id="result-overlay">
    <div class="scan-result-card">
      <div class="scan-result-card__icon scan-result-card__icon--loading" id="result-icon">
        <div class="scan-spinner"></div>
      </div>
      <div class="scan-result-card__title" id="result-title">Memproses QR...</div>
      <div class="scan-result-card__desc" id="result-desc">Mengidentifikasi kode QR</div>
      <button class="scan-result-card__btn" id="result-btn" style="display:none" onclick="closeResult()">Tutup</button>
    </div>
  </div>

  <!-- Hidden form untuk submit ke controller -->
  <form id="scan-form" method="POST" action="{{ route('scan.process') }}" style="display:none">
    @csrf
    <input type="hidden" name="code_value" id="code-value-input">
  </form>

  <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
  <script>
    // Theme
    const saved = localStorage.getItem('theme');
    if (saved) document.documentElement.setAttribute('data-theme', saved);

    function toggleFlash() {
      alert('Flashlight toggle (integrasi kamera diperlukan)');
    }

    // --- Import QR dari file gambar ---
    function handleQrImport(event) {
      const file = event.target.files[0];
      if (!file) return;

      showResult('loading', 'Memproses QR...', 'Mengidentifikasi kode QR');

      const reader = new FileReader();
      reader.onload = function(e) {
        const img = new Image();
        img.onload = function() {
          decodeQrImage(img);
        };
        img.onerror = function() {
          showResult('error', 'Gagal Membaca Gambar', 'File tidak bisa dibaca sebagai gambar.', true);
        };
        img.src = e.target.result;
      };
      reader.readAsDataURL(file);
    }

    function decodeQrImage(img) {
      // Buat canvas untuk decode
      const canvas = document.createElement('canvas');
      const ctx = canvas.getContext('2d', { willReadFrequently: true });

      // Scale gambar agar tidak terlalu besar
      const maxDim = 1000;
      let w = img.width;
      let h = img.height;
      if (w > maxDim || h > maxDim) {
        const scale = maxDim / Math.max(w, h);
        w = Math.round(w * scale);
        h = Math.round(h * scale);
      }

      canvas.width = w;
      canvas.height = h;
      ctx.drawImage(img, 0, 0, w, h);

      const imageData = ctx.getImageData(0, 0, w, h);
      const code = jsQR(imageData.data, w, h, { inversionAttempts: 'attemptBoth' });

      if (code && code.data) {
        // QR berhasil di-decode
        showResult('success', 'QR Terdeteksi!', 'Mengalihkan ke event...');
        document.getElementById('code-value-input').value = code.data;

        // Submit ke controller
        setTimeout(() => {
          document.getElementById('scan-form').submit();
        }, 800);
      } else {
        showResult('error', 'QR Tidak Ditemukan', 'Gambar tidak berisi kode QR yang valid. Coba gambar lain.', true);
      }
    }

    // --- Result overlay helpers ---
    function showResult(type, title, desc, showBtn = false) {
      const overlay = document.getElementById('result-overlay');
      const icon = document.getElementById('result-icon');
      const titleEl = document.getElementById('result-title');
      const descEl = document.getElementById('result-desc');
      const btn = document.getElementById('result-btn');

      overlay.classList.add('active');

      icon.className = 'scan-result-card__icon scan-result-card__icon--' + type;

      if (type === 'loading') {
        icon.innerHTML = '<div class="scan-spinner"></div>';
      } else if (type === 'success') {
        icon.innerHTML = '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>';
      } else if (type === 'error') {
        icon.innerHTML = '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>';
      }

      titleEl.textContent = title;
      descEl.textContent = desc;
      btn.style.display = showBtn ? 'block' : 'none';
    }

    function closeResult() {
      document.getElementById('result-overlay').classList.remove('active');
      // Reset file input agar bisa pilih file yang sama lagi
      document.getElementById('qr-file-input').value = '';
    }
  </script>
</body>
</html>
<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Scan QR Kandidat - VoteQR</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <style>
    body { padding-bottom: 0; overflow: hidden; }

    #camera-video {
      position: fixed; inset: 0; width: 100%; height: 100%;
      object-fit: cover; background: #000; z-index: 0;
    }

    .import-overlay {
      position: fixed; bottom: var(--space-8); left: 50%;
      transform: translateX(-50%); z-index: 40;
      display: flex; flex-direction: column; align-items: center;
      gap: var(--space-3); width: 100%; max-width: 360px;
      padding: 0 var(--space-5);
    }

    .import-btn {
      display: flex; align-items: center; justify-content: center;
      gap: var(--space-2); width: 100%; padding: var(--space-4);
      border: none; border-radius: var(--radius-lg);
      font-size: var(--font-size-base); font-weight: 600;
      cursor: pointer; transition: all .2s; font-family: inherit;
      background: var(--primary-500); color: #fff;
      box-shadow: 0 4px 20px rgba(59,130,246,.4);
    }
    .import-btn:hover { background: var(--primary-600); transform: translateY(-1px); }
    .import-btn:active { transform: scale(.98); }
    .import-hint { font-size: var(--font-size-xs); color: rgba(255,255,255,.55); text-align: center; }

    .scan-result-overlay {
      position: fixed; inset: 0; background: rgba(0,0,0,.85);
      display: none; flex-direction: column; align-items: center;
      justify-content: center; z-index: 50; padding: var(--space-6);
    }
    .scan-result-overlay.active { display: flex; }

    .scan-result-card {
      background: var(--neutral-800); border-radius: var(--radius-xl);
      padding: var(--space-8); max-width: 380px; width: 100%; text-align: center;
    }
    .scan-result-card__icon {
      width: 64px; height: 64px; border-radius: var(--radius-full);
      margin: 0 auto var(--space-4); display: flex;
      align-items: center; justify-content: center;
    }
    .scan-result-card__icon--loading { background: var(--primary-500); }
    .scan-result-card__icon--success { background: var(--success-500); animation: pop-in .4s cubic-bezier(.18,.89,.32,1.28); }
    .scan-result-card__icon--error   { background: var(--error-500);   animation: pop-in .4s cubic-bezier(.18,.89,.32,1.28); }
    .scan-result-card__title { font-size: var(--font-size-lg); font-weight: 700; color: #fff; margin-bottom: var(--space-2); }
    .scan-result-card__desc  { font-size: var(--font-size-sm); color: rgba(255,255,255,.6); margin-bottom: var(--space-4); line-height: 1.5; }
    .scan-result-card__btn {
      width: 100%; padding: var(--space-3); border: none;
      border-radius: var(--radius-md); font-size: var(--font-size-sm);
      font-weight: 600; cursor: pointer; font-family: inherit;
      background: rgba(255,255,255,.1); color: #fff; transition: background .2s;
    }
    .scan-result-card__btn:hover { background: rgba(255,255,255,.2); }

    .scan-spinner {
      width: 32px; height: 32px; border: 3px solid rgba(255,255,255,.3);
      border-top-color: #fff; border-radius: 50%; animation: spin .8s linear infinite;
    }

    @keyframes pop-in { 0% { transform: scale(0); opacity: 0; } 100% { transform: scale(1); opacity: 1; } }
    @keyframes spin { to { transform: rotate(360deg); } }

    .scan-flash {
      position: fixed; top: var(--space-5); left: 50%;
      transform: translateX(-50%); background: rgba(239,68,68,.95);
      color: #fff; padding: var(--space-3) var(--space-5);
      border-radius: var(--radius-md); font-size: var(--font-size-sm);
      font-weight: 500; z-index: 60; max-width: 90%; text-align: center;
      animation: slide-down .3s ease;
    }
    @keyframes slide-down {
      from { transform: translate(-50%,-100%); opacity: 0; }
      to   { transform: translateX(-50%); opacity: 1; }
    }
  </style>
</head>
<body>

  @if(session('error'))
  <div class="scan-flash">{{ session('error') }}</div>
  <script>setTimeout(() => document.querySelector('.scan-flash')?.remove(), 4000);</script>
  @endif

  <!-- Live camera -->
  <video id="camera-video" playsinline muted></video>

  <div class="scan-overlay" style="pointer-events:none; background: transparent;">
    <div class="scan-frame" style="pointer-events:none;">
      <div class="scan-box">
        <div class="scan-line"></div>
        <div class="scan-pulse"></div>
      </div>
    </div>

    <div class="scan-header" style="pointer-events:auto;">
      <a href="{{ route('event.show') }}?event_id={{ $event->id }}" class="scan-header__btn">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
      </a>
      <div class="scan-header__btn" style="background:transparent;width:auto;padding:0 var(--space-3);">
        <span style="font-size:var(--font-size-sm);font-weight:500;">{{ $event->name }}</span>
      </div>
      <div style="width:40px;"></div>
    </div>

    <div class="scan-text">Scan QR Kandidat</div>
    <div class="scan-hint">Arahkan kamera ke kode QR kandidat</div>
  </div>

  <div class="import-overlay">
    <button class="import-btn" onclick="document.getElementById('qr-file-input').click()">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
      Import QR dari PC
    </button>
    <div class="import-hint">Belum punya kamera? Pilih file gambar QR dari komputer</div>
  </div>

  <input type="file" id="qr-file-input" accept="image/*" style="display:none" onchange="handleQrImport(event)">

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

  <form id="scan-form" method="POST" action="{{ route('scan.candidate.process') }}" style="display:none">
    @csrf
    <input type="hidden" name="event_id" value="{{ $event->id }}">
    <input type="hidden" name="code_value" id="code-value-input">
  </form>

  <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
  <script>
    const saved = localStorage.getItem('theme');
    if (saved) document.documentElement.setAttribute('data-theme', saved);

    let stream = null, scanning = false, rafId = null, submitted = false;
    const video  = document.getElementById('camera-video');
    const canvas = document.createElement('canvas');
    const ctx    = canvas.getContext('2d', { willReadFrequently: true });

    async function startCamera() {
      if (!navigator.mediaDevices?.getUserMedia) return; // fallback: import file
      try {
        stream = await navigator.mediaDevices.getUserMedia({
          video: { facingMode: { ideal: 'environment' } }, audio: false
        });
        video.srcObject = stream;
        await video.play();
        scanning = true;
        tick();
      } catch (e) {
        console.warn('Kamera tidak tersedia:', e.name);
      }
    }

    function tick() {
      if (!scanning) return;
      if (video.readyState === video.HAVE_ENOUGH_DATA) {
        canvas.width  = video.videoWidth;
        canvas.height = video.videoHeight;
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        const data = ctx.getImageData(0, 0, canvas.width, canvas.height);
        const code = jsQR(data.data, canvas.width, canvas.height, { inversionAttempts: 'dontInvert' });
        if (code && code.data) { onQrDetected(code.data); return; }
      }
      rafId = requestAnimationFrame(tick);
    }

    function stopCamera() {
      scanning = false;
      if (rafId) cancelAnimationFrame(rafId);
      stream?.getTracks().forEach(t => t.stop());
    }

    function onQrDetected(value) {
      if (submitted) return;
      submitted = true;
      stopCamera();
      showResult('success', 'QR Kandidat Terdeteksi!', 'Membuka halaman penilaian...');
      document.getElementById('code-value-input').value = value;
      setTimeout(() => document.getElementById('scan-form').submit(), 700);
    }

    /* ---- Import file gambar QR ---- */
    function handleQrImport(event) {
      const file = event.target.files[0];
      if (!file) return;
      showResult('loading', 'Memproses QR...', 'Mengidentifikasi kode QR');
      const reader = new FileReader();
      reader.onload = e => {
        const img = new Image();
        img.onload  = () => decodeQrImage(img);
        img.onerror = () => showResult('error', 'Gagal Membaca Gambar', 'File tidak bisa dibaca sebagai gambar.', true);
        img.src = e.target.result;
      };
      reader.readAsDataURL(file);
    }

    function decodeQrImage(img) {
      const c = document.createElement('canvas');
      const cc = c.getContext('2d', { willReadFrequently: true });
      const maxDim = 1000;
      let w = img.width, h = img.height;
      if (w > maxDim || h > maxDim) {
        const s = maxDim / Math.max(w, h);
        w = Math.round(w * s); h = Math.round(h * s);
      }
      c.width = w; c.height = h;
      cc.drawImage(img, 0, 0, w, h);
      const code = jsQR(cc.getImageData(0, 0, w, h).data, w, h, { inversionAttempts: 'attemptBoth' });

      if (code && code.data) onQrDetected(code.data);
      else showResult('error', 'QR Tidak Ditemukan', 'Gambar tidak berisi kode QR yang valid.', true);
    }

    function showResult(type, title, desc, showBtn = false) {
      const overlay = document.getElementById('result-overlay');
      const icon = document.getElementById('result-icon');
      overlay.classList.add('active');
      icon.className = 'scan-result-card__icon scan-result-card__icon--' + type;
      if (type === 'loading') icon.innerHTML = '<div class="scan-spinner"></div>';
      if (type === 'success') icon.innerHTML = '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>';
      if (type === 'error')   icon.innerHTML = '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>';
      document.getElementById('result-title').textContent = title;
      document.getElementById('result-desc').textContent  = desc;
      document.getElementById('result-btn').style.display = showBtn ? 'block' : 'none';
    }

    function closeResult() {
      document.getElementById('result-overlay').classList.remove('active');
      document.getElementById('qr-file-input').value = '';
      submitted = false;
      if (!scanning) startCamera();
    }

    startCamera();
    window.addEventListener('pagehide', stopCamera);
  </script>
</body>
</html>

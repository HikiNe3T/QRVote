<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Input Kode Akses - VoteQR</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <style>
    body {
      margin: 0;
      background: var(--neutral-900);
      overflow-x: hidden;
    }

    .access-overlay {
      position: fixed;
      inset: 0;
      background: var(--neutral-900);
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: var(--space-6);
    }

    .scan-success {
      position: relative;
      width: 80px;
      height: 80px;
      border-radius: var(--radius-full);
      background: var(--success-500);
      display: flex;
      align-items: center;
      justify-content: center;
      animation: pop-in 0.4s cubic-bezier(0.18, 0.89, 0.32, 1.28);
      margin-bottom: var(--space-4);
    }

    .scan-success svg {
      color: white;
      animation: draw-check 0.4s ease 0.2s both;
    }

    @keyframes pop-in {
      0% { transform: scale(0); opacity: 0; }
      100% { transform: scale(1); opacity: 1; }
    }

    @keyframes draw-check {
      0% { stroke-dashoffset: 24; }
      100% { stroke-dashoffset: 0; }
    }

    .access-topbar {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      display: flex;
      align-items: center;
      padding: var(--space-4) var(--space-5);
      z-index: 10;
    }

    .access-topbar__back {
      width: 40px;
      height: 40px;
      border-radius: var(--radius-md);
      background: rgba(255, 255, 255, 0.1);
      border: none;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      cursor: pointer;
      transition: background 0.2s;
    }

    .access-topbar__back:hover { background: rgba(255, 255, 255, 0.2); }

    .access-content {
      flex: 1;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      width: 100%;
      max-width: 400px;
      text-align: center;
    }

    .access-title {
      font-size: var(--font-size-xl);
      font-weight: 700;
      color: white;
      margin-bottom: var(--space-2);
    }

    .access-subtitle {
      font-size: var(--font-size-sm);
      color: rgba(255, 255, 255, 0.6);
      margin-bottom: var(--space-8);
      line-height: 1.5;
    }

    .event-chip {
      display: flex;
      align-items: center;
      gap: var(--space-3);
      padding: var(--space-3) var(--space-4);
      background: rgba(255, 255, 255, 0.08);
      border-radius: var(--radius-lg);
      border: 1px solid rgba(255, 255, 255, 0.1);
      margin-bottom: var(--space-8);
      width: 100%;
    }

    .event-chip__icon {
      width: 44px;
      height: 44px;
      border-radius: var(--radius-md);
      background: linear-gradient(135deg, var(--primary-500), var(--accent-500));
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .event-chip__name {
      font-size: var(--font-size-sm);
      font-weight: 600;
      color: white;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .event-chip__meta {
      font-size: var(--font-size-xs);
      color: rgba(255, 255, 255, 0.5);
    }

    .event-chip__badge {
      display: inline-flex;
      align-items: center;
      gap: var(--space-1);
      padding: var(--space-1) var(--space-2);
      background: rgba(245, 158, 11, 0.2);
      color: var(--warning-500);
      border-radius: var(--radius-full);
      font-size: 10px;
      font-weight: 600;
      flex-shrink: 0;
    }

    .code-input-group {
      display: flex;
      gap: var(--space-2);
      margin-bottom: var(--space-4);
      width: 100%;
      justify-content: center;
    }

    .code-input {
      width: 48px;
      height: 56px;
      text-align: center;
      font-size: var(--font-size-xl);
      font-weight: 700;
      color: white;
      background: rgba(255, 255, 255, 0.08);
      border: 2px solid rgba(255, 255, 255, 0.15);
      border-radius: var(--radius-md);
      outline: none;
      transition: all 0.2s;
      font-family: inherit;
    }

    .code-input:focus {
      border-color: var(--primary-400);
      background: rgba(59, 130, 246, 0.15);
      box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
    }

    .code-input.filled {
      border-color: var(--primary-400);
      background: rgba(59, 130, 246, 0.1);
    }

    .code-input.error {
      border-color: var(--error-500);
      background: rgba(239, 68, 68, 0.1);
      animation: shake 0.4s ease;
    }

    @keyframes shake {
      0%, 100% { transform: translateX(0); }
      25% { transform: translateX(-4px); }
      75% { transform: translateX(4px); }
    }

    .access-submit {
      width: 100%;
      padding: var(--space-4);
      border: none;
      border-radius: var(--radius-lg);
      font-size: var(--font-size-base);
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s;
      font-family: inherit;
      margin-bottom: var(--space-4);
      background: var(--primary-500);
      color: white;
    }

    .access-submit:active { transform: scale(0.98); }
    .access-submit:disabled { opacity: 0.5; cursor: not-allowed; }

    .access-alert {
      display: none;
      padding: var(--space-3) var(--space-4);
      border-radius: var(--radius-md);
      font-size: var(--font-size-sm);
      text-align: center;
      margin-bottom: var(--space-4);
      width: 100%;
    }

    .access-alert--error {
      display: block;
      background: rgba(239, 68, 68, 0.15);
      color: var(--error-500);
      border: 1px solid rgba(239, 68, 68, 0.3);
    }

    .rescan-link {
      display: flex;
      align-items: center;
      gap: var(--space-2);
      color: rgba(255, 255, 255, 0.5);
      font-size: var(--font-size-sm);
      text-decoration: none;
      transition: color 0.2s;
    }

    .rescan-link:hover { color: rgba(255, 255, 255, 0.8); }

    .access-spinner {
      width: 20px;
      height: 20px;
      border: 2px solid rgba(255, 255, 255, 0.3);
      border-top-color: white;
      border-radius: 50%;
      animation: spin 0.8s linear infinite;
      display: inline-block;
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
    }
  </style>
</head>
<body>

  <div class="access-overlay">
    <!-- Top bar -->
    <div class="access-topbar">
      <a href="{{ route('scan.event') }}" class="access-topbar__back">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
      </a>
    </div>

    <!-- Content -->
    <div class="access-content">

      <!-- Success icon (from scan) -->
      <div class="scan-success">
        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
      </div>

      <h1 class="access-title">Event Private</h1>
      <p class="access-subtitle">Masukkan kode akses yang Anda terima untuk melanjutkan</p>

      <!-- Event info (dinamis dari controller) -->
      <div class="event-chip">
        <div class="event-chip__icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </div>
        <div class="flex-1" style="min-width: 0; text-align: left;">
          <div class="event-chip__name">{{ $event->name }}</div>
          <div class="event-chip__meta">
            @php
              $start = \Carbon\Carbon::parse($event->start_date)->translatedFormat('d F Y');
              $end = \Carbon\Carbon::parse($event->end_date)->translatedFormat('d F Y');
              echo $start === $end ? $start : $start . ' - ' . $end;
            @endphp
          </div>
        </div>
        <span class="event-chip__badge">
          <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          Private
        </span>
      </div>

      <!-- Alert -->
      <div class="access-alert" id="access-alert"></div>

      <!-- Code inputs -->
      <div class="code-input-group" id="code-inputs">
        <input type="text" inputmode="text" maxlength="1" class="code-input" data-index="0" placeholder="A">
        <input type="text" inputmode="text" maxlength="1" class="code-input" data-index="1" placeholder="B">
        <input type="text" inputmode="text" maxlength="1" class="code-input" data-index="2" placeholder="C">
        <input type="text" inputmode="text" maxlength="1" class="code-input" data-index="3" placeholder="D">
        <input type="text" inputmode="text" maxlength="1" class="code-input" data-index="4" placeholder="E">
        <input type="text" inputmode="text" maxlength="1" class="code-input" data-index="5" placeholder="F">
      </div>

      <!-- Submit -->
      <button class="access-submit" id="submit-btn" onclick="verifyCode()">
        Verifikasi Kode
      </button>

      <!-- Rescan -->
      <a href="{{ route('scan.event') }}" class="rescan-link">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
        Scan Ulang QR Code
      </a>
    </div>
  </div>

  <script>
    const saved = localStorage.getItem('theme');
    if (saved) document.documentElement.setAttribute('data-theme', saved);

    const EVENT_ID = "{{ $event->id }}";
    const VERIFY_URL = "{{ route('access.verify') }}";
    const CSRF_TOKEN = "{{ csrf_token() }}";

    // --- Code input behavior ---
    const inputs = document.querySelectorAll('.code-input');

    inputs.forEach((input, index) => {
      input.addEventListener('input', (e) => {
        const value = e.target.value.toUpperCase();
        e.target.value = value;

        if (value) {
          e.target.classList.add('filled');
          if (index < inputs.length - 1) {
            inputs[index + 1].focus();
          }
        } else {
          e.target.classList.remove('filled');
        }
      });

      input.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' && !e.target.value && index > 0) {
          inputs[index - 1].focus();
        }
        if (e.key === 'Enter') {
          verifyCode();
        }
      });

      input.addEventListener('paste', (e) => {
        e.preventDefault();
        const pasted = (e.clipboardData.getData('text') || '').toUpperCase().replace(/[^A-Z0-9]/g, '');
        const chars = pasted.split('').slice(0, inputs.length);
        chars.forEach((char, i) => {
          if (inputs[i]) {
            inputs[i].value = char;
            inputs[i].classList.add('filled');
          }
        });
        if (chars.length < inputs.length) {
          inputs[chars.length].focus();
        } else {
          inputs[inputs.length - 1].focus();
        }
      });
    });

    inputs[0].focus();

    // --- Verify code via AJAX ke controller ---
    function verifyCode() {
      const code = Array.from(inputs).map(i => i.value).join('');
      const alertEl = document.getElementById('access-alert');
      const btn = document.getElementById('submit-btn');

      if (code.length < 6) {
        showAlert('Kode belum lengkap. Masukkan 6 karakter.');
        const emptyInput = Array.from(inputs).find(i => !i.value);
        if (emptyInput) emptyInput.focus();
        return;
      }

      btn.disabled = true;
      btn.innerHTML = '<span class="access-spinner"></span> Memverifikasi...';
      alertEl.style.display = 'none';

      fetch(VERIFY_URL, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': CSRF_TOKEN,
          'Accept': 'application/json',
        },
        body: JSON.stringify({
          event_id: EVENT_ID,
          access_code: code,
        }),
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          btn.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Berhasil!';
          btn.style.background = 'var(--success-500)';
          setTimeout(() => {
            window.location.href = data.redirect;
          }, 800);
        } else {
          throw new Error(data.message || 'Kode akses salah.');
        }
      })
      .catch(err => {
        inputs.forEach(i => {
          i.classList.add('error');
          i.classList.remove('filled');
        });

        btn.disabled = false;
        btn.innerHTML = 'Verifikasi Kode';
        showAlert(err.message || 'Kode akses salah. Periksa kembali kode Anda.');

        setTimeout(() => {
          inputs.forEach(i => {
            i.value = '';
            i.classList.remove('error');
          });
          inputs[0].focus();
        }, 600);
      });
    }

    function showAlert(message) {
      const el = document.getElementById('access-alert');
      el.textContent = message;
      el.className = 'access-alert access-alert--error';
    }
  </script>
</body>
</html>
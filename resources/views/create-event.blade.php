<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Buat Event - VoteQR</title>
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
        <a href="{{ route('home') }}" class="btn btn--ghost btn--icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
        </a>
        <div>
          <h1 class="section-title" style="margin-bottom: 0;">Buat Event Baru</h1>
          <p class="section-subtitle" style="margin-bottom: 0;">Isi detail event dan konfigurasi voting</p>
        </div>
      </div>
@if ($errors->any())
    <div style="background:#ef4444;color:white;padding:12px;border-radius:8px;margin-bottom:16px;">
        <ul style="margin:0;padding-left:18px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
        <form class="form" method="POST" action="{{ route('store.event') }}" enctype="multipart/form-data">
@csrf
        <div class="form-section">
          <div class="form-section__title">
            <div class="form-section__title-icon">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            Detail Event
          </div>
          <div class="grid-2">
            <div class="input-group">
              <label for="event-name">Nama Event</label>
              <input type="text" id="event-name" name="name" class="input" placeholder="...">
            </div>
            <div class="input-group">
              <label for="event-image">Gambar Event</label>
              <div class="image-upload" id="image-upload">
                <input type="file" name="image" id="event-image" class="input" accept="image/*" style="display: none;">
                <div class="image-upload__placeholder" id="image-placeholder">
                  <div class="image-upload__icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                  </div>
                  <div class="image-upload__text">Klik atau drag gambar ke sini</div>
                  <div class="image-upload__hint">JPG, PNG, WEBP - Max 2MB</div>
                </div>
                <div class="image-upload__preview" id="image-preview" style="display: none;">
                  <img id="image-preview-img" src="" alt="Preview">
                  <button type="button" class="image-upload__remove" id="image-remove" title="Hapus gambar">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                  </button>
                </div>
              </div>
            </div>
          </div>
          <div class="input-group mt-4">
            <label for="event-desc">Deskripsi</label>
            <textarea id="event-desc" name="description" class="input" rows="3" placeholder="Jelaskan tentang event ini..."></textarea>
          </div>
          <div class="grid-2 mt-4">
            <div class="input-group">
              <label for="event-start">Tanggal Mulai</label>
              <input type="date" name="start_date" id="event-start" class="input" min="{{ date('Y-m-d') }}">
            </div>
            <div class="input-group">
              <label for="event-end">Tanggal Selesai</label>
              <input type="date" name="end_date" id="event-end" class="input" min="{{ date('Y-m-d') }}">
            </div>
          </div>
          <div class="grid-2 mt-4">
            <div class="input-group">
              <label for="event-time-start">Jam Mulai</label>
              <input type="time" name="start_time" id="event-time-start" class="input">
            </div>
            <div class="input-group">
              <label for="event-time-end">Jam Selesai</label>
              <input type="time" name="end_time" id="event-time-end" class="input">
            </div>
          </div>
        </div>

        <!-- Kandidat -->
        <div class="form-section">
          <div class="form-section__title">
            <div class="form-section__title-icon">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            Kandidat
          </div>
          <div class="input-group">
            <label>Tambah Kandidat</label>
            <div class="flex gap-2">
              <input type="text" id="candidate-input" class="input" placeholder="Nama kandidat">
              <button type="button" onclick="addCandidate()" class="btn btn--primary" style="white-space: nowrap;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
                Tambah
              </button>
            </div>
          </div>
          <div class="flex gap-2">
          <div class="mt-4">
              <!-- LIST -->
            <div id="candidate-list"></div>
          </div>
          </div>
        </div>

        <!-- Kategori Penilaian -->
        <input type="hidden" name="categories_data" id="categories-data">
        <div class="form-section">
          <div class="form-section__title">
            <div class="form-section__title-icon">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20V10"/><path d="M18 20V4"/><path d="M6 20v-4"/></svg>
            </div>
            Kategori Penilaian
            <label class="toggle" style="margin-left: auto;">
              <!-- TOGGLE -->
              <input type="hidden" name="categories_enabled" value="0">
              <input type="checkbox" class="toggle__input" id="toggle-kategori" name="categories_enabled" value="1">
              <span class="toggle__slider"></span>
            </label>
          </div>
          <div id="kategori-content" style="display: none;">
            <div class="input-group">
              <label>Tambah Kategori</label>
              <div class="flex gap-2">
                <input type="text" id="kategori-nama" class="input" placeholder="Nama kategori">
                <input type="number" id="kategori-bobot" class="input" placeholder="Bobot %" style="max-width: 100px;">

                <button type="button" onclick="tambahKategori()" class="btn btn--primary" style="white-space: nowrap;">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
                </button>
              </div>
            </div>

          <div class="flex gap-2">
          <div class="mt-4">
              <!-- LIST -->
            <div id="kategori-list"></div>
          </div>
          </div>



            <div class="mt-3 p-4" id="kategori-status" style="background: var(--success-50); border-radius: var(--radius-lg); border: 1px solid var(--success-100);">
            <div id="kategori-status-text" class="flex items-center gap-2 text-sm" style="color: var(--success-600);">
            </div>
            </div>
          </div>

        <div id="kategori-disabled" class="mt-3 p-4" style="background: var(--card-muted-bg); border-radius: var(--radius-lg);">
          <p class="text-sm" style="color: var(--card-muted-text);">Sistem hanya akan menghitung jumlah vote tanpa kategori penilaian.</p>
        </div>
        </div>

        <!-- Sistem Voting -->
        <div class="form-section">
          <div class="form-section__title">
            <div class="form-section__title-icon">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            </div>
            Sistem Voting
          </div>
          <div class="input-group">
            <label>Tipe Voting</label>
            <div class="flex gap-3 mt-2">
              <label class="flex items-center gap-2 p-3" style="background: var(--bg-input); border-radius: var(--radius-lg); border: 2px solid var(--primary-500); cursor: pointer; flex: 1;">
                <input type="radio" name="voting_type" value="public" checked style="accent-color: var(--primary-500);">
                <span class="text-sm font-medium">Publik</span>
              </label>
              <label class="flex items-center gap-2 p-3" style="background: var(--bg-input); border-radius: var(--radius-lg); border: 2px solid transparent; cursor: pointer; flex: 1;">
                <input type="radio" name="voting_type" value="private" style="accent-color: var(--primary-500);">
                <span class="text-sm font-medium">Private</span>
              </label>
            </div>
          </div>
          <div id="private-options" class="mt-4" style="display: none;">

            <!-- Form Tambah Peserta -->
            <div class="p-4" style="background: var(--bg-input); border-radius: var(--radius-lg); border: 1px solid var(--border-color);">
              <div class="grid-2">
                <div class="input-group" style="margin-bottom: 0;">
                  <label for="voter-email">Email Peserta</label>
                  <input type="email" id="voter-email" name="voter_email[]" class="input" placeholder="email@contoh.com">
                </div>
                <div class="input-group" style="margin-bottom: 0;">
                  <label for="voter-phone">No. WA</label>
                  <input type="tel" id="voter-phone" name="voter_phone[]" class="input" placeholder="0812xxxxxxx">
                </div>
              </div>
              <button type="button" onclick="addParticipant()" class="btn btn--primary w-full" style="margin-top: var(--space-3);">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
                Tambah Peserta
              </button>
            </div>

            <!-- Daftar Peserta -->
            <div class="mt-4">
            <div id="participant-list" class="mt-3" style="display: flex; flex-direction: column; gap: var(--space-2);"></div>
            </div>
          </div>
        </div>

        <!-- Metode Penilaian -->
        <div class="form-section">
          <div class="form-section__title">
            <div class="form-section__title-icon">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            </div>
            Metode Penilaian
          </div>

          <!-- Juara Harapan -->
          <div class="flex items-center justify-between p-4" style="background: var(--bg-input); border-radius: var(--radius-lg); margin-bottom: var(--space-3);">
            <div class="flex items-center gap-3">
              <div style="width: 36px; height: 36px; border-radius: var(--radius-md); background: linear-gradient(135deg, var(--warning-100), var(--error-100)); display: flex; align-items: center; justify-content: center; color: var(--warning-600);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg>
              </div>
              <div>
                <div class="font-medium">Juara Harapan</div>
                <div class="text-xs text-muted">Syarat: kandidat > 4</div>
              </div>
            </div>
            <label class="toggle">
              <input type="hidden" name="juara_harapan_enabled" value="0">
              <input type="checkbox" class="toggle__input" id="toggle-juara" name="juara_harapan_enabled" value="1">
              <span class="toggle__slider"></span>
            </label>
          </div>
          <div id="juara-options" class="mb-4" style="display: none; padding-left: var(--space-4);">
            <div class="input-group">
              <label>Jumlah Juara Harapan</label>
              <select class="input" style="max-width: 200px;" name="juara_harapan_count"></select>
              
            </div>
          </div>

          <!-- Pemenang Kategori -->
          <div class="flex items-center justify-between p-4" style="background: var(--bg-input); border-radius: var(--radius-lg);">
            <div class="flex items-center gap-3">
              <div style="width: 36px; height: 36px; border-radius: var(--radius-md); background: linear-gradient(135deg, var(--primary-100), var(--accent-100)); display: flex; align-items: center; justify-content: center; color: var(--primary-600);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20V10"/><path d="M18 20V4"/><path d="M6 20v-4"/></svg>
              </div>
              <div>
                <div class="font-medium">Pemenang Kategori</div>
                <div class="text-xs text-muted">Aktif jika kategori penilaian aktif</div>
              </div>
            </div>
            <label class="toggle">
              <input type="hidden" name="pemenang_kategori_enabled" value="0">
              <input type="checkbox" class="toggle__input" id="toggle-pemenang" name="pemenang_kategori_enabled" value="1">
              <span class="toggle__slider"></span>
            </label>
          </div>
          <div id="pemenang-options" class="mt-3" style="display: none; padding-left: var(--space-4);">
            <div class="input-group">
              <label>Kategori Penentu</label>
              <select class="input" style="max-width: 300px;" name="determining_category_id"></select>
              
            </div>
          </div>
        </div>

<div class="flex gap-3 mt-6 mb-8">
  <a href="{{ route('home') }}" class="btn btn--secondary flex-1">Batal</a>
  <button type="submit" id="btn-submit" class="btn btn--primary flex-1">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
      <polyline points="17 21 17 13 7 13 7 21"/>
      <polyline points="7 3 7 8 15 8"/>
    </svg>
    Buat Event
</button>
</div>
      </form>
    </div>
  </main>
  <script>

function toggleTheme() {
      const html = document.documentElement;
      const current = html.getAttribute('data-theme');
      html.setAttribute('data-theme', current === 'dark' ? 'light' : 'dark');
      localStorage.setItem('theme', current === 'dark' ? 'light' : 'dark');
    }
    const saved = localStorage.getItem('theme');
    if (saved) document.documentElement.setAttribute('data-theme', saved);

let candidates = [];
let editIndex = null;

function addCandidate() {
    const input = document.getElementById('candidate-input');
    const name = input.value.trim();

    if (!name) {
        alert('Nama kandidat wajib diisi');
        return;
    }

    if (editIndex !== null) {
        // MODE EDIT
        candidates[editIndex] = name;
        editIndex = null;
    } else {
        // MODE TAMBAH
        candidates.push(name);
    }

    input.value = '';
    renderCandidates();
}

function showImagePreview(file) {
    const imagePreviewImg = document.getElementById('image-preview-img');
    const imagePreview = document.getElementById('image-preview');
    const imagePlaceholder = document.getElementById('image-placeholder');
    const imageUpload = document.getElementById('image-upload');

    const reader = new FileReader();
    reader.onload = (e) => {
        imagePreviewImg.src = e.target.result;
        imagePreview.style.display = 'flex';
        imagePlaceholder.style.display = 'none';
        imageUpload.classList.add('image-upload--filled');
    };
    reader.readAsDataURL(file);
}

function updateJuaraHarapanOptions() {
    const select = document.querySelector('select[name="juara_harapan_count"]');

    if (!select) return;

    const currentValue = select.value;

    select.innerHTML = '<option value="">-- Tentukan jumlah juara harapan --</option>';

    const total = candidates.length;

    if (total <= 4) {
        select.innerHTML = '<option value="">Minimal 5 kandidat</option>';
        select.disabled = true;
        return;
    }

    select.disabled = false;

    const maxJuara = total - 3;

    for (let i = 1; i <= maxJuara; i++) {
        select.innerHTML += `<option value="${i}">${i} Juara</option>`;
    }

    if (currentValue && currentValue <= maxJuara) {
        select.value = currentValue;
    }
}

function controlJuaraHarapanToggle() {
    const toggle = document.getElementById('toggle-juara');
    const label = toggle.closest('.toggle');
    const select = document.querySelector('select[name="juara_harapan_count"]');
    const optionsBox = document.getElementById('juara-options');

    if (candidates.length <= 4) {
        // ❌ disable semua
        toggle.checked = false;
        toggle.disabled = true;

        if (label) {
            label.style.opacity = '0.5';
            label.style.cursor = 'not-allowed';
        }

        // 🔥 TAMBAHAN PENTING
        if (select) {
            select.value = '';
            select.disabled = true;
        }

        if (optionsBox) {
            optionsBox.style.display = 'none';
        }

    } else {
        // ✅ aktifkan lagi
        toggle.disabled = false;

        if (label) {
            label.style.opacity = '1';
            label.style.cursor = 'pointer';
        }

        if (select) {
            select.disabled = false;
        }
    }
}
// RENDER LIST
function renderCandidates() {
  updateJuaraHarapanOptions();
  controlJuaraHarapanToggle();
    const container = document.getElementById('candidate-list');
    container.innerHTML = '';

    candidates.forEach((name, index) => {
        container.innerHTML += `
        <div class="flex gap-2">
          <div class="dynamic-field">

            <input type="text" class="dynamic-field__input" value="${name}" readonly>

            <!-- kirim ke Laravel -->
            <input type="hidden" name="candidates[]" value="${name}">

            <!-- EDIT -->
            <button type="button" onclick="editCandidate(${index})" class="dynamic-field__remove btn btn--ghost btn--icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
              </button>

            <!-- HAPUS -->
            <button type="button" onclick="removeCandidate(${index})" class="dynamic-field__remove btn btn--ghost btn--icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
              </button>

        </div>
        </div>
        `;
    });
}

// EDIT
function editCandidate(index) {
    const input = document.getElementById('candidate-input');

    input.value = candidates[index];
    editIndex = index;
}

// HAPUS
function removeCandidate(index) {
    candidates.splice(index, 1);
    renderCandidates();
}

let kategoriList = [];

function tambahKategori() {
    const nama = document.getElementById('kategori-nama').value.trim();
    const bobot = parseInt(document.getElementById('kategori-bobot').value);

    if (!nama || isNaN(bobot)) {
        alert('Isi nama dan bobot!');
        return;
    }

    kategoriList.push({
        name: nama,
        weight: bobot
    });

    document.getElementById('kategori-nama').value = '';
    document.getElementById('kategori-bobot').value = '';
    renderKategori();

}

function hapusKategori(index) {
    kategoriList.splice(index, 1);
    renderKategori();

}

function updateKategoriPenentu() {
    const select = document.querySelector('select[name="determining_category_id"]');

    if (!select) return;

    const currentValue = select.value;

    select.innerHTML = '<option value="">-- Pilih kategori --</option>';

    kategoriList.forEach((item, index) => {
        select.innerHTML += `
            <option value="${index}">
                ${item.name} (${item.weight}%)
            </option>
        `;
    });

    if (currentValue && kategoriList[currentValue]) {
        select.value = currentValue;
    }
}

function renderKategori() {
    const container = document.getElementById('kategori-list');
    const statusText = document.getElementById('kategori-status-text');
    const submitBtn = document.getElementById('btn-submit');
    const toggleKategori = document.getElementById('toggle-kategori');

    container.innerHTML = '';
    document.getElementById('categories-data').value = JSON.stringify(kategoriList);

    if (!toggleKategori.checked) {
        submitBtn.disabled = false;
        return;
    }

    let total = 0;

    kategoriList.forEach((item, index) => {
        total += item.weight;

        container.innerHTML += `
        <div class="flex gap-2">
          <div class="dynamic-field">

            <!-- Nama kategori -->
            <input type="text" 
                  class="dynamic-field__input" 
                  value="${item.name}" 
                  readonly>

            <!-- Bobot -->
            <input type="text" 
                  class="dynamic-field__input" 
                  value="${item.weight}%" 
                  style="max-width:70px; flex:0 0 70px;" 
                  readonly>

            <!-- Hidden untuk backend -->
            <input type="hidden" name="categories[]" value='${JSON.stringify(item)}'>

            <!-- Hapus -->
            <button type="button" 
                    onclick="hapusKategori(${index})" 
                    class="dynamic-field__remove btn btn--ghost btn--icon">
                ❌
            </button>

          </div>
        </div>
        `;
    });

    updateKategoriPenentu();

    // VALIDASI
    if (total > 100) {
        statusText.innerHTML = `Total bobot: ${total}% (Kelebihan!)`;
        statusText.style.color = 'red';
        submitBtn.disabled = true;
        return;
    }

    if (total < 100) {
        statusText.innerHTML = `Total bobot: ${total}% (Kurang!)`;
        statusText.style.color = 'orange';
        submitBtn.disabled = true;
        return;
    }

    statusText.innerHTML = `Total bobot: 100% (Valid ✅)`;
    statusText.style.color = 'green';

    submitBtn.disabled = false;

    controlPemenangToggle();
}

function isKategoriValid() {
    if (kategoriList.length === 0) return false;

    let total = 0;
    kategoriList.forEach(item => total += item.weight);

    return total === 100;
}

function controlPemenangToggle() {
    const togglePemenang = document.getElementById('toggle-pemenang');
    const toggleKategori = document.getElementById('toggle-kategori');

    if (!togglePemenang || !toggleKategori) return;

    const label = togglePemenang.closest('.toggle');

    const kategoriAktif = toggleKategori.checked;
    const kategoriValid = isKategoriValid();

    if (!kategoriAktif || !kategoriValid) {
        togglePemenang.checked = false;
        togglePemenang.disabled = true;

        if (label) {
            label.style.opacity = '0.5';
            label.style.cursor = 'not-allowed';
        }
    } else {
        togglePemenang.disabled = false;

        if (label) {
            label.style.opacity = '1';
            label.style.cursor = 'pointer';
        }
    }
}

let participants = [];

function generateCode() {
    return Math.random().toString(36).substring(2, 8).toUpperCase();
}

function addParticipant() {
    const email = document.getElementById('voter-email').value;
    const phone = document.getElementById('voter-phone').value;

    if (!email.trim() && !phone.trim()) {
        alert('Isi minimal email atau nomor HP');
        return;
    }

    participants.push({
        email,
        phone,
        code: generateCode()
    });

    renderParticipants();

    document.getElementById('voter-email').value = '';
    document.getElementById('voter-phone').value = '';
}

function renderParticipants() {
    const container = document.getElementById('participant-list');
    container.innerHTML = '';

    participants.forEach((p, index) => {
        container.innerHTML += `
        <div class="dynamic-field">
            <input class="dynamic-field__input" value="${p.email || ''} ${p.phone ? '(' + p.phone + ')' : ''}" readonly>
            <input class="dynamic-field__input" value="${p.code}" readonly>

            <input type="hidden" name="participants[]" value='${JSON.stringify(p)}'>

            <button type="button" onclick="removeParticipant(${index})">❌</button>
        </div>
        `;
    });
}

function removeParticipant(index) {
    participants.splice(index, 1);
    renderParticipants();
}

    // Fungsi validasi Waktu (Jam)
document.addEventListener('DOMContentLoaded', function () {

    const startInput = document.getElementById('event-start');
    const endInput = document.getElementById('event-end');
    const startTimeInput = document.getElementById('event-time-start');
    const endTimeInput = document.getElementById('event-time-end');

    /*
    |--------------------------------------------------------------------------
    | Helper: tanggal hari ini
    |--------------------------------------------------------------------------
    */
    function getToday() {
        const now = new Date();

        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');

        return `${year}-${month}-${day}`;
    }

    /*
    |--------------------------------------------------------------------------
    | Helper: waktu sekarang dalam format HH:mm
    |--------------------------------------------------------------------------
    */
    function getCurrentTime() {
        const now = new Date();

        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');

        return `${hours}:${minutes}`;
    }

    /*
    |--------------------------------------------------------------------------
    | Update minimum jam mulai
    |--------------------------------------------------------------------------
    */
    function updateStartTimeMin() {

        if (!startInput.value) {
            startTimeInput.min = '';
            return;
        }

        const today = getToday();

        // Jika tanggal mulai adalah hari ini
        if (startInput.value === today) {

            const currentTime = getCurrentTime();

            startTimeInput.min = currentTime;

            // Jika jam yang sudah dipilih ternyata sudah lewat
            if (
                startTimeInput.value &&
                startTimeInput.value < currentTime
            ) {
                startTimeInput.value = '';
            }

        } else {

            // Jika bukan hari ini, tidak ada batas waktu sekarang
            startTimeInput.min = '';
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Update minimum jam selesai
    |--------------------------------------------------------------------------
    */
    function updateEndTimeMin() {

        if (!endInput.value) {
            endTimeInput.min = '';
            return;
        }

        const today = getToday();

        /*
        |--------------------------------------------------------------------------
        | Jika tanggal selesai adalah hari ini
        |--------------------------------------------------------------------------
        */
        if (endInput.value === today) {

            const currentTime = getCurrentTime();

            endTimeInput.min = currentTime;

            if (
                endTimeInput.value &&
                endTimeInput.value < currentTime
            ) {
                endTimeInput.value = '';
            }

        } else {

            endTimeInput.min = '';
        }

        /*
        |--------------------------------------------------------------------------
        | Jika tanggal mulai dan selesai sama
        |--------------------------------------------------------------------------
        */
        if (
            startInput.value &&
            endInput.value &&
            startInput.value === endInput.value &&
            startTimeInput.value
        ) {

            endTimeInput.min = startTimeInput.value;

            /*
            | Jika jam selesai <= jam mulai
            */
            if (
                endTimeInput.value &&
                endTimeInput.value <= startTimeInput.value
            ) {
                endTimeInput.value = '';
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Validasi lengkap tanggal + waktu
    |--------------------------------------------------------------------------
    */
    function validateDateTime(showAlert = false) {

        const startDate = startInput.value;
        const endDate = endInput.value;
        const startTime = startTimeInput.value;
        const endTime = endTimeInput.value;

        const today = getToday();
        const currentTime = getCurrentTime();

        /*
        |--------------------------------------------------------------------------
        | Tanggal selesai tidak boleh sebelum tanggal mulai
        |--------------------------------------------------------------------------
        */
        if (startDate && endDate && endDate < startDate) {

            if (showAlert) {
                alert('Tanggal selesai tidak boleh sebelum tanggal mulai.');
            }

            endInput.value = startDate;

            updateEndTimeMin();

            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | Jika tanggal mulai = hari ini
        |--------------------------------------------------------------------------
        */
        if (
            startDate === today &&
            startTime &&
            startTime < currentTime
        ) {

            if (showAlert) {
                alert('Jam mulai tidak boleh kurang dari waktu sekarang.');
            }

            startTimeInput.value = '';

            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | Jika tanggal selesai = hari ini
        |--------------------------------------------------------------------------
        */
        if (
            endDate === today &&
            endTime &&
            endTime < currentTime
        ) {

            if (showAlert) {
                alert('Jam selesai tidak boleh kurang dari waktu sekarang.');
            }

            endTimeInput.value = '';

            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | Jika event berlangsung di hari yang sama
        |--------------------------------------------------------------------------
        */
        if (
            startDate &&
            endDate &&
            startDate === endDate &&
            startTime &&
            endTime
        ) {

            /*
            | Jam selesai harus lebih besar dari jam mulai.
            |
            | Contoh:
            | 10:00 -> 12:00 ✅
            | 10:00 -> 10:00 ❌
            | 10:00 -> 09:00 ❌
            |
            | Dengan aturan ini event tidak bisa "melewati 00:00"
            | pada tanggal yang sama.
            */
            if (endTime <= startTime) {

                if (showAlert) {
                    alert(
                        'Jika tanggal mulai dan selesai sama, jam selesai harus lebih besar dari jam mulai.'
                    );
                }

                endTimeInput.value = '';

                return false;
            }
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Saat tanggal mulai berubah
    |--------------------------------------------------------------------------
    */
    startInput.addEventListener('change', function () {

        const today = getToday();

        /*
        | Tanggal mulai tidak boleh sebelum hari ini
        */
        if (this.value < today) {

            alert('Tanggal mulai tidak boleh sebelum hari ini.');

            this.value = today;
        }

        /*
        | Tanggal selesai minimal sama dengan tanggal mulai
        */
        endInput.min = this.value;

        /*
        | Jika tanggal selesai sebelumnya lebih kecil
        */
        if (
            endInput.value &&
            endInput.value < this.value
        ) {
            endInput.value = this.value;
        }

        updateStartTimeMin();
        updateEndTimeMin();

        validateDateTime(false);
    });

    /*
    |--------------------------------------------------------------------------
    | Saat tanggal selesai berubah
    |--------------------------------------------------------------------------
    */
    endInput.addEventListener('change', function () {

        const today = getToday();

        /*
        | Tanggal selesai tidak boleh sebelum hari ini
        */
        if (this.value < today) {

            alert('Tanggal selesai tidak boleh sebelum hari ini.');

            this.value = today;
        }

        /*
        | Tidak boleh sebelum tanggal mulai
        */
        if (
            startInput.value &&
            this.value < startInput.value
        ) {

            alert('Tanggal selesai tidak boleh sebelum tanggal mulai.');

            this.value = startInput.value;
        }

        updateEndTimeMin();

        validateDateTime(false);
    });

    /*
    |--------------------------------------------------------------------------
    | Saat jam mulai berubah
    |--------------------------------------------------------------------------
    */
    startTimeInput.addEventListener('change', function () {

        updateEndTimeMin();

        validateDateTime(true);
    });

    /*
    |--------------------------------------------------------------------------
    | Saat jam selesai berubah
    |--------------------------------------------------------------------------
    */
    endTimeInput.addEventListener('change', function () {

        validateDateTime(true);
    });

    /*
    |--------------------------------------------------------------------------
    | Validasi ketika form disubmit
    |--------------------------------------------------------------------------
    */
    const form = document.querySelector('form');

    form.addEventListener('submit', function (e) {

        if (!validateDateTime(true)) {
            e.preventDefault();
            return;
        }

    });

    /*
    |--------------------------------------------------------------------------
    | Inisialisasi
    |--------------------------------------------------------------------------
    */
    endInput.min = startInput.value || getToday();

    updateStartTimeMin();
    updateEndTimeMin();

    /*
    |--------------------------------------------------------------------------
    | Update waktu setiap menit
    |
    | Ini berguna jika user membuka halaman cukup lama.
    | Misalnya sekarang 22:29, 1 menit kemudian menjadi 22:30.
    |--------------------------------------------------------------------------
    */
    setInterval(function () {

        updateStartTimeMin();
        updateEndTimeMin();

    }, 60000);

    
document.getElementById('btn-submit').disabled = false;
document.getElementById('toggle-kategori').addEventListener('change', function () {
    const content = document.getElementById('kategori-content');
    const disabledBox = document.getElementById('kategori-disabled');
    const submitBtn = document.getElementById('btn-submit');

    if (this.checked) {
        content.style.display = 'block';
        disabledBox.style.display = 'none';
    renderKategori();

    } else {
        content.style.display = 'none';
        disabledBox.style.display = 'block';

        kategoriList = [];
        document.getElementById('kategori-list').innerHTML = '';
        document.getElementById('categories-data').value = '';

        submitBtn.disabled = false;
    }

    controlPemenangToggle();
});

        // Toggle juara harapan
    document.getElementById('toggle-juara').addEventListener('change', function() {

    if (this.disabled) {
        this.checked = false;
        return;
    }

    document.getElementById('juara-options').style.display = this.checked ? 'block' : 'none';
    });

        // Toggle pemenang kategori
document.getElementById('toggle-pemenang').addEventListener('change', function() {

    if (this.disabled) {
        this.checked = false;
        return;
    }

    document.getElementById('pemenang-options').style.display = this.checked ? 'block' : 'none';

    });

        // Toggle private
    document.querySelectorAll('input[name="voting_type"]').forEach(radio => {
      radio.addEventListener('change', function() {
        document.getElementById('private-options').style.display = this.value === 'private' ? 'block' : 'none';
      });
    });

    const imageUpload = document.getElementById('image-upload');
    const imageInput = document.getElementById('event-image');
    const imagePlaceholder = document.getElementById('image-placeholder');
    const imagePreview = document.getElementById('image-preview');
    const imagePreviewImg = document.getElementById('image-preview-img');
    const imageRemoveBtn = document.getElementById('image-remove');

    imageUpload.addEventListener('click', (e) => {
      if (e.target.closest('.image-upload__remove')) return;
      imageInput.click();
    });

    imageInput.addEventListener('change', (e) => {
      const file = e.target.files[0];
      if (file) showImagePreview(file);
    });

    imageUpload.addEventListener('dragover', (e) => {
      e.preventDefault();
      imageUpload.classList.add('dragover');
    });

    imageUpload.addEventListener('dragleave', () => {
      imageUpload.classList.remove('dragover');
    });

    imageUpload.addEventListener('drop', (e) => {
      e.preventDefault();
      imageUpload.classList.remove('dragover');
      const file = e.dataTransfer.files[0];
      if (file && file.type.startsWith('image/')) {
        imageInput.files = e.dataTransfer.files;
        showImagePreview(file);
      }
    });

    imageRemoveBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      imageInput.value = '';
      imagePreviewImg.src = '';
      imagePreview.style.display = 'none';
      imagePlaceholder.style.display = 'flex';
      imageUpload.classList.remove('image-upload--filled');
    });

    document.getElementById('btn-submit').addEventListener('click', function(e) {
    const pemenang = document.getElementById('toggle-pemenang').checked;
    const kategori = document.getElementById('toggle-kategori').checked;
    const select = document.querySelector('select[name="determining_category_id"]');

    if (pemenang && (!kategori || !select.value)) {
        e.preventDefault();
        alert('Kategori penentu wajib dipilih');
    }
});
    renderCandidates();
    controlPemenangToggle();
});
  </script>


</body>
</html>

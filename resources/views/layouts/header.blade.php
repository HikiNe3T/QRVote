<header class="header">
  <div class="container header__inner">

    <!-- LOGO -->
    <a href="{{ route('home') }}" class="header__logo">VoteQR</a>

    <div class="header__actions">

      <!-- THEME TOGGLE -->
      <button class="theme-toggle" onclick="toggleTheme()" title="Ganti Tema">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="5"/>
          <path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>
        </svg>
      </button>

      <!-- CEK LOGIN -->
      @auth

        <!-- LINK PROFILE -->
        <a href="{{ route('edit.profile') }}">

          <!-- AVATAR DINAMIS -->
          <img 
            src="{{ auth()->user()->avatar_url 
                    ? asset('storage/avatars/' . auth()->user()->avatar_url) 
                    : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->full_name) }}" 
            alt="Avatar" 
            class="avatar"
            style="cursor:pointer;"
          >
        </a>

      @else

        <!-- BUTTON LOGIN -->
        <a href="{{ route('login') }}" 
          class="btn btn--primary" 
          style="padding: var(--space-2) var(--space-4); font-size: var(--font-size-sm);">
          Masuk
        </a>

      @endauth

    </div>
  </div>
</header>
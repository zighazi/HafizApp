<nav class="navbar navbar-expand-lg bg-light border-bottom sticky-top">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
      <img src="{{ asset('images/logo-sekolah.png') }}" alt="Logo Sekolah">
      <span class="fw-semibold">HafizApp</span>
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topNav"
            aria-controls="topNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="topNav">
      <ul class="navbar-nav ms-auto align-items-lg-center">

        <li class="nav-item">
  <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
    Beranda
  </a>
</li>

<li><a class="dropdown-item" href="{{ route('rekap.kelas.bulanan') }}">Rekap Kelas Bulanan</a></li>

        {{-- Dropdown Hafalan --}}
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle {{ request()->routeIs('hafalans.*') || request()->routeIs('rekap.*') || request()->routeIs('rekap-kelas.index') ? 'active' : '' }}"
             href="#" id="menuHafalan" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Hafalan
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="menuHafalan">
            <li>
              <a class="dropdown-item {{ request()->routeIs('hafalans.index') ? 'active' : '' }}"
                 href="{{ route('hafalans.index') }}">
                Daftar Hafalan
              </a>
            </li>
            <li>
              <a class="dropdown-item {{ request()->routeIs('hafalans.create') ? 'active' : '' }}"
                 href="{{ route('hafalans.create') }}">
                Tambah Hafalan
              </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <a class="dropdown-item {{ request()->routeIs('rekap.*') || request()->routeIs('rekap-kelas.index') ? 'active' : '' }}"
                 href="{{ route('rekap.kelas') }}">
                Rekap Kelas
              </a>
            </li>
          </ul>
        </li>

        {{-- Import Santri --}}
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('santris.import.form') ? 'active' : '' }}"
             href="{{ route('santris.import.form') }}">
            Import Santri
          </a>
        </li>

      </ul>
    </div>
  </div>
</nav>
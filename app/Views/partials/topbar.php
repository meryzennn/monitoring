<nav class="navbar app-topbar sticky-top">
  <div class="container-fluid">
    <!-- Toggler mobile -->
    <button class="btn btn-topbar d-md-none" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar" aria-controls="offcanvasSidebar">
      <i class="bi bi-list"></i>
    </button>

    <!-- Judul halaman -->
    <span class="navbar-brand ms-2"><?= esc($title ?? 'Dashboard') ?></span>

    <!-- Aksi kanan -->
    <div class="d-flex align-items-center gap-2">
      <div class="dropdown">
        <button class="btn btn-topbar position-relative" data-bs-toggle="dropdown" aria-expanded="false" id="notifBell">
          <i class="bi bi-bell fs-5"></i>
          <span id="notifCount" class="position-absolute top-0 start-100 badge rounded-pill bg-danger d-none">0</span>
        </button>
        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-notif" id="notifList" style="min-width:320px;">
          <li class="dropdown-header">Laporan Masuk</li>
          <li><hr class="dropdown-divider"></li>
          <li class="px-3 text-muted small">Tidak ada laporan baru.</li>
        </ul>
      </div>
    </div>
  </div>
</nav>

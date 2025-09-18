<?php
$active    = $activeMenu ?? '';
$showBrand = isset($showBrand) ? (bool) $showBrand : true;
$brandLogo = $brandLogo ?? base_url('assets/img/logo-kementerian.svg'); // fallback
?>
<nav class="sidebar-nav w-100">
  <?php if ($showBrand): ?>
    <div class="sidebar-brand d-flex align-items-center gap-2 p-3">
      <img src="<?= $brandLogo ?>" class="brand-img" alt="Logo">
      <strong class="brand-text">BRBIH</strong>
    </div>
  <?php endif; ?>

  <ul class="nav flex-column pb-3">
    <li class="nav-item">
      <a class="nav-link <?= $active==='dashboard'?'active':'' ?>" href="<?= base_url('dashboard') ?>">
        <i class="bi bi-speedometer2 me-2"></i>Dashboard
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link" href="<?= base_url('data-user') ?>">
        <i class="bi bi-people me-2"></i>Data User
      </a>
    </li>

    <li class="nav-item">
    <a class="nav-link <?= $active==='data-kendala'?'active':'' ?>" href="<?= base_url('data_kendala') ?>">
        <i class="bi bi-exclamation-triangle me-2"></i>Data Kendala
      </a>
    </li>

    <li class="nav-item">
      <button class="nav-link d-flex justify-content-between align-items-center w-100 text-start"
              data-bs-toggle="collapse" data-bs-target="#menuAlat" aria-expanded="false" aria-controls="menuAlat">
        <span><i class="bi bi-hdd-network me-2"></i>Data Alat</span>
        <i class="bi bi-chevron-down small sidebar-caret"></i>
      </button>
      <div class="collapse" id="menuAlat">
        <ul class="nav flex-column ms-4 border-start">
          <li><a class="nav-link" href="<?= base_url('alat/ac') ?>">AC</a></li>
          <li><a class="nav-link" href="<?= base_url('alat/kendaraan') ?>">Kendaraan</a></li>
        </ul>
      </div>
    </li>

    <li class="nav-item">
      <a class="nav-link" href="<?= base_url('laporan') ?>"><i class="bi bi-file-earmark-text me-2"></i>Laporan</a>
    </li>

    <li class="nav-item">
      <a class="nav-link" href="<?= base_url('pengaturan') ?>"><i class="bi bi-gear me-2"></i>Pengaturan</a>
    </li>

    <li class="nav-item mt-auto">
      <a class="nav-link text-danger" href="<?= base_url('logout') ?>"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
    </li>
  </ul>
</nav>

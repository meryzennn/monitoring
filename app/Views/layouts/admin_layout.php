<?php helper('url'); ?>
<?php $brandLogo = base_url('assets/img/logo-kementerian.svg'); ?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= esc($title ?? 'BRBIH') ?></title>

  <meta name="base-url" content="<?= rtrim(base_url('/'), '/') ?>/">

  <!-- Vendor CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

  <!-- Global CSS -->
  <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>?v=1.2.3">

  <!-- Page-level CSS -->
  <?= $this->renderSection('styles') ?>
</head>
<body>

<div class="app-wrapper d-flex">
  <!-- Sidebar desktop -->
  <aside class="app-sidebar d-none d-md-flex flex-column">
    <?= $this->include('partials/sidebar', [
      'showBrand'  => true,
      'activeMenu' => $activeMenu ?? '',
      'brandLogo'  => $brandLogo
    ]) ?>
  </aside>

  <!-- Offcanvas mobile (hanya muncul di < md) -->
  <div class="offcanvas offcanvas-start shadow d-md-none" tabindex="-1" id="offcanvasSidebar" aria-labelledby="offcanvasSidebarLabel">
    <div class="offcanvas-header py-3">
      <div class="d-flex align-items-center gap-2">
        <img src="<?= $brandLogo ?>" class="brand-img-sm" alt="Logo">
        <strong id="offcanvasSidebarLabel" class="brand-text">BRBIH</strong>
      </div>
      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
    </div>
    <div class="offcanvas-body p-0">
      <!-- Penting: brand di body dimatikan supaya tidak dobel -->
      <?= $this->include('partials/sidebar', [
        'showBrand'  => false,
        'activeMenu' => $activeMenu ?? '',
        'brandLogo'  => $brandLogo
      ]) ?>
    </div>
  </div>

  <!-- Main content -->
  <main class="app-content flex-grow-1 min-vh-100">
    <?= $this->include('partials/topbar', ['title' => $title ?? 'Dashboard']) ?>

    <div class="container-fluid py-3">
      <?= $this->renderSection('content') ?>
    </div>
  </main>
</div>

<!-- Vendor JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Global JS -->
<script src="<?= base_url('assets/js/app.js') ?>?v=1.2.3"></script>

<!-- Page-level JS -->
<?= $this->renderSection('scripts') ?>
</body>
</html>

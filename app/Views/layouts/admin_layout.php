<?php helper('url'); ?>
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
  <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">

  <!-- Page-level CSS -->
  <?= $this->renderSection('styles') ?>
</head>
<body>

<div class="app-wrapper d-flex">
  <!-- Sidebar desktop -->
  <aside class="app-sidebar d-none d-md-flex flex-column">
    <?= $this->include('partials/sidebar', ['renderHeader' => true]) ?>
  </aside>

  <!-- Offcanvas mobile -->
<div class="offcanvas offcanvas-start shadow" tabindex="-1" id="offcanvasSidebar" aria-labelledby="offcanvasSidebarLabel">
  <!-- HAPUS border-bottom biar ga ada garis putih -->
  <div class="offcanvas-header py-3">
    <div class="d-flex align-items-center gap-2">
      <img src="<?= base_url('assets/img/logo.png') ?>" alt="Logo" height="24" class="rounded">
      <strong id="offcanvasSidebarLabel" class="text-white">BRBIH</strong>
    </div>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
  </div>
  <div class="offcanvas-body p-0">
    <!-- PENTING: jangan render brand di body offcanvas -->
    <?= $this->include('partials/sidebar', ['showBrand' => false]) ?>
  </div>
</div>

<!-- Sidebar desktop -->


  <!-- Main content -->
  <main class="app-content flex-grow-1 min-vh-100">
    <?= $this->include('partials/topbar') ?>

    <div class="container-fluid py-3">
      <?= $this->renderSection('content') ?>
    </div>
  </main>
</div>

<!-- Vendor JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Global JS -->
<script src="<?= base_url('assets/js/app.js') ?>"></script>

<!-- Page-level JS -->
<?= $this->renderSection('scripts') ?>
</body>
</html>

<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Login</title>

  <!-- (opsional) font -->
  <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">

  <!-- CSS eksternal -->
  <link rel="stylesheet" href="<?= base_url('assets/css/auth-admin.css') ?>?v=1.0.0">
</head>
<body>

  <!-- Background jaringan titik–garis -->
  <canvas id="bg-net" data-mode="repulse"></canvas>

  <div class="auth-wrap">
    <div class="auth-card">
      <img class="auth-logo" src="<?= base_url('assets/img/logo-kementerian.svg') ?>" alt="Logo">
      <h2 class="auth-title">Selamat Datang di Website<br>Monitoring Alat</h2>

      <form method="post" action="<?= base_url('auth/do') ?>">
        <!-- <?= function_exists('csrf_field') ? csrf_field() : '' ?> -->
        <div class="mb-3">
          <label for="username">Username</label>
          <input class="form-control" type="text" id="username" name="username" placeholder="username" required autofocus>
        </div>
        <div class="mb-4">
          <label for="password">Password</label>
          <div class="input-groupish">
            <input class="form-control" type="password" id="password" name="password" placeholder="Your password" required>
            <span id="togglePwd" class="password-toggle" title="Show/Hide">👁</span>
          </div>
        </div>
        <button type="submit" class="btn">Masuk</button>
      </form>
    </div>
  </div>

  <!-- JS eksternal -->
  <script src="<?= base_url('assets/js-admin/auth.js') ?>?v=1.0.0" defer></script>
</body>
</html>

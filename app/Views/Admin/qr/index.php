<?php?>

<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 mb-0">Generate QR Perangkat</h1>
</div>

<div class="row g-3">
  <!-- FORM -->
  <div class="col-12 col-lg-5">
    <div class="card shadow-sm">
      <div class="card-body">
        <h2 class="h6 mb-3">Detail Perangkat</h2>

        <form id="formQR" class="row g-3 needs-validation" novalidate>
          <?= csrf_field() ?>

          <div class="col-12">
            <label class="form-label">Nama Perangkat</label>
            <input name="nama" class="form-control" placeholder="AC Ruang Rapat" required>
            <div class="invalid-feedback">Wajib diisi.</div>
          </div>

          <div class="col-6">
            <label class="form-label">Merek</label>
            <input name="merek" class="form-control" placeholder="Daikin">
          </div>
          <div class="col-6">
            <label class="form-label">Model</label>
            <input name="model" class="form-control" placeholder="FTKC25U">
          </div>

          <div class="col-6">
            <label class="form-label">Serial No</label>
            <input name="serial_no" class="form-control" placeholder="SN12345">
          </div>
          <div class="col-6">
            <label class="form-label">Lokasi</label>
            <input name="lokasi" class="form-control" placeholder="Lantai 2 - Ruang Rapat">
          </div>

          <div class="col-6">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
              <option value="normal">Normal</option>
              <option value="rusak">Rusak</option>
              <option value="maintenance">Maintenance</option>
              <option value="diproses">Diproses</option>
            </select>
          </div>
          <div class="col-6">
            <label class="form-label">Kode (opsional)</label>
            <input name="kode_qr" class="form-control" placeholder="KHA-AC-0001">
          </div>

          <div class="col-12">
            <label class="form-label">Mode URL QR</label>
            <div class="d-flex flex-wrap gap-3">
              <div class="form-check">
                <input class="form-check-input" type="radio" name="mode" id="modeP" value="p" checked>
                <label class="form-check-label" for="modeP">/p/{TOKEN}</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="mode" id="modeTeknisi" value="teknisi">
                <label class="form-check-label" for="modeTeknisi">/teknisi/?t={TOKEN}</label>
              </div>
            </div>
          </div>

          <div class="col-12">
            <label class="form-label">Base URL publik</label>
            <input name="base" id="baseUrl" class="form-control" value="<?= rtrim(site_url(), '/') ?>">
            <div class="form-text">Bisa diubah. Default mengikuti site_url().</div>
          </div>

          <div class="col-12 d-grid d-sm-flex gap-2 mt-2">
            <button id="btnGen" class="btn btn-primary" type="submit">
              <i class="bi bi-magic"></i> Generate
            </button>
            <button type="reset" id="btnReset" class="btn btn-outline-secondary">Reset</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- PREVIEW / QR -->
  <div class="col-12 col-lg-7">
    <div class="card shadow-sm h-100">
      <div class="card-body">
        <h2 class="h6 mb-3">Preview & QR</h2>

        <div id="alertBox" class="alert alert-success d-none"></div>

        <div class="row g-3 align-items-start">
          <div class="col-md-6">
            <div class="device-card p-3 border rounded">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <div id="pvNama" class="fw-semibold">—</div>
                  <div class="small text-muted">Kode: <code id="pvKode">—</code></div>
                </div>
                <span id="pvBadge" class="badge text-bg-secondary">Status</span>
              </div>
              <hr class="my-3">
              <div class="small">
                <div><span class="text-muted">Merek:</span> <span id="pvMerek">—</span></div>
                <div><span class="text-muted">Model/SN:</span> <span id="pvModelSn">—</span></div>
                <div><span class="text-muted">Lokasi:</span> <span id="pvLokasi">—</span></div>
              </div>
            </div>
          </div>

          <div class="col-md-6 text-center">
            <div id="qrWrap" class="qr-wrap border rounded p-2 bg-white">
              <div id="qrcode"></div>
            </div>
            <div class="mt-2">
              <div class="small text-muted">URL Publik:</div>
              <div class="text-break" id="pvUrl">—</div>
            </div>
            <div class="d-grid d-sm-flex gap-2 mt-3">
              <button id="btnCopy" class="btn btn-outline-primary btn-sm"><i class="bi bi-clipboard"></i> Copy URL</button>
              <button id="btnDownload" class="btn btn-outline-success btn-sm"><i class="bi bi-download"></i> Download PNG</button>
              <button id="btnPrint" class="btn btn-outline-secondary btn-sm"><i class="bi bi-printer"></i> Cetak Label</button>
              <button id="btnJson" class="btn btn-outline-dark btn-sm"><i class="bi bi-filetype-json"></i> Simpan JSON</button>
            </div>
          </div>
        </div>

        <!-- area cetak -->
        <div id="printArea" class="print-card mt-4 bg-white">
          <div class="row g-3 align-items-center">
            <div class="col-4 text-center"><div id="printQR"></div></div>
            <div class="col-8">
              <div class="fw-semibold" id="prNama">—</div>
              <div class="small" id="prLokasi">—</div>
              <div class="small text-muted">Kode: <code id="prKode">—</code></div>
              <div class="small text-muted d-flex align-items-center gap-1">
                <i class="bi bi-link-45deg"></i><span id="prUrl">—</span>
              </div>
            </div>
          </div>
          <div class="small text-muted mt-2">Tempel di unit. Teknisi cukup scan kamera HP.</div>
        </div>

      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

<?php // ----- styles & scripts -----
      // Pastikan layout-mu punya renderSection('styles') & ('scripts') ?>
<?= $this->section('styles') ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="<?= base_url('assets/css/admin-qr.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script src="<?= base_url('assets/js-admin/qr-generator.js') ?>"></script>
<?= $this->endSection() ?>

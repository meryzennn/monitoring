<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/js-admin/data_kendala.css') ?>?v=1.0.0">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Header bar -->
<div class="d-flex flex-wrap gap-2 align-items-center justify-content-between mb-3">

  <div class="d-flex align-items-center gap-2">
    <form class="d-flex" role="search" onsubmit="return false">
      <input id="qGlobal" class="form-control form-control-sm" type="search" placeholder="Cari ID/Asset/Judul" style="width:260px">
    </form>
    <a href="#" class="btn btn-outline-secondary btn-sm" id="btnExport"><i class="bi bi-download me-1"></i>Export</a>
    <a href="#" class="btn btn-primary btn-sm" id="btnTambah"><i class="bi bi-plus-lg me-1"></i>Tambah Manual</a>
  </div>
</div>

<!-- KPI ringkas -->
<div class="row g-2 mb-3">
  <div class="col-6 col-md-3">
    <div class="card card-kpi bg-pending text-white">
      <div class="card-body py-3">
        <div class="kpi-title">Pending ACC</div>
        <div class="kpi-val"><?= esc($kpiPending ?? 0) ?></div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card card-kpi bg-proses text-white">
      <div class="card-body py-3">
        <div class="kpi-title">Dalam Perbaikan</div>
        <div class="kpi-val"><?= esc($kpiProcess ?? 0) ?></div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card card-kpi bg-sla text-white">
      <div class="card-body py-3">
        <div class="kpi-title">Terlewat SLA</div>
        <div class="kpi-val"><?= esc($kpiSLA ?? 0) ?></div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card card-kpi bg-done text-white">
      <div class="card-body py-3">
        <div class="kpi-title">Selesai Bulan Ini</div>
        <div class="kpi-val"><?= esc($kpiDone ?? 0) ?></div>
      </div>
    </div>
  </div>
</div>

<!-- Filter bar -->
<div class="card shadow-sm mb-3">
  <div class="card-body">
    <div class="row g-2 align-items-end">
      <div class="col-6 col-md-2">
        <label class="form-label form-label-sm">Tipe</label>
        <select id="fTipe" class="form-select form-select-sm">
          <option value="">Semua</option>
          <option value="ac">AC</option>
          <option value="kendaraan">Kendaraan</option>
        </select>
      </div>

      <div class="col-6 col-md-3">
        <label class="form-label form-label-sm">Status</label>
        <select id="fStatus" class="form-select form-select-sm">
          <option value="">Semua</option>
          <option>Pending ACC</option>
          <option>Disetujui</option>
          <option>Proses</option>
          <option>Menunggu Sparepart</option>
          <option>Selesai</option>
          <option>Ditolak</option>
        </select>
      </div>

      <div class="col-6 col-md-2">
        <label class="form-label form-label-sm">Severity</label>
        <select id="fSeverity" class="form-select form-select-sm">
          <option value="">Semua</option>
          <option>Low</option>
          <option>Med</option>
          <option>High</option>
        </select>
      </div>

      <div class="col-6 col-md-2">
        <label class="form-label form-label-sm">Tanggal (dari)</label>
        <input id="fFrom" type="date" class="form-control form-control-sm">
      </div>
      <div class="col-6 col-md-2">
        <label class="form-label form-label-sm">Tanggal (sampai)</label>
        <input id="fTo" type="date" class="form-control form-control-sm">
      </div>

      <div class="col-12 col-md-3">
        <label id="fLabelLokasiPlat" class="form-label form-label-sm">Lokasi / Plat</label>
        <input id="fLokasiPlat" type="text" class="form-control form-control-sm" placeholder="Lokasi (AC) / Plat (Kendaraan)">
      </div>

      <div class="col-12 col-md-auto ms-auto">
        <div class="d-flex gap-2">
          <button id="btnApply" class="btn btn-primary btn-sm"><i class="bi bi-funnel me-1"></i>Terapkan</button>
          <button id="btnReset" class="btn btn-outline-secondary btn-sm">Reset</button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Tabs status -->
<ul class="nav nav-tabs mb-2" id="tabStatus">
  <li class="nav-item"><a class="nav-link active" data-status="" href="#">Semua <span class="badge bg-secondary"><?= esc($countAll ?? 0) ?></span></a></li>
  <li class="nav-item"><a class="nav-link" data-status="Pending ACC" href="#">Pending ACC <span class="badge bg-secondary"><?= esc($countPending ?? 0) ?></span></a></li>
  <li class="nav-item"><a class="nav-link" data-status="Proses" href="#">Proses <span class="badge bg-secondary"><?= esc($countProcess ?? 0) ?></span></a></li>
  <li class="nav-item"><a class="nav-link" data-status="SLA" href="#">SLA Terlewat <span class="badge bg-secondary"><?= esc($countSLA ?? 0) ?></span></a></li>
  <li class="nav-item"><a class="nav-link" data-status="Selesai" href="#">Selesai <span class="badge bg-secondary"><?= esc($countDone ?? 0) ?></span></a></li>
</ul>

<!-- Bulk action bar (muncul saat ada yang dipilih) -->
<div id="bulkBar" class="alert alert-secondary py-2 px-3 d-none">
  <div class="d-flex flex-wrap align-items-center gap-2">
    <div><strong id="bulkCount">0</strong> dipilih</div>
    <div class="vr"></div>
    <button class="btn btn-success btn-sm" id="bulkAcc">ACC</button>
    <button class="btn btn-outline-danger btn-sm" id="bulkTolak">Tolak</button>
    <button class="btn btn-outline-secondary btn-sm" id="bulkAssign">Assign Teknisi</button>
    <button class="btn btn-outline-primary btn-sm" id="bulkUbahStatus">Ubah Status</button>
  </div>
</div>

<!-- Tabel utama -->
<div class="card shadow-sm">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover table-sm align-middle mb-0" id="tblKendala">
        <thead class="table-light">
          <tr>
            <th width="1%"><input type="checkbox" id="chkAll"></th>
            <th>ID</th>
            <th>Tipe</th>
            <th>Asset</th>
            <th>Judul</th>
            <th>Severity</th>
            <th>Status</th>
            <th>Pelapor</th>
            <th>Teknisi</th>
            <th>Dibuat</th>
            <th>ETA/SLA</th>
            <th width="1%"></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach (($kendala ?? []) as $r): ?>
          <tr class="row-kendala" data-id="<?= esc($r['id']) ?>">
            <td><input type="checkbox" class="chkRow" value="<?= esc($r['id']) ?>"></td>
            <td><a href="#" class="link-detail">#<?= esc($r['id']) ?></a></td>
            <td>
              <?php if (($r['tipe'] ?? '') === 'ac'): ?>
                <i class="bi bi-snow2 me-1"></i> AC
              <?php else: ?>
                <i class="bi bi-truck-front me-1"></i> Kendaraan
              <?php endif; ?>
            </td>
            <td><?= esc($r['asset_label'] ?? '-') ?></td>
            <td class="text-truncate" style="max-width:240px"><?= esc($r['judul'] ?? '-') ?></td>
            <td><span class="badge <?= 'sev-'.strtolower($r['severity'] ?? 'low') ?>"><?= esc($r['severity'] ?? 'Low') ?></span></td>
            <td><span class="badge <?= 'st-'.strtolower(str_replace(' ', '-', $r['status'] ?? 'Pending ACC')) ?>"><?= esc($r['status'] ?? 'Pending ACC') ?></span></td>
            <td><?= esc($r['pelapor'] ?? '-') ?></td>
            <td><?= esc($r['teknisi'] ?? '-') ?></td>
            <td><span class="small"><?= esc($r['created_at'] ?? '-') ?></span></td>
            <td><?= esc($r['eta'] ?? '-') ?></td>
            <td class="text-nowrap">
              <?php if (($r['status'] ?? '') === 'Pending ACC'): ?>
                <button class="btn btn-success btn-sm btn-acc">ACC</button>
                <button class="btn btn-outline-danger btn-sm btn-tolak">Tolak</button>
              <?php else: ?>
                <div class="btn-group btn-group-sm">
                  <button class="btn btn-outline-secondary btn-assign">Assign</button>
                  <button class="btn btn-outline-primary btn-mulai">Mulai</button>
                  <button class="btn btn-outline-success btn-selesai">Selesai</button>
                </div>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>

          <?php if (empty($kendala)): ?>
          <tr><td colspan="12" class="text-center text-muted py-4">Belum ada data.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Offcanvas detail (kanan) -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="ocDetail" style="width:520px">
  <div class="offcanvas-header">
    <h6 class="offcanvas-title">Detail Kendala</h6>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body">
    <div id="detailBody">
      <!-- diisi via JS: info inti, lampiran, timeline, form catatan -->
      <div class="text-muted">Pilih baris untuk melihat detail.</div>
    </div>
  </div>
</div>

<!-- Modal stub (aksi cepat) -->
<div class="modal fade" id="mdAcc" tabindex="-1"><div class="modal-dialog modal-sm"><div class="modal-content">
  <div class="modal-header"><h6 class="modal-title">ACC Kendala</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
  <div class="modal-body">
    <label class="form-label">Catatan (opsional)</label>
    <textarea class="form-control" rows="3" placeholder="Catatan untuk pelapor/teknisi"></textarea>
  </div>
  <div class="modal-footer"><button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button><button class="btn btn-success btn-sm">ACC</button></div>
</div></div></div>

<div class="modal fade" id="mdTolak" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
  <div class="modal-header"><h6 class="modal-title">Tolak Kendala</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
  <div class="modal-body">
    <label class="form-label">Alasan (wajib)</label>
    <textarea class="form-control" rows="3" placeholder="Contoh: duplikat/bukan kerusakan"></textarea>
  </div>
  <div class="modal-footer"><button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button><button class="btn btn-danger btn-sm">Tolak</button></div>
</div></div></div>

<div class="modal fade" id="mdAssign" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
  <div class="modal-header"><h6 class="modal-title">Assign Teknisi</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
  <div class="modal-body">
    <div class="mb-2">
      <label class="form-label">Teknisi</label>
      <select class="form-select"><option>— pilih —</option></select>
    </div>
    <div>
      <label class="form-label">ETA</label>
      <input type="datetime-local" class="form-control">
    </div>
  </div>
  <div class="modal-footer"><button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary btn-sm">Assign</button></div>
</div></div></div>

<div class="modal fade" id="mdSelesai" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
  <div class="modal-header"><h6 class="modal-title">Tandai Selesai</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
  <div class="modal-body">
    <label class="form-label">Ringkasan Tindakan</label>
    <textarea class="form-control mb-2" rows="3"></textarea>
    <label class="form-label">Biaya (opsional)</label>
    <input type="number" class="form-control" placeholder="0">
  </div>
  <div class="modal-footer"><button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button><button class="btn btn-success btn-sm">Selesai</button></div>
</div></div></div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js-admin/data_kendala.js') ?>?v=1.0.0"></script>
<?= $this->endSection() ?>

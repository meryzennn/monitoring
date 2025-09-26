<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AcUnitModel;
use CodeIgniter\HTTP\ResponseInterface;

class Qr extends BaseController
{
    public function index()
    {
        return view('Admin/qr/index', [
            'title'      => 'Generate QR',
            'activeMenu' => 'qr',
        ]);
    }

    // POST /admin/qr/save
    // Simpan perangkat baru; token (untuk URL) disimpan ke ac_units.kode_qr
    public function save()
    {
        if ($this->request->getMethod(true) !== 'POST') {
            return $this->response->setJSON(['error' => 'Method Not Allowed'])
                                  ->setStatusCode(ResponseInterface::HTTP_METHOD_NOT_ALLOWED);
        }

        $r = $this->request;

        // Dari form/JS
        $token   = trim((string)$r->getPost('token'));      // ← wajib, dipakai untuk URL
        $nama    = trim((string)$r->getPost('nama'));       // → ac_units.nomor_unik (UNIQUE)
        $merek   = trim((string)$r->getPost('merek'));
        $model   = trim((string)$r->getPost('model'));
        $serial  = trim((string)$r->getPost('serial_no'));
        $lokasi  = trim((string)$r->getPost('lokasi'));
        $kodeOp  = trim((string)$r->getPost('kode_qr'));    // opsional, hanya catatan internal
        $status  = strtoupper(trim((string)$r->getPost('status') ?: 'NORMAL'));

        if ($token === '' || $nama === '') {
            return $this->response->setJSON(['error' => 'Nama & token wajib'])
                                  ->setStatusCode(ResponseInterface::HTTP_UNPROCESSABLE_ENTITY);
        }

        $AC = new AcUnitModel();

        // nomor_unik unik → jika sudah dipakai, tambahkan suffix -2, -3, ...
        $nomorUnik = $this->ensureUniqueNomorUnik($AC, $nama);

        $tipeModel = trim(($merek ? $merek.' ' : '').$model);
        $catatan   = [];
        if ($kodeOp !== '') $catatan[] = 'KODE='.$kodeOp;
        if ($serial !== '') $catatan[] = 'SN='.$serial;

        // Siapkan data sesuai skema ac_units (NOT NULL di beberapa kolom)
        $data = [
            'kode_qr'       => $token,                 // <— KUNCI URL
            'nomor_unik'    => $nomorUnik,             // unik & human-readable
            'tipe_model'    => ($tipeModel !== '' ? $tipeModel : '-'),
            'kapasitas_btu' => 12000,                  // default aman (>0). Ubah kalau ada input
            'lokasi'        => ($lokasi !== '' ? $lokasi : '-'),
            'status_ac'     => in_array($status, ['NORMAL','MENUNGGU_PERBAIKAN','DALAM_PERBAIKAN'], true) ? $status : 'NORMAL',
            'catatan'       => ($catatan ? implode("\n", $catatan) : null),
        ];

        $id = $AC->insert($data, true);
        if ($id === false) {
            return $this->response->setJSON(['error' => 'Validasi gagal', 'detail' => $AC->errors()])
                                  ->setStatusCode(ResponseInterface::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Upload foto opsional → simpan sebagai main.ext
        $foto = $r->getFile('foto'); // name="foto"
        if ($foto && $foto->isValid()) {
            $dir = FCPATH.'uploads/ac_units/'.$id;
            if (!is_dir($dir) && !@mkdir($dir, 0775, true) && !is_dir($dir)) {
                return $this->response->setJSON(['error' => 'Gagal membuat folder upload'])->setStatusCode(500);
            }

            $ext = strtolower($foto->getClientExtension() ?: $foto->getExtension() ?: $foto->guessExtension() ?: 'jpg');
            if (!in_array($ext, ['jpg','jpeg','png','webp'], true)) $ext = 'jpg';

            // bersihkan main.* lama
            foreach (['jpg','jpeg','png','webp'] as $x) {
                $old = $dir.'/main.'.$x;
                if (is_file($old)) @unlink($old);
            }

            $foto->move($dir, 'main.'.$ext, true);
        }

        return $this->response->setJSON([
            'ok'  => true,
            'id'  => (int)$id,
            'url' => site_url('ac/'.$token), // selalu /ac/{token}
        ]);
    }

    /**
     * Pastikan nomor_unik unik. Jika sudah dipakai, tambahkan suffix -2/-3/...
     */
    private function ensureUniqueNomorUnik(AcUnitModel $AC, string $base): string
    {
        $clean = trim($base);
        if ($clean === '') $clean = 'AC';
        $name  = $clean;
        $i = 1;
        while ($AC->where('nomor_unik', $name)->first()) {
            $i++;
            $name = $clean.'-'.$i;
            if ($i > 200) { // guard
                $name = $clean.'-'.bin2hex(random_bytes(2));
                break;
            }
        }
        return $name;
    }
}

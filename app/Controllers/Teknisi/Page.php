<?php
namespace App\Controllers\Teknisi;

use App\Controllers\BaseController;
use App\Models\AcUnitModel;
use CodeIgniter\HTTP\ResponseInterface;

class Page extends BaseController
{
    // GET /ac/{token}
    public function detailByToken(string $token)
    {
        $token = trim($token);
        $AC = new AcUnitModel();

        // Cari berdasarkan kode_qr (token URL)
        $ac = $AC->where('kode_qr', $token)->first();
        // Fallback untuk data lama (opsional):
        if (!$ac) $ac = $AC->where('nomor_unik', $token)->first();

        $wantsJson = ($this->request->getGet('format') === 'json') || $this->request->isAJAX();
        if ($wantsJson) {
            if (!$ac) return $this->response->setJSON(['error'=>'Perangkat tidak ditemukan'])->setStatusCode(404);

            // foto utama: uploads/ac_units/{id}/main.*
            $fotoUrl = null;
            $dir = FCPATH.'uploads/ac_units/'.$ac['id'].'/';
            if (is_dir($dir)) {
                foreach (['jpg','jpeg','png','webp'] as $ext) {
                    $f = $dir.'main.'.$ext; if (is_file($f)) { $fotoUrl = site_url('uploads/ac_units/'.$ac['id'].'/main.'.$ext); break; }
                }
            }

            return $this->response->setJSON([
                'ok' => true,
                'ac' => [
                    'id'            => (int)$ac['id'],
                    'nomor_unik'    => $ac['nomor_unik'] ?? null,
                    'kode_qr'       => $ac['kode_qr'] ?? null,
                    'tipe_model'    => $ac['tipe_model'] ?? null,
                    'kapasitas_btu' => $ac['kapasitas_btu'] ?? null,
                    'lokasi'        => $ac['lokasi'] ?? null,
                    'status_ac'     => $ac['status_ac'] ?? 'NORMAL',
                    'catatan'       => $ac['catatan'] ?? null,
                    'foto_url'      => $fotoUrl,
                ],
                'tickets' => [],
            ]);
        }

        return view('teknisi/ac_detail', [
            'title' => $ac['nomor_unik'] ?? 'Perangkat',
            'token' => $token,
        ]);
    }

    // GET /ac/{token}/perbaikan → VIEW: teknisi/perbaikan.php
    public function perbaikanByToken(string $token)
    {
        $token = trim($token);
        return view('teknisi/perbaikan', [
            'title' => 'Laporan Perbaikan',
            'token' => $token,
        ]);
    }

    // POST /ac/{token}/perbaikan → simpan laporan & fotoAfter (opsional), tandai selesai (NORMAL)
    public function submitPerbaikanByToken(string $token)
    {
        if ($this->request->getMethod(true) !== 'POST') {
            return $this->response->setJSON(['error'=>'Method Not Allowed'])->setStatusCode(405);
        }

        $token = trim($token);
        $AC = new AcUnitModel();
        $ac = $AC->where('kode_qr', $token)->first();
        if (!$ac) return $this->response->setJSON(['error'=>'Perangkat tidak ditemukan'])->setStatusCode(404);

        $tindakan = trim((string)$this->request->getPost('tindakan'));
        $part     = trim((string)$this->request->getPost('part'));
        $biaya    = (int)($this->request->getPost('biaya') ?? 0);

        if ($tindakan === '') {
            return $this->response->setJSON(['error'=>'Tindakan wajib diisi'])->setStatusCode(422);
        }

        // simpan foto after (opsional)
        $afterPath = null;
        $foto = $this->request->getFile('fotoAfter');
        if ($foto && $foto->isValid()) {
            $dir = FCPATH.'uploads/ac_units/'.$ac['id'].'/repairs';
            if (!is_dir($dir) && !@mkdir($dir, 0775, true) && !is_dir($dir)) {
                return $this->response->setJSON(['error'=>'Gagal buat folder upload'])->setStatusCode(500);
            }
            $ext = strtolower($foto->getClientExtension() ?: $foto->getExtension() ?: $foto->guessExtension() ?: 'jpg');
            if (!in_array($ext, ['jpg','jpeg','png','webp'], true)) $ext = 'jpg';
            $fname = 'after_'.date('Ymd_His').'.'.$ext;
            $foto->move($dir, $fname, true);
            $afterPath = '/uploads/ac_units/'.$ac['id'].'/repairs/'.$fname;
        }

        // update status AC jadi NORMAL & simpan catatan ringkas
        $append = "Perbaikan: ".$tindakan;
        if ($part !== '')  $append .= " | Part: ".$part;
        if ($biaya > 0)    $append .= " | Biaya: ".$biaya;

        $newNote = trim(($ac['catatan'] ?? '')."\n".$append);

        $AC->update($ac['id'], [
            'status_ac' => 'NORMAL',
            'catatan'   => $newNote,
        ]);

        return $this->response->setJSON([
            'ok'        => true,
            'message'   => 'Perbaikan disimpan & status diset ke NORMAL',
            'fotoAfter' => $afterPath ? site_url(ltrim($afterPath,'/')) : null,
        ]);
    }
}

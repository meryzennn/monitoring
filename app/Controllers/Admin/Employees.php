<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\EmployeeModel;

class Employees extends BaseController
{
    public function index()
    {
        $q       = trim($this->request->getGet('q') ?? '');
        $perPage = (int)($this->request->getGet('perPage') ?? 10);
        $perPage = max($perPage, 10);

        $model = new EmployeeModel();
        $builder = $model->orderBy('id','DESC');
        if ($q !== '') {
            $builder = $builder->groupStart()
                ->like('kode_pegawai',$q)
                ->orLike('nama',$q)
                ->orLike('email',$q)
                ->orLike('no_telp',$q)
            ->groupEnd();
        }

        $rows = $builder->paginate($perPage);
        $countTotal  = $model->countAllResults(false);
        $countActive = (clone $model)->where('is_active',1)->countAllResults();

        return view('Admin/employees/index', [
            'title'       => 'Data Pegawai',
            'activeMenu'  => 'employees',
            'q'           => $q,
            'perPage'     => $perPage,
            'rows'        => $rows,
            'pager'       => $model->pager,
            'countTotal'  => $countTotal,
            'countActive' => $countActive,
        ]);
    }

    // ===== JSON endpoints untuk modal =====
    public function show($id)
    {
        $row = (new EmployeeModel())->find($id);
        if (!$row) return $this->response->setJSON(['success'=>false,'message'=>'Data tidak ditemukan','csrf'=>csrf_hash()])->setStatusCode(404);
        return $this->response->setJSON(['success'=>true,'data'=>$row,'csrf'=>csrf_hash()]);
    }

    public function store()
    {
        $model = new EmployeeModel();
        $data  = $this->request->getPost(['kode_pegawai','nama','email','no_telp','is_active']);

        $rules = $model->getValidationRules();
        // unik saat create
        $rules['kode_pegawai'] .= '|is_unique[employees.kode_pegawai]';
        if (!empty($data['email'])) $rules['email'] .= '|is_unique[employees.email]';

        if (! $this->validate($rules)) {
            return $this->response->setJSON(['success'=>false,'errors'=>$this->validator->getErrors(),'csrf'=>csrf_hash()])->setStatusCode(422);
        }

        $data['is_active'] = (int)($data['is_active'] ?? 1);
        $model->insert($data);
        return $this->response->setJSON(['success'=>true,'message'=>'Pegawai ditambahkan','csrf'=>csrf_hash()]);
    }

    public function update($id)
    {
        $model = new EmployeeModel();
        if (! $model->find($id)) return $this->response->setJSON(['success'=>false,'message'=>'Data tidak ditemukan','csrf'=>csrf_hash()])->setStatusCode(404);

        $data  = $this->request->getPost(['kode_pegawai','nama','email','no_telp','is_active']);
        $rules = $model->getValidationRules();
        // unik dengan ignore row
        $rules['kode_pegawai'] .= '|is_unique[employees.kode_pegawai,id,{id}]';
        $rules = str_replace('{id}', (string)$id, $rules);
        if (!empty($data['email'])) $rules['email'] .= '|is_unique[employees.email,id,'.(int)$id.']';

        if (! $this->validate($rules)) {
            return $this->response->setJSON(['success'=>false,'errors'=>$this->validator->getErrors(),'csrf'=>csrf_hash()])->setStatusCode(422);
        }

        $data['is_active'] = (int)($data['is_active'] ?? 1);
        $model->update($id,$data);
        return $this->response->setJSON(['success'=>true,'message'=>'Pegawai diperbarui','csrf'=>csrf_hash()]);
    }

    public function delete($id)
    {
        $m = new EmployeeModel();
        if (! $m->find($id)) return redirect()->to(site_url('pegawai'))->with('msg_error','Data tidak ditemukan.');
        $m->delete($id);
        return redirect()->to(site_url('pegawai'))->with('msg_success','Pegawai dihapus.');
    }

        public function search()
    {
        $q       = trim($this->request->getGet('q') ?? '');
        $perPage = max((int)($this->request->getGet('perPage') ?? 10), 1);
        $page    = max((int)($this->request->getGet('page') ?? 1), 1);
        $offset  = ($page - 1) * $perPage;

        $model   = new EmployeeModel();

        // builder dasar (untuk data & total)
        $base = $model->orderBy('id', 'DESC');
        if ($q !== '') {
            $base = $base->groupStart()
                ->like('kode_pegawai', $q)
                ->orLike('nama', $q)
                ->orLike('email', $q)
                ->orLike('no_telp', $q)
            ->groupEnd();
        }

        // total cocok (pakai clone agar query utama tidak reset)
        $total = (clone $base)->countAllResults();

        // ambil data halaman ini
        $rows = $base->limit($perPage, $offset)->get()->getResultArray();

        $data = array_map(static function($r){
            return [
                'id'           => (int)$r['id'],
                'kode_pegawai' => (string)$r['kode_pegawai'],
                'nama'         => (string)$r['nama'],
                'email'        => (string)($r['email'] ?? ''),
                'no_telp'      => (string)($r['no_telp'] ?? ''),
                'is_active'    => (int)$r['is_active'],
                'delete_url'   => site_url('pegawai/'.$r['id']),
            ];
        }, $rows ?? []);

        return $this->response->setJSON([
            'success'    => true,
            'q'          => $q,
            'total'      => (int)$total,
            'perPage'    => (int)$perPage,
            'page'       => (int)$page,
            'pageCount'  => (int)ceil(max($total,1)/$perPage),
            'rows'       => $data,
        ]);
    }
}

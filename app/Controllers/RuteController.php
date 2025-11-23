<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class RuteController extends BaseController
{
    protected $ruteModel;

    public function __construct()
    {
        $this->ruteModel = model('App\Models\RuteModel');
    }

    public function index()
    {
        $data['rute'] = $this->ruteModel->findAll();
        return view('pages/rute/index', $data);
    }

    public function create()
    {
        if ($this->request->getMethod() == 'POST') {
            $data = [
                'kode_rute' => $this->request->getPost('kode_rute'),
                'nama_wilayah' => $this->request->getPost('nama_wilayah'),
            ];

            if ($this->ruteModel->insert($data)) {
                return $this->response->setJSON(['success' => true, 'message' => 'Data berhasil ditambahkan', 'url' => '/rute']);
            } else {
                return $this->response->setJSON(['success' => false, 'message' => $this->ruteModel->errors()]);
            }
        }
        return view('pages/rute/create');
    }

    public function detail($id)
    {
        $data['rute'] = $this->ruteModel->find($id);
        return view('pages/rute/detail', $data);
    }

    public function edit($id)
    {
        $dataRute['rute'] = $this->ruteModel->find($id);

        if ($this->request->getMethod() == 'POST') {
            $kode_rute = $this->request->getPost('kode_rute');
            $nama_wilayah = $this->request->getPost('nama_wilayah');

            $data = [
                'nama_wilayah' => $nama_wilayah
            ];

            // Only validate unique if kode_rute changed
            if ($dataRute['rute']['kode_rute'] !== $kode_rute) {
                // Kode rute changed, check if new kode already exists
                $existing = $this->ruteModel->where('kode_rute', $kode_rute)
                    ->where('id_rute !=', $id)
                    ->first();
                
                if ($existing) {
                    return $this->response->setJSON(['success' => false, 'message' => ['kode_rute' => 'Kode Rute sudah ada']]);
                }
                
                $data['kode_rute'] = $kode_rute;
            }

            if ($this->ruteModel->update($id, $data)) {
                return $this->response->setJSON(['success' => true, 'message' => 'Data berhasil diubah', 'url' => '/rute']);
            } else {
                return $this->response->setJSON(['success' => false, 'message' => $this->ruteModel->errors()]);
            }
        }

        return view('pages/rute/edit', $dataRute);
    }

    public function delete($id)
    {
        return $this->ruteModel->delete($id);
    }

    public function getRute()
    {
        return $this->response->setJSON($this->ruteModel->findAll());
    }
}

<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProductCategoryModel;

class ProductCategoryController extends BaseController
{
    protected ProductCategoryModel $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new ProductCategoryModel();
    }

    public function index()
    {
        $categories = $this->categoryModel->orderBy('name', 'ASC')->findAll();
        return view('pages/product_category/index', ['categories' => $categories]);
    }

    public function create()
    {
        if ($this->request->getMethod() === 'POST') {
            $name = $this->request->getPost('name');
            $data = [
                'name' => $name,
                'description' => $this->request->getPost('description'),
            ];
            // Validasi manual: cek apakah nama sudah ada
            $existing = $this->categoryModel->where('name', $name)->first();
            if ($existing) {
                return $this->response->setJSON(['success' => false, 'message' => ['name' => 'Nama kategori sudah ada']]);
            }
            try {
                if ($this->categoryModel->insert($data)) {
                    return $this->response->setJSON(['success' => true, 'message' => 'Kategori berhasil ditambahkan', 'url' => '/product-category']);
                }
            } catch (\Throwable $th) {
                return $this->response->setJSON(['success' => false, 'message' => ['name' => 'Nama kategori sudah ada']]);
            }
            // Jika gagal insert, ambil error dari model
            $errors = $this->categoryModel->errors();
            if (isset($errors['name']) && (strpos($errors['name'], 'already exists') !== false || strpos($errors['name'], 'sudah ada') !== false)) {
                return $this->response->setJSON(['success' => false, 'message' => ['name' => 'Nama kategori sudah ada']]);
            }
            return $this->response->setJSON(['success' => false, 'message' => $errors]);
        }
        return view('pages/product_category/create');
    }

    public function detail($id)
    {
        $category = $this->categoryModel->find($id);
        return view('pages/product_category/detail', ['category' => $category]);
    }

    public function edit($id)
    {
        if ($this->request->getMethod() === 'POST') {
            // Get current category data
            $currentCategory = $this->categoryModel->find($id);
            $newName = $this->request->getPost('name');
            
            $data = [
                'name' => $newName,
                'description' => $this->request->getPost('description'),
            ];
            
            // Only validate unique if name changed
            if ($currentCategory['name'] !== $newName) {
                // Name changed, check if new name already exists
                $existing = $this->categoryModel->where('name', $newName)
                    ->where('id_category !=', $id)
                    ->first();
                
                if ($existing) {
                    return $this->response->setJSON(['success' => false, 'message' => ['name' => 'Nama kategori sudah ada']]);
                }
            }
            
            if ($this->categoryModel->update($id, $data)) {
                return $this->response->setJSON(['success' => true, 'message' => 'Kategori diubah', 'url' => '/product-category']);
            }
            return $this->response->setJSON(['success' => false, 'message' => $this->categoryModel->errors()]);
        }
        $category = $this->categoryModel->find($id);
        return view('pages/product_category/edit', ['category' => $category]);
    }

    public function delete($id)
    {
        if ($this->categoryModel->delete($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Kategori dihapus']);
        }
        return $this->response->setJSON(['success' => false, 'message' => $this->categoryModel->errors()]);
    }
}

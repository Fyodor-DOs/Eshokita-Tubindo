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
            $data = [
                'name' => $this->request->getPost('name'),
                'description' => $this->request->getPost('description'),
            ];

            // Try insert and capture result for debugging
            try {
                $result = $this->categoryModel->insert($data);
            } catch (\Throwable $e) {
                // Log unexpected exception
                log_message('error', 'ProductCategoryController::create exception: ' . $e->getMessage());
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menyimpan kategori',
                    'debug' => $e->getMessage(),
                ]);
            }

            // Log result and any model errors to writable/logs for diagnosis
            $errors = $this->categoryModel->errors();
            log_message('debug', 'ProductCategoryController::create result=' . var_export($result, true) . ' errors=' . json_encode($errors));

            if ($result === false) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $errors ?: 'Gagal menyimpan kategori',
                    'debug' => ['insertResult' => $result],
                ]);
            }

            // Success: include insert id in response for verification
            $insertId = $this->categoryModel->getGeneratedId();
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Kategori berhasil ditambahkan',
                'url' => '/product-category',
                'id' => $insertId,
            ]);
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
            $currentCategory = $this->categoryModel->find($id);
            $newName = $this->request->getPost('name');

            $data = [
                'name' => $newName,
                'description' => $this->request->getPost('description'),
            ];

            // Jika nama tidak berubah, skip validasi unique
            if ($currentCategory && $currentCategory['name'] === $newName) {
                // Hapus aturan is_unique sementara untuk update tanpa ubah nama
                $this->categoryModel->setValidationRule('name', 'required|min_length[2]');
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

<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProductModel;
use App\Models\ProductCategoryModel;

class ProductController extends BaseController
{
    protected ProductModel $productModel;
    protected ProductCategoryModel $categoryModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->categoryModel = new ProductCategoryModel();
    }

    public function index()
    {
        $products = $this->productModel
            ->select('product.*, product_category.name as category_name')
            ->join('product_category', 'product.id_category = product_category.id_category', 'left')
            ->orderBy('product.name', 'ASC')
            ->findAll();
        return view('pages/product/index', ['products' => $products]);
    }

    public function create()
    {
        if ($this->request->getMethod() === 'POST') {
            $data = [
                'sku' => $this->request->getPost('sku'),
                'name' => $this->request->getPost('name'),
                'id_category' => $this->request->getPost('id_category') ?: null,
                'unit' => $this->request->getPost('unit'), // berat satuan dalam kg
                'price' => $this->request->getPost('price') !== null ? (float) $this->request->getPost('price') : 0,
                'qty' => (int) $this->request->getPost('qty') ?: 0,
                'notes' => $this->request->getPost('notes'),
                'active' => 1, // Default aktif saat create
            ];
            
            if ($this->productModel->insert($data)) {
                return $this->response->setJSON(['success' => true, 'message' => 'Produk berhasil ditambahkan', 'url' => '/product']);
            }
            return $this->response->setJSON(['success' => false, 'message' => $this->productModel->errors()]);
        }

        $categories = $this->categoryModel->findAll();
        return view('pages/product/create', ['categories' => $categories]);
    }

    public function detail($id)
    {
        $product = $this->productModel
            ->select('product.*, product_category.name as category_name')
            ->join('product_category', 'product.id_category = product_category.id_category', 'left')
            ->find($id);
        return view('pages/product/detail', ['product' => $product]);
    }

    public function edit($id)
    {
        if ($this->request->getMethod() === 'POST') {
            $currentProduct = $this->productModel->find($id);
            $newSku = $this->request->getPost('sku');
            $data = [];

            // Only check SKU uniqueness if user entered a new SKU AND it is different from current
            if ($newSku !== null && $newSku !== '' && $newSku !== $currentProduct['sku']) {
                $existing = $this->productModel->where('sku', $newSku)
                    ->where('id_product !=', $id)
                    ->first();
                if ($existing) {
                    return $this->response->setJSON(['success' => false, 'message' => ['sku' => 'SKU sudah digunakan']]);
                }
                $data['sku'] = $newSku;
            }
            $name = $this->request->getPost('name');
            if ($name !== null && $name !== '') $data['name'] = $name;
            $id_category = $this->request->getPost('id_category');
            if ($id_category !== null && $id_category !== '') $data['id_category'] = $id_category;
            $unit = $this->request->getPost('unit');
            if ($unit !== null && $unit !== '') $data['unit'] = $unit;
            $price = $this->request->getPost('price');
            if ($price !== null && $price !== '') $data['price'] = (float)$price;
            $qty = $this->request->getPost('qty');
            if ($qty !== null && $qty !== '') $data['qty'] = (int)$qty;
            $notes = $this->request->getPost('notes');
            if ($notes !== null && $notes !== '') $data['notes'] = $notes;

            // If no fields are provided, allow saving (no update, just return success)
            if (empty($data)) {
                return $this->response->setJSON(['success' => true, 'message' => 'Tidak ada perubahan', 'url' => '/product']);
            }

            if ($this->productModel->update($id, $data)) {
                return $this->response->setJSON(['success' => true, 'message' => 'Produk berhasil diubah', 'url' => '/product']);
            }
            return $this->response->setJSON(['success' => false, 'message' => $this->productModel->errors()]);
        }
        $product = $this->productModel->find($id);
        $categories = $this->categoryModel->findAll();
        return view('pages/product/edit', ['product' => $product, 'categories' => $categories]);
    }

    public function delete($id)
    {
        if ($this->productModel->delete($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Produk dihapus']);
        }
        return $this->response->setJSON(['success' => false, 'message' => $this->productModel->errors()]);
    }
}

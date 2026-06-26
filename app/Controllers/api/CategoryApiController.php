<?php

require_once __DIR__ . '/BaseApiController.php';
require_once __DIR__ . '/../../Models/Category.php';

class CategoryApiController extends BaseApiController
{
    private $categoryModel;

    public function __construct()
    {
        parent::__construct();
        $this->categoryModel = new Category();
    }

    /**
     * Lấy danh sách danh mục
     * GET /api/categories
     */
    public function index()
    {
        try {
            $categories = $this->categoryModel->getAll();
            return $this->success(['categories' => $categories], 'Danh sách danh mục');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), [], 500);
        }
    }

    /**
     * Lấy chi tiết danh mục
     * GET /api/categories/{id}
     */
    public function show($params = [])
    {
        try {
            $id = $params['id'] ?? $this->request->get('id');
            if (!$id) {
                return $this->error('Thiếu ID danh mục', [], 422);
            }

            $category = $this->categoryModel->getById($id);
            if (!$category) {
                return $this->error('Danh mục không tồn tại', [], 404);
            }

            return $this->success(['category' => $category], 'Chi tiết danh mục');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), [], 500);
        }
    }

    /**
     * Tạo danh mục mới
     * POST /api/categories
     */
    public function store($params = [])
    {
        try {
            // Kiểm tra quyền (chỉ admin và librarian)
            $role = $_SESSION['role'] ?? null;
            if ($role !== 'admin' && $role !== 'librarian') {
                return $this->error('Bạn không có quyền tạo danh mục', [], 403);
            }

            $name = $this->request->get('name');
            $description = $this->request->get('description');
            $parentId = $this->request->get('parent_id');

            if (!$name) {
                return $this->error('Vui lòng cung cấp tên danh mục', [], 422);
            }

            $data = [
                'name' => $name,
                'description' => $description ?? null,
                'parent_id' => $parentId ?? null
            ];

            if ($this->categoryModel->create($data)) {
                return $this->success([], 'Tạo danh mục thành công', 201);
            } else {
                return $this->error('Tạo danh mục thất bại', [], 400);
            }
        } catch (Exception $e) {
            return $this->error($e->getMessage(), [], 500);
        }
    }

    /**
     * Cập nhật danh mục
     * PUT /api/categories/{id}
     */
    public function update($params = [])
    {
        try {
            // Kiểm tra quyền
            $role = $_SESSION['role'] ?? null;
            if ($role !== 'admin' && $role !== 'librarian') {
                return $this->error('Bạn không có quyền cập nhật danh mục', [], 403);
            }

            $id = $params['id'] ?? $this->request->get('id');
            if (!$id) {
                return $this->error('Thiếu ID danh mục', [], 422);
            }

            $category = $this->categoryModel->getById($id);
            if (!$category) {
                return $this->error('Danh mục không tồn tại', [], 404);
            }

            $name = $this->request->get('name');
            $description = $this->request->get('description');
            $parentId = $this->request->get('parent_id');

            if (!$name) {
                return $this->error('Vui lòng cung cấp tên danh mục', [], 422);
            }

            $data = [
                'name' => $name,
                'description' => $description ?? null,
                'parent_id' => $parentId ?? null
            ];

            if ($this->categoryModel->update($id, $data)) {
                return $this->success(['id' => $id], 'Cập nhật danh mục thành công');
            } else {
                return $this->error('Cập nhật danh mục thất bại', [], 400);
            }
        } catch (Exception $e) {
            return $this->error($e->getMessage(), [], 500);
        }
    }

    /**
     * Xóa danh mục
     * DELETE /api/categories/{id}
     */
    public function destroy($params = [])
    {
        try {
            // Kiểm tra quyền
            $role = $_SESSION['role'] ?? null;
            if ($role !== 'admin' && $role !== 'librarian') {
                return $this->error('Bạn không có quyền xóa danh mục', [], 403);
            }

            $id = $params['id'] ?? $this->request->get('id');
            if (!$id) {
                return $this->error('Thiếu ID danh mục', [], 422);
            }

            $category = $this->categoryModel->getById($id);
            if (!$category) {
                return $this->error('Danh mục không tồn tại', [], 404);
            }

            if ($this->categoryModel->delete($id)) {
                return $this->success(['id' => $id], 'Xóa danh mục thành công');
            } else {
                return $this->error('Xóa danh mục thất bại', [], 400);
            }
        } catch (Exception $e) {
            return $this->error($e->getMessage(), [], 500);
        }
    }
}

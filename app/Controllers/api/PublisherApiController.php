<?php

require_once __DIR__ . '/BaseApiController.php';
require_once __DIR__ . '/../../Models/Publisher.php';

class PublisherApiController extends BaseApiController
{
    private $publisherModel;

    public function __construct()
    {
        parent::__construct();
        $this->publisherModel = new Publisher();
    }

    /**
     * GET /api/publishers
     */
    public function index()
    {
        try {
            $publishers = $this->publisherModel->getAll();
            return $this->success(['publishers' => $publishers], 'Danh sách nhà xuất bản');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), [], 500);
        }
    }

    /**
     * GET /api/publishers/{id}
     */
    public function show($params = [])
    {
        try {
            $id = $params['id'] ?? $this->request->get('id');
            if (!$id) {
                return $this->error('Thiếu ID nhà xuất bản', [], 422);
            }

            $publisher = $this->publisherModel->getById($id);
            if (!$publisher) {
                return $this->error('Nhà xuất bản không tồn tại', [], 404);
            }

            return $this->success(['publisher' => $publisher], 'Chi tiết nhà xuất bản');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), [], 500);
        }
    }

    /**
     * POST /api/publishers
     */
    public function store($params = [])
    {
        try {
            $role = $_SESSION['role'] ?? null;
            if ($role !== 'admin' && $role !== 'librarian') {
                return $this->error('Bạn không có quyền tạo nhà xuất bản', [], 403);
            }

            $name = $this->request->get('name');
            if (!$name) {
                return $this->error('Vui lòng cung cấp tên nhà xuất bản', [], 422);
            }

            $data = [
                'name' => $name,
                'address' => $this->request->get('address'),
                'phone' => $this->request->get('phone'),
                'email' => $this->request->get('email'),
                'website' => $this->request->get('website'),
            ];

            if ($this->publisherModel->create($data)) {
                return $this->success([], 'Tạo nhà xuất bản thành công', 201);
            }

            return $this->error('Tạo nhà xuất bản thất bại', [], 400);
        } catch (Exception $e) {
            return $this->error($e->getMessage(), [], 500);
        }
    }

    /**
     * PUT /api/publishers/{id}
     */
    public function update($params = [])
    {
        try {
            $role = $_SESSION['role'] ?? null;
            if ($role !== 'admin' && $role !== 'librarian') {
                return $this->error('Bạn không có quyền cập nhật nhà xuất bản', [], 403);
            }

            $id = $params['id'] ?? $this->request->get('id');
            if (!$id) {
                return $this->error('Thiếu ID nhà xuất bản', [], 422);
            }

            $publisher = $this->publisherModel->getById($id);
            if (!$publisher) {
                return $this->error('Nhà xuất bản không tồn tại', [], 404);
            }

            $name = $this->request->get('name');
            if (!$name) {
                return $this->error('Vui lòng cung cấp tên nhà xuất bản', [], 422);
            }

            $data = [
                'name' => $name,
                'address' => $this->request->get('address'),
                'phone' => $this->request->get('phone'),
                'email' => $this->request->get('email'),
                'website' => $this->request->get('website'),
            ];

            if ($this->publisherModel->update($id, $data)) {
                return $this->success(['id' => $id], 'Cập nhật nhà xuất bản thành công');
            }

            return $this->error('Cập nhật nhà xuất bản thất bại', [], 400);
        } catch (Exception $e) {
            return $this->error($e->getMessage(), [], 500);
        }
    }

    /**
     * DELETE /api/publishers/{id}
     */
    public function destroy($params = [])
    {
        try {
            $role = $_SESSION['role'] ?? null;
            if ($role !== 'admin' && $role !== 'librarian') {
                return $this->error('Bạn không có quyền xóa nhà xuất bản', [], 403);
            }

            $id = $params['id'] ?? $this->request->get('id');
            if (!$id) {
                return $this->error('Thiếu ID nhà xuất bản', [], 422);
            }

            $publisher = $this->publisherModel->getById($id);
            if (!$publisher) {
                return $this->error('Nhà xuất bản không tồn tại', [], 404);
            }

            if ($this->publisherModel->delete($id)) {
                return $this->success(['id' => $id], 'Xóa nhà xuất bản thành công');
            }

            return $this->error('Xóa nhà xuất bản thất bại', [], 400);
        } catch (Exception $e) {
            return $this->error($e->getMessage(), [], 500);
        }
    }
}

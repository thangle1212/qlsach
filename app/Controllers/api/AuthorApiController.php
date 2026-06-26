<?php

require_once __DIR__ . '/BaseApiController.php';
require_once __DIR__ . '/../../Models/Author.php';

class AuthorApiController extends BaseApiController
{
    private $authorModel;

    public function __construct()
    {
        parent::__construct();
        $this->authorModel = new Author();
    }

    /**
     * GET /api/authors
     */
    public function index()
    {
        try {
            $authors = $this->authorModel->getAll();
            return $this->success(['authors' => $authors], 'Danh sách tác giả');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), [], 500);
        }
    }

    /**
     * GET /api/authors/{id}
     */
    public function show($params = [])
    {
        try {
            $id = $params['id'] ?? $this->request->get('id');
            if (!$id) {
                return $this->error('Thiếu ID tác giả', [], 422);
            }

            $author = $this->authorModel->getById($id);
            if (!$author) {
                return $this->error('Tác giả không tồn tại', [], 404);
            }

            return $this->success(['author' => $author], 'Chi tiết tác giả');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), [], 500);
        }
    }

    /**
     * POST /api/authors
     */
    public function store($params = [])
    {
        try {
            $role = $_SESSION['role'] ?? null;
            if ($role !== 'admin' && $role !== 'librarian') {
                return $this->error('Bạn không có quyền tạo tác giả', [], 403);
            }

            $name = $this->request->get('name');
            if (!$name) {
                return $this->error('Vui lòng cung cấp tên tác giả', [], 422);
            }

            $data = [
                'name' => $name,
                'biography' => $this->request->get('biography'),
                'nationality' => $this->request->get('nationality'),
                'birth_year' => $this->request->get('birth_year'),
                'death_year' => $this->request->get('death_year'),
            ];

            if ($this->authorModel->create($data)) {
                return $this->success([], 'Tạo tác giả thành công', 201);
            }

            return $this->error('Tạo tác giả thất bại', [], 400);
        } catch (Exception $e) {
            return $this->error($e->getMessage(), [], 500);
        }
    }

    /**
     * PUT /api/authors/{id}
     */
    public function update($params = [])
    {
        try {
            $role = $_SESSION['role'] ?? null;
            if ($role !== 'admin' && $role !== 'librarian') {
                return $this->error('Bạn không có quyền cập nhật tác giả', [], 403);
            }

            $id = $params['id'] ?? $this->request->get('id');
            if (!$id) {
                return $this->error('Thiếu ID tác giả', [], 422);
            }

            $author = $this->authorModel->getById($id);
            if (!$author) {
                return $this->error('Tác giả không tồn tại', [], 404);
            }

            $name = $this->request->get('name');
            if (!$name) {
                return $this->error('Vui lòng cung cấp tên tác giả', [], 422);
            }

            $data = [
                'name' => $name,
                'biography' => $this->request->get('biography'),
                'nationality' => $this->request->get('nationality'),
                'birth_year' => $this->request->get('birth_year'),
                'death_year' => $this->request->get('death_year'),
            ];

            if ($this->authorModel->update($id, $data)) {
                return $this->success(['id' => $id], 'Cập nhật tác giả thành công');
            }

            return $this->error('Cập nhật tác giả thất bại', [], 400);
        } catch (Exception $e) {
            return $this->error($e->getMessage(), [], 500);
        }
    }

    /**
     * DELETE /api/authors/{id}
     */
    public function destroy($params = [])
    {
        try {
            $role = $_SESSION['role'] ?? null;
            if ($role !== 'admin' && $role !== 'librarian') {
                return $this->error('Bạn không có quyền xóa tác giả', [], 403);
            }

            $id = $params['id'] ?? $this->request->get('id');
            if (!$id) {
                return $this->error('Thiếu ID tác giả', [], 422);
            }

            $author = $this->authorModel->getById($id);
            if (!$author) {
                return $this->error('Tác giả không tồn tại', [], 404);
            }

            if ($this->authorModel->delete($id)) {
                return $this->success(['id' => $id], 'Xóa tác giả thành công');
            }

            return $this->error('Xóa tác giả thất bại', [], 400);
        } catch (Exception $e) {
            return $this->error($e->getMessage(), [], 500);
        }
    }
}

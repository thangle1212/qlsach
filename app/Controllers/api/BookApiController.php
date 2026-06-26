<?php

require_once __DIR__ . '/BaseApiController.php';
require_once __DIR__ . '/../../Models/Book.php';

class BookApiController extends BaseApiController
{
    private $book;

    public function __construct()
    {
        parent::__construct();
        $this->book = new Book();
    }

    public function index()
    {
        try {
            $search = $this->request->get('search', null);
            $authorId = $this->request->get('author_id', null);
            $categoryId = $this->request->get('category_id', null);
            $publisherId = $this->request->get('publisher_id', null);

            if (!empty($search)) {
                $books = $this->book->search($search);
            } elseif (!empty($authorId)) {
                $books = $this->book->getByAuthor($authorId);
            } elseif (!empty($categoryId)) {
                $books = $this->book->getByCategory($categoryId);
            } elseif (!empty($publisherId)) {
                $books = $this->book->getByPublisher($publisherId);
            } else {
                $books = $this->book->getAll();
            }

            return $this->success(['books' => $books], 'Danh sách sách');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), [], 500);
        }
    }

    public function show($params = [])
    {
        try {
            $bookId = $params['id'] ?? $this->request->get('id');
            if (!$bookId) {
                return $this->error('Thiếu ID sách', [], 422);
            }

            $book = $this->book->find($bookId);
            if (!$book) {
                return $this->error('Sách không tồn tại', [], 404);
            }

            return $this->success(['book' => $book], 'Chi tiết sách');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), [], 500);
        }
    }

    public function store($params = [])
    {
        try {
            $role = $_SESSION['role'] ?? 'member';
            if ($role !== 'admin' && $role !== 'librarian') {
                return $this->error('Bạn không có quyền tạo sách', [], 403);
            }

            $data = [
                'title' => $this->request->get('title'),
                'isbn' => $this->request->get('isbn'),
                'author_id' => $this->request->get('author_id'),
                'publisher_id' => $this->request->get('publisher_id'),
                'category_id' => $this->request->get('category_id'),
                'total_copies' => (int)$this->request->get('total_copies'),
                'publication_year' => $this->request->get('publication_year'),
                'pages' => $this->request->get('pages'),
                'description' => $this->request->get('description'),
            ];

            if (empty($data['title']) || empty($data['total_copies'])) {
                return $this->error('Vui lòng cung cấp title và total_copies', [], 422);
            }

            if ($data['total_copies'] <= 0) {
                return $this->error('total_copies phải lớn hơn 0', [], 422);
            }

            $inserted = $this->book->insert($data);
            if (!$inserted) {
                return $this->error('Tạo sách thất bại', [], 400);
            }

            return $this->success(['book_id' => $this->book->getLastInsertId()], 'Tạo sách thành công', 201);
        } catch (Exception $e) {
            return $this->error('Tạo sách thất bại: ' . $e->getMessage(), [], 400);
        }
    }

    public function update($params = [])
    {
        try {
            $role = $_SESSION['role'] ?? 'member';
            if ($role !== 'admin' && $role !== 'librarian') {
                return $this->error('Bạn không có quyền cập nhật sách', [], 403);
            }

            $bookId = $params['id'] ?? $this->request->get('id');
            if (!$bookId) {
                return $this->error('Thiếu ID sách', [], 422);
            }

            $book = $this->book->getById($bookId);
            if (!$book) {
                return $this->error('Sách không tồn tại', [], 404);
            }

            $data = [
                'title' => $this->request->get('title'),
                'isbn' => $this->request->get('isbn'),
                'author_id' => $this->request->get('author_id'),
                'publisher_id' => $this->request->get('publisher_id'),
                'category_id' => $this->request->get('category_id'),
                'total_copies' => (int)$this->request->get('total_copies'),
                'publication_year' => $this->request->get('publication_year'),
                'pages' => $this->request->get('pages'),
                'description' => $this->request->get('description'),
            ];

            if (empty($data['title']) || empty($data['total_copies'])) {
                return $this->error('Vui lòng cung cấp title và total_copies', [], 422);
            }

            if ($data['total_copies'] <= 0) {
                return $this->error('total_copies phải lớn hơn 0', [], 422);
            }

            $updated = $this->book->update($bookId, $data);
            if (!$updated) {
                return $this->error('Cập nhật sách thất bại', [], 400);
            }

            return $this->success(['book_id' => $bookId], 'Cập nhật sách thành công');
        } catch (Exception $e) {
            return $this->error('Cập nhật sách thất bại: ' . $e->getMessage(), [], 400);
        }
    }

    public function destroy($params = [])
    {
        try {
            $role = $_SESSION['role'] ?? 'member';
            if ($role !== 'admin' && $role !== 'librarian') {
                return $this->error('Bạn không có quyền xóa sách', [], 403);
            }

            $bookId = $params['id'] ?? $this->request->get('id');
            if (!$bookId) {
                return $this->error('Thiếu ID sách', [], 422);
            }

            $book = $this->book->getById($bookId);
            if (!$book) {
                return $this->error('Sách không tồn tại', [], 404);
            }

            if (!$this->book->delete($bookId)) {
                return $this->error('Xóa sách thất bại. Có thể sách đang được mượn', [], 400);
            }

            return $this->success(['book_id' => $bookId], 'Xóa sách thành công');
        } catch (Exception $e) {
            return $this->error('Xóa sách thất bại: ' . $e->getMessage(), [], 400);
        }
    }
}

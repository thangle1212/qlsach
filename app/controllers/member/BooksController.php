<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../../models/member/BookModel.php';

class BooksController extends BaseController
{
    private $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new BookModel();
    }

    public function index()
    {
        $books = $this->model->getAvailableBooks();
        require_once __DIR__ . '/../../views/member/books/index.php';
    }
}

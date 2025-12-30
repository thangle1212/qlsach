<?php
class FineController extends Controller {
    private $model;

    function __construct() {
        $this->model = $this->model("FineModel");
    }

    function index() {
        $this->view("Master", [
            "page"=>"admin/fines",
            "fines"=>$this->model->getAll()
        ]);
    }

    function add() {
        $users = $this->model->getUsers();
        $error = '';

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_POST['user_id'];
            $amount = $_POST['amount'];
            $reason = $_POST['reason'];
            $status = $_POST['status'];

            if(!$user_id || !$amount || !$reason || !$status) {
                $error = "Bạn chưa nhập đủ thông tin!";
            } elseif(!$this->model->insert($user_id, $amount, $reason, $status)) {
                $error = "Số tiền không hợp lệ hoặc lỗi thêm dữ liệu!";
            } else {
                header("Location: /ADMIN/Fine");
                exit;
            }
        }

        $this->view("Master", [
            "page"=>"admin/fine_add",
            "users"=>$users,
            "error"=>$error
        ]);
    }

    function edit($id) {
        $fine = $this->model->get($id);
        $users = $this->model->getUsers();
        $error = '';

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_POST['user_id'];
            $amount = $_POST['amount'];
            $reason = $_POST['reason'];
            $status = $_POST['status'];

            if(!$user_id || !$amount || !$reason || !$status) {
                $error = "Bạn chưa nhập đủ thông tin!";
            } elseif(!$this->model->update($id, $user_id, $amount, $reason, $status)) {
                $error = "Số tiền không hợp lệ hoặc lỗi cập nhật!";
            } else {
                header("Location: /ADMIN/Fine");
                exit;
            }
        }

        $this->view("Master", [
            "page"=>"admin/fine_edit",
            "fine"=>$fine,
            "users"=>$users,
            "error"=>$error
        ]);
    }

    function delete($id) {
        $this->model->delete($id);
        header("Location: /ADMIN/Fine");
    }
}

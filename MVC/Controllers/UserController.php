<?php
class UserController extends Controller {
    private $model;

    function __construct() {
        $this->model = $this->model("UserModel");
    }

    function index() {
        $this->view("Master", [
            "page"=>"admin/users",
            "users"=>$this->model->getAll()
        ]);
    }

    function add() {
        $error = '';
        if(isset($_POST["add"])) {
            $username = trim($_POST["username"]);
            $email = trim($_POST["email"]);
            $password = trim($_POST["password"]);
            $role = $_POST["role"];

            if(!$username || !$email || !$password) {
                $error = "Bạn chưa nhập đủ thông tin!";
            } elseif($this->model->exists($username, $email)) {
                $error = "Username hoặc Email đã tồn tại!";
            } else {
                $this->model->insert($username, $email, $password, $role);
                header("Location: /ADMIN/User");
                exit;
            }
        }

        $this->view("Master", [
            "page"=>"admin/user_add",
            "error"=>$error
        ]);
    }

    function edit($id) {
        $user = $this->model->getOne($id);
        $error = '';

        if(isset($_POST["save"])) {
            $username = trim($_POST["username"]);
            $email = trim($_POST["email"]);
            $password = trim($_POST["password"]);
            $role = $_POST["role"];

            if(!$username || !$email) {
                $error = "Bạn chưa nhập đủ thông tin!";
            } elseif($this->model->exists($username, $email, $id)) {
                $error = "Username hoặc Email đã tồn tại!";
            } else {
                $this->model->update($id, $username, $email, $password, $role);
                header("Location: /ADMIN/User");
                exit;
            }
        }

        $this->view("Master", [
            "page"=>"admin/user_edit",
            "user"=>$user,
            "error"=>$error
        ]);
    }

    function delete($id) {
        $this->model->delete($id);
        header("Location: /ADMIN/User");
    }
}

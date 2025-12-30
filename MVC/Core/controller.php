<?php
class Controller {
    function model($model) {
        require_once "./MVC/Models/".$model.".php";
        return new $model;
    }

    function view($layout, $data = []) {
        require_once "./MVC/Views/".$layout.".php";
    }
}

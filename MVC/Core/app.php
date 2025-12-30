<?php
class app {

    protected $controller = 'UserController';
    protected $action = 'index';
    protected $params = [];

    function __construct() {
        if (isset($_GET['url'])) {
            $url = trim($_GET['url'], '/');
            $url = explode('/', filter_var($url, FILTER_SANITIZE_URL));
        } else {
            $url = [];
        }

        // Xác định controller
        if (isset($url[0])) {
            $controllerName = $url[0] . 'Controller';
            if (file_exists("./MVC/Controllers/".$controllerName.".php")) {
                $this->controller = $controllerName;
                unset($url[0]);
            }
        }

        require_once "./MVC/Controllers/".$this->controller.".php";
        $this->controller = new $this->controller;

        // Xác định action
        if (isset($url[1]) && method_exists($this->controller, $url[1])) {
            $this->action = $url[1];
            unset($url[1]);
        }

        // Params
        $this->params = $url ? array_values($url) : [];

        call_user_func_array([$this->controller, $this->action], $this->params);
    }
}

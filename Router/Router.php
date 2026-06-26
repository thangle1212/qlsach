<?php

/**
 * Router Class - Xử lý routing cho ứng dụng
 * Hỗ trợ cả URL query params và clean URLs
 */

class Router
{
    private $routes = [];
    private $url;
    private $method;
    private $controller;
    private $action;
    private $params = [];
    private $currentRoute = null;

    public function __construct($url = '', $method = '')
    {
        $this->url = rtrim($url, '/') ?: '/';
        $this->method = strtoupper($method ?: ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
        $this->parseUrl();
    }

    /**
     * Phân tích URL từ REQUEST_URI
     */
    private function parseUrl()
    {
        // Hỗ trợ cả URL gốc và rewrite bằng ?url=/api/borrowings/1
        $url = $_GET['url'] ?? $_SERVER['REQUEST_URI'] ?? '';

        // Loại bỏ query string
        if (strpos($url, '?') !== false) {
            $url = substr($url, 0, strpos($url, '?'));
        }

        // Nếu URL bắt đầu bằng /index.php, bỏ phần này để giữ clean URL
        if (strpos($url, '/index.php') === 0) {
            $url = substr($url, strlen('/index.php'));
        }

        // Loại bỏ script path nếu URL có đầy đủ path /qlsach/api/...
        $scriptPath = dirname($_SERVER['SCRIPT_NAME']);
        if ($scriptPath !== '/' && strpos($url, $scriptPath) === 0) {
            $url = substr($url, strlen($scriptPath));
        }

        $this->url = rtrim($url, '/') ?: '/';
    }

    /**
     * Đăng ký route GET
     */
    public function get($pattern, $callback)
    {
        $this->addRoute('GET', $pattern, $callback);
    }

    /**
     * Đăng ký route POST
     */
    public function post($pattern, $callback)
    {
        $this->addRoute('POST', $pattern, $callback);
    }

    /**
     * Đăng ký route PUT
     */
    public function put($pattern, $callback)
    {
        $this->addRoute('PUT', $pattern, $callback);
    }

    /**
     * Đăng ký route DELETE
     */
    public function delete($pattern, $callback)
    {
        $this->addRoute('DELETE', $pattern, $callback);
    }

    /**
     * Thêm route vào mảng
     */
    private function addRoute($method, $pattern, $callback)
    {
        $route = [
            'method' => $method,
            'pattern' => $pattern,
            'callback' => $callback,
            'middleware' => []
        ];

        $this->routes[] = $route;
        $this->currentRoute = &$this->routes[count($this->routes) - 1];

        return $this;
    }

    /**
     * Thêm middleware cho route hiện tại
     * Sử dụng: $router->get('/admin/books', 'book@index')->middleware('auth');
     */
    public function middleware($middlewareName)
    {
        if ($this->currentRoute !== null) {
            if (!in_array($middlewareName, $this->currentRoute['middleware'])) {
                $this->currentRoute['middleware'][] = $middlewareName;
            }
        }
        return $this;
    }

    /**
     * Kiểm tra pattern có match với URL không
     */
    private function matchPattern($pattern, $url)
    {
        // Chuyển pattern thành regex
        $regex = preg_replace_callback('/\{(\w+)\}/', function ($matches) {
            return '(?P<' . $matches[1] . '>[^/]+)';
        }, $pattern);

        $regex = '^' . $regex . '$';

        if (preg_match('#' . $regex . '#', $url, $matches)) {
            // Loại bỏ các match toàn bộ chuỗi
            foreach ($matches as $key => $value) {
                if (is_numeric($key)) {
                    unset($matches[$key]);
                }
            }
            return $matches;
        }

        return false;
    }

    /**
     * Chạy router - tìm route phù hợp và thực hiện
     */
    public function run()
    {
        global $request, $response;

        // Kiểm tra các routes đã đăng ký
        foreach ($this->routes as $route) {
            if ($route['method'] === $this->method) {
                $matches = $this->matchPattern($route['pattern'], $this->url);

                if ($matches !== false) {
                    $this->params = $matches;

                    // Thực hiện middlewares trước khi chạy callback
                    if (!empty($route['middleware'])) {
                        $this->executeMiddleware($route['middleware']);
                    }

                    $this->executeCallback($route['callback']);
                    return;
                }
            }
        }

        // Nếu không tìm thấy route, chuyển sang query params
        $this->handleQueryParams();
    }

    /**
     * Thực hiện middleware stack
     */
    private function executeMiddleware($middlewares)
    {
        global $request, $response;

        $index = 0;

        $next = function () use (&$index, $middlewares, &$next) {
            if ($index >= count($middlewares)) {
                return;
            }

            $middlewareName = $middlewares[$index];
            $index++;

            // Load middleware class
            $middlewareFile = SYSTEM_PATH . 'Middleware/' . ucfirst($middlewareName) . 'Middleware.php';

            if (!file_exists($middlewareFile)) {
                throw new Exception("Middleware file not found: " . $middlewareFile, 500);
            }

            require_once $middlewareFile;
            $middlewareClass = ucfirst($middlewareName) . 'Middleware';

            if (!class_exists($middlewareClass)) {
                throw new Exception("Middleware class not found: $middlewareClass", 500);
            }

            $middleware = new $middlewareClass();
            $middleware->handle($request, $response, $next);
        };

        $next();
    }

    /**
     * Xử lý URL query params (backward compatibility)
     * Ví dụ: index.php?controller=book&action=index
     */
    private function handleQueryParams()
    {
        $this->controller = ucfirst($_GET['controller'] ?? 'auth');
        $this->action = $_GET['action'] ?? 'index';

        // Trích xuất các params khác từ GET
        $this->params = array_diff_key(
            $_GET,
            array_flip(['controller', 'action'])
        );

        $this->executeController();
    }

    /**
     * Thực hiện callback (có thể là closure hoặc controller@action)
     */
    private function executeCallback($callback)
    {
        // Nếu là closure
        if (is_callable($callback)) {
            call_user_func_array($callback, [$this->params]);
            return;
        }

        // Nếu là string dạng "controller@action"
        if (is_string($callback) && strpos($callback, '@') !== false) {
            list($controller, $action) = explode('@', $callback);
            $this->controller = ucfirst($controller);
            $this->action = $action;
            $this->executeController();
        }
    }

    /**
     * Thực hiện controller method
     */
    private function executeController()
    {
        if (substr($this->controller,-3) === 'Api')
{
    $controllerFile =
        APP_PATH .
        'Controllers/api/' .
        $this->controller .
        'Controller.php';
}
else
{
    $controllerFile =
        APP_PATH .
        'Controllers/' .
        $this->controller .
        'Controller.php';
}

        if (!file_exists($controllerFile)) {
            $this->handle404();
            return;
        }

        require_once $controllerFile;
        $controllerClass = $this->controller . 'Controller';

        if (!class_exists($controllerClass)) {
            $this->handle404();
            return;
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $this->action)) {
            $this->handle404();
            return;
        }

        // Gọi action method với params
        call_user_func_array([$controller, $this->action], [$this->params]);
    }

    /**
     * Xử lý 404 Not Found
     */
    private function handle404()
    {
        http_response_code(404);
        echo "404 - Trang không tìm thấy";
        exit;
    }

    /**
     * Getter cho controller
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Getter cho action
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Getter cho params
     */
    public function getParams()
    {
        return $this->params;
    }
}

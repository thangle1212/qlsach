<?php

/**
 * BaseController - Base class cho tất cả controllers
 * Cung cấp các phương thức tiện ích dùng chung
 */

require_once __DIR__ . '/../Services/PermissionService.php';

abstract class BaseController
{

    /**
     * Request object
     * @var \Http\Request
     */
    protected $request;

    /**
     * Response object
     * @var \Http\Response
     */
    protected $response;

    /**
     * Permission service
     */
    protected $permissionService;

    /**
     * Constructor - khởi tạo controller
     * 
     * Tự động:
     *   - Kiểm tra xác thực (auth)
     *   - Load $request và $response từ global
     *   - Khởi tạo PermissionService
     */
    public function __construct()
    {
        // Lấy global request và response
        global $request, $response;
        $this->request = $request;
        $this->response = $response;

        // Kiểm tra xác thực
        $this->checkAuth();

        // Khởi tạo permission service
        $this->permissionService = new PermissionService();
    }

    /**
     * Kiểm tra user đã đăng nhập chưa
     * Nếu chưa, redirect tới login
     */
    protected function checkAuth()
    {
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';
        $rewriteUrl = $_GET['url'] ?? '';
        $pathFromUri = parse_url($requestUri, PHP_URL_PATH) ?? '';
        $requestPath = trim($rewriteUrl !== '' ? $rewriteUrl : $pathFromUri, '/');
        $requestPath = ltrim(str_replace('/qlsach', '', $requestPath), '/');
        $requestPath = preg_replace('#^index\.php#', '', $requestPath);
        $requestPath = ltrim($requestPath, '/');

        if (!isset($_SESSION['user_id'])) {
            if (strpos($requestPath, 'api/') === 0) {
                $this->error('Bạn cần đăng nhập để truy cập API', [], 401);
            }

            redirect('index.php?controller=auth&action=showLogin');
        }
    }

    /**
     * Kiểm tra user có quyền không
     * Nếu không, redirect tới trang trước
     * 
     * @param string $redirect - Đường dẫn redirect nếu không quyền
     */
    protected function handleUnauthorized($redirect = "index.php?controller=book")
    {
        $_SESSION['error'] = 'Bạn không có quyền thực hiện hành động này';
        redirect($redirect);
    }

    /**
     * Redirect tới URL với flash message
     * 
     * @param string $location - URL redirect
     * @param string|null $message - Thông báo
     * @param string $type - Loại thông báo (success, error, warning, info)
     */
    protected function handleRedirect($location, $message = null, $type = 'success')
    {
        if ($message) {
            $_SESSION[$type] = $message;
        }
        redirect($location);
    }

    /**
     * Render view
     * 
     * Cách sử dụng:
     *   $this->view('books/index', ['books' => $books]);
     * 
     * @param string $viewPath - Đường dẫn view (không cần .php)
     * @param array $data - Dữ liệu truyền vào view
     * @param int $statusCode - HTTP status code
     */
    protected function view($viewPath, $data = [], $statusCode = 200)
    {
        $this->response->view($viewPath, $data, $statusCode);
        exit;
    }

    /**
     * Trả về JSON success
     * 
     * Cách sử dụng:
     *   $this->success(['user' => $user], 'User created', 201);
     * 
     * @param array|object $data - Dữ liệu
     * @param string $message - Thông báo
     * @param int $statusCode - HTTP status code (mặc định 200)
     */
    protected function success($data = [], $message = 'Success', $statusCode = 200)
    {
        $this->response->success($data, $message, $statusCode)->render();
        exit;
    }

    /**
     * Trả về JSON error
     * 
     * Cách sử dụng:
     *   $this->error('User not found', [], 404);
     *   $this->error('Validation failed', ['email' => 'Invalid email'], 400);
     * 
     * @param string $message - Thông báo lỗi
     * @param array $errors - Chi tiết lỗi
     * @param int $statusCode - HTTP status code (mặc định 400)
     */
    protected function error($message = 'Error', $errors = [], $statusCode = 400)
    {
        $this->response->error($message, $errors, $statusCode)->render();
        exit;
    }

    /**
     * Validate input từ request
     * 
     * Cách sử dụng:
     *   $errors = $this->validate([
     *       'email' => 'required|email',
     *       'name' => 'required|string|min:3|max:100',
     *       'age' => 'numeric'
     *   ]);
     * 
     *   if (!empty($errors)) {
     *       return $this->error('Validation failed', $errors, 422);
     *   }
     * 
     * @param array $rules - Validation rules
     * @return array - Error messages (empty nếu valid)
     */
    protected function validate($rules)
    {
        return $this->request->validate($rules);
    }

    /**
     * Get input từ request
     * 
     * Cách sử dụng:
     *   $email = $this->input('email');
     *   $email = $this->input('email', 'default@example.com');
     * 
     * @param string $key - Key input
     * @param mixed $default - Giá trị mặc định
     * @return mixed
     */
    protected function input($key, $default = null)
    {
        return $this->request->input($key, $default);
    }

    /**
     * Get all input từ request
     * 
     * @return array
     */
    protected function allInput()
    {
        return $this->request->getAllInput();
    }

    /**
     * Check xem input có không
     * 
     * @param string $key
     * @return bool
     */
    protected function has($key)
    {
        return $this->request->has($key);
    }

    /**
     * Redirect đến URL
     * 
     * @param string $url - URL redirect
     * @param int $statusCode - HTTP status code
     */
    protected function redirect($url, $statusCode = 302)
    {
        $this->response->redirect($url, $statusCode);
    }

    /**
     * Download file
     * 
     * @param string $filePath - Đường dẫn file
     * @param string $fileName - Tên file khi download
     */
    protected function download($filePath, $fileName = null)
    {
        $this->response->download($filePath, $fileName);
    }

    /**
     * Validate và lấy loan slip
     * 
     * @param mixed $loanSlipId
     * @param mixed $borrowService
     * @param mixed $action
     * @return mixed
     */
    protected function validateAndGetLoan($loanSlipId, $borrowService = null, $action = null, $api = false)
    {
        if (!$loanSlipId) {
            if ($api) {
                $this->error('ID phiếu mượn không hợp lệ', [], 400);
            }

            $this->handleRedirect(
                "index.php?controller=borrowing",
                'ID phiếu mượn không hợp lệ',
                'error'
            );
        }

        if ($borrowService === null) {
            if ($api) {
                $this->error('Service không được cung cấp', [], 500);
            }

            $this->handleRedirect(
                "index.php?controller=borrowing",
                'Service không được cung cấp',
                'error'
            );
        }

        $loan = $borrowService->getLoanSlipById($loanSlipId);
        if (!$loan) {
            if ($api) {
                $this->error('Phiếu mượn không tồn tại', [], 404);
            }

            $this->handleRedirect(
                "index.php?controller=borrowing",
                'Phiếu mượn không tồn tại',
                'error'
            );
        }

        return $loan;
    }

    protected function checkPermission($permissionMethod, $params = [])
    {
        $callable = [$this->permissionService, $permissionMethod];
        if (!call_user_func_array($callable, $params)) {
            $this->handleUnauthorized();
        }
    }
}

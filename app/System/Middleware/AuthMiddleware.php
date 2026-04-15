<?php

/**
 * AuthMiddleware - Kiểm tra xác thực người dùng
 * 
 * Sử dụng:
 * $router->get('/books', 'book@index')->middleware('auth');
 * 
 * Kiểm tra $_SESSION['user_id'] có tồn tại hay không
 * Nếu không, trả về 401 Unauthorized
 */

class AuthMiddleware extends Middleware
{
    /**
     * Kiểm tra user đã đăng nhập hay chưa
     */
    public function handle($request, $response, $next)
    {
        // Kiểm tra session
        if (!isset($_SESSION['user_id'])) {
            $this->abort('Bạn cần đăng nhập để truy cập tài nguyên này', 401);
        }

        // User đã xác thực, tiếp tục xử lý
        return $this->proceed($next);
    }
}

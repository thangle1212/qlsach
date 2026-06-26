<?php

/**
 * Middleware - Base class cho tất cả middlewares
 * 
 * Middleware là các layer xử lý requests trước khi tới controllers
 * Có thể dùng để:
 * - Kiểm tra authentication
 * - Validate requests
 * - Log requests
 * - Modify headers
 */

abstract class Middleware
{
    /**
     * Handle request - phải được implement bởi subclasses
     * 
     * @param Request $request
     * @param Response $response
     * @param Closure $next Callback để tiếp tục xử lý
     * @return mixed
     */
    abstract public function handle($request, $response, $next);

    /**
     * Tiếp tục xử lý request tới layer tiếp theo
     * 
     * @param Closure $next
     * @return mixed
     */
    protected function proceed($next)
    {
        return call_user_func($next);
    }

    /**
     * Dừng xử lý và trả về error
     * 
     * @param string $message
     * @param int $statusCode
     */
    protected function abort($message, $statusCode = 403)
    {
        ErrorHandler::throw($message, $statusCode);
    }
}

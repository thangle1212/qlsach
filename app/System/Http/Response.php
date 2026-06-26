<?php

/**
 * Response Class - Xử lý HTTP Responses
 * Giúp trả về JSON, HTML, redirects một cách dễ dàng
 */

namespace Http;

class Response
{

    /**
     * HTTP response code hiện tại
     * 
     * @var int
     */
    private $statusCode = 200;

    /**
     * HTML body
     * 
     * @var string
     */
    private $body = '';

    /**
     * Headers
     * 
     * @var array
     */
    private $headers = [];

    /**
     * Response data (cho JSON)
     * 
     * @var array|object
     */
    private $data = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        // Set default headers for API
        $this->setHeader('Content-Type', 'application/json; charset=UTF-8');
        $this->setHeader('Access-Control-Allow-Origin', '*');
        $this->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS, PATCH');
        $this->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    }

    /**
     * Set HTTP status code
     * 
     * @param int $code - HTTP status code (200, 404, 500, etc.)
     * @return self
     */
    public function setStatusCode($code)
    {
        $this->statusCode = $code;
        return $this;
    }

    /**
     * Get HTTP status code
     * 
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Set response header
     * 
     * @param string $key - Header key (Content-Type, Authorization, etc.)
     * @param string $value - Header value
     * @return self
     */
    public function setHeader($key, $value)
    {
        $this->headers[$key] = $value;
        return $this;
    }

    /**
     * Get header value
     * 
     * @param string $key
     * @return string|null
     */
    public function getHeader($key)
    {
        return $this->headers[$key] ?? null;
    }

    /**
     * Get all headers
     * 
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Set response body (HTML)
     * 
     * @param string $body
     * @return self
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * Get response body
     * 
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set response data (JSON)
     * 
     * @param array|object $data
     * @return self
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Get response data
     * 
     * @return array|object
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Trả về JSON success response
     * 
     * @param array|object $data - Dữ liệu trả về
     * @param string $message - Thông báo
     * @param int $statusCode - HTTP status code (mặc định 200)
     * @return self
     */
    public function success($data = [], $message = 'Success', $statusCode = 200)
    {
        $this->setStatusCode($statusCode);
        $this->setData([
            'success' => true,
            'message' => $message,
            'data' => $data
        ]);
        return $this;
    }

    /**
     * Trả về JSON error response
     * 
     * @param string $message - Thông báo lỗi
     * @param array $errors - Chi tiết lỗi (optional)
     * @param int $statusCode - HTTP status code (mặc định 400)
     * @return self
     */
    public function error($message = 'Error', $errors = [], $statusCode = 400)
    {
        $this->setStatusCode($statusCode);
        $response = [
            'success' => false,
            'message' => $message
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        $this->setData($response);
        return $this;
    }

    /**
     * Trả về JSON với status code 400 Bad Request
     * 
     * @param string $message
     * @param array $errors
     * @return self
     */
    public function badRequest($message = 'Bad Request', $errors = [])
    {
        return $this->error($message, $errors, 400);
    }

    /**
     * Trả về JSON với status code 401 Unauthorized
     * 
     * @param string $message
     * @return self
     */
    public function unauthorized($message = 'Unauthorized')
    {
        return $this->error($message, [], 401);
    }

    /**
     * Trả về JSON với status code 403 Forbidden
     * 
     * @param string $message
     * @return self
     */
    public function forbidden($message = 'Forbidden')
    {
        return $this->error($message, [], 403);
    }

    /**
     * Trả về JSON với status code 404 Not Found
     * 
     * @param string $message
     * @return self
     */
    public function notFound($message = 'Not Found')
    {
        return $this->error($message, [], 404);
    }

    /**
     * Trả về JSON với status code 500 Internal Server Error
     * 
     * @param string $message
     * @return self
     */
    public function serverError($message = 'Internal Server Error')
    {
        return $this->error($message, [], 500);
    }

    /**
     * Set Content-Type header
     * 
     * @param string $contentType - Content type (application/json, text/html, etc.)
     * @return self
     */
    public function setContentType($contentType)
    {
        $this->setHeader('Content-Type', $contentType);
        return $this;
    }

    /**
     * Redirect tới URL
     * 
     * @param string $url - URL redirect tới
     * @param int $statusCode - HTTP status code (mặc định 302)
     */
    public function redirect($url, $statusCode = 302)
    {
        header("Location: $url", true, $statusCode);
        exit;
    }

    /**
     * Download file
     * 
     * @param string $filePath - Đường dẫn file
     * @param string $fileName - Tên file khi download (optional)
     */
    public function download($filePath, $fileName = null)
    {
        if (!file_exists($filePath)) {
            $this->notFound('File not found')->render();
            return;
        }

        $fileName = $fileName ?? basename($filePath);
        $fileSize = filesize($filePath);

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Length: ' . $fileSize);

        readfile($filePath);
        exit;
    }

    /**
     * Render response (gửi về client)
     * 
     * Output JSON nếu có data, HTML nếu có body
     */
    public function render()
    {
        // Set status code
        http_response_code($this->statusCode);

        // Send headers
        foreach ($this->headers as $key => $value) {
            header("$key: $value");
        }

        // Send body
        if (!empty($this->data)) {
            // JSON response
            echo json_encode($this->data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        } else if (!empty($this->body)) {
            // HTML response
            echo $this->body;
        }
    }

    /**
     * Render JSON
     * 
     * @param array|object $data
     * @param int $statusCode
     */
    public function json($data, $statusCode = 200)
    {
        $this->setStatusCode($statusCode);
        $this->setData($data);
        $this->render();
    }

    /**
     * Render HTML
     * 
     * @param string $html
     * @param int $statusCode
     */
    public function html($html, $statusCode = 200)
    {
        $this->setStatusCode($statusCode);
        $this->setContentType('text/html; charset=UTF-8');
        $this->setBody($html);
        $this->render();
    }

    /**
     * Render view file
     * 
     * @param string $viewPath - Đường dẫn file view
     * @param array $data - Dữ liệu truyền vào view
     * @param int $statusCode
     */
    public function view($viewPath, $data = [], $statusCode = 200)
    {
        // Kiểm tra file tồn tại
        $fullPath = VIEWS_PATH . $viewPath . '.php';
        if (!file_exists($fullPath)) {
            $this->notFound("View file not found: $viewPath")->render();
            return;
        }

        // Extract data thành biến
        extract($data);

        // Render view
        ob_start();
        include $fullPath;
        $html = ob_get_clean();

        $this->html($html, $statusCode);
    }

    /**
     * Get status text (200 => OK, 404 => Not Found, etc.)
     * 
     * @param int $statusCode
     * @return string
     */
    private function getStatusText($statusCode)
    {
        $statuses = [
            200 => 'OK',
            201 => 'Created',
            204 => 'No Content',
            301 => 'Moved Permanently',
            302 => 'Found',
            304 => 'Not Modified',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
        ];

        return $statuses[$statusCode] ?? 'Unknown';
    }

    /**
     * To array (cho debug)
     * 
     * @return array
     */
    public function toArray()
    {
        return [
            'status_code' => $this->statusCode,
            'headers' => $this->headers,
            'data' => $this->data,
            'body' => $this->body
        ];
    }
}

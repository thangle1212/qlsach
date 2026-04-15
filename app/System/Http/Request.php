<?php

/**
 * Request Class - Xử lý HTTP Requests
 * Giúp lấy dữ liệu từ request một cách dễ dàng
 */

namespace Http;

class Request
{

    /**
     * Lấy HTTP method (GET, POST, PUT, DELETE, v.v.)
     * 
     * @return string
     */
    public function getMethod()
    {
        return strtoupper($_SERVER['REQUEST_METHOD']);
    }

    /**
     * Lấy request URL (PATH_INFO)
     * VD: /books/5 → /books/5
     * 
     * @return string
     */
    public function getUrl()
    {
        $url = $_SERVER['REQUEST_URI'] ?? '';

        // Loại bỏ query string (?...)
        if (strpos($url, '?') !== false) {
            $url = substr($url, 0, strpos($url, '?'));
        }

        // Loại bỏ script path
        $scriptPath = dirname($_SERVER['SCRIPT_NAME']);
        if ($scriptPath !== '/' && strpos($url, $scriptPath) === 0) {
            $url = substr($url, strlen($scriptPath));
        }

        return rtrim($url, '/') ?: '/';
    }

    /**
     * Kiểm tra là GET request không
     * 
     * @return bool
     */
    public function isGet()
    {
        return $this->getMethod() === 'GET';
    }

    /**
     * Kiểm tra là POST request không
     * 
     * @return bool
     */
    public function isPost()
    {
        return $this->getMethod() === 'POST';
    }

    /**
     * Kiểm tra là PUT request không
     * 
     * @return bool
     */
    public function isPut()
    {
        return $this->getMethod() === 'PUT';
    }

    /**
     * Kiểm tra là DELETE request không
     * 
     * @return bool
     */
    public function isDelete()
    {
        return $this->getMethod() === 'DELETE';
    }

    /**
     * Kiểm tra là AJAX request không
     * 
     * @return bool
     */
    public function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Lấy query parameters từ URL (?key=value)
     * VD: GET /books?sort=name → ['sort' => 'name']
     * 
     * @param string|null $key - Nếu có key, lấy value của key đó
     * @param mixed $default - Giá trị mặc định nếu key không tồn tại
     * @return mixed
     */
    public function getQuery($key = null, $default = null)
    {
        if ($key === null) {
            return $_GET;
        }
        return $_GET[$key] ?? $default;
    }

    /**
     * Lấy POST data từ request body
     * Support cả form-data và JSON
     * 
     * @param string|null $key - Nếu có key, lấy value của key đó
     * @param mixed $default - Giá trị mặc định
     * @return mixed
     */
    public function getPost($key = null, $default = null)
    {
        // Nếu là JSON request
        if ($this->isJson()) {
            $data = $this->getJsonData();
        } else {
            // Nếu là form-data
            $data = $_POST;
        }

        if ($key === null) {
            return $data;
        }
        return $data[$key] ?? $default;
    }

    /**
     * Lấy tất cả input (GET + POST)
     * 
     * @return array
     */
    public function getAllInput()
    {
        return array_merge($_GET, $_POST);
    }

    /**
     * Kiểm tra input key có tồn tại không
     * 
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        return isset($_GET[$key]) || isset($_POST[$key]);
    }

    /**
     * Lấy input value (từ GET hoặc POST)
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function input($key, $default = null)
    {
        if (isset($_POST[$key])) {
            return $_POST[$key];
        }
        if (isset($_GET[$key])) {
            return $_GET[$key];
        }
        return $default;
    }

    /**
     * Kiểm tra là JSON request không
     * 
     * @return bool
     */
    public function isJson()
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        return strpos($contentType, 'application/json') !== false;
    }

    /**
     * Lấy JSON data từ request body
     * 
     * @return array|object
     */
    public function getJsonData()
    {
        $json = file_get_contents('php://input');
        return json_decode($json, true) ?? [];
    }

    /**
     * Lấy value từ JSON hoặc POST data
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        // Nếu là JSON
        if ($this->isJson()) {
            $data = $this->getJsonData();
            return $data[$key] ?? $default;
        }

        // Nếu là form-data
        return $this->input($key, $default);
    }

    /**
     * Lấy header value
     * 
     * @param string $key
     * @param mixed $default
     * @return string|null
     */
    public function getHeader($key, $default = null)
    {
        $key = strtoupper(str_replace('-', '_', $key));
        $key = 'HTTP_' . $key;
        return $_SERVER[$key] ?? $default;
    }

    /**
     * Lấy Authorization header
     * VD: "Bearer token123" → "Bearer"
     * 
     * @return string|null
     */
    public function getAuthorizationType()
    {
        $auth = $this->getHeader('Authorization');
        if ($auth && strpos($auth, ' ') !== false) {
            return explode(' ', $auth)[0];
        }
        return null;
    }

    /**
     * Lấy token từ Authorization header
     * VD: "Bearer token123" → "token123"
     * 
     * @return string|null
     */
    public function getAuthorizationToken()
    {
        $auth = $this->getHeader('Authorization');
        if ($auth && strpos($auth, ' ') !== false) {
            return explode(' ', $auth)[1] ?? null;
        }
        return null;
    }

    /**
     * Lấy client IP address
     * 
     * @return string|null
     */
    public function getIp()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        } else {
            return $_SERVER['REMOTE_ADDR'] ?? null;
        }
    }

    /**
     * Lấy user agent (browser, device info)
     * 
     * @return string|null
     */
    public function getUserAgent()
    {
        return $_SERVER['HTTP_USER_AGENT'] ?? null;
    }

    /**
     * Lấy host
     * 
     * @return string|null
     */
    public function getHost()
    {
        return $_SERVER['HTTP_HOST'] ?? null;
    }

    /**
     * Lấy referer (trang trước đó)
     * 
     * @return string|null
     */
    public function getReferer()
    {
        return $_SERVER['HTTP_REFERER'] ?? null;
    }

    /**
     * Lấy file uploads
     * 
     * @param string|null $name - Nếu có name, lấy file đó
     * @return array|null
     */
    public function getFile($name = null)
    {
        if ($name === null) {
            return $_FILES;
        }
        return $_FILES[$name] ?? null;
    }

    /**
     * Kiểm tra có file upload không
     * 
     * @param string $name
     * @return bool
     */
    public function hasFile($name)
    {
        return isset($_FILES[$name]) && $_FILES[$name]['error'] === UPLOAD_ERR_OK;
    }

    /**
     * Lấy tất cả file upload
     * 
     * @return array
     */
    public function getAllFiles()
    {
        return $_FILES;
    }

    /**
     * Validate input
     * 
     * @param array $rules - Rules: ['email' => 'required|email', 'name' => 'required|string']
     * @return array - Error messages
     * 
     * Supported rules:
     *   - required: field bắt buộc không để trống
     *   - email: phải là email hợp lệ
     *   - string: phải là string
     *   - numeric: phải là số
     *   - min:n: độ dài tối thiểu n ký tự
     *   - max:n: độ dài tối đa n ký tự
     */
    public function validate($rules)
    {
        $errors = [];

        foreach ($rules as $field => $ruleString) {
            $fieldRules = explode('|', $ruleString);

            foreach ($fieldRules as $rule) {
                $value = $this->input($field);

                // required
                if ($rule === 'required' && empty($value)) {
                    $errors[$field] = "$field bắt buộc không được để trống";
                    break;
                }

                // email
                if ($rule === 'email' && !empty($value)) {
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $errors[$field] = "$field phải là email hợp lệ";
                        break;
                    }
                }

                // string
                if ($rule === 'string' && !empty($value)) {
                    if (!is_string($value)) {
                        $errors[$field] = "$field phải là chuỗi";
                        break;
                    }
                }

                // numeric
                if ($rule === 'numeric' && !empty($value)) {
                    if (!is_numeric($value)) {
                        $errors[$field] = "$field phải là số";
                        break;
                    }
                }

                // min
                if (strpos($rule, 'min:') === 0 && !empty($value)) {
                    $min = (int) substr($rule, 4);
                    if (strlen($value) < $min) {
                        $errors[$field] = "$field phải có độ dài tối thiểu $min ký tự";
                        break;
                    }
                }

                // max
                if (strpos($rule, 'max:') === 0 && !empty($value)) {
                    $max = (int) substr($rule, 4);
                    if (strlen($value) > $max) {
                        $errors[$field] = "$field phải có độ dài tối đa $max ký tự";
                        break;
                    }
                }
            }
        }

        return $errors;
    }
}

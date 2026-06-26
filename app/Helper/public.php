<?php

/**
 * Helper Functions - Các hàm tiện ích toàn ứng dụng
 * Sử dụng được ở khắp nơi trong ứng dụng
 */

/**
 * Lấy base URL của ứng dụng
 */
function base_url($path = '')
{
    return BASE_URL . ltrim($path, '/');
}

/**
 * Lấy asset URL (CSS, JS, images)
 */
function asset($path)
{
    return base_url('assets/' . ltrim($path, '/'));
}

/**
 * Lấy upload URL
 */
function upload($path = '')
{
    return base_url('Upload/' . ltrim($path, '/'));
}

/**
 * Redirect tới URL
 */
function redirect($url, $statusCode = 302)
{
    if (!preg_match('#^https?://#i', $url) && strpos($url, '/') !== 0) {
        $url = BASE_URL . $url;
    }

    header("Location: " . $url, true, $statusCode);
    exit;
}

/**
 * Kiểm tra user đã login chưa
 */
function is_logged_in()
{
    return isset($_SESSION['user_id']);
}

/**
 * Lấy user_id hiện tại
 */
function current_user_id()
{
    return $_SESSION['user_id'] ?? null;
}

/**
 * Lấy role của user hiện tại
 */
function current_user_role()
{
    return $_SESSION['role'] ?? null;
}

/**
 * Kiểm tra user có phải admin không
 */
function is_admin()
{
    return current_user_role() === 'admin';
}

/**
 * Kiểm tra user có phải librarian không
 */
function is_librarian()
{
    return current_user_role() === 'librarian';
}

/**
 * Kiểm tra user có phải member không
 */
function is_member()
{
    return current_user_role() === 'member';
}

/**
 * Escape HTML characters
 */
function escape($string)
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Lấy value từ mảng với key mặc định
 */
function array_value($array, $key, $default = null)
{
    return isset($array[$key]) ? $array[$key] : $default;
}

/**
 * Lấy flash message từ session
 */
function get_flash($type = 'success')
{
    if (isset($_SESSION[$type])) {
        $message = $_SESSION[$type];
        unset($_SESSION[$type]);
        return $message;
    }
    return null;
}

/**
 * Set flash message vào session
 */
function set_flash($message, $type = 'success')
{
    $_SESSION[$type] = $message;
}

/**
 * Format ngày theo Vietnamese format
 */
function format_date($date, $format = 'd/m/Y')
{
    if (empty($date)) {
        return '';
    }
    return date($format, strtotime($date));
}

/**
 * Format tiền tệ VND
 */
function format_currency($amount, $currency = 'đ')
{
    return number_format($amount, 0, ',', '.') . $currency;
}

/**
 * Validate email
 */
function is_valid_email($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate phone
 */
function is_valid_phone($phone)
{
    return preg_match('/^\d{10,11}$/', $phone) === 1;
}

/**
 * Kiểm tra string rỗng hoặc chỉ chứa whitespace
 */
function is_empty($str)
{
    return empty(trim($str));
}

/**
 * Truncate string
 */
function str_limit($str, $limit = 100, $end = '...')
{
    if (strlen($str) > $limit) {
        return substr($str, 0, $limit) . $end;
    }
    return $str;
}

/**
 * Parse ID từ URL params
 * Ví dụ: [1, 2, 3] từ /books/1/comments/2/replies/3
 */
function parse_url_params($params)
{
    $ids = [];
    foreach ($params as $key => $value) {
        if (is_numeric($value)) {
            $ids[] = $value;
        }
    }
    return $ids;
}

/**
 * Lấy config value
 */
function get_config($key = null)
{
    return config($key);
}

/**
 * Debug print data
 */
function dd($data)
{
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    exit;
}

/**
 * Debug print data (không exit)
 */
function dump($data)
{
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
}

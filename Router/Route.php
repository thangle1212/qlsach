<?php

/**
 * Router.php - Định nghĩa các routes của ứng dụng
 */

// ===== AUTH ROUTES (Không cần đăng nhập) =====
$router->get('/', function () {
    header("Location: " . BASE_URL . "index.php?controller=auth&action=showLogin");
});

$router->get('/login', 'auth@showLogin');
$router->get('/register', 'auth@showRegister');
$router->post('/login', 'auth@login');
$router->post('/register', 'auth@register');
$router->post('/logout', 'auth@logout');

// ===== BOOK ROUTES (Cần đăng nhập) =====
$router->get('/books', 'book@index');
$router->get('/books/{id}', 'book@show');
$router->get('/books/create', 'book@create');
$router->post('/books', 'book@store');
$router->get('/books/{id}/edit', 'book@edit');
$router->put('/books/{id}', 'book@update');
$router->delete('/books/{id}', 'book@destroy');

// ===== CATEGORY ROUTES =====
$router->get('/categories', 'category@index');
$router->get('/categories/{id}', 'category@show');
$router->post('/categories', 'category@store');
$router->put('/categories/{id}', 'category@update');
$router->delete('/categories/{id}', 'category@destroy');

// ===== AUTHOR ROUTES =====
$router->get('/authors', 'author@index');
$router->get('/authors/{id}', 'author@show');
$router->post('/authors', 'author@store');
$router->put('/authors/{id}', 'author@update');
$router->delete('/authors/{id}', 'author@destroy');

// ===== PUBLISHER ROUTES =====
$router->get('/publishers', 'publisher@index');
$router->get('/publishers/{id}', 'publisher@show');
$router->post('/publishers', 'publisher@store');
$router->put('/publishers/{id}', 'publisher@update');
$router->delete('/publishers/{id}', 'publisher@destroy');

// ===== BORROWING ROUTES =====
$router->get('/borrowing', 'borrowing@index');
$router->get('/borrowing/{id}', 'borrowing@show');
$router->post('/borrowing', 'borrowing@store');
$router->put('/borrowing/{id}', 'borrowing@update');
$router->delete('/borrowing/{id}', 'borrowing@destroy');
$router->get('/api/borrowings', 'borrowingApi@index');
$router->get('/api/borrowings/{id}', 'borrowingApi@show');
$router->post('/api/borrowings', 'borrowingApi@store');
$router->put('/api/borrowings/{id}/renew', 'borrowingApi@renew');
$router->put('/api/borrowings/{id}/return', 'borrowingApi@return');
$router->delete('/api/borrowings/{id}', 'borrowingApi@destroy');

<<<<<<< HEAD
// ===== CATEGORY API ROUTES =====
$router->get('/api/categories', 'categoryApi@index');
$router->get('/api/categories/{id}', 'categoryApi@show');
$router->post('/api/categories', 'categoryApi@store');
$router->put('/api/categories/{id}', 'categoryApi@update');
$router->delete('/api/categories/{id}', 'categoryApi@destroy');

// ===== AUTHOR API ROUTES =====
$router->get('/api/authors', 'authorApi@index');
$router->get('/api/authors/{id}', 'authorApi@show');
$router->post('/api/authors', 'authorApi@store');
$router->put('/api/authors/{id}', 'authorApi@update');
$router->delete('/api/authors/{id}', 'authorApi@destroy');

// ===== PUBLISHER API ROUTES =====
$router->get('/api/publishers', 'publisherApi@index');
$router->get('/api/publishers/{id}', 'publisherApi@show');
$router->post('/api/publishers', 'publisherApi@store');
$router->put('/api/publishers/{id}', 'publisherApi@update');
$router->delete('/api/publishers/{id}', 'publisherApi@destroy');

=======
>>>>>>> c1cdffb792e7ecfa3ebcb0f61602ebc68bd54a57
// ===== MEMBER ROUTES (Admin) =====
$router->get('/members', 'member@index');
$router->get('/members/{id}', 'member@show');
$router->post('/members', 'member@store');
$router->put('/members/{id}', 'member@update');
$router->delete('/members/{id}', 'member@destroy');

// ===== ADMIN ROUTES =====
$router->get('/admin', 'admin@dashboard');
$router->get('/admin/dashboard', 'admin@dashboard');
$router->get('/admin/users', 'admin@users');
$router->get('/admin/settings', 'settings@index');
$router->post('/admin/settings', 'settings@update');

// ===== FALLBACK: Query params routes (Backward Compatibility) =====
// Ở index.php sẽ xử lý query params tự động nếu không match clean URLs

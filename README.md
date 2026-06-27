# API Documentation

## Khởi động

* Chạy Apache và MySQL bằng XAMPP.
* Đặt project trong `htdocs/qlsach`.
* Mở Postman để test API.

---

# Authentication API

## Đăng nhập

```
POST http://localhost/qlsach/api/login
```

**Header**

```
Content-Type: application/json
```

**Body**

```json
{
  "username": "admin",
  "password": "password"
}
```

## Đăng xuất

```
POST http://localhost/qlsach/api/logout
```

**Lỗi thường gặp**

* **401**: Sai tài khoản hoặc chưa đăng nhập.

---

# Books API

## Request

```
GET    http://localhost/qlsach/api/books
GET    http://localhost/qlsach/api/books/{id}
POST   http://localhost/qlsach/api/books
PUT    http://localhost/qlsach/api/books/{id}
DELETE http://localhost/qlsach/api/books/{id}
```

**Header**

```
Content-Type: application/json
```

**Body (POST/PUT)**

```json
{
  "title": "Lập Trình PHP Advanced",
  "isbn": "9781234567890",
  "author_id": 1,
  "publisher_id": 1,
  "category_id": 1,
  "total_copies": 10,
  "publication_year": 2024,
  "pages": 350,
  "description": "Hướng dẫn lập trình PHP"
}
```

### Flow test

1. POST tạo sách.
2. GET danh sách hoặc GET theo ID.
3. PUT cập nhật sách.
4. DELETE xóa sách.

**Lỗi thường gặp**

* **400**: Không thể xóa sách đang được mượn.
* **401**: Chưa đăng nhập.
* **403**: Không có quyền.
* **404**: Không tìm thấy sách.
* **422**: Thiếu hoặc sai dữ liệu.

---

# Borrowings API

## Request

```
GET    http://localhost/qlsach/api/borrowings
GET    http://localhost/qlsach/api/borrowings/{id}
POST   http://localhost/qlsach/api/borrowings
PUT    http://localhost/qlsach/api/borrowings/{id}/renew
PUT    http://localhost/qlsach/api/borrowings/{id}/return
DELETE http://localhost/qlsach/api/borrowings/{id}
```

**Header**

```
Content-Type: application/json
```

**Body (POST)**

```json
{
  "user_id": 4,
  "book_ids": [3],
  "due_date": "2026-06-30"
}
```

**Body (PUT return)**

```json
{
  "return_items": [
    {
      "loan_item_id": 1,
      "quantity": 1
    }
  ],
  "note": "test"
}
```

### Flow test

1. POST tạo phiếu → lấy `loan_slip_id`.
2. GET chi tiết → lấy `loan_item_id`.
3. PUT return.
4. DELETE phiếu.

**Lỗi thường gặp**

* **400**: Lỗi xử lý.
* **404**: Không tồn tại ID.
* **422**: Thiếu hoặc sai dữ liệu.

---

# Users API

## Request

```
GET    http://localhost/qlsach/api/users
GET    http://localhost/qlsach/api/users/{id}
POST   http://localhost/qlsach/api/users
PUT    http://localhost/qlsach/api/users/{id}
DELETE http://localhost/qlsach/api/users/{id}
```

**Header**

```
Content-Type: application/json
```

**Body (POST/PUT)**

```json
{
  "username": "newuser",
  "email": "newuser@example.com",
  "password": "securepassword",
  "full_name": "Nguyễn Văn C",
  "phone": "0987654321",
  "address": "Hà Nội",
  "role": "member",
  "max_borrow_limit": 5
}
```

*Lưu ý:*
- Khi tạo mới hoặc cập nhật người dùng, `password` sẽ tự động được mã hóa bằng bcrypt trước khi lưu vào cơ sở dữ liệu.
- Chỉ tài khoản có quyền `admin` mới được xem toàn bộ danh sách người dùng (`GET /api/users`), tạo người dùng mới (`POST /api/users`), hoặc xóa người dùng (`DELETE /api/users/{id}`).
- Thành viên thường (`member`) chỉ có thể xem (`GET /api/users/{id}`) và cập nhật (`PUT /api/users/{id}`) thông tin cá nhân của chính họ.

---

# HTTP Status

| Code | Ý nghĩa              |
| ---- | -------------------- |
| 200  | Thành công           |
| 201  | Tạo thành công       |
| 400  | Bad Request          |
| 401  | Unauthorized         |
| 403  | Forbidden            |
| 404  | Not Found            |
| 422  | Unprocessable Entity |



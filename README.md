Mở Postman

Tạo request:

GET: http://localhost/qlsach/api/borrowings
GET: http://localhost/qlsach/api/borrowings/1
POST: http://localhost/qlsach/api/borrowings
Header: Content-Type = application/json
Body raw JSON:
{
"user_id": 4,
"book_ids": [3],
"due_date": "2026-06-30"
}
PUT: http://localhost/qlsach/api/borrowings/1/renew
PUT: http://localhost/qlsach/api/borrowings/1/return
Header: Content-Type = application/json
Body raw JSON:
{
"return_items": [
{ "loan_item_id": 1, "quantity": 1 }
],
"note": "test"
}
DELETE: http://localhost/qlsach/api/borrowings/1
Nếu muốn test đúng flow:

POST tạo phiếu → lấy loan_slip_id
GET chi tiết → lấy loan_item_id
PUT return → dùng loan_item_id đó
DELETE xóa phiếu
Nếu lỗi:

422: thiếu/ sai dữ liệu input
400: logic lỗi trong service
404: id không tồn tại

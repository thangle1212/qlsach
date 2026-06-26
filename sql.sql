-- ================================
  -- 1. TẠO DATABASE
  -- ================================
  DROP DATABASE IF EXISTS library_management;
  CREATE DATABASE library_management
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
  USE library_management;

  -- ================================
  -- 2. DANH MỤC
  -- ================================

  CREATE TABLE categories (
      id INT AUTO_INCREMENT PRIMARY KEY,
      name VARCHAR(100) NOT NULL,
      parent_id INT DEFAULT NULL,
      description TEXT,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
  );

  CREATE TABLE authors (
      id INT AUTO_INCREMENT PRIMARY KEY,
      name VARCHAR(100) NOT NULL,
      biography TEXT,
      nationality VARCHAR(50),
      birth_year YEAR,
      death_year YEAR,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  );

  CREATE TABLE publishers (
      id INT AUTO_INCREMENT PRIMARY KEY,
      name VARCHAR(100) NOT NULL,
      address TEXT,
      phone VARCHAR(20),
      email VARCHAR(100),
      website VARCHAR(255),
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  );

  -- ================================
  -- 3. USERS
  -- ================================

  CREATE TABLE users (
      id INT AUTO_INCREMENT PRIMARY KEY,
      username VARCHAR(50) UNIQUE NOT NULL,
      email VARCHAR(100) UNIQUE NOT NULL,
      password_hash VARCHAR(255) NOT NULL,
      full_name VARCHAR(100) NOT NULL,
      phone VARCHAR(20),
      address TEXT,
      role ENUM('admin', 'librarian', 'member') DEFAULT 'member',
      status ENUM('active', 'inactive') DEFAULT 'active',
      max_borrow_limit INT DEFAULT 5,
      current_borrow_count INT DEFAULT 0,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  );

  -- ================================
  -- 4. BOOKS
  -- ================================

  CREATE TABLE books (
      id INT AUTO_INCREMENT PRIMARY KEY,
      title VARCHAR(255) NOT NULL,
      isbn VARCHAR(20) UNIQUE,
      author_id INT,
      publisher_id INT,
      category_id INT,
      description TEXT,
      total_copies INT DEFAULT 1,
      available_copies INT DEFAULT 1,
      shelf_location VARCHAR(50),
      cover_image VARCHAR(255),
      publication_year YEAR,
      language VARCHAR(50) DEFAULT 'Vietnamese',
      pages INT,
      price DECIMAL(10,2),
      is_reference BOOLEAN DEFAULT FALSE,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

      FOREIGN KEY (author_id) REFERENCES authors(id) ON DELETE SET NULL,
      FOREIGN KEY (publisher_id) REFERENCES publishers(id) ON DELETE SET NULL,
      FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
  );

  -- ================================
  -- 5. PHIẾU MƯỢN (LOAN SLIP)
  -- ================================

  CREATE TABLE loan_slips (
      id INT AUTO_INCREMENT PRIMARY KEY,
      user_id INT NOT NULL,
      librarian_id INT,
      borrow_date DATE NOT NULL,
      due_date DATE NOT NULL,
      status ENUM('active', 'completed', 'overdue') DEFAULT 'active',
      note TEXT,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

      FOREIGN KEY (user_id) REFERENCES users(id),
      FOREIGN KEY (librarian_id) REFERENCES users(id)
  );

  -- ================================
  -- 6. CHI TIẾT MƯỢN SÁCH
  -- ================================

  CREATE TABLE loan_items (
      id INT AUTO_INCREMENT PRIMARY KEY,
      loan_id INT NOT NULL,
      book_id INT NOT NULL,
      quantity INT DEFAULT 1,
      returned_quantity INT DEFAULT 0,
      status ENUM('borrowed', 'returned', 'lost') DEFAULT 'borrowed',

      FOREIGN KEY (loan_id) REFERENCES loan_slips(id) ON DELETE CASCADE,
      FOREIGN KEY (book_id) REFERENCES books(id)
  );

  -- ================================
  -- 7. PHIẾU TRẢ
  -- ================================

  CREATE TABLE return_slips (
      id INT AUTO_INCREMENT PRIMARY KEY,
      loan_id INT NOT NULL,
      return_date DATE NOT NULL,
      librarian_id INT,
      note TEXT,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

      FOREIGN KEY (loan_id) REFERENCES loan_slips(id),
      FOREIGN KEY (librarian_id) REFERENCES users(id)
  );

  -- ================================
  -- 8. CHI TIẾT TRẢ
  -- ================================

  CREATE TABLE return_items (
      id INT AUTO_INCREMENT PRIMARY KEY,
      return_id INT NOT NULL,
      loan_item_id INT NOT NULL,
      quantity INT NOT NULL,

      FOREIGN KEY (return_id) REFERENCES return_slips(id) ON DELETE CASCADE,
      FOREIGN KEY (loan_item_id) REFERENCES loan_items(id)
  );

  -- ================================
  -- 9. PHIẾU PHẠT
  -- ================================

  CREATE TABLE fines (
      id INT AUTO_INCREMENT PRIMARY KEY,
      user_id INT NOT NULL,
      loan_id INT NOT NULL,
      amount DECIMAL(10,2) NOT NULL,
      reason ENUM('overdue', 'lost', 'damaged') DEFAULT 'overdue',
      status ENUM('unpaid', 'paid', 'waived') DEFAULT 'unpaid',
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

      FOREIGN KEY (user_id) REFERENCES users(id),
      FOREIGN KEY (loan_id) REFERENCES loan_slips(id)
  );

  -- ================================
  -- 10. ĐẶT TRƯỚC
  -- ================================

  CREATE TABLE reservations (
      id INT AUTO_INCREMENT PRIMARY KEY,
      user_id INT NOT NULL,
      book_id INT NOT NULL,
      reservation_date DATE NOT NULL,
      expiry_date DATE,
      status ENUM('pending', 'available', 'cancelled') DEFAULT 'pending',

      FOREIGN KEY (user_id) REFERENCES users(id),
      FOREIGN KEY (book_id) REFERENCES books(id)
  );

  -- ================================
  -- 11. SETTINGS
  -- ================================

  CREATE TABLE settings (
      id INT AUTO_INCREMENT PRIMARY KEY,
      `key` VARCHAR(100) UNIQUE NOT NULL,
      value TEXT NOT NULL,
      description TEXT,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
  );

  INSERT INTO settings (`key`, value, description) VALUES
  ('max_borrow_days', '14', 'Số ngày mượn tối đa'),
  ('fine_per_day', '5000', 'Tiền phạt mỗi ngày quá hạn'),
  ('max_books_per_user', '5', 'Số sách tối đa được mượn');


  -- ================================
  -- 1. CATEGORIES
  -- ================================
  INSERT INTO categories (name, description) VALUES
  ('Computer Science', 'IT & Programming'),
  ('Economics', 'Business and Economics'),
  ('Literature', 'Literary works');

  -- ================================
  -- 2. AUTHORS
  -- ================================
  INSERT INTO authors (name, nationality, birth_year) VALUES
  ('Robert C. Martin', 'USA', 1952),
  ('Martin Fowler', 'UK', 1963),
  ('Nguyễn Nhật Ánh', 'Vietnam', 1955);

  -- ================================
  -- 3. PUBLISHERS
  -- ================================
  INSERT INTO publishers (name, address, phone) VALUES
  ('O’Reilly Media', 'USA', '123456789'),
  ('NXB Trẻ', 'TP.HCM', '0909000999');

  -- ================================
  -- 4. USERS
  -- ================================
  INSERT INTO users (username, email, password_hash, full_name, role) VALUES
  ('admin', 'admin@lib.com', '$2y$10$BTX2NC2lmE.h2aJfnUu2TuzQSngpvt8xJzP8jUaAOiQUiCwmzbD3W', 'Library Admin', 'admin'),
  ('librarian1', 'lib@lib.com', '$2y$10$/FuoKg0n0irE.bkyCBI3EuDBNk5czg/19abZ5dPpyg8rEL/JUDFI6', 'Main Librarian', 'librarian'),
  ('member1', 'mem1@lib.com', '$2y$10$XZc3dFhgMnRAT02Qakx/ku5HovtBJfsIdCOb.XwDmJS7XUbr9pv6i', 'Nguyễn Văn A', 'member'),
  ('member2', 'mem2@lib.com', '$2y$10$zzRVeQYggfQOmYlKnTPY2OxNXKxbaVGYJgQGqzOGqAJYOk20ZVb2O', 'Trần Thị B', 'member');

  -- ================================
  -- 5. BOOKS
  -- ================================
  INSERT INTO books
  (title, isbn, author_id, publisher_id, category_id, total_copies, available_copies, price) VALUES
  ('Clean Code', '9780132350884', 1, 1, 1, 5, 5, 350000),
  ('Refactoring', '9780201485677', 2, 1, 1, 3, 3, 420000),
  ('Cho Tôi Xin Một Vé Đi Tuổi Thơ', '9786042083723', 3, 2, 3, 10, 10, 95000);

  -- ================================
  -- 6. PHIẾU MƯỢN (1 mượn nhiều sách)
  -- ================================
  INSERT INTO loan_slips (user_id, librarian_id, borrow_date, due_date) VALUES
  (3, 2, '2024-01-01', '2024-01-15');

  -- ================================
  -- 7. CHI TIẾT MƯỢN
  -- ================================
  INSERT INTO loan_items (loan_id, book_id, quantity) VALUES
  (1, 1, 1), -- Clean Code
  (1, 2, 1), -- Refactoring
  (1, 3, 2); -- Nguyễn Nhật Ánh

  -- ================================
  -- 8. PHIẾU TRẢ (trả 1 phần)
  -- ================================
  INSERT INTO return_slips (loan_id, librarian_id, return_date) VALUES
  (1, 2, '2024-01-10');

  INSERT INTO return_items (return_id, loan_item_id, quantity) VALUES
  (1, 1, 1), -- Trả Clean Code
  (1, 3, 1); -- Trả 1 quyển truyện

  -- ================================
  -- 9. CẬP NHẬT TRẠNG THÁI MƯỢN
  -- ================================
  UPDATE loan_items SET
      returned_quantity = 1,
      status = 'returned'
  WHERE id = 1;

  UPDATE loan_items SET
      returned_quantity = 1,
      status = 'borrowed'
  WHERE id = 3;

  -- ================================
  -- 10. PHIẾU PHẠT (quá hạn)
  -- ================================
  INSERT INTO fines (user_id, loan_id, amount, reason) VALUES
  (3, 1, 50000, 'overdue');

  -- ================================
  -- 11. ĐẶT TRƯỚC
  -- ================================
  INSERT INTO reservations (user_id, book_id, reservation_date) VALUES
  (4, 1, '2024-01-05');
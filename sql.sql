DROP DATABASE IF EXISTS library_management;
CREATE DATABASE library_management
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE library_management;

CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    parent_id INT DEFAULT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE CASCADE,
    INDEX idx_parent (parent_id)
);

CREATE TABLE authors (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    biography TEXT,
    nationality VARCHAR(50),
    birth_year YEAR,
    death_year YEAR,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_name (name)
);


CREATE TABLE publishers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    address TEXT,
    phone VARCHAR(20),
    email VARCHAR(100),
    website VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_name (name)
);


CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    role ENUM('admin', 'librarian', 'member') DEFAULT 'member',
    status ENUM('active', 'inactive', 'pending') DEFAULT 'pending',
    max_borrow_limit INT DEFAULT 5,
    current_borrow_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_role (role)
);


CREATE TABLE books (
    id INT PRIMARY KEY AUTO_INCREMENT,
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
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_title (title),
    INDEX idx_isbn (isbn),
    INDEX idx_author (author_id),
    INDEX idx_category (category_id)
);


CREATE TABLE borrowings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    librarian_id INT,
    borrow_date DATE NOT NULL,
    due_date DATE NOT NULL,
    return_date DATE,
    status ENUM('borrowed', 'returned', 'overdue', 'lost') DEFAULT 'borrowed',
    fine_amount DECIMAL(10,2) DEFAULT 0.00,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    FOREIGN KEY (librarian_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_book_id (book_id),
    INDEX idx_status (status),
    INDEX idx_due_date (due_date)
);


CREATE TABLE reservations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    reservation_date DATE NOT NULL,
    status ENUM('pending', 'available', 'cancelled', 'expired') DEFAULT 'pending',
    expiry_date DATE,
    priority INT DEFAULT 1,
    notification_sent BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    UNIQUE KEY unique_active_reservation (user_id, book_id, status),
    INDEX idx_user_book (user_id, book_id),
    INDEX idx_status_expiry (status, expiry_date)
);


CREATE TABLE fines (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    borrowing_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    reason ENUM('overdue', 'lost', 'damaged') DEFAULT 'overdue',
    status ENUM('paid', 'unpaid', 'waived') DEFAULT 'unpaid',
    paid_date DATE,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (borrowing_id) REFERENCES borrowings(id) ON DELETE CASCADE,
    INDEX idx_user_status (user_id, status)
);


CREATE TABLE settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    `key` VARCHAR(100) UNIQUE NOT NULL,
    value TEXT NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO settings (`key`, value, description) VALUES
('max_borrow_days', '14', 'Số ngày mượn tối đa'),
('fine_per_day', '5000', 'Tiền phạt mỗi ngày quá hạn'),
('max_books_per_user', '5', 'Số sách tối đa mỗi người dùng có thể mượn');

-- ========== DỮ LIỆU MẪU ==========

-- Chuyên mục
INSERT INTO categories (name, description) VALUES
('Văn học Việt Nam', 'Các tác phẩm văn học của các tác giả Việt Nam'),
('Văn học Nước ngoài', 'Tác phẩm dịch từ các nước khác'),
('Khoa học - Công nghệ', 'Sách về khoa học, công nghệ, lập trình'),
('Kinh tế - Quản lý', 'Sách về kinh tế, quản lý doanh nghiệp'),
('Lịch sử - Địa lý', 'Sách về lịch sử và địa lý'),
('Tự giáo dục', 'Sách kỹ năng, phát triển bản thân'),
('Trẻ em', 'Sách dành cho trẻ em'),
('Nghệ thuật - Thể thao', 'Sách về nghệ thuật, âm nhạc, thể thao');

-- Tác giả
INSERT INTO authors (name, biography, nationality, birth_year) VALUES
('Nguyễn Du', 'Tác giả của Truyện Kiều - tác phẩm vĩ đại của văn học Việt Nam', 'Việt Nam', 1765),
('Mạc Dinh Chi', 'Nhà thơ, nhà dịch nổi tiếng thế kỷ 20', 'Việt Nam', 1906),
('Dương Thu Hương', 'Nhà văn nổi tiếng, tác giả Những đứa con lưu vong', 'Việt Nam', 1947),
('Harper Lee', 'Tác giả tiểu thuyết To Kill a Mockingbird', 'Mỹ', 1926),
('George Orwell', 'Nhà văn, tác giả 1984', 'Anh', 1903),
('Jane Austen', 'Nhà văn tiểu thuyết lãng mạn tiếng Anh', 'Anh', 1775),
('Steve Jobs', 'Người sáng lập Apple', 'Mỹ', 1955),
('Malcolm Gladwell', 'Nhà tác giả chuyên về tâm lý học xã hội', 'Canada', 1963);

-- Nhà xuất bản
INSERT INTO publishers (name, address, phone, email, website) VALUES
('Nhà Xuất Bản Hội Nhà Văn', '30 Tạ Quang Bửu, Hà Nội', '0243922928', 'info@writers.vn', 'https://nhaxuatbanhoinhavans.vn'),
('Nhà Xuất Bản Kim Đồng', 'Phố Huế, Hà Nội', '0243826336', 'info@kimdongjsc.com.vn', 'https://kimdong.vn'),
('Nhà Xuất Bản Trẻ', 'TP. Hồ Chí Minh', '02837625023', 'info@nxbtre.com.vn', 'https://nxbtre.com.vn'),
('Nhà Xuất Bản Lao Động', 'Hà Nội', '0243766342', 'contact@nxblaodong.vn', 'https://nxblaodong.vn'),
('Penguin Books', 'London, UK', '+44123456789', 'info@penguin.co.uk', 'https://www.penguin.co.uk'),
('Hachette Book Group', 'New York, USA', '+12125224200', 'info@hachette.com', 'https://www.hachettebookgroup.com');

-- Người dùng
INSERT INTO users (username, email, password_hash, full_name, phone, address, role, status, max_borrow_limit) VALUES
('admin', 'admin@library.vn', 'admin', 'Quản trị viên', '0987654321', '123 Đường Hùng Vương, Hà Nội', 'admin', 'active', 10),
('librarian1', 'lib1@library.vn', 'librarian1', 'Nguyễn Thị Liên', '0976543210', '45 Nguyễn Huệ, Hà Nội', 'librarian', 'active', 10),
('librarian2', 'lib2@library.vn', 'librarian2', 'Trần Văn Tuấn', '0965432109', '67 Tô Ngọc Vân, Hà Nội', 'librarian', 'active', 10),
('member1', 'member1@library.vn', 'member1', 'Phạm Minh Đức', '0954321098', '12 Trần Phú, Hà Nội', 'member', 'active', 5),
('member2', 'member2@library.vn', 'member2', 'Lê Thị Hương', '0943210987', '89 Láng Hạ, Hà Nội', 'member', 'active', 5),
('member3', 'member3@library.vn', 'member3', 'Võ Quang Hùng', '0932109876', '34 Nguyễn Trãi, Hà Nội', 'member', 'active', 5),
('member4', 'member4@library.vn', 'member4', 'Đặng Hữu Tâm', '0921098765', '56 Phan Bội Châu, Hà Nội', 'member', 'inactive', 5);

-- Sách
INSERT INTO books (title, isbn, author_id, publisher_id, category_id, description, total_copies, available_copies, publication_year, language, pages, price) VALUES
('Truyện Kiều', '978-604-1', 1, 1, 1, 'Tác phẩm vĩ đại của Nguyễn Du, được xem là kiệt tác văn học Việt Nam', 5, 3, 1813, 'Vietnamese', 520, 85000),
('Những đứa con lưu vong', '978-604-2', 3, 2, 1, 'Tiểu thuyết nổi tiếng về cuộc sống của những người phụ nữ Việt Nam', 4, 2, 1991, 'Vietnamese', 398, 120000),
('To Kill a Mockingbird', '978-0-06-1', 4, 5, 2, 'Tiểu thuyết kinh điển về công lý và nhân tính tại Mỹ', 3, 1, 1960, 'English', 324, 250000),
('1984', '978-0-452-1', 5, 5, 2, 'Tác phẩm khoa học viễn tưởng kinh điển về một xã hội độc tài', 6, 4, 1949, 'English', 328, 280000),
('Pride and Prejudice', '978-0-14-1', 6, 6, 2, 'Tiểu thuyết lãng mạn kinh điển của Jane Austen', 3, 2, 1813, 'English', 432, 220000),
('Walter Isaacson: Steve Jobs', '978-1-4516-1', 7, 6, 4, 'Tiểu sử chi tiết về cuộc đời của Steve Jobs', 4, 3, 2011, 'English', 656, 350000),
('The Tipping Point', '978-0-316-1', 8, 5, 4, 'Sách về cách những ý tưởng lây lan trong xã hội', 5, 4, 2000, 'English', 301, 240000),
('Dạy con làm giàu', '978-604-3', 3, 3, 6, 'Sách hướng dẫn dạy con học kiến thức tài chính', 7, 5, 2019, 'Vietnamese', 312, 95000),
('Tư duy phản biện', '978-604-4', 1, 1, 6, 'Sách hướng dẫn phát triển kỹ năng tư duy', 6, 4, 2020, 'Vietnamese', 284, 110000),
('Lịch sử Việt Nam', '978-604-5', 2, 4, 5, 'Cuốn sách nói về lịch sử của dân tộc Việt Nam qua các thời kỳ', 3, 1, 2018, 'Vietnamese', 528, 280000);

-- Phiếu mượn
INSERT INTO borrowings (user_id, book_id, librarian_id, borrow_date, due_date, return_date, status) VALUES
(4, 1, 2, '2025-12-15', '2025-12-29', NULL, 'borrowed'),
(4, 3, 2, '2025-12-10', '2025-12-24', '2025-12-23', 'returned'),
(5, 2, 3, '2025-12-18', '2026-01-01', NULL, 'borrowed'),
(5, 4, 3, '2025-12-20', '2026-01-03', NULL, 'borrowed'),
(6, 5, 2, '2025-12-16', '2025-12-30', NULL, 'borrowed'),
(6, 6, 3, '2025-12-12', '2025-12-26', '2025-12-28', 'returned'),
(7, 7, 2, '2025-12-19', '2026-01-02', NULL, 'borrowed'),
(4, 8, 3, '2025-12-22', '2026-01-05', NULL, 'borrowed');

-- Đặt trước sách
INSERT INTO reservations (user_id, book_id, reservation_date, expiry_date, status) VALUES
(5, 1, '2025-12-20', '2026-01-19', 'pending'),
(6, 3, '2025-12-21', '2026-01-20', 'pending'),
(7, 2, '2025-12-22', '2026-01-21', 'available'),
(4, 4, '2025-12-18', '2026-01-17', 'pending');

-- Phạt
INSERT INTO fines (user_id, borrowing_id, amount, reason, status) VALUES
(6, 6, 15000, 'overdue', 'unpaid'),
(4, 2, 10000, 'overdue', 'paid'),
(5, 4, 25000, 'overdue', 'unpaid');
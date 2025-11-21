-- Database: bukutamu_pnbatam
CREATE DATABASE IF NOT EXISTS bukutamu_pnbatam CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE bukutamu_pnbatam;

CREATE TABLE IF NOT EXISTS guests (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(200) NOT NULL,
  email VARCHAR(200) DEFAULT NULL,
  phone VARCHAR(50) DEFAULT NULL,
  institution VARCHAR(200) DEFAULT NULL,
  purpose TEXT NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  name VARCHAR(200) DEFAULT NULL
) ENGINE=InnoDB;

-- Default admin: username 'admin', password 'admin123' (change immediately)
INSERT INTO admins (username, password_hash, name) VALUES
('admin', '$2y$10$u1qz5qN1YlY8h6f2kK2ZSO2w0Y7E6h3xY7kGx5VJbI6mWlq3Xh1aW', 'Administrator');

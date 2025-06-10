-- 데이터베이스 생성
CREATE DATABASE IF NOT EXISTS onjava_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

USE onjava_db;

-- 회원 테이블
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    nickname VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    userType ENUM('user', 'seller', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 사업자 신청 테이블
CREATE TABLE seller_requests (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL,
  shop_name VARCHAR(100) NOT NULL,
  biz_number VARCHAR(30) NOT NULL,
  requested_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

--게시판 테이블
CREATE TABLE IF NOT EXISTS posts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  content TEXT NOT NULL,
  author VARCHAR(50) NOT NULL,
  board_type ENUM('free', 'share', 'job') NOT NULL DEFAULT 'free',
  views INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

--게시판 댓글 테이블
CREATE TABLE IF NOT EXISTS comments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  post_id INT NOT NULL,
  nickname VARCHAR(50) NOT NULL,
  content TEXT NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

--마트 전단지 테이블
CREATE TABLE flyers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  mart_name VARCHAR(100) NOT NULL,
  mart_address VARCHAR(200),
  image_url VARCHAR(255) NOT NULL,
  uploaded_by INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

--공지사항 테이블
CREATE TABLE IF NOT EXISTS notices (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  content TEXT NOT NULL,
  user_id INT NOT NULL,
  nickname VARCHAR(50) NOT NULL,
  board_type VARCHAR(20) DEFAULT 'notice',
  views INT DEFAULT 0,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

--문의글 테이블
CREATE TABLE IF NOT EXISTS inquiries (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  content TEXT NOT NULL,
  user_id INT NOT NULL,
  nickname VARCHAR(50) NOT NULL,
  status VARCHAR(20) DEFAULT '접수 완료', -- 상태: 접수 완료, 해결 중, 해결 완료
  board_type VARCHAR(20) DEFAULT 'inquiry',
  views INT DEFAULT 0,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

--문의글 댓글 테이블
CREATE TABLE IF NOT EXISTS inquiry_comments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  inquiry_id INT NOT NULL,
  user_id INT NOT NULL,
  nickname VARCHAR(50) NOT NULL,
  content TEXT NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (inquiry_id) REFERENCES inquiries(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
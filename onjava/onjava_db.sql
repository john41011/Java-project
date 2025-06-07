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
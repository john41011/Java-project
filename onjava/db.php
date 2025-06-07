<?php
$host = 'localhost';
$user = 'root';
$pass = ''; // XAMPP 기본 비밀번호 없음
$dbname = 'onjava_db';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
  die("DB 연결 실패: " . $conn->connect_error);
}
?>
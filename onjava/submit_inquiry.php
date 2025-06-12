<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username']) || $_SESSION['userType'] === 'admin') {
  echo "<script>alert('관리자 계정은 문의글을 작성할 수 없습니다.'); history.back();</script>";
  exit;
}

$title = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');

if (empty($title) || empty($content)) {
  echo "<script>alert('제목과 내용을 입력하세요.'); history.back();</script>";
  exit;
}

// 사용자 정보 가져오기
$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT id, nickname FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($user_id, $nickname);
$stmt->fetch();
$stmt->close();

// 글 저장 (status 기본값 '접수 완료')
$status = '접수 완료';
$stmt = $conn->prepare("INSERT INTO inquiries (title, content, user_id, nickname, board_type, status) VALUES (?, ?, ?, ?, 'inquiry', ?)");
$stmt->bind_param("ssiss", $title, $content, $user_id, $nickname, $status);
$success = $stmt->execute();
$stmt->close();

if ($success) {
  echo "<script>alert('문의글이 등록되었습니다.'); location.href='support.html';</script>";
} else {
  echo "<script>alert('등록 실패'); history.back();</script>";
}
?>

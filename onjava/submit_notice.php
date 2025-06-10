<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username']) || $_SESSION['userType'] !== 'admin') {
  echo "<script>alert('관리자만 등록할 수 있습니다.'); history.back();</script>";
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

// 글 저장
$stmt = $conn->prepare("INSERT INTO notices (title, content, user_id, nickname, board_type) VALUES (?, ?, ?, ?, 'notice')");
$stmt->bind_param("ssis", $title, $content, $user_id, $nickname);
$success = $stmt->execute();
$stmt->close();

if ($success) {
  echo "<script>alert('공지사항이 등록되었습니다.'); location.href='support.html';</script>";
} else {
  echo "<script>alert('등록 실패'); history.back();</script>";
}
?>

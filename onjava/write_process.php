<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username'])) {
  echo "<script>alert('로그인이 필요합니다.'); location.href='login.html';</script>";
  exit;
}

$title = $_POST['title'];
$content = $_POST['content'];
$board_type = $_POST['board_type'];
$author = $_SESSION['username'];

$stmt = $conn->prepare("INSERT INTO posts (title, content, author, board_type) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $title, $content, $author, $board_type);

if ($stmt->execute()) {
  echo "<script>alert('글이 작성되었습니다.'); location.href='community.html';</script>";
} else {
  echo "<script>alert('글 작성에 실패했습니다.'); history.back();</script>";
}
?>

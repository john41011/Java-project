<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username'])) {
  echo "<script>alert('로그인이 필요합니다.'); location.href='login.html';</script>";
  exit;
}

$username = $_SESSION['username'];
$action = $_GET['action'] ?? '';

if ($action === 'cancel') {
  // 1. seller_requests에서 삭제
  $del = $conn->prepare("DELETE FROM seller_requests WHERE username = ?");
  $del->bind_param("s", $username);
  $del->execute();

  // 2. userType을 일반회원으로 변경
  $update = $conn->prepare("UPDATE users SET userType = 'user' WHERE username = ?");
  $update->bind_param("s", $username);
  $update->execute();

  echo "<script>alert('사업자 등록이 해지되었습니다.'); location.href='mypage.html';</script>";
} else {
  echo "<script>alert('잘못된 접근입니다.'); history.back();</script>";
}
?>
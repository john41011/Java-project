<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username'])) {
  echo "<script>alert('로그인이 필요합니다.'); location.href='login.html';</script>";
  exit;
}

$username = $_SESSION['username'];
$nickname = $_POST['nickname'];
$email = $_POST['email'];
$current_pw = $_POST['current_password'];
$new_pw = $_POST['new_password'];

// 1. 사용자 인증 (현재 비밀번호 검증)
$stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();

if (!password_verify($current_pw, $res['password'])) {
  echo "<script>alert('현재 비밀번호가 올바르지 않습니다.'); history.back();</script>";
  exit;
}

// 2. 정보 업데이트
if (!empty($new_pw)) {
  $hashed = password_hash($new_pw, PASSWORD_DEFAULT);
  $stmt2 = $conn->prepare("UPDATE users SET nickname = ?, email = ?, password = ? WHERE username = ?");
  $stmt2->bind_param("ssss", $nickname, $email, $hashed, $username);
} else {
  $stmt2 = $conn->prepare("UPDATE users SET nickname = ?, email = ? WHERE username = ?");
  $stmt2->bind_param("sss", $nickname, $email, $username);
}

if ($stmt2->execute()) {
  echo "<script>alert('회원 정보가 수정되었습니다.'); location.href='mypage.html';</script>";
} else {
  echo "<script>alert('수정 실패'); history.back();</script>";
}
?>

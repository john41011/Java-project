<?php
include 'db.php';

$type = $_POST['type'];

if ($type === 'id') {
  $email = $_POST['email'];
  $stmt = $conn->prepare("SELECT username FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $res = $stmt->get_result()->fetch_assoc();

  if ($res) {
    echo "<script>alert('회원님의 아이디는 {$res['username']} 입니다.'); history.back();</script>";
  } else {
    echo "<script>alert('일치하는 계정이 없습니다.'); history.back();</script>";
  }

} elseif ($type === 'pw') {
  $username = $_POST['username'];
  $email = $_POST['email'];
  $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND email = ?");
  $stmt->bind_param("ss", $username, $email);
  $stmt->execute();
  $res = $stmt->get_result()->fetch_assoc();

  if ($res) {
    $temp = bin2hex(random_bytes(4));  // 8자리 임시비밀번호
    $hashed = password_hash($temp, PASSWORD_DEFAULT);
    $update = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
    $update->bind_param("ss", $hashed, $username);
    $update->execute();
    echo "<script>alert('임시 비밀번호는 \"$temp\" 입니다. 로그인 후 변경해주세요.'); location.href='login.html';</script>";
  } else {
    echo "<script>alert('정보가 일치하지 않습니다.'); history.back();</script>";
  }
}
?>

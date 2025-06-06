<?php
include 'db.php';

$username = $_POST['username'];
$nickname = $_POST['nickname'];
$password = $_POST['password'];
$email = $_POST['email'];

$hashed = password_hash($password, PASSWORD_DEFAULT);

// 다시 서버에서 중복 확인
$stmt1 = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt1->bind_param("s", $username);
$stmt1->execute();
$res1 = $stmt1->get_result();

$stmt2 = $conn->prepare("SELECT * FROM users WHERE nickname = ?");
$stmt2->bind_param("s", $nickname);
$stmt2->execute();
$res2 = $stmt2->get_result();

if ($res1->num_rows > 0 || $res2->num_rows > 0) {
  echo "<script>alert('중복된 아이디 또는 닉네임입니다.'); history.back();</script>";
  exit;
}

// 저장
$stmt = $conn->prepare("INSERT INTO users (username, nickname, password, email) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $username, $nickname, $hashed, $email);

if ($stmt->execute()) {
  echo "<script>alert('회원가입이 완료되었습니다!'); location.href='login.html';</script>";
} else {
  echo "<script>alert('회원가입 실패'); history.back();</script>";
}
?>

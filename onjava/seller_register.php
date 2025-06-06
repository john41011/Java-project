<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username'])) {
  echo "<script>alert('로그인 후 이용해주세요.'); location.href='login.html';</script>";
  exit;
}

$username = $_SESSION['username'];
$shopName = $_POST['shop_name'];
$bizNumber = $_POST['biz_number'];

// 중복 신청 방지
$check = $conn->prepare("SELECT * FROM seller_requests WHERE username = ?");
$check->bind_param("s", $username);
$check->execute();
$res = $check->get_result();
if ($res->num_rows > 0) {
  echo "<script>alert('이미 사업자 등록을 신청하셨습니다.'); history.back();</script>";
  exit;
}

// 신청 저장
$stmt = $conn->prepare("INSERT INTO seller_requests (username, shop_name, biz_number) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $username, $shopName, $bizNumber);
if ($stmt->execute()) {
  echo "<script>alert('사업자 등록 신청이 완료되었습니다. 관리자의 승인을 기다려주세요.'); location.href='mypage.html';</script>";
} else {
  echo "<script>alert('신청 실패. 다시 시도해주세요.'); history.back();</script>";
}
?>
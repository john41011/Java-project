<?php
require_once 'db.php';
session_start();

if (!isset($_SESSION['username']) || !in_array($_SESSION['userType'], ['seller', 'admin'])) {
  die("권한이 없습니다.");
}

$martName = $_POST['mart_name'] ?? '';
$martAddress = $_POST['mart_address'] ?? '';
$file = $_FILES['flyer_image'];

if (!$file || $file['error'] != UPLOAD_ERR_OK) {
  die("파일 업로드 실패");
}

// 파일 저장
$uploadDir = 'uploads/flyers/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = uniqid() . '.' . $ext;
$filepath = $uploadDir . $filename;

if (!move_uploaded_file($file['tmp_name'], $filepath)) {
  die("파일 저장 실패");
}

// DB 저장
$stmt = $conn->prepare("INSERT INTO flyers (mart_name, mart_address, image_url, uploaded_by) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $martName, $martAddress, $filepath, $_SESSION['nickname']);
if ($stmt->execute()) {
    echo "<script>alert('전단지가 등록되었습니다.'); location.href='martFlyers.html';</script>";
} else {
    echo "<script>alert('전단지 등록 실패'); history.back();</script>";
}
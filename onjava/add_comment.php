<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

// 로그인 확인
if (!isset($_SESSION['username'])) {
  echo json_encode(['success' => false, 'message' => '로그인이 필요합니다.']);
  exit;
}

$user_id = null;
$post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
$content = trim($_POST['content'] ?? '');

// 필수값 체크
if ($post_id === 0 || empty($content)) {
  echo json_encode(['success' => false, 'message' => '내용을 입력해주세요.']);
  exit;
}

// 사용자 ID 가져오기
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();

if (!$user_id) {
  echo json_encode(['success' => false, 'message' => '유저 정보 없음']);
  exit;
}

// 댓글 저장
$insert = $conn->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)");
$insert->bind_param("iis", $post_id, $user_id, $content);
$success = $insert->execute();
$insert->close();

if ($success) {
  echo json_encode(['success' => true]);
} else {
  echo json_encode(['success' => false, 'message' => '댓글 저장 실패']);
}
?>
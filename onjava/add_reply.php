<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

// 로그인 확인
if (!isset($_SESSION['username'])) {
  echo json_encode(['success' => false, 'message' => '로그인이 필요합니다.']);
  exit;
}

$username = $_SESSION['username'];
$content = trim($_POST['content'] ?? '');
$inquiry_id = isset($_POST['inquiry_id']) ? intval($_POST['inquiry_id']) : 0;

// 입력값 확인
if ($inquiry_id === 0 || empty($content)) {
  echo json_encode(['success' => false, 'message' => '내용을 입력해주세요.']);
  exit;
}

// 사용자 정보 조회
$stmt = $conn->prepare("SELECT id, userType, nickname FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($user_id, $user_type, $nickname);
$stmt->fetch();
$stmt->close();

if (!$user_id) {
  echo json_encode(['success' => false, 'message' => '유저 정보를 찾을 수 없습니다.']);
  exit;
}

// 문의글 작성자 확인
$stmt = $conn->prepare("SELECT user_id FROM inquiries WHERE id = ?");
$stmt->bind_param("i", $inquiry_id);
$stmt->execute();
$stmt->bind_result($inquiry_user_id);
$stmt->fetch();
$stmt->close();

// 권한 확인: 관리자 또는 작성자만
if ($user_type !== 'admin' && $user_id !== $inquiry_user_id) {
  echo json_encode(['success' => false, 'message' => '댓글 작성 권한이 없습니다.']);
  exit;
}

// 댓글 저장
$stmt = $conn->prepare("INSERT INTO inquiry_comments (inquiry_id, user_id, nickname, content) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iiss", $inquiry_id, $user_id, $nickname, $content);
$success = $stmt->execute();
$stmt->close();

if ($success) {
  echo json_encode(['success' => true]);
} else {
  echo json_encode(['success' => false, 'message' => '댓글 저장 실패']);
}
?>

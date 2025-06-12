<?php
include 'db.php';

header('Content-Type: application/json');

// 입력값 확인
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$type = isset($_GET['type']) ? $_GET['type'] : '';

if ($id <= 0 || !in_array($type, ['notice', 'inquiry'])) {
  echo json_encode(["error" => "유효하지 않은 요청입니다."]);
  exit;
}
// 테이블명 매핑
$table = $type === 'notice' ? 'notices' : 'inquiries';

$update = $conn->prepare("UPDATE $table SET views = views + 1 WHERE id = ?");
$update->bind_param("i", $id);
$update->execute();

$stmt = $conn->prepare("
  SELECT p.*, u.nickname, u.username 
  FROM $table p 
  JOIN users u ON p.user_id = u.id 
  WHERE p.id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($post = $result->fetch_assoc()) {
  echo json_encode($post);
} else {
  echo json_encode(["error" => "글을 찾을 수 없습니다."]);
}
?>

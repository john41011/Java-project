<?php
include 'db.php';

header('Content-Type: application/json');

// 요청 값 확인
$inquiry_id = isset($_GET['inquiry_id']) ? intval($_GET['inquiry_id']) : 0;

if ($inquiry_id <= 0) {
  echo json_encode(["error" => "유효하지 않은 요청입니다."]);
  exit;
}

// 댓글 조회
$stmt = $conn->prepare("
  SELECT r.id, r.content, r.created_at, u.nickname 
  FROM inquiry_comments r 
  JOIN users u ON r.user_id = u.id 
  WHERE r.inquiry_id = ? 
  ORDER BY r.created_at ASC
");
$stmt->bind_param("i", $inquiry_id);
$stmt->execute();
$result = $stmt->get_result();

$replies = [];
while ($row = $result->fetch_assoc()) {
  $replies[] = $row;
}

echo json_encode($replies);
?>

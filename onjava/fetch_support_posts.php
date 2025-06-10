<?php
include 'db.php';

$type = $_GET['type'] ?? 'notice';
$validTypes = ['notice', 'inquiry'];
if (!in_array($type, $validTypes)) {
  echo json_encode([]);
  exit;
}

if ($type === 'notice') {
  // 공지사항: notices 테이블, board_type으로 필터링
  $stmt = $conn->prepare("SELECT s.id, s.title, s.created_at, s.views, u.nickname 
                          FROM notices s 
                          JOIN users u ON s.user_id = u.id 
                          WHERE s.board_type = 'notice'
                          ORDER BY s.created_at DESC");
} else {
  // 문의하기: inquiries 테이블, status 포함
  $stmt = $conn->prepare("SELECT s.id, s.title, s.created_at, s.views, s.status, u.nickname 
                          FROM inquiries s 
                          JOIN users u ON s.user_id = u.id 
                          ORDER BY s.created_at DESC");
}

$stmt->execute();
$result = $stmt->get_result();

$posts = [];
while ($row = $result->fetch_assoc()) {
  $posts[] = $row;
}

echo json_encode($posts);
?>

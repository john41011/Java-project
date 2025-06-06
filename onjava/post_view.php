<?php
include 'db.php';

$id = $_GET['id'] ?? null;

if (!$id) {
  http_response_code(400);
  echo json_encode(['error' => '잘못된 요청입니다.']);
  exit;
}

// 조회수 증가
$update = $conn->prepare("UPDATE posts SET views = views + 1 WHERE id = ?");
$update->bind_param("i", $id);
$update->execute();

// 게시글 조회
$stmt = $conn->prepare("
  SELECT posts.*, users.nickname 
  FROM posts 
  JOIN users ON posts.author = users.username 
  WHERE posts.id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
  $response = [
    'title' => $row['title'],
    'content' => nl2br($row['content']),
    'nickname' => $row['nickname'],
    'date' => date('Y.m.d', strtotime($row['created_at'])),
    'views' => $row['views'] + 1
  ];
  header('Content-Type: application/json');
  echo json_encode($response);
} else {
  http_response_code(404);
  echo json_encode(['error' => '게시글을 찾을 수 없습니다.']);
}
?>

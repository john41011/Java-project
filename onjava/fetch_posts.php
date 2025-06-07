<?php
include 'db.php';

$board = $_GET['board'] ?? 'free';
$sort = $_GET['sort'] ?? 'date_desc';
$field = $_GET['field'] ?? '';
$keyword = $_GET['keyword'] ?? '';

// 기본 쿼리
$query = "SELECT posts.*, users.nickname FROM posts
          JOIN users ON posts.author = users.username
          WHERE board_type = ?";

// 검색 조건 추가
$params = [$board];
$types = "s";

if ($field && $keyword) {
  if ($field === 'nickname') {
    $query .= " AND users.nickname LIKE ?";
  } else {
    $query .= " AND posts.$field LIKE ?";
  }
  $params[] = "%$keyword%";
  $types .= "s";
}

// 정렬
switch ($sort) {
  case 'date_asc':
    $query .= " ORDER BY posts.created_at ASC";
    break;
  case 'views_desc':
    $query .= " ORDER BY posts.views DESC";
    break;
  case 'views_asc':
    $query .= " ORDER BY posts.views ASC";
    break;
  default:
    $query .= " ORDER BY posts.created_at DESC";
}

$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$posts = [];
while ($row = $result->fetch_assoc()) {
  $posts[] = [
    'id' => $row['id'],
    'author' => $row['author'],
    'title' => $row['title'],
    'nickname' => $row['nickname'],
    'date' => date('Y.m.d', strtotime($row['created_at'])),
    'views' => $row['views']
  ];
}

header('Content-Type: application/json');
echo json_encode($posts);
?>

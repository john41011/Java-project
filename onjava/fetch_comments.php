<?php
include 'db.php';

$post_id = isset($_GET['post_id']) ? intval($_GET['post_id']) : 0;

$stmt = $conn->prepare("SELECT c.id, c.content, c.created_at, u.nickname 
                        FROM comments c 
                        JOIN users u ON c.user_id = u.id 
                        WHERE c.post_id = ? 
                        ORDER BY c.created_at ASC");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();

$comments = [];
while ($row = $result->fetch_assoc()) {
    $comments[] = $row;
}

echo json_encode($comments);
?>
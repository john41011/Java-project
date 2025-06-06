<?php
include 'db.php';

$type = $_GET['type'] ?? '';
$value = trim($_GET['value'] ?? '');

if (!in_array($type, ['username', 'nickname']) || empty($value)) {
  echo "fail";
  exit;
}

$query = "SELECT * FROM users WHERE $type = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $value);
$stmt->execute();
$result = $stmt->get_result();

echo trim($result->num_rows > 0 ? "fail" : "ok");
?>

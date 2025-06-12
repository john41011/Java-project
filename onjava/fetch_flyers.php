<?php
require_once 'db.php';

header('Content-Type: application/json');

$martName = $_GET['mart'] ?? '';

if (empty($martName)) {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM flyers WHERE mart_name = ? ORDER BY created_at DESC");
$stmt->bind_param("s", $martName);
$stmt->execute();
$result = $stmt->get_result();

$flyers = [];
while ($row = $result->fetch_assoc()) {
    $flyers[] = $row;
}

echo json_encode($flyers);

$stmt->close();
$conn->close();
?>
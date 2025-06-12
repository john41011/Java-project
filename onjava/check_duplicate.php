<?php
header('Content-Type: application/json');
include 'db.php';

if ($_POST['type'] && $_POST['value']) {
    $type = $_POST['type'];
    $value = trim($_POST['value']);
    
    if ($type === 'username') {
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE username = ?");
    } else if ($type === 'nickname') {
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE nickname = ?");
    } else {
        echo json_encode(['available' => false, 'message' => '잘못된 요청입니다.']);
        exit;
    }
    
    $stmt->bind_param("s", $value);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if ($row['count'] > 0) {
        echo json_encode(['available' => false, 'message' => '이미 사용중입니다.']);
    } else {
        echo json_encode(['available' => true, 'message' => '사용 가능합니다.']);
    }
    
    $stmt->close();
} else {
    echo json_encode(['available' => false, 'message' => '필수 데이터가 누락되었습니다.']);
}

$conn->close();
?>
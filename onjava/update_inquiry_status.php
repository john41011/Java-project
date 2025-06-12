<?php
session_start();
include 'db.php';
header('Content-Type: application/json');

// 관리자 여부 확인
if (!isset($_SESSION['userType']) || $_SESSION['userType'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => '관리자만 상태를 변경할 수 있습니다.']);
    exit;
}

// JSON 데이터 받기
$input = json_decode(file_get_contents('php://input'), true);

$inquiry_id = isset($input['id']) ? intval($input['id']) : 0;
$status = isset($input['status']) ? trim($input['status']) : '';

// 필수값 체크
if ($inquiry_id === 0 || empty($status)) {
    echo json_encode(['success' => false, 'message' => '유효하지 않은 요청입니다.']);
    exit;
}

// 유효한 상태값 확인
$valid_statuses = ['접수 완료', '해결 중', '해결 완료'];
if (!in_array($status, $valid_statuses)) {
    echo json_encode(['success' => false, 'message' => '유효하지 않은 상태값입니다.']);
    exit;
}

// 문의글 존재 여부 확인
$stmt = $conn->prepare("SELECT id FROM inquiries WHERE id = ?");
$stmt->bind_param("i", $inquiry_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => '존재하지 않는 문의글입니다.']);
    exit;
}

// 상태 업데이트
$stmt = $conn->prepare("UPDATE inquiries SET status = ? WHERE id = ?");
$stmt->bind_param("si", $status, $inquiry_id);
$success = $stmt->execute();
$stmt->close();

if ($success) {
    echo json_encode(['success' => true, 'message' => '상태가 성공적으로 변경되었습니다.']);
} else {
    echo json_encode(['success' => false, 'message' => '상태 변경에 실패했습니다.']);
}
?>
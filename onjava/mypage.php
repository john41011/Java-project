<?php
session_start();
header('Content-Type: application/json');
include 'db.php';

$response = ['loggedIn' => false];

if (isset($_SESSION['username'])) {
  $username = $_SESSION['username'];
  $response['loggedIn'] = true;
  $response['username'] = $username;

  // DB에서 실시간으로 userType을 조회
  $stmt = $conn->prepare("SELECT userType FROM users WHERE username = ?");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($row = $result->fetch_assoc()) {
    $response['role'] = $row['userType'];
  } else {
    $response['role'] = 'user'; // 기본값
  }
}

echo json_encode($response);
?>

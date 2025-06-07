<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username'])) {
  echo "<p>로그인 필요</p>";
  exit;
}

$username = $_SESSION['username'];
$roleCheck = $conn->prepare("SELECT userType FROM users WHERE username = ?");
$roleCheck->bind_param("s", $username);
$roleCheck->execute();
$res = $roleCheck->get_result();
$role = $res->fetch_assoc()['userType'];

if ($role !== 'admin') {
  echo "<p>접근 권한이 없습니다.</p>";
  exit;
}

// 인증 버튼 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $target = $_POST['username'];

  $update = $conn->prepare("UPDATE users SET userType = 'seller' WHERE username = ?");
  $update->bind_param("s", $target);
  $update->execute();

  echo "<script>alert('{$target} 계정을 사업자 회원으로 전환했습니다.'); location.href='admin_seller_manage.php';</script>";
  exit;
}

$result = $conn->query("SELECT * FROM seller_requests");
echo "<table border='1' cellpadding='10' cellspacing='0'>";
echo "<tr><th>아이디</th><th>상호명</th><th>사업자번호</th><th>처리</th></tr>";

while ($row = $result->fetch_assoc()) {
  echo "<tr>";
  echo "<td>{$row['username']}</td>";
  echo "<td>{$row['shop_name']}</td>";
  echo "<td>{$row['biz_number']}</td>";
  echo "<td>
          <form method='POST'>
            <input type='hidden' name='username' value='{$row['username']}'>
            <button type='submit'>인증</button>
          </form>
        </td>";
  echo "</tr>";
}
echo "</table>";
?>
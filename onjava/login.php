<?php
session_start();
include 'db.php';

$username = $_POST['username'];
$password = $_POST['password'];
$userType = $_POST['userType']; // 'user', 'seller', 'admin'

$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
  if (password_verify($password, $user['password'])) {
    // 로그인 성공 - 세션 저장
    $_SESSION['username'] = $user['username'];
    $_SESSION['nickname'] = $user['nickname'];
    $_SESSION['userType'] = $user['userType']; // 'user', 'seller', 'admin'

    echo "<script>
      alert('" . $_SESSION['nickname'] . "님, 반갑습니다!');
      location.href = 'homepage.html';
    </script>";
  } else {
    // 비밀번호 틀림
    echo "<script>
      alert('아이디 또는 비밀번호가 일치하지 않습니다.');
      history.back();
    </script>";
  }
} else {
  // 아이디 존재하지 않음
  echo "<script>
    alert('아이디 또는 비밀번호가 일치하지 않습니다.');
    history.back();
  </script>";
}
?>
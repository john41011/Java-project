<?php
session_start();
include 'db.php';

$username = $_POST['username'];
$password = $_POST['password'];
$role = $_POST['role'];

$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
  if (password_verify($password, $user['password'])) {
    $_SESSION['username'] = $user['username'];
    echo "<script>
      alert('" . $_SESSION['username'] . "님, 반갑습니다!');
      location.href = 'index.html';
    </script>";
  } else {
    echo "<script>
      alert('아이디/비밀번호가 일치하지 않습니다.');
      history.back();
    </script>";
  }
} else {
  echo "<script>
    alert('아이디/비밀번호가 일치하지 않습니다.');
    history.back();
  </script>";
}
?>

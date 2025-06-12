<?php
include 'db.php';

// POST 데이터 확인
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "<script>alert('잘못된 접근입니다.'); history.back();</script>";
    exit;
}

// 필수 데이터 검증
$required_fields = ['username', 'nickname', 'password', 'email'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        echo "<script>alert('모든 필수 항목을 입력해주세요.'); history.back();</script>";
        exit;
    }
}

$username = trim($_POST['username']);
$nickname = trim($_POST['nickname']);
$password = $_POST['password'];
$email = trim($_POST['email']);

// 필수 약관 동의 확인
if (!isset($_POST['terms']) || !isset($_POST['privacy']) || !isset($_POST['location'])) {
    echo "<script>alert('필수 약관에 모두 동의해주세요.'); history.back();</script>";
    exit;
}

// 비밀번호 유효성 검사 (서버측)
function validatePassword($password) {
    // 8-20자 길이
    if (strlen($password) < 8 || strlen($password) > 20) {
        return false;
    }
    
    // 영문, 숫자, 특수문자 중 2가지 이상 조합
    $hasLetter = preg_match('/[a-zA-Z]/', $password);
    $hasNumber = preg_match('/\d/', $password);
    $hasSpecial = preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password);
    
    $typeCount = $hasLetter + $hasNumber + $hasSpecial;
    if ($typeCount < 2) {
        return false;
    }
    
    // 3개 이상 연속되는 영문/숫자 체크
    for ($i = 0; $i < strlen($password) - 2; $i++) {
        $char1 = ord($password[$i]);
        $char2 = ord($password[$i + 1]);
        $char3 = ord($password[$i + 2]);
        
        if ($char2 === $char1 + 1 && $char3 === $char2 + 1) {
            return false;
        }
    }
    
    return true;
}

if (!validatePassword($password)) {
    echo "<script>alert('비밀번호는 영문/숫자/특수문자 중 2가지 이상 조합으로 8~20자여야 하며, 3개 이상 연속되는 문자는 사용할 수 없습니다.'); history.back();</script>";
    exit;
}

// 이메일 유효성 검사
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('올바른 이메일 형식을 입력해주세요.'); history.back();</script>";
    exit;
}

// 비밀번호 해시화
$hashed = password_hash($password, PASSWORD_DEFAULT);

// 트랜잭션 시작
$conn->begin_transaction();

try {
    // 다시 서버에서 중복 확인
    $stmt1 = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE username = ?");
    $stmt1->bind_param("s", $username);
    $stmt1->execute();
    $result1 = $stmt1->get_result();
    $row1 = $result1->fetch_assoc();
    
    $stmt2 = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE nickname = ?");
    $stmt2->bind_param("s", $nickname);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    $row2 = $result2->fetch_assoc();
    
    if ($row1['count'] > 0) {
        throw new Exception('이미 사용중인 아이디입니다.');
    }
    
    if ($row2['count'] > 0) {
        throw new Exception('이미 사용중인 닉네임입니다.');
    }
    
    // 사용자 데이터 저장
    $stmt = $conn->prepare("INSERT INTO users (username, nickname, password, email) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $nickname, $hashed, $email);
    
    if (!$stmt->execute()) {
        throw new Exception('회원가입 처리 중 오류가 발생했습니다.');
    }
    
    // 트랜잭션 커밋
    $conn->commit();
    
    echo "<script>alert('회원가입이 완료되었습니다!'); location.href='login.html';</script>";
    
} catch (Exception $e) {
    // 트랜잭션 롤백
    $conn->rollback();
    echo "<script>alert('" . $e->getMessage() . "'); history.back();</script>";
}

// 연결 해제
if (isset($stmt1)) $stmt1->close();
if (isset($stmt2)) $stmt2->close();
if (isset($stmt)) $stmt->close();
$conn->close();
?>
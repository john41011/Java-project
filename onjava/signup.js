// 아이디 중복확인
function checkUsername() {
    const username = document.getElementById('username').value;
    
    if (!username) {
        alert('아이디를 입력해주세요.');
        return;
    }
    
    // AJAX로 중복확인
    fetch('check_duplicate.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `type=username&value=${encodeURIComponent(username)}`
    })
    .then(response => response.json())
    .then(data => {
        const okMsg = document.getElementById('id-ok');
        const failMsg = document.getElementById('id-fail');
        
        if (data.available) {
            okMsg.style.display = 'block';
            failMsg.style.display = 'none';
        } else {
            okMsg.style.display = 'none';
            failMsg.style.display = 'block';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('중복확인 중 오류가 발생했습니다.');
    });
}

// 닉네임 중복확인
function checkNickname() {
    const nickname = document.getElementById('nickname').value;
    
    if (!nickname) {
        alert('닉네임을 입력해주세요.');
        return;
    }
    
    fetch('check_duplicate.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `type=nickname&value=${encodeURIComponent(nickname)}`
    })
    .then(response => response.json())
    .then(data => {
        const okMsg = document.getElementById('nick-ok');
        const failMsg = document.getElementById('nick-fail');
        
        if (data.available) {
            okMsg.style.display = 'block';
            failMsg.style.display = 'none';
        } else {
            okMsg.style.display = 'none';
            failMsg.style.display = 'block';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('중복확인 중 오류가 발생했습니다.');
    });
}

// 비밀번호 확인
function matchPassword() {
    const password = document.getElementById('password').value;
    const passwordConfirm = document.getElementById('password-confirm').value;
    const okMsg = document.getElementById('pw-match-ok');
    const failMsg = document.getElementById('pw-match-fail');
    
    if (passwordConfirm === '') {
        okMsg.style.display = 'none';
        failMsg.style.display = 'none';
        return;
    }
    
    if (password === passwordConfirm) {
        okMsg.style.display = 'block';
        failMsg.style.display = 'none';
    } else {
        okMsg.style.display = 'none';
        failMsg.style.display = 'block';
    }
}

// 비밀번호 유효성 검사
function validatePassword(password) {
    // 8-20자 길이 체크
    if (password.length < 8 || password.length > 20) {
        return false;
    }
    
    // 영문, 숫자, 특수문자 중 2가지 이상 조합
    const hasLetter = /[a-zA-Z]/.test(password);
    const hasNumber = /\d/.test(password);
    const hasSpecial = /[!@#$%^&*(),.?":{}|<>]/.test(password);
    
    const typeCount = [hasLetter, hasNumber, hasSpecial].filter(Boolean).length;
    if (typeCount < 2) {
        return false;
    }
    
    // 3개 이상 연속되는 영문/숫자 체크
    for (let i = 0; i < password.length - 2; i++) {
        const char1 = password.charCodeAt(i);
        const char2 = password.charCodeAt(i + 1);
        const char3 = password.charCodeAt(i + 2);
        
        if (char2 === char1 + 1 && char3 === char2 + 1) {
            return false;
        }
    }
    
    return true;
}

// 전체 체크박스 토글
function toggleAllCheckboxes(masterCheckbox) {
    // 전체 동의 체크박스를 제외한 나머지 체크박스들을 선택
    const checkboxes = document.querySelectorAll('.checkbox-group input[type="checkbox"][name]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = masterCheckbox.checked;
    });
}

// 회원가입 폼 검증
function validateSignup() {
    const username = document.getElementById('username').value;
    const nickname = document.getElementById('nickname').value;
    const password = document.getElementById('password').value;
    const passwordConfirm = document.getElementById('password-confirm').value;
    const email = document.getElementById('email').value;
    
    // 필수 입력 체크
    if (!username || !nickname || !password || !passwordConfirm || !email) {
        alert('모든 필수 항목을 입력해주세요.');
        return false;
    }
    
    // 비밀번호 유효성 검사
    if (!validatePassword(password)) {
        alert('비밀번호 조합을 확인해주세요.');
        return false;
    }
    
    // 비밀번호 확인
    if (password !== passwordConfirm) {
        alert('비밀번호가 일치하지 않습니다.');
        return false;
    }
    
    // 필수 약관 동의 체크
    const termsChecked = document.querySelector('input[name="terms"]').checked;
    const privacyChecked = document.querySelector('input[name="privacy"]').checked;
    const locationChecked = document.querySelector('input[name="location"]').checked;
    
    if (!termsChecked || !privacyChecked || !locationChecked) {
        alert('필수 약관에 모두 동의해주세요.');
        return false;
    }
    
    return true;
}
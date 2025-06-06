function checkNickname() {
  const nickname = document.getElementById('nickname').value.trim();
  const okMsg = document.getElementById('nick-ok');
  const failMsg = document.getElementById('nick-fail');

  if (!nickname) {
    okMsg.style.display = 'none';
    failMsg.style.display = 'none';
    window.nicknameChecked = false;
    return;
  }

  fetch(`check_duplicate.php?type=nickname&value=${encodeURIComponent(nickname)}`)
    .then(res => res.text())
    .then(data => {
      data = data.trim();
      if (data === 'ok') {
        okMsg.style.display = 'block';
        failMsg.style.display = 'none';
        window.nicknameChecked = true;
      } else {
        okMsg.style.display = 'none';
        failMsg.style.display = 'block';
        window.nicknameChecked = false;
      }
    });
}


function matchPassword() {
  const pw1 = document.getElementById('password').value;
  const pw2 = document.getElementById('password-confirm').value;
  const ok = document.getElementById('pw-match-ok');
  const fail = document.getElementById('pw-match-fail');

  if (!pw2) {
    ok.style.display = 'none';
    fail.style.display = 'none';
    return;
  }

  if (pw1 === pw2) {
    ok.style.display = 'block';
    fail.style.display = 'none';
  } else {
    ok.style.display = 'none';
    fail.style.display = 'block';
  }
}

function toggleAllCheckboxes(source) {
  const checkboxes = document.querySelectorAll('.checkbox-group input[type="checkbox"]');
  checkboxes.forEach((cb, i) => {
    if (i !== 0) cb.checked = source.checked;
  });
}

function validateSignup() {
  if (!window.usernameChecked) {
    alert("아이디 중복확인을 해주세요.");
    return false;
  }
  if (!window.nicknameChecked) {
    alert("닉네임 중복확인을 해주세요.");
    return false;
  }

  const password = document.getElementById('password').value;
  const pwRule = /^(?=.*[A-Za-z])(?=.*[\d!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,20}$/;
  if (!pwRule.test(password)) {
    alert("비밀번호를 조건에 맞게 입력해주세요.");
    return false;
  }

  const requiredChecks = ['terms', 'privacy', 'location'];
  for (const name of requiredChecks) {
    const checkbox = document.querySelector(`input[name="${name}"]`);
    if (!checkbox.checked) {
      alert("필수 약관에 동의해주세요.");
      return false;
    }
  }

  return true;
}

function toggleNav() {
  const nav = document.getElementById('navMenu');
  nav.classList.toggle('active');
}
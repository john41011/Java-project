document.addEventListener("DOMContentLoaded", () => {
  fetch('mypage.php')
    .then(res => res.json())
    .then(data => {
      if (data.loggedIn) {
        document.getElementById('loginBtn').style.display = 'none';
        document.getElementById('userIcon').style.display = 'block';

        const roleMap = {
          "user": "일반 회원",
          "seller": "사업자 회원",
          "admin": "관리자"
        };
        document.getElementById('user-role').textContent = roleMap[data.role] || "알 수 없음";

        // 역할에 따라 링크 표시
        const sellerLink = document.getElementById("sellerLink");
        if (sellerLink) {
          if (data.role === "user") {
            sellerLink.innerHTML = `<a href="seller_register.html">사업자 등록</a>`;
            sellerLink.style.display = "block";
          } else if (data.role === "seller") {
            sellerLink.innerHTML = `<a href="seller_action.php?action=cancel" onclick="return confirm('사업자 등록을 해지하시겠습니까?')">사업자 등록 해지</a>`;
            sellerLink.style.display = "block";
          } else if (data.role === "admin") {
            sellerLink.innerHTML = `<a href="admin_seller_manage.html">사업자 등록 인증</a>`;
            sellerLink.style.display = "block";
          }
        }
      } else {
        alert("로그인이 필요합니다.");
        window.location.href = "login.html";
      }
    });
});
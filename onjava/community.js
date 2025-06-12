document.addEventListener('DOMContentLoaded', () => {
  const params = new URLSearchParams(window.location.search);
  const boardType = params.get('board') || 'free';
  changeBoard(boardType);

  // 로그인 상태 확인
  fetch('session_check.php')
    .then(res => res.json())
    .then(data => {
      if (data.loggedIn) {
        document.getElementById('writeBtn').style.display = 'inline-block';
      }
    });
});

function changeBoard(type) {
  window.currentBoard = type;
  loadBoard(type);
}

function loadBoard(type) {
  fetch(`board.php?type=${type}`)
    .then(res => res.text())
    .then(html => {
      document.getElementById('boardContainer').innerHTML = html;

      setTimeout(() => {
        fetchPosts();
      }, 0);
    });
}

function fetchPosts() {
  const sort = document.getElementById('sortType')?.value || 'date_desc';
  const field = document.getElementById('searchField')?.value || 'title';
  const keyword = document.getElementById('searchKeyword')?.value || '';

  fetch(`fetch_posts.php?board=${window.currentBoard}&sort=${sort}&field=${field}&keyword=${encodeURIComponent(keyword)}`)
    .then(res => res.json())
    .then(data => {
      const tbody = document.getElementById('postTableBody');
      if (!tbody) return;

      tbody.innerHTML = "";

      data.forEach((post, index) => {
        const row = document.createElement('tr');
        row.innerHTML = `
          <td>${index + 1}</td>
          <td><a href="post_view.html?id=${post.id}">${post.title}</a></td>
          <td>${post.nickname}</td>
          <td>${post.date}</td>
          <td>${post.views}</td>
        `;
        tbody.appendChild(row);
      });
    });
}

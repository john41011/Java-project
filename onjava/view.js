document.addEventListener("DOMContentLoaded", () => {
  const urlParams = new URLSearchParams(window.location.search);
  const id = urlParams.get("id");

  if (!id) {
    alert("잘못된 접근입니다.");
    window.location.href = "community.html";
    return;
  }

  fetch(`post_view.php?id=${id}`)
    .then(res => res.json())
    .then(data => {
      document.getElementById('post-title').textContent = data.title;
      document.getElementById('post-author').textContent = data.nickname;
      document.getElementById('post-date').textContent = data.date;
      document.getElementById('post-views').textContent = data.views;
      document.getElementById('post-content').innerText = data.content;
    });
});

<?php
$type = $_GET['type'] ?? 'free';
?>

<!-- 게시판 타이틀만 표시 -->
<h2>
  <?php
    if ($type === 'free') echo "자유 게시판";
    elseif ($type === 'share') echo "정보 게시판";
    elseif ($type === 'job') echo "구인 게시판";
    else echo "게시판";
  ?>
</h2>

<!-- JS가 데이터를 삽입할 테이블 뼈대 -->
<section class="post-list">
  <table>
    <thead>
      <tr>
        <th>번호</th>
        <th>제목</th>
        <th>작성자</th>
        <th>날짜</th>
        <th>조회수</th>
      </tr>
    </thead>
    <tbody id="postTableBody">
      <!-- fetchPosts()로 JS가 여기에 삽입 -->
    </tbody>
  </table>
</section>

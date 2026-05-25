<?php
$postId = (int) ($_GET['id'] ?? $_POST['post_id'] ?? 0);
header('Location: blog_manager.php' . ($postId > 0 ? '?edit=' . $postId : '?compose=1'));
exit;

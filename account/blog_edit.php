<?php
$postId = (int) ($_GET['id'] ?? 0);
header('Location: blog_manager.php' . ($postId > 0 ? '?edit=' . $postId : ''));
exit;

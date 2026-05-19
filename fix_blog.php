<?php
$files = [
    'account/blog_list.php',
    'account/blog_categories.php',
    'account/blog_comments.php',
    'account/blog_new.php',
    'account/blog_edit.php',
    'account/blog_view.php',
    'account/blog_posts.php',
];
foreach ($files as $f) {
    $content = file_get_contents($f);
    if (preg_match('/require\s+[\'"]inc\/admin_header\.php[\'"];\s*\n(?!\?>)/', $content)) {
        $content = preg_replace('/(require\s+[\'"]inc\/admin_header\.php[\'"];)\s*\n/', "$1\n?>\n", $content, 1);
        file_put_contents($f, $content);
        echo "FIXED: $f\n";
    } else {
        echo "OK: $f\n";
    }
}

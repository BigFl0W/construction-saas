<?php
// post.php - Display a single blog post by slug with comments
require_once 'config/config.php';
require_once 'classes/Auth.php';   // optional
require_once 'classes/Blog.php';
require_once 'classes/Functions.php';

$blog = new Blog();
$functions = Functions::getInstance();
$db = Database::getInstance();

// Get slug from URL (rewritten by .htaccess)
$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    header("HTTP/1.0 404 Not Found");
    include '404.php';
    exit;
}

// Fetch the published post with that slug
$sql = "SELECT p.*, 
               CONCAT(e.first_name, ' ', e.last_name) AS employee_author,
               c.company_name AS client_author,
               m.file_path AS featured_image_path,
               m.original_filename AS featured_image_name
        FROM blog_posts p
        LEFT JOIN employees e ON p.author_employee_id = e.id AND p.author_type = 'employee'
        LEFT JOIN clients c ON p.author_client_id = c.id AND p.author_type = 'client'
        LEFT JOIN media m ON p.featured_image_id = m.id
        WHERE p.status = 'published' AND p.deleted_at IS NULL AND p.slug = :slug
        LIMIT 1";
$stmt = $db->query($sql, ['slug' => $slug]);
$post = $stmt->fetch();

if (!$post) {
    header("HTTP/1.0 404 Not Found");
    include '404.php';
    exit;
}

// Fetch categories for this post
$catSql = "SELECT c.id, c.name, c.slug 
           FROM blog_categories c
           JOIN blog_post_categories pc ON c.id = pc.category_id
           WHERE pc.post_id = :post_id";
$catStmt = $db->query($catSql, ['post_id' => $post['id']]);
$categories = $catStmt->fetchAll();

// Fetch tags for this post
$tagSql = "SELECT t.id, t.name, t.slug 
           FROM blog_tags t
           JOIN blog_post_tags pt ON t.id = pt.tag_id
           WHERE pt.post_id = :post_id";
$tagStmt = $db->query($tagSql, ['post_id' => $post['id']]);
$tags = $tagStmt->fetchAll();

// Fetch related posts (same first category, exclude current)
$relatedPosts = [];
if (!empty($categories)) {
    $firstCatId = $categories[0]['id'];
    $relatedSql = "SELECT p.id, p.title, p.slug, p.excerpt, 
                          m.file_path AS featured_image_path
                   FROM blog_posts p
                   LEFT JOIN blog_post_categories pc ON p.id = pc.post_id
                   LEFT JOIN media m ON p.featured_image_id = m.id
                   WHERE p.status = 'published' AND p.deleted_at IS NULL 
                     AND p.id != :current_id
                     AND pc.category_id = :cat_id
                   ORDER BY p.published_at DESC
                   LIMIT 3";
    $relatedStmt = $db->query($relatedSql, ['current_id' => $post['id'], 'cat_id' => $firstCatId]);
    $relatedPosts = $relatedStmt->fetchAll();
}

// Format date
$publishedDate = date('F j, Y', strtotime($post['published_at'] ?? $post['created_at']));

// Determine author name
$authorName = 'TPV Construction and Services LTD';
if ($post['author_type'] == 'employee' && !empty($post['employee_author'])) {
    $authorName = $post['employee_author'];
} elseif ($post['author_type'] == 'client' && !empty($post['client_author'])) {
    $authorName = $post['client_author'];
}

// ----- COMMENT HANDLING -----
$commentMessage = '';
$commentError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
    $author = trim($_POST['author'] ?? '');
    $email  = trim($_POST['email'] ?? '');
    $url    = trim($_POST['url'] ?? '');
    $content = trim($_POST['comment'] ?? '');
    $parentId = !empty($_POST['comment_parent']) ? (int)$_POST['comment_parent'] : 0;

    if (empty($author) || empty($email) || empty($content)) {
        $commentError = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $commentError = 'Invalid email address.';
    } else {
        $uuid = $functions->generateUUID();
        $sql = "INSERT INTO blog_comments (uuid, post_id, parent_id, author_name, author_email, author_url, content, status, created_at, updated_at)
                VALUES (:uuid, :post_id, :parent_id, :author_name, :author_email, :author_url, :content, 'pending', NOW(), NOW())";
        $params = [
            'uuid'       => $uuid,
            'post_id'    => $post['id'],
            'parent_id'  => $parentId ?: null,
            'author_name'=> $author,
            'author_email'=> $email,
            'author_url' => $url ?: null,
            'content'    => $content
        ];
        try {
            $db->query($sql, $params);
            $commentMessage = 'Your comment has been submitted and is awaiting moderation.';
        } catch (Exception $e) {
            $commentError = 'Failed to submit comment. Please try again.';
            // Log the actual error for debugging
            error_log("Comment insert error: " . $e->getMessage());
        }
    }
}

// Fetch approved comments for this post
$commentSql = "SELECT * FROM blog_comments 
               WHERE post_id = :post_id AND status = 'approved' AND deleted_at IS NULL
               ORDER BY created_at ASC";
$commentStmt = $db->query($commentSql, ['post_id' => $post['id']]);
$allComments = $commentStmt->fetchAll();

// Build a comment tree (by parent)
$commentsByParent = [];
foreach ($allComments as $comment) {
    $parent = $comment['parent_id'] ?: 0;
    $commentsByParent[$parent][] = $comment;
}

// Recursive function to render comments
function renderComments($parentId, $commentsByParent, $depth = 0) {
    if (!isset($commentsByParent[$parentId])) return '';
    $html = '<ul class="comment-list' . ($depth > 0 ? ' children' : '') . '">';
    foreach ($commentsByParent[$parentId] as $comment) {
        // Safely escape values, handling possible nulls
        $author = htmlspecialchars($comment['author_name'] ?? 'Anonymous', ENT_QUOTES, 'UTF-8');
        $date = date('F j, Y \a\t g:i a', strtotime($comment['created_at']));
        $content = nl2br(htmlspecialchars($comment['content'] ?? '', ENT_QUOTES, 'UTF-8'));
        // For the onclick, we need to escape the author name for JavaScript string
        $authorJs = addslashes($comment['author_name'] ?? 'Anonymous');
        
        $html .= '<li id="comment-' . $comment['id'] . '" class="comment">';
        $html .= '<div class="comment-body">';
        $html .= '<div class="comment-author">' . $author . '</div>';
        $html .= '<div class="comment-meta">' . $date . '</div>';
        $html .= '<div class="comment-content">' . $content . '</div>';
        $html .= '<div class="reply"><a href="#" onclick="showReplyForm(' . $comment['id'] . ', \'' . $authorJs . '\'); return false;">Reply</a></div>';
        $html .= '</div>';
        $html .= renderComments($comment['id'], $commentsByParent, $depth + 1);
        $html .= '</li>';
    }
    $html .= '</ul>';
    return $html;
}

$commentsHtml = renderComments(0, $commentsByParent);
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo htmlspecialchars($post['title'] ?? 'Blog Post', ENT_QUOTES, 'UTF-8'); ?> – TPV Construction and Services LTD</title>
    <meta name='robots' content='max-image-preview:large' />
    <link rel='dns-prefetch' href='//fonts.googleapis.com' />
    <link rel="alternate" type="application/rss+xml" title="TPV Construction and Services LTD &raquo; Feed" href="feed/" />
    <link rel="alternate" type="application/rss+xml" title="TPV Construction and Services LTD &raquo; Comments Feed" href="comments/feed/" />
    <style id="wp-img-auto-sizes-contain-inline-css">img:is([sizes="auto" i], [sizes^="auto," i]) { contain-intrinsic-size: 3000px 1500px; }</style>
    <style id="wp-emoji-styles-inline-css">img.wp-smiley, img.emoji { display: inline !important; border: none !important; box-shadow: none !important; height: 1em !important; width: 1em !important; margin: 0 0.07em !important; vertical-align: -0.1em !important; background: none !important; padding: 0 !important; }</style>
    <link rel='stylesheet' id='wp-block-library-css' href='../wp-includes/css/dist/block-library/style.min.css?ver=6.9.1' media='all' />
    <style id='classic-theme-styles-inline-css'>.wp-block-button__link { color: #fff; background-color: #32373c; border-radius: 9999px; box-shadow: none; text-decoration: none; padding: calc(0.667em + 2px) calc(1.333em + 2px); font-size: 1.125em; } .wp-block-file__button { background: #32373c; color: #fff; text-decoration: none; }</style>
    <style id='global-styles-inline-css'>:root { --wp--preset--aspect-ratio--square: 1; --wp--preset--aspect-ratio--4-3: 4/3; --wp--preset--aspect-ratio--3-4: 3/4; --wp--preset--aspect-ratio--3-2: 3/2; --wp--preset--aspect-ratio--2-3: 2/3; --wp--preset--aspect-ratio--16-9: 16/9; --wp--preset--aspect-ratio--9-16: 9/16; --wp--preset--color--black: #000000; --wp--preset--color--cyan-bluish-gray: #abb8c3; --wp--preset--color--white: #ffffff; --wp--preset--color--pale-pink: #f78da7; --wp--preset--color--vivid-red: #cf2e2e; --wp--preset--color--luminous-vivid-orange: #ff6900; --wp--preset--color--luminous-vivid-amber: #fcb900; --wp--preset--color--light-green-cyan: #7bdcb5; --wp--preset--color--vivid-green-cyan: #00d084; --wp--preset--color--pale-cyan-blue: #8ed1fc; --wp--preset--color--vivid-cyan-blue: #0693e3; --wp--preset--color--vivid-purple: #9b51e0; --wp--preset--gradient--vivid-cyan-blue-to-vivid-purple: linear-gradient(135deg,rgba(6,147,227,1) 0%,rgb(155,81,224) 100%); --wp--preset--gradient--light-green-cyan-to-vivid-green-cyan: linear-gradient(135deg,rgb(122,220,180) 0%,rgb(0,208,130) 100%); --wp--preset--gradient--luminous-vivid-amber-to-luminous-vivid-orange: linear-gradient(135deg,rgba(252,185,0,1) 0%,rgba(255,105,0,1) 100%); --wp--preset--gradient--luminous-vivid-orange-to-vivid-red: linear-gradient(135deg,rgba(255,105,0,1) 0%,rgb(207,46,46) 100%); --wp--preset--gradient--very-light-gray-to-cyan-bluish-gray: linear-gradient(135deg,rgb(238,238,238) 0%,rgb(169,184,195) 100%); --wp--preset--gradient--cool-to-warm-spectrum: linear-gradient(135deg,rgb(74,234,220) 0%,rgb(151,120,209) 20%,rgb(207,42,186) 40%,rgb(238,44,130) 60%,rgb(251,105,98) 80%,rgb(254,248,76) 100%); --wp--preset--gradient--blush-light-purple: linear-gradient(135deg,rgb(255,206,236) 0%,rgb(152,150,240) 100%); --wp--preset--gradient--blush-bordeaux: linear-gradient(135deg,rgb(254,205,165) 0%,rgb(254,45,45) 50%,rgb(107,0,62) 100%); --wp--preset--gradient--luminous-dusk: linear-gradient(135deg,rgb(255,203,112) 0%,rgb(199,81,192) 50%,rgb(65,88,208) 100%); --wp--preset--gradient--pale-ocean: linear-gradient(135deg,rgb(255,245,203) 0%,rgb(182,227,212) 50%,rgb(51,167,181) 100%); --wp--preset--gradient--electric-grass: linear-gradient(135deg,rgb(202,248,128) 0%,rgb(113,206,126) 100%); --wp--preset--gradient--midnight: linear-gradient(135deg,rgb(2,3,129) 0%,rgb(40,116,252) 100%); --wp--preset--font-size--small: 13px; --wp--preset--font-size--medium: 20px; --wp--preset--font-size--large: 36px; --wp--preset--font-size--x-large: 42px; --wp--preset--spacing--20: 0.44rem; --wp--preset--spacing--30: 0.67rem; --wp--preset--spacing--40: 1rem; --wp--preset--spacing--50: 1.5rem; --wp--preset--spacing--60: 2.25rem; --wp--preset--spacing--70: 3.38rem; --wp--preset--spacing--80: 5.06rem; --wp--preset--shadow--natural: 6px 6px 9px rgba(0,0,0,0.2); --wp--preset--shadow--deep: 12px 12px 50px rgba(0,0,0,0.4); --wp--preset--shadow--sharp: 6px 6px 0px rgba(0,0,0,0.2); --wp--preset--shadow--outlined: 6px 6px 0px -3px rgba(255,255,255,1), 6px 6px rgba(0,0,0,1); --wp--preset--shadow--crisp: 6px 6px 0px rgba(0,0,0,1); } :where(.is-layout-flex){gap: 0.5em;}:where(.is-layout-grid){gap: 0.5em;}body .is-layout-flex{display: flex;}.is-layout-flex{flex-wrap: wrap;align-items: center;}.is-layout-flex > :is(*, div){margin: 0;}body .is-layout-grid{display: grid;}.is-layout-grid > :is(*, div){margin: 0;}:where(.wp-block-columns.is-layout-flex){gap: 2em;}:where(.wp-block-columns.is-layout-grid){gap: 2em;}:where(.wp-block-post-template.is-layout-flex){gap: 1.25em;}:where(.wp-block-post-template.is-layout-grid){gap: 1.25em;}.has-black-color{color: var(--wp--preset--color--black) !important;}.has-cyan-bluish-gray-color{color: var(--wp--preset--color--cyan-bluish-gray) !important;}.has-white-color{color: var(--wp--preset--color--white) !important;}.has-pale-pink-color{color: var(--wp--preset--color--pale-pink) !important;}.has-vivid-red-color{color: var(--wp--preset--color--vivid-red) !important;}.has-luminous-vivid-orange-color{color: var(--wp--preset--color--luminous-vivid-orange) !important;}.has-luminous-vivid-amber-color{color: var(--wp--preset--color--luminous-vivid-amber) !important;}.has-light-green-cyan-color{color: var(--wp--preset--color--light-green-cyan) !important;}.has-vivid-green-cyan-color{color: var(--wp--preset--color--vivid-green-cyan) !important;}.has-pale-cyan-blue-color{color: var(--wp--preset--color--pale-cyan-blue) !important;}.has-vivid-cyan-blue-color{color: var(--wp--preset--color--vivid-cyan-blue) !important;}.has-vivid-purple-color{color: var(--wp--preset--color--vivid-purple) !important;}.has-black-background-color{background-color: var(--wp--preset--color--black) !important;}.has-cyan-bluish-gray-background-color{background-color: var(--wp--preset--color--cyan-bluish-gray) !important;}.has-white-background-color{background-color: var(--wp--preset--color--white) !important;}.has-pale-pink-background-color{background-color: var(--wp--preset--color--pale-pink) !important;}.has-vivid-red-background-color{background-color: var(--wp--preset--color--vivid-red) !important;}.has-luminous-vivid-orange-background-color{background-color: var(--wp--preset--color--luminous-vivid-orange) !important;}.has-luminous-vivid-amber-background-color{background-color: var(--wp--preset--color--luminous-vivid-amber) !important;}.has-light-green-cyan-background-color{background-color: var(--wp--preset--color--light-green-cyan) !important;}.has-vivid-green-cyan-background-color{background-color: var(--wp--preset--color--vivid-green-cyan) !important;}.has-pale-cyan-blue-background-color{background-color: var(--wp--preset--color--pale-cyan-blue) !important;}.has-vivid-cyan-blue-background-color{background-color: var(--wp--preset--color--vivid-cyan-blue) !important;}.has-vivid-purple-background-color{background-color: var(--wp--preset--color--vivid-purple) !important;}.has-black-border-color{border-color: var(--wp--preset--color--black) !important;}.has-cyan-bluish-gray-border-color{border-color: var(--wp--preset--color--cyan-bluish-gray) !important;}.has-white-border-color{border-color: var(--wp--preset--color--white) !important;}.has-pale-pink-border-color{border-color: var(--wp--preset--color--pale-pink) !important;}.has-vivid-red-border-color{border-color: var(--wp--preset--color--vivid-red) !important;}.has-luminous-vivid-orange-border-color{border-color: var(--wp--preset--color--luminous-vivid-orange) !important;}.has-luminous-vivid-amber-border-color{border-color: var(--wp--preset--color--luminous-vivid-amber) !important;}.has-light-green-cyan-border-color{border-color: var(--wp--preset--color--light-green-cyan) !important;}.has-vivid-green-cyan-border-color{border-color: var(--wp--preset--color--vivid-green-cyan) !important;}.has-pale-cyan-blue-border-color{border-color: var(--wp--preset--color--pale-cyan-blue) !important;}.has-vivid-cyan-blue-border-color{border-color: var(--wp--preset--color--vivid-cyan-blue) !important;}.has-vivid-purple-border-color{border-color: var(--wp--preset--color--vivid-purple) !important;}.has-vivid-cyan-blue-to-vivid-purple-gradient-background{background: var(--wp--preset--gradient--vivid-cyan-blue-to-vivid-purple) !important;}.has-light-green-cyan-to-vivid-green-cyan-gradient-background{background: var(--wp--preset--gradient--light-green-cyan-to-vivid-green-cyan) !important;}.has-luminous-vivid-amber-to-luminous-vivid-orange-gradient-background{background: var(--wp--preset--gradient--luminous-vivid-amber-to-luminous-vivid-orange) !important;}.has-luminous-vivid-orange-to-vivid-red-gradient-background{background: var(--wp--preset--gradient--luminous-vivid-orange-to-vivid-red) !important;}.has-very-light-gray-to-cyan-bluish-gray-gradient-background{background: var(--wp--preset--gradient--very-light-gray-to-cyan-bluish-gray) !important;}.has-cool-to-warm-spectrum-gradient-background{background: var(--wp--preset--gradient--cool-to-warm-spectrum) !important;}.has-blush-light-purple-gradient-background{background: var(--wp--preset--gradient--blush-light-purple) !important;}.has-blush-bordeaux-gradient-background{background: var(--wp--preset--gradient--blush-bordeaux) !important;}.has-luminous-dusk-gradient-background{background: var(--wp--preset--gradient--luminous-dusk) !important;}.has-pale-ocean-gradient-background{background: var(--wp--preset--gradient--pale-ocean) !important;}.has-electric-grass-gradient-background{background: var(--wp--preset--gradient--electric-grass) !important;}.has-midnight-gradient-background{background: var(--wp--preset--gradient--midnight) !important;}.has-small-font-size{font-size: var(--wp--preset--font-size--small) !important;}.has-medium-font-size{font-size: var(--wp--preset--font-size--medium) !important;}.has-large-font-size{font-size: var(--wp--preset--font-size--large) !important;}.has-x-large-font-size{font-size: var(--wp--preset--font-size--x-large) !important;}</style>
    <link rel='stylesheet' id='contact-form-7-css' href='../wp-content/plugins/contact-form-7/includes/css/styles.css?ver=6.1.5' media='all' />
    <link rel='stylesheet' id='ekit-widget-styles-css' href='../wp-content/plugins/elementskit-lite/widgets/init/assets/css/widget-styles.css?ver=3.7.9' media='all' />
    <link rel='stylesheet' id='ekit-widget-styles-pro-css' href='../wp-content/plugins/elementskit/widgets/init/assets/css/widget-styles-pro.css?ver=4.2.1' media='all' />
    <link rel='stylesheet' id='ekit-responsive-css' href='../wp-content/plugins/elementskit-lite/widgets/init/assets/css/responsive.css?ver=3.7.9' media='all' />
    <link rel='stylesheet' id='tpv-font-css' href='../../css2?family=Manrope%3Awght%40200..800&#038;family=DM+Sans%3Aital%2Copsz%2Cwght%400%2C9..40%2C100..1000%3B1%2C9..40%2C100..1000&#038;display=swap' media='all' />
    <link rel='stylesheet' id='tpv-css-variable-css' href='../wp-content/themes/tpv/assets/css/css-variable.css?ver=1.0.8' media='all' />
    <link rel='stylesheet' id='fontawesome-6.4.0-css' href='../wp-content/themes/tpv/assets/css/all.min.css?ver=1.0.8' media='all' />
    <link rel='stylesheet' id='bootstrap-5.3.2-css' href='../wp-content/themes/tpv/assets/css/bootstrap.min.css?ver=1.0.8' media='all' />
    <link rel='stylesheet' id='tpv-style-css' href='../wp-content/themes/tpv/style.css?ver=1.0.8' media='all' />
    <script src="../wp-includes/js/jquery/jquery.min.js?ver=3.7.1" id="jquery-core-js"></script>
    <script src="../wp-includes/js/jquery/jquery-migrate.min.js?ver=3.4.1" id="jquery-migrate-js"></script>
    <link rel="https://api.w.org/" href="wp-json/" />
    <link rel="EditURI" type="application/rsd+xml" title="RSD" href="https://tpvconstruction.com.ng/xmlrpc.php?rsd" />
    <link rel="canonical" href="<?php echo htmlspecialchars($post['slug'] ?? '', ENT_QUOTES, 'UTF-8'); ?>/" />
    <meta name="generator" content="Elementor 3.35.3; features: e_font_icon_svg, additional_custom_breakpoints; settings: css_print_method-external, google_font-enabled, font_display-swap" />
    <script type="text/javascript">var elementskit_module_parallax_url = "https://tpvconstruction.com.ng/../wp-content/plugins/elementskit/modules/parallax/";</script>
    <style>
        .e-con.e-parent:nth-of-type(n+4):not(.e-lazyloaded):not(.e-no-lazyload),
        .e-con.e-parent:nth-of-type(n+4):not(.e-lazyloaded):not(.e-no-lazyload) * { background-image: none !important; }
        @media screen and (max-height: 1024px) {
            .e-con.e-parent:nth-of-type(n+3):not(.e-lazyloaded):not(.e-no-lazyload),
            .e-con.e-parent:nth-of-type(n+3):not(.e-lazyloaded):not(.e-no-lazyload) * { background-image: none !important; }
        }
        @media screen and (max-height: 640px) {
            .e-con.e-parent:nth-of-type(n+2):not(.e-lazyloaded):not(.e-no-lazyload),
            .e-con.e-parent:nth-of-type(n+2):not(.e-lazyloaded):not(.e-no-lazyload) * { background-image: none !important; }
        }
        /* Related posts styling */
        .related-posts { margin-top: 50px; padding-top: 30px; border-top: 1px solid #e0e0e0; }
        .related-post-item { margin-bottom: 30px; }
        .related-post-image { margin-bottom: 15px; }
        .related-post-image img { width: 100%; height: auto; border-radius: 8px; }
        .related-post-title { font-size: 1.2rem; font-weight: 600; margin-bottom: 10px; }
        .related-post-excerpt { color: #666; font-size: 0.95rem; }
        .related-post-readmore { color: #007bff; text-decoration: none; }
        
        /* Comment styling */
        .comment-list {
            list-style: none;
            padding: 0;
            margin: 0 0 30px 0;
        }
        .comment-list ul.children {
            margin-left: 40px;
            list-style: none;
            border-left: 2px solid #f0f0f0;
            padding-left: 20px;
        }
        .comment {
            margin-bottom: 25px;
            padding-bottom: 25px;
            border-bottom: 1px solid #eee;
        }
        .comment:last-child {
            border-bottom: none;
        }
        .comment-author {
            font-weight: 600;
            color: #333;
        }
        .comment-meta {
            font-size: 0.8rem;
            color: #777;
            margin-bottom: 10px;
        }
        .comment-content {
            line-height: 1.6;
            color: #444;
        }
        .reply {
            margin-top: 10px;
        }
        .reply a {
            color: #007bff;
            text-decoration: none;
            font-size: 0.9rem;
        }
        .reply a:hover {
            text-decoration: underline;
        }
        #reply-note {
            background: #f8f9fa;
            padding: 10px;
            border-left: 3px solid #007bff;
            margin-bottom: 15px;
            font-size: 0.9rem;
        }
    </style>
    <link rel="icon" href="../wp-content/uploads/2024/06/favicon.png" sizes="32x32" />
    <link rel="icon" href="../wp-content/uploads/2024/06/favicon.png" sizes="192x192" />
    <link rel="apple-touch-icon" href="../wp-content/uploads/2024/06/favicon.png" />
    <meta name="msapplication-TileImage" content="https://tpvconstrcution.com.ng/../wp-content/uploads/2024/06/favicon.png" />
</head>
<body class="wp-singular post-template-default single single-post postid-<?php echo (int)($post['id'] ?? 0); ?> single-format-standard wp-custom-logo wp-theme-tpv tt-magic-cursor elementor-default elementor-kit-7">


</div>

<a class="skip-link screen-reader-text" href="#content">Skip to content</a>

<?php include 'includes/header.php'; ?>

<main id="content" class="site-main post-<?php echo (int)($post['id'] ?? 0); ?> post type-post status-publish format-standard has-post-thumbnail hentry <?php foreach ($categories as $cat) echo 'category-' . htmlspecialchars($cat['slug'] ?? '', ENT_QUOTES, 'UTF-8') . ' '; ?>">
    <div class="page-header" style="background-image: url('../wp-content/uploads/2024/06/page-header-bg.jpg');">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="page-header-box">
                        <h1 class="text-anime"><?php echo htmlspecialchars($post['title'] ?? '', ENT_QUOTES, 'UTF-8'); ?></h1>
                        <div class="post-single-meta wow fadeInUp" data-wow-delay="0.25s">
                            <ul>
                                <li><i class="fa-solid fa-calendar-days"></i><?php echo htmlspecialchars($publishedDate ?? '', ENT_QUOTES, 'UTF-8'); ?></li>
                                <li><i class="fa-solid fa-user"></i><?php echo htmlspecialchars($authorName ?? 'TPV Construction and Services LTD', ENT_QUOTES, 'UTF-8'); ?></li>
                                <?php if (!empty($categories)): ?>
                                <li><i class="fa-solid fa-tag"></i>
                                    <?php 
                                    $catLinks = [];
                                    foreach ($categories as $cat) {
                                        $catLinks[] = '<a href="category/' . urlencode($cat['slug'] ?? '') . '/">' . htmlspecialchars($cat['name'] ?? '', ENT_QUOTES, 'UTF-8') . '</a>';
                                    }
                                    echo implode(', ', $catLinks);
                                    ?>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-single-post">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <?php if (!empty($post['featured_image_path'])): ?>
                    <div class="post-single-image">
                        <figure class="image-anime">
                            <img fetchpriority="high" width="800" height="450" 
                                 src="../<?php echo htmlspecialchars($post['featured_image_path'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" 
                                 class="attachment-large size-large wp-post-image" 
                                 alt="<?php echo htmlspecialchars($post['title'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                 decoding="async" />
                        </figure>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-12">
                    <div class="post-content">
                        <div class="post-entry tpv-block-style">
                            <?php echo $post['content'] ?? ''; // already contains HTML ?>
                        </div>

                        <div class="row align-items-center">
                            <div class="col-lg-8">
                                <?php if (!empty($tags)): ?>
                                <div class="post-tags">
                                    <span class="tag-links">Tags:
                                        <?php 
                                        $tagLinks = [];
                                        foreach ($tags as $tag) {
                                            $tagLinks[] = '<a href="tag/' . urlencode($tag['slug'] ?? '') . '/" rel="tag">' . htmlspecialchars($tag['name'] ?? '', ENT_QUOTES, 'UTF-8') . '</a>';
                                        }
                                        echo implode(' ', $tagLinks);
                                        ?>
                                    </span>
                                </div>
                                <?php endif; ?>
                            </div>

                            <div class="col-lg-4">
                                <div class="post-social-sharing">
                                    <ul>
                                        <li><a href="https://www.facebook.com/sharer.php?u=<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . '/' . ($post['slug'] ?? '') . '/'); ?>&t=<?php echo urlencode($post['title'] ?? ''); ?>" rel="nofollow" target="_blank"><i class="fab fa-facebook-f"></i></a></li>
                                        <li><a href="https://twitter.com/share?text=<?php echo urlencode($post['title'] ?? ''); ?>&url=<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . '/' . ($post['slug'] ?? '') . '/'); ?>" rel="nofollow" target="_blank"><i class="fab fa-twitter"></i></a></li>
                                        <li><a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . '/' . ($post['slug'] ?? '') . '/'); ?>&title=<?php echo urlencode($post['title'] ?? ''); ?>&summary=<?php echo urlencode(strip_tags(substr($post['excerpt'] ?? $post['content'] ?? '', 0, 200))); ?>" rel="nofollow" target="_blank"><i class="fab fa-linkedin-in"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Related Posts Section -->
                    <?php if (!empty($relatedPosts)): ?>
                    <div class="related-posts">
                        <h3>Related Posts</h3>
                        <div class="row">
                            <?php foreach ($relatedPosts as $related): ?>
                            <div class="col-lg-4 col-md-6">
                                <div class="related-post-item">
                                    <?php if (!empty($related['featured_image_path'])): ?>
                                    <div class="related-post-image">
                                        <a href="../<?php echo htmlspecialchars($related['slug'] ?? '', ENT_QUOTES, 'UTF-8'); ?>/">
                                            <img src="../<?php echo htmlspecialchars($related['featured_image_path'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($related['title'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="img-fluid">
                                        </a>
                                    </div>
                                    <?php endif; ?>
                                    <h4 class="related-post-title"><a href="<?php echo htmlspecialchars($related['slug'] ?? '', ENT_QUOTES, 'UTF-8'); ?>/"><?php echo htmlspecialchars($related['title'] ?? '', ENT_QUOTES, 'UTF-8'); ?></a></h4>
                                    <p class="related-post-excerpt"><?php echo htmlspecialchars($functions->truncateText($related['excerpt'] ?? '', 100) ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                                    <a href="../<?php echo htmlspecialchars($related['slug'] ?? '', ENT_QUOTES, 'UTF-8'); ?>/" class="related-post-readmore">Read More <i class="fas fa-arrow-right"></i></a>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Comments Section -->
                    <div class="comment-box">
                        <?php if ($commentMessage): ?>
                            <div class="alert alert-success"><?php echo htmlspecialchars($commentMessage, ENT_QUOTES, 'UTF-8'); ?></div>
                        <?php endif; ?>
                        <?php if ($commentError): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($commentError, ENT_QUOTES, 'UTF-8'); ?></div>
                        <?php endif; ?>

                        <section id="comments" class="comments-area">
                            <h3>Comments</h3>
                            <?php if (!empty($commentsHtml)): ?>
                                <?php echo $commentsHtml; ?>
                            <?php else: ?>
                                <p>No comments yet. Be the first to comment!</p>
                            <?php endif; ?>

                            <div id="respond" class="comment-respond">
                                <h2 id="reply-title" class="comment-reply-title">Leave a Reply</h2>
                                <form action="" method="post" class="comment-form">
                                    <input type="hidden" name="submit_comment" value="1">
                                    <input type="hidden" name="comment_post_ID" value="<?php echo (int)($post['id'] ?? 0); ?>" id="comment_post_ID" />
                                    <input type="hidden" name="comment_parent" id="comment_parent" value="0" />

                                    <p class="comment-notes">
                                        <span id="email-notes">Your email address will not be published.</span>
                                        <span class="required-field-message">Required fields are marked <span class="required">*</span></span>
                                    </p>

                                    <p class="comment-form-comment">
                                        <label for="comment">Comment <span class="required">*</span></label>
                                        <textarea id="comment" name="comment" cols="45" rows="8" maxlength="65525" required></textarea>
                                    </p>

                                    <p class="comment-form-author">
                                        <label for="author">Name <span class="required">*</span></label>
                                        <input id="author" name="author" type="text" value="" size="30" maxlength="245" autocomplete="name" required />
                                    </p>

                                    <p class="comment-form-email">
                                        <label for="email">Email <span class="required">*</span></label>
                                        <input id="email" name="email" type="email" value="" size="30" maxlength="100" aria-describedby="email-notes" autocomplete="email" required />
                                    </p>

                                    <p class="comment-form-url">
                                        <label for="url">Website</label>
                                        <input id="url" name="url" type="url" value="" size="30" maxlength="200" autocomplete="url" />
                                    </p>

                                    <p class="comment-form-cookies-consent">
                                        <input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes" />
                                        <label for="wp-comment-cookies-consent">Save my name, email, and website in this browser for the next time I comment.</label>
                                    </p>

                                    <p class="form-submit">
                                        <input name="submit" type="submit" id="submit" class="submit" value="Post Comment" />
                                    </p>
                                </form>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

<!-- JavaScript for reply functionality -->
<script>
function showReplyForm(commentId, authorName) {
    document.getElementById('comment_parent').value = commentId;
    document.getElementById('comment').focus();

    // Show a small note that we are replying
    let replyNote = document.getElementById('reply-note');
    if (!replyNote) {
        replyNote = document.createElement('div');
        replyNote.id = 'reply-note';
        document.querySelector('.comment-form').insertBefore(replyNote, document.querySelector('.comment-form').firstChild);
    }
    replyNote.innerHTML = 'Replying to <strong>' + authorName + '</strong> <a href="#" onclick="cancelReply(); return false;">[Cancel]</a>';
}

function cancelReply() {
    document.getElementById('comment_parent').value = 0;
    let replyNote = document.getElementById('reply-note');
    if (replyNote) replyNote.remove();
}
</script>

<!-- Lazy load script -->
<script>
const lazyloadRunObserver = () => {
    const lazyloadBackgrounds = document.querySelectorAll(`.e-con.e-parent:not(.e-lazyloaded)`);
    const lazyloadBackgroundObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                let lazyloadBackground = entry.target;
                if (lazyloadBackground) {
                    lazyloadBackground.classList.add("e-lazyloaded");
                }
                lazyloadBackgroundObserver.unobserve(entry.target);
            }
        });
    }, { rootMargin: "200px 0px 200px 0px" });
    lazyloadBackgrounds.forEach((lazyloadBackground) => {
        lazyloadBackgroundObserver.observe(lazyloadBackground);
    });
};
const events = ["DOMContentLoaded", "elementor/lazyload/observe"];
events.forEach((event) => {
    document.addEventListener(event, lazyloadRunObserver);
});
</script>

<!-- Additional plugin scripts (keep as original) -->
<link rel='stylesheet' id='elementor-frontend-css' href='../wp-content/plugins/elementor/assets/css/frontend.min.css?ver=3.35.3' media='all' />
<link rel='stylesheet' id='elementor-post-225-css' href='../wp-content/uploads/elementor/css/post-225.css?ver=1770715449' media='all' />
<link rel='stylesheet' id='elementor-post-1688-css' href='../wp-content/uploads/elementor/css/post-1688.css?ver=1770715449' media='all' />
<link rel='stylesheet' id='widget-image-css' href='../wp-content/plugins/elementor/assets/css/widget-image.min.css?ver=3.35.3' media='all' />
<link rel='stylesheet' id='widget-heading-css' href='../wp-content/plugins/elementor/assets/css/widget-heading.min.css?ver=3.35.3' media='all' />
<link rel='stylesheet' id='elementor-post-7-css' href='../wp-content/uploads/elementor/css/post-7.css?ver=1770715450' media='all' />
<link rel='stylesheet' id='elementor-icons-ekiticons-css' href='../wp-content/plugins/elementskit-lite/modules/elementskit-icon-pack/assets/css/ekiticons.css?ver=3.7.9' media='all' />
<link rel='stylesheet' id='elementor-gf-local-manrope-css' href='../wp-content/uploads/elementor/google-fonts/css/manrope.css?ver=1744107304' media='all' />
<link rel='stylesheet' id='elementor-gf-local-dmsans-css' href='../wp-content/uploads/elementor/google-fonts/css/dmsans.css?ver=1744107305' media='all' />

<script src="../wp-includes/js/dist/hooks.min.js?ver=dd5603f07f9220ed27f1" id="wp-hooks-js"></script>
<script src="../wp-includes/js/dist/i18n.min.js?ver=c26c3dc7bed366793375" id="wp-i18n-js"></script>
<script id="wp-i18n-js-after">wp.i18n.setLocaleData({ "text direction\u0004ltr": ["ltr"] });</script>
<script src="../wp-content/plugins/contact-form-7/includes/swv/js/index.js?ver=6.1.5" id="swv-js"></script>
<script id="contact-form-7-js-before">var wpcf7 = { api: { root: "https:\/\/demo.awaikenthemes.com\/tpv\/wp-json\/", namespace: "contact-form-7\/v1" }, cached: 1 };</script>
<script src="../wp-content/plugins/contact-form-7/includes/js/index.js?ver=6.1.5" id="contact-form-7-js"></script>
<script src="../wp-content/themes/tpv/assets/js/SmoothScroll.js?ver=1.0.8" id="SmoothScroll-js"></script>
<script src="../wp-content/themes/tpv/assets/js/gsap.min.js?ver=1.0.8" id="gsap-js"></script>
<script src="../wp-content/themes/tpv/assets/js/magiccursor.js?ver=1.0.8" id="magiccursor-js"></script>
<script src="../wp-content/themes/tpv/assets/js/SplitText.js?ver=1.0.8" id="SplitText-js"></script>
<script src="../wp-content/themes/tpv/assets/js/ScrollTrigger.min.js?ver=1.0.8" id="ScrollTrigger-js"></script>
<script src="../wp-content/themes/tpv/assets/js/function.js?ver=1.0.8" id="theme-js-js"></script>
<script src="../wp-includes/js/comment-reply.min.js?ver=6.9.1" id="comment-reply-js" async="" data-wp-strategy="async"></script>
<script src="../wp-content/plugins/elementskit-lite/libs/framework/assets/js/frontend-script.js?ver=3.7.9" id="elementskit-framework-js-frontend-js"></script>
<script id="elementskit-framework-js-frontend-js-after">var elementskit = { resturl: "https://tpvconstruction.com.ng/wp-json/elementskit/v1/" };</script>
<script src="../wp-content/plugins/elementskit-lite/widgets/init/assets/js/widget-scripts.js?ver=3.7.9" id="ekit-widget-scripts-js"></script>
<script src="../wp-content/plugins/elementor/assets/js/webpack.runtime.min.js?ver=3.35.3" id="elementor-webpack-runtime-js"></script>
<script src="../wp-content/plugins/elementor/assets/js/frontend-modules.min.js?ver=3.35.3" id="elementor-frontend-modules-js"></script>
<script src="../wp-includes/js/jquery/ui/core.min.js?ver=1.13.3" id="jquery-ui-core-js"></script>
<script id="elementor-frontend-js-before">
var elementorFrontendConfig = { /* ... same as before ... */ };
</script>
<script src="../wp-content/plugins/elementor/assets/js/frontend.min.js?ver=3.35.3" id="elementor-frontend-js"></script>
<script src="../wp-content/plugins/elementskit-lite/widgets/init/assets/js/animate-circle.min.js?ver=3.7.9" id="animate-circle-js"></script>
<script id="elementskit-elementor-js-extra">var ekit_config = { ajaxurl: "https://tpvconstruction.com.ng/wp-admin/admin-ajax.php", nonce: "fcf6af66da" };</script>
<script src="../wp-content/plugins/elementskit-lite/widgets/init/assets/js/elementor.js?ver=3.7.9" id="elementskit-elementor-js"></script>
<script src="../wp-content/plugins/elementskit/widgets/init/assets/js/elementor.js?ver=4.2.1" id="elementskit-elementor-pro-js"></script>
<script src="assets/js/theme-panel.js"></script>
</body>
</html>
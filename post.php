<?php
require_once 'config/config.php';
require_once 'classes/Blog.php';
require_once 'classes/Functions.php';

$blog = new Blog();
$functions = Functions::getInstance();
$db = Database::getInstance();

$slug = trim((string) ($_GET['slug'] ?? ''));
if ($slug === '') {
    header('HTTP/1.0 404 Not Found');
    include '404.php';
    exit;
}

$post = $blog->getPostBySlug($slug);
if (!$post) {
    header('HTTP/1.0 404 Not Found');
    include '404.php';
    exit;
}

$blog->incrementViewCount((int) $post['id']);

$primaryCategoryId = !empty($post['categories'][0]['id']) ? (int) $post['categories'][0]['id'] : null;
$relatedPosts = $blog->getRelatedPosts((int) $post['id'], $primaryCategoryId, 3);

$authorName = 'TPV Editorial Team';
if (($post['author_type'] ?? '') === 'client' && !empty($post['client_author'])) {
    $authorName = (string) $post['client_author'];
} elseif (!empty($post['employee_author'])) {
    $authorName = (string) $post['employee_author'];
}

$publishedDate = date('F j, Y', strtotime((string) ($post['published_at'] ?? $post['created_at'])));
$commentMessage = '';
$commentError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment']) && ($post['comment_status'] ?? 'open') === 'open') {
    $author = trim((string) ($_POST['author'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $url = trim((string) ($_POST['url'] ?? ''));
    $content = trim((string) ($_POST['comment'] ?? ''));
    $parentId = !empty($_POST['comment_parent']) ? (int) $_POST['comment_parent'] : 0;

    if ($author === '' || $email === '' || $content === '') {
        $commentError = 'Name, email, and comment are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $commentError = 'Please enter a valid email address.';
    } else {
        try {
            $db->query(
                "INSERT INTO blog_comments (
                    uuid, post_id, parent_id, author_name, author_email, content, status, ip_address, user_agent, created_at, updated_at
                ) VALUES (
                    :uuid, :post_id, :parent_id, :author_name, :author_email, :content, 'pending', :ip_address, :user_agent, NOW(), NOW()
                )",
                [
                    'uuid' => $functions->generateUUID(),
                    'post_id' => (int) $post['id'],
                    'parent_id' => $parentId ?: null,
                    'author_name' => $author,
                    'author_email' => $email,
                    'content' => $content,
                    'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
                    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
                ]
            );
            $commentMessage = 'Your comment has been submitted and is awaiting moderation.';
        } catch (Exception $e) {
            error_log('Comment insert error: ' . $e->getMessage());
            $commentError = 'We could not submit your comment right now. Please try again.';
        }
    }
}

$commentRows = $db->query(
    "SELECT c.*,
            CONCAT(e.first_name, ' ', e.last_name) AS employee_name,
            cl.company_name AS client_name,
            cl.contact_person AS client_contact
     FROM blog_comments c
     LEFT JOIN employees e ON c.author_employee_id = e.id
     LEFT JOIN clients cl ON c.author_client_id = cl.id
     WHERE c.post_id = :post_id AND c.status = 'approved' AND c.deleted_at IS NULL
     ORDER BY created_at ASC",
    ['post_id' => (int) $post['id']]
)->fetchAll();

$commentsByParent = [];
foreach ($commentRows as $comment) {
    $parentKey = $comment['parent_id'] ? (int) $comment['parent_id'] : 0;
    $commentsByParent[$parentKey][] = $comment;
}

function render_blog_comments(array $tree, int $parentId = 0): string
{
    if (!isset($tree[$parentId])) {
        return '';
    }

    $html = '<div class="blog-comment-thread">';
    foreach ($tree[$parentId] as $comment) {
        $authorName = trim((string) ($comment['author_name'] ?? ''));
        if (!empty($comment['employee_name'])) {
            $authorName = trim((string) $comment['employee_name']);
        } elseif (!empty($comment['client_name'])) {
            $authorName = trim((string) $comment['client_name']);
        } elseif (!empty($comment['client_contact'])) {
            $authorName = trim((string) $comment['client_contact']);
        }
        if ($authorName === '') {
            $authorName = 'TPV Construction Team';
        }

        $author = htmlspecialchars($authorName);
        $date = date('M d, Y \a\t g:i a', strtotime((string) $comment['created_at']));
        $content = nl2br(htmlspecialchars((string) ($comment['content'] ?? '')));
        $replyAuthor = addslashes($authorName);

        $html .= '<article class="blog-comment">';
        $html .= '<div class="blog-comment__head">';
        $html .= '<strong>' . $author . '</strong>';
        $html .= '<span>' . htmlspecialchars($date) . '</span>';
        $html .= '</div>';
        $html .= '<div class="blog-comment__body">' . $content . '</div>';
        $html .= '<a class="blog-comment__reply" href="#" onclick="showReplyForm(' . (int) $comment['id'] . ', \'' . $replyAuthor . '\'); return false;">Reply</a>';
        $html .= render_blog_comments($tree, (int) $comment['id']);
        $html .= '</article>';
    }
    $html .= '</div>';

    return $html;
}

$commentsHtml = render_blog_comments($commentsByParent);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars((string) $post['title']); ?> | TPV Construction and Services LTD</title>
    <meta name="description" content="<?php echo htmlspecialchars((string) ($post['excerpt'] ?: mb_strimwidth(strip_tags((string) $post['content']), 0, 155, '...'))); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --tpv-navy: #15233c;
            --tpv-copy: #5f708c;
            --tpv-line: #e6ebf2;
            --tpv-accent: #d4a13e;
            --tpv-danger: #ef4444;
            --tpv-bg: #f7f9fc;
            --tpv-card: #ffffff;
            --tpv-shadow: 0 24px 60px rgba(16, 32, 58, 0.08);
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: "Manrope", sans-serif;
            color: #10203a;
            background: linear-gradient(180deg, #f8fafc 0%, #ffffff 36%, #f7f9fc 100%);
        }
        a { color: inherit; text-decoration: none; }
        .post-shell {
            width: min(1160px, calc(100% - 32px));
            margin: 0 auto;
        }
        .post-hero {
            padding: 34px 0 22px;
        }
        .post-hero__panel {
            background: linear-gradient(135deg, #ffffff 0%, #f5f8ff 100%);
            border: 1px solid #ebf0f7;
            border-radius: 34px;
            padding: 28px;
            box-shadow: var(--tpv-shadow);
        }
        .post-breadcrumbs {
            display: flex;
            flex-wrap: wrap;
            gap: 0.55rem;
            color: var(--tpv-copy);
            font-size: 0.84rem;
            margin-bottom: 1.1rem;
        }
        .post-categories {
            display: flex;
            flex-wrap: wrap;
            gap: 0.7rem;
            margin-bottom: 1rem;
        }
        .post-categories a,
        .post-categories span {
            display: inline-flex;
            padding: 0.52rem 0.8rem;
            border-radius: 999px;
            background: rgba(212, 161, 62, 0.14);
            color: #96620d;
            font-size: 0.78rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }
        .post-hero h1 {
            margin: 0;
            font-size: clamp(2.8rem, 4.6vw, 5rem);
            line-height: 0.92;
            letter-spacing: -0.06em;
            color: var(--tpv-navy);
            max-width: 14.5ch;
        }
        .post-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-top: 1.15rem;
            color: #6f809d;
            font-size: 0.88rem;
            font-weight: 700;
        }
        .post-layout {
            display: grid;
            grid-template-columns: minmax(0, 1.05fr) minmax(280px, 0.42fr);
            gap: 1.35rem;
            padding: 0 0 70px;
        }
        .post-main-card,
        .post-side-card {
            background: var(--tpv-card);
            border: 1px solid #e7edf5;
            border-radius: 32px;
            box-shadow: var(--tpv-shadow);
        }
        .post-main-card {
            overflow: hidden;
        }
        .post-hero-image {
            min-height: 360px;
            background: linear-gradient(135deg, #d9e4f4 0%, #b6c5da 100%);
        }
        .post-hero-image img {
            width: 100%;
            height: 100%;
            min-height: 360px;
            object-fit: cover;
            display: block;
        }
        .post-body {
            padding: 2rem;
        }
        .post-excerpt {
            margin: 0 0 1.5rem;
            padding: 1.2rem 1.35rem;
            border-left: 4px solid var(--tpv-accent);
            background: #fafbfd;
            color: #42546f;
            line-height: 1.85;
            border-radius: 0 18px 18px 0;
        }
        .post-prose {
            color: #324761;
            line-height: 1.9;
            font-size: 1rem;
        }
        .post-prose h2,
        .post-prose h3,
        .post-prose h4 {
            color: var(--tpv-navy);
            line-height: 1.2;
            margin-top: 2rem;
            margin-bottom: 0.8rem;
            letter-spacing: -0.03em;
        }
        .post-prose img {
            max-width: 100%;
            border-radius: 22px;
            height: auto;
        }
        .post-prose ul,
        .post-prose ol {
            padding-left: 1.2rem;
        }
        .post-sidebar {
            display: grid;
            gap: 1rem;
            align-content: start;
        }
        .post-side-card {
            padding: 1.3rem;
        }
        .post-side-card h3 {
            margin: 0 0 0.85rem;
            font-size: 1.1rem;
            color: var(--tpv-navy);
        }
        .post-side-card p,
        .post-side-card li {
            color: var(--tpv-copy);
            line-height: 1.8;
        }
        .post-side-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: grid;
            gap: 0.85rem;
        }
        .post-side-list li strong {
            color: #16253f;
        }
        .post-tag-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.55rem;
        }
        .post-tag-list span {
            display: inline-flex;
            padding: 0.52rem 0.8rem;
            border-radius: 999px;
            background: #f3f6fb;
            color: #53667f;
            font-size: 0.84rem;
            font-weight: 700;
        }
        .post-related {
            display: grid;
            gap: 0.9rem;
        }
        .post-related a {
            display: grid;
            gap: 0.55rem;
            padding: 1rem;
            border-radius: 22px;
            background: #fbfcfe;
            border: 1px solid #edf2f7;
        }
        .post-related strong {
            color: var(--tpv-navy);
            line-height: 1.3;
        }
        .post-comments-section {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid var(--tpv-line);
        }
        .post-comments-section h3 {
            margin: 0 0 1rem;
            color: var(--tpv-navy);
            font-size: 1.45rem;
        }
        .blog-comment-thread {
            display: grid;
            gap: 1rem;
            margin: 1.1rem 0 0;
        }
        .blog-comment {
            padding: 1.1rem 1.2rem;
            border: 1px solid var(--tpv-line);
            border-radius: 22px;
            background: #fbfcff;
            margin-left: 0;
        }
        .blog-comment .blog-comment-thread {
            margin-left: 1rem;
            margin-top: 1rem;
        }
        .blog-comment__head {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 0.6rem;
            margin-bottom: 0.6rem;
        }
        .blog-comment__head strong {
            color: var(--tpv-navy);
        }
        .blog-comment__head span,
        .blog-comment__body {
            color: var(--tpv-copy);
        }
        .blog-comment__body {
            line-height: 1.8;
        }
        .blog-comment__reply {
            display: inline-flex;
            margin-top: 0.85rem;
            color: var(--tpv-danger);
            font-weight: 800;
        }
        .post-comment-form {
            display: grid;
            gap: 1rem;
            margin-top: 1.1rem;
        }
        .post-comment-form input,
        .post-comment-form textarea {
            width: 100%;
            border: 1px solid #dbe2ec;
            border-radius: 16px;
            padding: 0.95rem 1rem;
            font: inherit;
        }
        .post-comment-form textarea {
            min-height: 160px;
            resize: vertical;
        }
        .post-comment-form button {
            width: fit-content;
            border: 0;
            border-radius: 16px;
            padding: 0.95rem 1.35rem;
            background: linear-gradient(135deg, #ef4444 0%, #cf2f35 100%);
            color: #fff;
            font: inherit;
            font-weight: 800;
            cursor: pointer;
        }
        .post-alert {
            padding: 0.95rem 1rem;
            border-radius: 16px;
            margin-bottom: 1rem;
            font-weight: 700;
        }
        .post-alert--success {
            background: #e8f8ef;
            color: #1b6b44;
        }
        .post-alert--error {
            background: #fff1f2;
            color: #b42318;
        }
        #reply-note {
            display: none;
            padding: 0.95rem 1rem;
            border-radius: 16px;
            background: #f7faff;
            color: #42546f;
            margin-bottom: 1rem;
        }
        @media (max-width: 980px) {
            .post-layout {
                grid-template-columns: 1fr;
            }
        }
        @media (max-width: 720px) {
            .post-shell {
                width: min(100% - 20px, 1160px);
            }
            .post-hero__panel,
            .post-body,
            .post-side-card {
                padding-left: 18px;
                padding-right: 18px;
            }
            .post-hero-image,
            .post-hero-image img {
                min-height: 230px;
            }
        }
    </style>
</head>
<body>
<?php include 'includes/quote_header.php'; ?>

<main>
    <section class="post-hero">
        <div class="post-shell">
            <div class="post-hero__panel">
                <div class="post-breadcrumbs">
                    <a href="./">Home</a>
                    <span>/</span>
                    <a href="./blog/">Blog</a>
                    <span>/</span>
                    <span><?php echo htmlspecialchars((string) $post['title']); ?></span>
                </div>

                <?php if (!empty($post['categories'])): ?>
                    <div class="post-categories">
                        <?php foreach ($post['categories'] as $category): ?>
                            <a href="blog/?category=<?php echo urlencode((string) $category['slug']); ?>">
                                <?php echo htmlspecialchars((string) $category['name']); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <h1><?php echo htmlspecialchars((string) $post['title']); ?></h1>

                <div class="post-meta">
                    <span><?php echo htmlspecialchars($publishedDate); ?></span>
                    <span><?php echo htmlspecialchars($authorName); ?></span>
                    <span><?php echo number_format((int) ($post['view_count'] ?? 0) + 1); ?> views</span>
                </div>
            </div>
        </div>
    </section>

    <section class="post-shell" style="padding-bottom:72px;">
        <div class="post-layout">
            <article class="post-main-card">
                <?php if (!empty($post['featured_image_path'])): ?>
                    <div class="post-hero-image">
                        <img src="<?php echo htmlspecialchars(tpv_asset_url((string) $post['featured_image_path'])); ?>" alt="<?php echo htmlspecialchars((string) $post['title']); ?>">
                    </div>
                <?php endif; ?>
                <div class="post-body">
                    <?php if (!empty($post['excerpt'])): ?>
                        <p class="post-excerpt"><?php echo htmlspecialchars((string) $post['excerpt']); ?></p>
                    <?php endif; ?>

                    <div class="post-prose">
                        <?php echo (string) $post['content']; ?>
                    </div>

                    <section class="post-comments-section">
                        <h3>Conversation</h3>

                        <?php if ($commentMessage !== ''): ?>
                            <div class="post-alert post-alert--success"><?php echo htmlspecialchars($commentMessage); ?></div>
                        <?php endif; ?>
                        <?php if ($commentError !== ''): ?>
                            <div class="post-alert post-alert--error"><?php echo htmlspecialchars($commentError); ?></div>
                        <?php endif; ?>

                        <?php if ($commentsHtml !== ''): ?>
                            <?php echo $commentsHtml; ?>
                        <?php else: ?>
                            <p class="text-muted" style="color:#6b7a90;">No approved comments yet. Start the conversation below.</p>
                        <?php endif; ?>

                        <?php if (($post['comment_status'] ?? 'open') === 'open'): ?>
                            <form method="post" class="post-comment-form" id="commentForm">
                                <input type="hidden" name="comment_parent" id="comment_parent" value="0">
                                <div id="reply-note"></div>
                                <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:1rem;">
                                    <input type="text" name="author" placeholder="Your name" required>
                                    <input type="email" name="email" placeholder="Your email" required>
                                </div>
                                <input type="url" name="url" placeholder="Website (optional)">
                                <textarea name="comment" placeholder="Share your thoughts..." required></textarea>
                                <button type="submit" name="submit_comment">Submit Comment</button>
                            </form>
                        <?php else: ?>
                            <p class="text-muted" style="color:#6b7a90;">Comments are closed for this article.</p>
                        <?php endif; ?>
                    </section>
                </div>
            </article>

            <aside class="post-sidebar">
                <div class="post-side-card">
                    <h3>Article Details</h3>
                    <ul class="post-side-list">
                        <li><strong>Published:</strong> <?php echo htmlspecialchars($publishedDate); ?></li>
                        <li><strong>Author:</strong> <?php echo htmlspecialchars($authorName); ?></li>
                        <li><strong>Comments:</strong> <?php echo ($post['comment_status'] ?? 'open') === 'open' ? 'Open' : 'Closed'; ?></li>
                    </ul>
                </div>

                <?php if (!empty($post['tags'])): ?>
                    <div class="post-side-card">
                        <h3>Tags</h3>
                        <div class="post-tag-list">
                            <?php foreach ($post['tags'] as $tag): ?>
                                <span><?php echo htmlspecialchars((string) $tag['name']); ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($relatedPosts)): ?>
                    <div class="post-side-card">
                        <h3>Related Reads</h3>
                        <div class="post-related">
                            <?php foreach ($relatedPosts as $relatedPost): ?>
                                <a href="post.php?slug=<?php echo urlencode((string) $relatedPost['slug']); ?>">
                                    <strong><?php echo htmlspecialchars((string) $relatedPost['title']); ?></strong>
                                    <span style="color:#6b7a90;"><?php echo htmlspecialchars(date('M d, Y', strtotime((string) ($relatedPost['published_at'] ?? $relatedPost['created_at'])))); ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="post-side-card">
                    <h3>Need a construction partner?</h3>
                    <p>Let’s help you scope your project, refine the plan, and move toward delivery with confidence.</p>
                    <a href="quote/" style="display:inline-flex; margin-top:0.5rem; color:#ef4444; font-weight:800;">Request a quote</a>
                </div>
            </aside>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>

<script>
    function showReplyForm(commentId, authorName) {
        const replyField = document.getElementById('comment_parent');
        const replyNote = document.getElementById('reply-note');
        replyField.value = commentId;
        replyNote.style.display = 'block';
        replyNote.innerHTML = 'Replying to <strong>' + authorName + '</strong>. <a href="#" onclick="cancelReply(); return false;">Cancel</a>';
        window.scrollTo({ top: document.getElementById('commentForm').offsetTop - 80, behavior: 'smooth' });
    }

    function cancelReply() {
        document.getElementById('comment_parent').value = '0';
        document.getElementById('reply-note').style.display = 'none';
        document.getElementById('reply-note').innerHTML = '';
    }
</script>
</body>
</html>

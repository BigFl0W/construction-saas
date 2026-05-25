<?php
require_once '../config/config.php';
require_once '../classes/Blog.php';
require_once '../classes/Settings.php';

$blog = new Blog();
$settings = new Settings();

$search = trim((string) ($_GET['q'] ?? ''));
$categorySlug = trim((string) ($_GET['category'] ?? ''));
$categories = $blog->getCategorySummaries();
$selectedCategoryId = null;

if ($categorySlug !== '') {
    foreach ($categories as $category) {
        if (($category['slug'] ?? '') === $categorySlug) {
            $selectedCategoryId = (int) $category['id'];
            break;
        }
    }
}

$filters = [];
if ($search !== '') {
    $filters['search'] = $search;
}
if ($selectedCategoryId !== null) {
    $filters['category_id'] = $selectedCategoryId;
}

$posts = $blog->getPublishedPosts($filters, 24, 0);

$metaDescription = (string) $settings->get(
    'blog_page_meta_description',
    'Read practical construction insights, project stories, design ideas, and company updates from TPV Construction and Services LTD.'
);
$heroBadge = (string) $settings->get('blog_page_badge', 'Insights & Project Stories');
$heroTitle = (string) $settings->get('blog_page_title', 'The TPV Journal');
$heroDescription = (string) $settings->get(
    'blog_page_description',
    'Explore practical construction guidance, project milestones, engineering perspectives, and company updates from our team.'
);
$featuredLabel = (string) $settings->get('blog_page_featured_label', 'Featured Story');
$latestHeading = (string) $settings->get('blog_page_latest_heading', 'Latest Articles');
$latestDescription = (string) $settings->get(
    'blog_page_latest_description',
    'Fresh updates, practical advice, and behind-the-scenes highlights from our construction work.'
);
$emptyTitle = (string) $settings->get('blog_page_empty_title', 'Fresh stories are on the way.');
$emptyBody = (string) $settings->get(
    'blog_page_empty_body',
    'We are preparing new project updates and practical construction articles. Please check back soon.'
);
$ctaTitle = (string) $settings->get('blog_page_cta_title', 'Need a partner for your next build?');
$ctaBody = (string) $settings->get(
    'blog_page_cta_body',
    'From design to execution, our team can help you plan, deliver, and maintain exceptional projects.'
);
$ctaButtonText = (string) $settings->get('blog_page_cta_button_text', 'Request a Quote');
$ctaButtonLink = (string) $settings->get('blog_page_cta_button_link', '../quote/');

function blog_listing_excerpt(array $post): string
{
    $excerpt = trim((string) ($post['excerpt'] ?? ''));
    if ($excerpt !== '') {
        return $excerpt;
    }

    $plain = trim(preg_replace('/\s+/', ' ', strip_tags((string) ($post['content'] ?? ''))));
    if ($plain === '') {
        return 'Read the full article for practical project insights and construction guidance.';
    }

    return mb_strimwidth($plain, 0, 190, '...');
}

function blog_listing_author(array $post): string
{
    if (($post['author_type'] ?? '') === 'client' && !empty($post['client_author'])) {
        return (string) $post['client_author'];
    }
    if (!empty($post['employee_author'])) {
        return (string) $post['employee_author'];
    }
    return 'TPV Editorial Team';
}

function blog_listing_read_time(array $post): string
{
    $words = str_word_count(strip_tags((string) ($post['content'] ?? '')));
    $minutes = max(1, (int) ceil($words / 220));
    return $minutes . ' min read';
}

function blog_listing_image(array $post): string
{
    $path = trim((string) ($post['featured_image_path'] ?? ''));
    return $path !== '' ? tpv_asset_url($path) : '';
}

$publishedCategories = array_values(array_filter($categories, static function ($category) {
    return (int) ($category['post_count'] ?? 0) > 0;
}));
$featuredPost = $posts[0] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog | TPV Construction and Services LTD</title>
    <meta name="description" content="<?php echo htmlspecialchars($metaDescription); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --blog-navy: #13233e;
            --blog-navy-soft: #213758;
            --blog-copy: #5f708c;
            --blog-line: #e4eaf2;
            --blog-surface: #f5f7fb;
            --blog-card: #ffffff;
            --blog-accent: #d4a13e;
            --blog-danger: #ef4444;
            --blog-shadow: 0 24px 60px rgba(16, 32, 58, 0.08);
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: "Manrope", sans-serif;
            color: var(--blog-navy);
            background:
                radial-gradient(circle at top right, rgba(212, 161, 62, 0.12), transparent 18%),
                linear-gradient(180deg, #f7f9fc 0%, #ffffff 36%, #f6f8fb 100%);
        }
        a { color: inherit; text-decoration: none; }
        .blog-shell {
            width: min(1180px, calc(100% - 32px));
            margin: 0 auto;
        }
        .blog-page {
            padding: 36px 0 76px;
        }
        .blog-hero {
            display: grid;
            grid-template-columns: minmax(0, 1.18fr) minmax(320px, 0.82fr);
            gap: 1.1rem;
            margin-bottom: 1.2rem;
            align-items: start;
        }
        .blog-hero__intro,
        .blog-hero__feature,
        .blog-toolbar,
        .blog-empty,
        .blog-inline-cta {
            border: 1px solid var(--blog-line);
            background: rgba(255, 255, 255, 0.94);
            box-shadow: var(--blog-shadow);
        }
        .blog-hero__intro {
            border-radius: 34px;
            padding: 28px;
            display: grid;
            gap: 1rem;
            align-content: start;
            align-self: start;
            background:
                radial-gradient(circle at top right, rgba(212, 161, 62, 0.16), transparent 28%),
                linear-gradient(135deg, #ffffff 0%, #f7faff 100%);
        }
        .blog-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            width: fit-content;
            padding: 0.68rem 1rem;
            border-radius: 999px;
            background: rgba(19, 35, 62, 0.06);
            color: var(--blog-navy);
            font-size: 0.82rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }
        .blog-badge::before {
            content: "";
            width: 10px;
            height: 10px;
            border-radius: 999px;
            background: var(--blog-accent);
        }
        .blog-hero__intro h1 {
            margin: 0;
            font-size: clamp(3rem, 5vw, 5.4rem);
            line-height: 0.9;
            letter-spacing: -0.07em;
            max-width: 12.5ch;
        }
        .blog-hero__intro p {
            margin: 0;
            color: var(--blog-copy);
            font-size: 1rem;
            line-height: 1.85;
            max-width: 56ch;
        }
        .blog-hero__feature {
            border-radius: 34px;
            padding: 18px;
            display: grid;
            gap: 0.9rem;
            align-self: start;
        }
        .blog-feature-tag {
            display: inline-flex;
            width: fit-content;
            padding: 0.48rem 0.75rem;
            border-radius: 999px;
            background: rgba(239, 68, 68, 0.09);
            color: #d6323a;
            font-size: 0.76rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }
        .blog-feature-media {
            height: 230px;
            border-radius: 24px;
            overflow: hidden;
            background: linear-gradient(135deg, #d7e2f3 0%, #bac9dc 100%);
        }
        .blog-feature-media img,
        .blog-card-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .blog-placeholder {
            width: 100%;
            height: 100%;
            display: grid;
            place-items: center;
            background:
                radial-gradient(circle at top right, rgba(212, 161, 62, 0.22), transparent 28%),
                linear-gradient(135deg, #d9e4f3 0%, #bccadc 100%);
            color: rgba(19, 35, 62, 0.88);
            font-size: 1.05rem;
            font-weight: 800;
            letter-spacing: -0.03em;
        }
        .blog-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 0.85rem;
            color: #70819f;
            font-size: 0.82rem;
            font-weight: 700;
        }
        .blog-hero__feature h3 {
            margin: 0;
            font-size: 1.7rem;
            line-height: 1.08;
            letter-spacing: -0.05em;
        }
        .blog-hero__feature p {
            margin: 0;
            color: var(--blog-copy);
            line-height: 1.8;
            font-size: 0.95rem;
        }
        .blog-readmore {
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            width: fit-content;
            color: var(--blog-danger);
            font-weight: 800;
        }
        .blog-toolbar {
            border-radius: 30px;
            padding: 18px;
            margin-bottom: 1.35rem;
        }
        .blog-toolbar__head {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 1rem;
            margin-bottom: 0.95rem;
        }
        .blog-toolbar__head h2 {
            margin: 0;
            font-size: 1.55rem;
            letter-spacing: -0.04em;
        }
        .blog-toolbar__head p {
            margin: 0.28rem 0 0;
            color: var(--blog-copy);
            line-height: 1.75;
            max-width: 54ch;
        }
        .blog-search-form {
            display: flex;
            flex-wrap: wrap;
            gap: 0.8rem;
            margin-bottom: 0.95rem;
        }
        .blog-search-form input,
        .blog-search-form select {
            min-width: 0;
            flex: 1 1 220px;
            border: 1px solid #dce3ee;
            border-radius: 16px;
            padding: 0.92rem 1rem;
            font: inherit;
            color: var(--blog-navy);
            background: #fff;
        }
        .blog-search-form button {
            flex: 0 0 auto;
            min-width: 190px;
            border: 0;
            border-radius: 16px;
            padding: 0.92rem 1.25rem;
            background: linear-gradient(135deg, #ef4444 0%, #cf2f35 100%);
            color: #fff;
            font: inherit;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 18px 30px rgba(207, 47, 53, 0.2);
        }
        .blog-categories {
            display: flex;
            flex-wrap: wrap;
            gap: 0.72rem;
        }
        .blog-categories a {
            display: inline-flex;
            align-items: center;
            gap: 0.42rem;
            padding: 0.7rem 0.95rem;
            border-radius: 999px;
            background: #fff;
            border: 1px solid #e4ebf4;
            color: #4c5d79;
            font-size: 0.88rem;
            font-weight: 700;
        }
        .blog-categories a.active {
            background: var(--blog-navy);
            border-color: var(--blog-navy);
            color: #fff;
        }
        .blog-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1.15rem;
        }
        .blog-card {
            background: var(--blog-card);
            border: 1px solid #e6ecf5;
            border-radius: 30px;
            overflow: hidden;
            box-shadow: 0 18px 40px rgba(16, 32, 58, 0.06);
        }
        .blog-card--lead {
            grid-column: span 2;
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
        }
        .blog-card-media {
            height: 230px;
            background: linear-gradient(135deg, #d9e4f3 0%, #bac8dc 100%);
        }
        .blog-card--lead .blog-card-media {
            height: 100%;
        }
        .blog-card-body {
            padding: 1.4rem;
        }
        .blog-card--lead .blog-card-body {
            padding: 1.65rem;
        }
        .blog-card-kicker {
            display: inline-flex;
            padding: 0.45rem 0.72rem;
            border-radius: 999px;
            background: rgba(212, 161, 62, 0.14);
            color: #96620d;
            font-size: 0.74rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin: 0.1rem 0 0.7rem;
        }
        .blog-card h3 {
            margin: 0.55rem 0 0.65rem;
            font-size: 1.26rem;
            line-height: 1.18;
            letter-spacing: -0.04em;
        }
        .blog-card--lead h3 {
            font-size: 1.72rem;
            line-height: 1.08;
        }
        .blog-card p {
            margin: 0 0 1rem;
            color: var(--blog-copy);
            font-size: 0.95rem;
            line-height: 1.82;
        }
        .blog-inline-cta {
            margin-top: 1.45rem;
            border-radius: 30px;
            padding: 1.9rem;
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 1rem;
            align-items: center;
            background: linear-gradient(135deg, #15233c 0%, #23395f 100%);
            color: #fff;
        }
        .blog-inline-cta h3 {
            margin: 0 0 0.35rem;
            font-size: 1.55rem;
            letter-spacing: -0.04em;
        }
        .blog-inline-cta p {
            margin: 0;
            color: rgba(255,255,255,0.76);
            line-height: 1.8;
        }
        .blog-inline-cta a {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            padding: 0.95rem 1.35rem;
            border-radius: 16px;
            background: #fff;
            color: var(--blog-navy);
            font-weight: 800;
        }
        .blog-empty {
            border-radius: 30px;
            padding: 2.9rem 2rem;
            text-align: center;
        }
        .blog-empty h3 {
            margin: 0 0 0.7rem;
            font-size: 1.65rem;
            letter-spacing: -0.04em;
        }
        .blog-empty p {
            max-width: 50ch;
            margin: 0 auto;
            color: var(--blog-copy);
            line-height: 1.85;
        }
        @media (max-width: 1080px) {
            .blog-hero,
            .blog-inline-cta {
                grid-template-columns: 1fr;
            }
            .blog-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
            .blog-card--lead {
                grid-column: span 2;
            }
        }
        @media (max-width: 760px) {
            .blog-shell {
                width: min(100% - 20px, 1180px);
            }
            .blog-page {
                padding-top: 22px;
            }
            .blog-hero__intro,
            .blog-hero__feature,
            .blog-toolbar,
            .blog-empty,
            .blog-inline-cta {
                padding-left: 18px;
                padding-right: 18px;
            }
            .blog-search-form button {
                width: 100%;
            }
            .blog-grid {
                grid-template-columns: 1fr;
            }
            .blog-card--lead {
                grid-column: auto;
                grid-template-columns: 1fr;
            }
            .blog-card--lead .blog-card-media {
                height: 240px;
            }
            .blog-inline-cta {
                padding-top: 1.5rem;
                padding-bottom: 1.5rem;
            }
        }
    </style>
</head>
<body>
<?php include '../includes/quote_header.php'; ?>

<main class="blog-page">
    <div class="blog-shell">
        <section class="blog-hero">
            <div class="blog-hero__intro">
                <span class="blog-badge"><?php echo htmlspecialchars($heroBadge); ?></span>
                <h1><?php echo htmlspecialchars($heroTitle); ?></h1>
                <p><?php echo htmlspecialchars($heroDescription); ?></p>
            </div>

            <?php if ($featuredPost): ?>
                <article class="blog-hero__feature">
                    <span class="blog-feature-tag"><?php echo htmlspecialchars($featuredLabel); ?></span>
                    <a href="../post.php?slug=<?php echo urlencode((string) $featuredPost['slug']); ?>">
                        <div class="blog-feature-media">
                            <?php $featuredImage = blog_listing_image($featuredPost); ?>
                            <?php if ($featuredImage !== ''): ?>
                                <img src="<?php echo htmlspecialchars($featuredImage); ?>" alt="<?php echo htmlspecialchars((string) $featuredPost['title']); ?>">
                            <?php else: ?>
                                <div class="blog-placeholder">TPV Journal</div>
                            <?php endif; ?>
                        </div>
                    </a>
                    <div class="blog-meta">
                        <span><?php echo htmlspecialchars(date('M d, Y', strtotime((string) ($featuredPost['published_at'] ?? $featuredPost['created_at'])))); ?></span>
                        <span><?php echo htmlspecialchars(blog_listing_author($featuredPost)); ?></span>
                        <span><?php echo htmlspecialchars(blog_listing_read_time($featuredPost)); ?></span>
                    </div>
                    <h3>
                        <a href="../post.php?slug=<?php echo urlencode((string) $featuredPost['slug']); ?>">
                            <?php echo htmlspecialchars((string) $featuredPost['title']); ?>
                        </a>
                    </h3>
                    <p><?php echo htmlspecialchars(blog_listing_excerpt($featuredPost)); ?></p>
                    <a class="blog-readmore" href="../post.php?slug=<?php echo urlencode((string) $featuredPost['slug']); ?>">Read article</a>
                </article>
            <?php else: ?>
                <div class="blog-hero__feature">
                    <span class="blog-feature-tag">Company Update</span>
                    <h3><?php echo htmlspecialchars($ctaTitle); ?></h3>
                    <p><?php echo htmlspecialchars($ctaBody); ?></p>
                    <a class="blog-readmore" href="<?php echo htmlspecialchars($ctaButtonLink); ?>"><?php echo htmlspecialchars($ctaButtonText); ?></a>
                </div>
            <?php endif; ?>
        </section>

        <section class="blog-toolbar">
            <div class="blog-toolbar__head">
                <div>
                    <h2><?php echo htmlspecialchars($latestHeading); ?></h2>
                    <p><?php echo htmlspecialchars($latestDescription); ?></p>
                </div>
            </div>

            <form class="blog-search-form" method="get">
                <input type="text" name="q" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search articles">
                <select name="category">
                    <option value="">All categories</option>
                    <?php foreach ($publishedCategories as $category): ?>
                        <option value="<?php echo htmlspecialchars((string) $category['slug']); ?>" <?php echo $categorySlug === ($category['slug'] ?? '') ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars((string) $category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Browse Articles</button>
            </form>

            <div class="blog-categories">
                <a href="index.php" class="<?php echo $categorySlug === '' ? 'active' : ''; ?>">All</a>
                <?php foreach ($publishedCategories as $category): ?>
                    <a href="?category=<?php echo urlencode((string) $category['slug']); ?>" class="<?php echo $categorySlug === ($category['slug'] ?? '') ? 'active' : ''; ?>">
                        <?php echo htmlspecialchars((string) $category['name']); ?>
                        <span><?php echo (int) $category['post_count']; ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>

        <?php if (!empty($posts)): ?>
            <section class="blog-grid">
                <?php foreach ($posts as $index => $post): ?>
                    <article class="blog-card <?php echo $index === 0 ? 'blog-card--lead' : ''; ?>">
                        <a href="../post.php?slug=<?php echo urlencode((string) $post['slug']); ?>">
                            <div class="blog-card-media">
                                <?php $imageUrl = blog_listing_image($post); ?>
                                <?php if ($imageUrl !== ''): ?>
                                    <img src="<?php echo htmlspecialchars($imageUrl); ?>" alt="<?php echo htmlspecialchars((string) $post['title']); ?>">
                                <?php else: ?>
                                    <div class="blog-placeholder">TPV Journal</div>
                                <?php endif; ?>
                            </div>
                        </a>
                        <div class="blog-card-body">
                            <div class="blog-meta">
                                <span><?php echo htmlspecialchars(date('M d, Y', strtotime((string) ($post['published_at'] ?? $post['created_at'])))); ?></span>
                                <span><?php echo htmlspecialchars(blog_listing_author($post)); ?></span>
                                <span><?php echo htmlspecialchars(blog_listing_read_time($post)); ?></span>
                            </div>
                            <?php if ($index === 0): ?>
                                <span class="blog-card-kicker"><?php echo htmlspecialchars($featuredLabel); ?></span>
                            <?php endif; ?>
                            <h3>
                                <a href="../post.php?slug=<?php echo urlencode((string) $post['slug']); ?>">
                                    <?php echo htmlspecialchars((string) $post['title']); ?>
                                </a>
                            </h3>
                            <p><?php echo htmlspecialchars(blog_listing_excerpt($post)); ?></p>
                            <a class="blog-readmore" href="../post.php?slug=<?php echo urlencode((string) $post['slug']); ?>">Read article</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </section>

            <section class="blog-inline-cta">
                <div>
                    <h3><?php echo htmlspecialchars($ctaTitle); ?></h3>
                    <p><?php echo htmlspecialchars($ctaBody); ?></p>
                </div>
                <a href="<?php echo htmlspecialchars($ctaButtonLink); ?>"><?php echo htmlspecialchars($ctaButtonText); ?></a>
            </section>
        <?php else: ?>
            <section class="blog-empty">
                <h3><?php echo htmlspecialchars($emptyTitle); ?></h3>
                <p><?php echo htmlspecialchars($emptyBody); ?></p>
            </section>
        <?php endif; ?>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
</body>
</html>

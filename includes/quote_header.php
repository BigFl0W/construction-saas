<?php
$uri = $_SERVER['REQUEST_URI'] ?? '';
$isHome = preg_match('#/Archive/?$#', $uri) ? 'is-active' : '';
$isAbout = strpos($uri, '/about-us') !== false ? 'is-active' : '';
$isContact = strpos($uri, '/contact-us') !== false ? 'is-active' : '';
$isBlog = strpos($uri, '/blog') !== false ? 'is-active' : '';
$isServices = strpos($uri, '/services') !== false ? 'is-active' : '';
$isQuote = strpos($uri, '/quote') !== false ? 'is-active' : '';
?>
<style>
    .tpv-site-header {
        position: sticky;
        top: 0;
        z-index: 1000;
        background: rgba(255, 255, 255, 0.96);
        backdrop-filter: blur(14px);
        border-bottom: 1px solid rgba(15, 23, 42, 0.08);
        box-shadow: 0 12px 34px -28px rgba(15, 23, 42, 0.4);
    }

    .tpv-site-header__inner {
        max-width: 1220px;
        margin: 0 auto;
        padding: 16px 24px;
        display: grid;
        grid-template-columns: auto 1fr auto;
        align-items: center;
        gap: 24px;
    }

    .tpv-site-header__brand {
        display: inline-flex;
        align-items: center;
        text-decoration: none;
        min-width: 0;
    }

    .tpv-site-header__brand img {
        width: 136px;
        height: auto;
        display: block;
    }

    .tpv-site-header__nav {
        display: flex;
        justify-content: center;
    }

    .tpv-site-header__nav-list {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        list-style: none;
        margin: 0;
        padding: 0;
        flex-wrap: wrap;
    }

    .tpv-site-header__nav-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 10px 16px;
        border-radius: 999px;
        text-decoration: none;
        color: #1e293b;
        font-size: 15px;
        font-weight: 600;
        transition: background-color 0.2s ease, color 0.2s ease, transform 0.2s ease;
    }

    .tpv-site-header__nav-link:hover,
    .tpv-site-header__nav-link:focus-visible {
        background: rgba(239, 68, 68, 0.08);
        color: #dc2626;
        outline: none;
        transform: translateY(-1px);
    }

    .tpv-site-header__nav-link.is-active {
        background: rgba(239, 68, 68, 0.1);
        color: #dc2626;
    }

    .tpv-site-header__cta {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 180px;
        padding: 13px 22px;
        border-radius: 999px;
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: #ffffff;
        text-decoration: none;
        font-size: 14px;
        font-weight: 700;
        letter-spacing: 0.01em;
        box-shadow: 0 18px 30px -20px rgba(220, 38, 38, 0.75);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        white-space: nowrap;
    }

    .tpv-site-header__cta:hover,
    .tpv-site-header__cta:focus-visible {
        transform: translateY(-1px);
        box-shadow: 0 22px 34px -20px rgba(220, 38, 38, 0.9);
        outline: none;
    }

    .tpv-site-header__mobile-toggle {
        display: none;
    }

    .tpv-site-header__menu-button {
        display: none;
        width: 46px;
        height: 46px;
        border: 1px solid rgba(15, 23, 42, 0.1);
        border-radius: 14px;
        background: #ffffff;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 12px 20px -18px rgba(15, 23, 42, 0.5);
    }

    .tpv-site-header__menu-button span,
    .tpv-site-header__menu-button::before,
    .tpv-site-header__menu-button::after {
        content: "";
        display: block;
        width: 18px;
        height: 2px;
        border-radius: 999px;
        background: #0f172a;
        transition: transform 0.2s ease, opacity 0.2s ease;
    }

    .tpv-site-header__menu-button span {
        margin: 5px 0;
    }

    @media (max-width: 1080px) {
        .tpv-site-header__inner {
            grid-template-columns: auto auto;
            justify-content: space-between;
        }

        .tpv-site-header__menu-button {
            display: inline-flex;
        }

        .tpv-site-header__nav,
        .tpv-site-header__actions {
            grid-column: 1 / -1;
        }

        .tpv-site-header__nav,
        .tpv-site-header__actions {
            display: none;
        }

        .tpv-site-header__mobile-toggle:checked ~ .tpv-site-header__nav,
        .tpv-site-header__mobile-toggle:checked ~ .tpv-site-header__actions {
            display: block;
        }

        .tpv-site-header__mobile-toggle:checked + .tpv-site-header__menu-button::before {
            transform: translateY(7px) rotate(45deg);
        }

        .tpv-site-header__mobile-toggle:checked + .tpv-site-header__menu-button span {
            opacity: 0;
        }

        .tpv-site-header__mobile-toggle:checked + .tpv-site-header__menu-button::after {
            transform: translateY(-7px) rotate(-45deg);
        }

        .tpv-site-header__nav {
            width: 100%;
            padding-top: 12px;
        }

        .tpv-site-header__nav-list {
            flex-direction: column;
            align-items: stretch;
            gap: 6px;
        }

        .tpv-site-header__nav-link {
            justify-content: flex-start;
            border-radius: 16px;
            padding: 13px 16px;
            background: #f8fafc;
        }

        .tpv-site-header__actions {
            width: 100%;
            padding-top: 12px;
        }

        .tpv-site-header__cta {
            width: 100%;
            min-width: 0;
        }
    }

    @media (max-width: 680px) {
        .tpv-site-header__inner {
            padding: 14px 16px;
            gap: 16px;
        }

        .tpv-site-header__brand img {
            width: 108px;
        }
    }
</style>

<header class="tpv-site-header">
    <div class="tpv-site-header__inner">
        <a class="tpv-site-header__brand" href="../" aria-label="TPV Construction and Services LTD home">
            <img src="../wp-content/uploads/2024/06/logo.png" alt="TPV Construction and Services LTD">
        </a>

        <input class="tpv-site-header__mobile-toggle" type="checkbox" id="tpv-site-header-toggle">
        <label class="tpv-site-header__menu-button" for="tpv-site-header-toggle" aria-label="Toggle navigation">
            <span></span>
        </label>

        <nav class="tpv-site-header__nav" aria-label="Primary navigation">
            <ul class="tpv-site-header__nav-list">
                <li><a class="tpv-site-header__nav-link <?php echo $isHome; ?>" href="../">Home</a></li>
                <li><a class="tpv-site-header__nav-link <?php echo $isAbout; ?>" href="../about-us/">About Us</a></li>
                <li><a class="tpv-site-header__nav-link <?php echo $isContact; ?>" href="../contact-us/">Contact Us</a></li>
                <li><a class="tpv-site-header__nav-link <?php echo $isBlog; ?>" href="../blog/">Blog</a></li>
                <li><a class="tpv-site-header__nav-link <?php echo $isServices; ?>" href="../services/">Services</a></li>
            </ul>
        </nav>

        <div class="tpv-site-header__actions">
            <a class="tpv-site-header__cta <?php echo $isQuote; ?>" href="../quote/" aria-current="<?php echo $isQuote ? 'page' : 'false'; ?>">
                Get Free Quote
            </a>
        </div>
    </div>
</header>

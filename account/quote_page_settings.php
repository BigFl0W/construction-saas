<?php
require_once '../config/config.php';
require_once '../classes/Auth.php';
require_once '../classes/Settings.php';

$auth = new Auth();
$auth->requireAuth();
$currentUser = $auth->getUserData();
$settings = new Settings();

function qp_text(string $key, string $default): string {
    global $settings;
    return (string) $settings->get($key, $default);
}

$fields = [
    'quote_page_meta_description' => 'Request a free, no-obligation construction quote from TPV Construction and Services LTD. Tell us about your project and we\'ll respond within 24 hours.',
    'quote_page_response_time_long' => '24 hours',
    'quote_page_response_time_short' => '24h',
    'quote_page_hero_badge' => 'Free No-Obligation Estimate',
    'quote_page_hero_title_before' => 'Start Your',
    'quote_page_hero_title_emphasis' => 'Dream Project',
    'quote_page_hero_title_after' => 'With a Free Quote',
    'quote_page_hero_description' => 'Fill in the form and our expert team will prepare a tailored cost estimate for your construction project within 24 hours.',
    'quote_page_hero_stat_1_value' => '24h',
    'quote_page_hero_stat_1_label' => 'Response Time',
    'quote_page_hero_stat_2_value' => '500+',
    'quote_page_hero_stat_2_label' => 'Projects Delivered',
    'quote_page_hero_stat_3_value' => '100%',
    'quote_page_hero_stat_3_label' => 'Free & No Obligation',
    'quote_page_breadcrumb_label' => 'Get a Free Quote',
    'quote_page_form_title' => 'Tell Us About Your Project',
    'quote_page_form_intro' => 'The more detail you provide, the more accurate your estimate will be. Fields marked * are required.',
    'quote_page_submit_note' => 'Your information is secure and will never be shared. We respond within 24 hours.',
    'quote_page_success_title' => 'Quote Request Sent!',
    'quote_page_success_body' => 'Thank you! Our team has received your project details and will prepare a tailored estimate within 24 hours.',
    'quote_page_success_button_text' => 'Back to Home',
    'quote_page_success_button_link' => '../',
    'quote_page_why_heading' => 'Why Choose TPV?',
    'quote_page_why_items' => "Over 10 years of proven construction expertise across Nigeria\nTransparent pricing — no hidden fees or surprise charges\nCertified engineers, architects & project managers on every job\nOn-time delivery with stringent quality assurance\n12-month post-construction workmanship guarantee",
    'quote_page_steps_heading' => 'How It Works',
    'quote_page_steps' => "Submit Your Request|Fill in this form with your project details.\nExpert Review|Our team analyses your requirements within 24h.\nReceive Your Estimate|We send a detailed, itemised cost estimate.\nConsultation Call|Our PM calls to discuss and refine details.\nBreak Ground!|We mobilise and your project begins.",
    'quote_page_direct_heading' => 'Prefer to Talk Directly?',
    'quote_page_contacts' => "phone|09097128241|Abuja / Ogun offices\nphone|08069418816|Nasarawa office\nphone|08104830712|Lagos office\nemail|info@tpvconstruction.com.ng|Email us anytime",
    'quote_page_testimonial_quote' => '"TPV Construction and Services LTD delivered our three-storey commercial complex on time and within budget. The quality surpassed expectations — highly recommended!"',
    'quote_page_testimonial_name' => 'Alhaji Abubakar O.',
    'quote_page_testimonial_role' => 'Real Estate Developer, Abuja',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$auth->verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['toast_error'] = 'Invalid security token.';
    } else {
        foreach ($fields as $key => $default) {
            $value = isset($_POST['settings'][$key]) ? trim((string) $_POST['settings'][$key]) : $default;
            $settings->set($key, $value, 'quote_page');
        }
        $_SESSION['toast_success'] = 'Quote page content updated successfully.';
    }
    header('Location: quote_page_settings.php');
    exit;
}

$values = [];
foreach ($fields as $key => $default) {
    $values[$key] = qp_text($key, $default);
}

$pageActive = 'quote_page_settings';
$pageTitle = 'TPV Construction and Services LTD · Quote Page Settings';
require 'inc/admin_header.php';
?>

<div data-pages="parallax">
    <div class="container-fluid p-l-25 p-r-25 sm-p-l-0 sm-p-r-0">
        <div class="inner">
            <ol class="breadcrumb sm-p-b-5">
                <li class="breadcrumb-item"><a href="index.php">TPV Construction and Services LTD</a></li>
                <li class="breadcrumb-item active">Quote Page Settings</li>
            </ol>
        </div>
    </div>
</div>

<div class="container-fluid p-l-25 p-r-25 p-t-0 p-b-25">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 m-b-20">
        <div>
            <h1 class="m-b-5" style="font-size:1.55rem;">Quote Page Content</h1>
            <p class="text-muted m-0">Manage the public quote page separately from incoming quote requests.</p>
        </div>
        <a href="../quote/" class="btn btn-outline-secondary" target="_blank" rel="noopener">
            <i class="fas fa-arrow-up-right-from-square me-2"></i>Preview quote page
        </a>
    </div>

    <form method="POST" class="card">
        <div class="card-body p-4">
            <?php echo $auth->csrfField(); ?>

            <div class="row g-4">
                <div class="col-12"><h5 class="mb-0">Hero</h5><hr class="mt-2"></div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Meta Description</label>
                    <textarea name="settings[quote_page_meta_description]" class="form-control" rows="2"><?php echo htmlspecialchars($values['quote_page_meta_description']); ?></textarea>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Response Time Long</label>
                    <input type="text" name="settings[quote_page_response_time_long]" class="form-control" value="<?php echo htmlspecialchars($values['quote_page_response_time_long']); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Response Time Short</label>
                    <input type="text" name="settings[quote_page_response_time_short]" class="form-control" value="<?php echo htmlspecialchars($values['quote_page_response_time_short']); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Hero Badge</label>
                    <input type="text" name="settings[quote_page_hero_badge]" class="form-control" value="<?php echo htmlspecialchars($values['quote_page_hero_badge']); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Title Before Highlight</label>
                    <input type="text" name="settings[quote_page_hero_title_before]" class="form-control" value="<?php echo htmlspecialchars($values['quote_page_hero_title_before']); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Highlighted Title</label>
                    <input type="text" name="settings[quote_page_hero_title_emphasis]" class="form-control" value="<?php echo htmlspecialchars($values['quote_page_hero_title_emphasis']); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Title After Highlight</label>
                    <input type="text" name="settings[quote_page_hero_title_after]" class="form-control" value="<?php echo htmlspecialchars($values['quote_page_hero_title_after']); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Breadcrumb Label</label>
                    <input type="text" name="settings[quote_page_breadcrumb_label]" class="form-control" value="<?php echo htmlspecialchars($values['quote_page_breadcrumb_label']); ?>">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Hero Description</label>
                    <textarea name="settings[quote_page_hero_description]" class="form-control" rows="2"><?php echo htmlspecialchars($values['quote_page_hero_description']); ?></textarea>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Stat 1 Value</label>
                    <input type="text" name="settings[quote_page_hero_stat_1_value]" class="form-control" value="<?php echo htmlspecialchars($values['quote_page_hero_stat_1_value']); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Stat 1 Label</label>
                    <input type="text" name="settings[quote_page_hero_stat_1_label]" class="form-control" value="<?php echo htmlspecialchars($values['quote_page_hero_stat_1_label']); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Stat 2 Value</label>
                    <input type="text" name="settings[quote_page_hero_stat_2_value]" class="form-control" value="<?php echo htmlspecialchars($values['quote_page_hero_stat_2_value']); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Stat 2 Label</label>
                    <input type="text" name="settings[quote_page_hero_stat_2_label]" class="form-control" value="<?php echo htmlspecialchars($values['quote_page_hero_stat_2_label']); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Stat 3 Value</label>
                    <input type="text" name="settings[quote_page_hero_stat_3_value]" class="form-control" value="<?php echo htmlspecialchars($values['quote_page_hero_stat_3_value']); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Stat 3 Label</label>
                    <input type="text" name="settings[quote_page_hero_stat_3_label]" class="form-control" value="<?php echo htmlspecialchars($values['quote_page_hero_stat_3_label']); ?>">
                </div>

                <div class="col-12"><h5 class="mb-0 mt-2">Form Card</h5><hr class="mt-2"></div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Form Title</label>
                    <input type="text" name="settings[quote_page_form_title]" class="form-control" value="<?php echo htmlspecialchars($values['quote_page_form_title']); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Submit Note</label>
                    <input type="text" name="settings[quote_page_submit_note]" class="form-control" value="<?php echo htmlspecialchars($values['quote_page_submit_note']); ?>">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Form Intro</label>
                    <textarea name="settings[quote_page_form_intro]" class="form-control" rows="2"><?php echo htmlspecialchars($values['quote_page_form_intro']); ?></textarea>
                </div>

                <div class="col-12"><h5 class="mb-0 mt-2">Success State</h5><hr class="mt-2"></div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Success Title</label>
                    <input type="text" name="settings[quote_page_success_title]" class="form-control" value="<?php echo htmlspecialchars($values['quote_page_success_title']); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Success Button Text</label>
                    <input type="text" name="settings[quote_page_success_button_text]" class="form-control" value="<?php echo htmlspecialchars($values['quote_page_success_button_text']); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Success Button Link</label>
                    <input type="text" name="settings[quote_page_success_button_link]" class="form-control" value="<?php echo htmlspecialchars($values['quote_page_success_button_link']); ?>">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Success Body</label>
                    <textarea name="settings[quote_page_success_body]" class="form-control" rows="2"><?php echo htmlspecialchars($values['quote_page_success_body']); ?></textarea>
                </div>

                <div class="col-12"><h5 class="mb-0 mt-2">Sidebar</h5><hr class="mt-2"></div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Why Choose Heading</label>
                    <input type="text" name="settings[quote_page_why_heading]" class="form-control" value="<?php echo htmlspecialchars($values['quote_page_why_heading']); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">How It Works Heading</label>
                    <input type="text" name="settings[quote_page_steps_heading]" class="form-control" value="<?php echo htmlspecialchars($values['quote_page_steps_heading']); ?>">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Why Choose Items</label>
                    <textarea name="settings[quote_page_why_items]" class="form-control" rows="5"><?php echo htmlspecialchars($values['quote_page_why_items']); ?></textarea>
                    <div class="form-text">One item per line.</div>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">How It Works Steps</label>
                    <textarea name="settings[quote_page_steps]" class="form-control" rows="6"><?php echo htmlspecialchars($values['quote_page_steps']); ?></textarea>
                    <div class="form-text">One step per line in the format: `Title|Description`.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Direct Contact Heading</label>
                    <input type="text" name="settings[quote_page_direct_heading]" class="form-control" value="<?php echo htmlspecialchars($values['quote_page_direct_heading']); ?>">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Direct Contacts</label>
                    <textarea name="settings[quote_page_contacts]" class="form-control" rows="5"><?php echo htmlspecialchars($values['quote_page_contacts']); ?></textarea>
                    <div class="form-text">One contact per line in the format: `phone|09097128241|Abuja / Ogun offices` or `email|info@example.com|Email us anytime`.</div>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Testimonial Quote</label>
                    <textarea name="settings[quote_page_testimonial_quote]" class="form-control" rows="3"><?php echo htmlspecialchars($values['quote_page_testimonial_quote']); ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Testimonial Name</label>
                    <input type="text" name="settings[quote_page_testimonial_name]" class="form-control" value="<?php echo htmlspecialchars($values['quote_page_testimonial_name']); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Testimonial Role</label>
                    <input type="text" name="settings[quote_page_testimonial_role]" class="form-control" value="<?php echo htmlspecialchars($values['quote_page_testimonial_role']); ?>">
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Quote Page
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<?php require 'inc/admin_footer.php'; ?>

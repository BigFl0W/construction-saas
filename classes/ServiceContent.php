<?php
require_once dirname(__DIR__) . '/config/Database.php';
require_once dirname(__DIR__) . '/classes/Settings.php';

class ServiceContent {
    private Database $db;
    private Settings $settings;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->settings = new Settings();
        $this->ensureTable();
    }

    public static function getRegistry(): array {
        return [
            'building-construction' => [
                'slug' => 'building-construction',
                'prefix' => 'service_bc',
                'name' => 'Building Construction',
                'icon' => 'fas fa-hard-hat',
                'asset_dir' => 'services/building-construction',
            ],
            'architecture-design' => [
                'slug' => 'architecture-design',
                'prefix' => 'service_ad',
                'name' => 'Architecture Design',
                'icon' => 'fas fa-drafting-compass',
                'asset_dir' => 'services/architecture-design',
            ],
            'building-renovation' => [
                'slug' => 'building-renovation',
                'prefix' => 'service_br',
                'name' => 'Building Renovation',
                'icon' => 'fas fa-screwdriver-wrench',
                'asset_dir' => 'services/building-renovation',
            ],
            'interior-exterior' => [
                'slug' => 'interior-exterior',
                'prefix' => 'service_ie',
                'name' => 'Interior / Exterior',
                'icon' => 'fas fa-palette',
                'asset_dir' => 'services/interior-exterior',
            ],
            'project-management' => [
                'slug' => 'project-management',
                'prefix' => 'service_pm',
                'name' => 'Project Management',
                'icon' => 'fas fa-list-check',
                'asset_dir' => 'services/project-management',
            ],
            'steel-and-fabrication' => [
                'slug' => 'steel-and-fabrication',
                'prefix' => 'service_sf',
                'name' => 'Steel & Fabrication',
                'icon' => 'fas fa-industry',
                'asset_dir' => 'services/steel-and-fabrication',
            ],
        ];
    }

    public static function getEditableColumns(): array {
        return [
            'name', 'status', 'page_title', 'seo_description',
            'hero_eyebrow', 'hero_secondary_button_text', 'hero_secondary_button_link', 'hero_empty_note',
            'overview_eyebrow',
            'overview_title', 'overview_body',
            'highlight_2_title', 'highlight_2_body',
            'highlight_3_title', 'highlight_3_body',
            'content_body',
            'benefits_eyebrow', 'benefits_title',
            'feature_1', 'feature_2', 'feature_3', 'feature_4', 'feature_5',
            'sustainable_title', 'sustainable_body_1', 'sustainable_body_2',
            'process_eyebrow', 'process_title', 'process_body',
            'step_1_title', 'step_1_body',
            'step_2_title', 'step_2_body',
            'step_3_title', 'step_3_body',
            'gallery_eyebrow', 'gallery_title', 'gallery_empty_note',
            'cta_eyebrow', 'cta_title', 'cta_body', 'cta_button_text', 'cta_button_link',
            'support_title', 'support_body', 'phone_label', 'email_label',
            'contact_eyebrow', 'contact_title', 'office_eyebrow',
            'office_link_1_title', 'office_link_1_body',
            'office_link_2_title', 'office_link_2_body',
            'related_eyebrow', 'related_title',
            'gallery_1', 'gallery_2', 'gallery_3', 'gallery_4', 'gallery_5', 'gallery_6',
            'sustainable_image_1', 'sustainable_image_2',
            'cta_image', 'contact_image',
        ];
    }

    public function ensureTable(): void {
        $this->db->query(
            "CREATE TABLE IF NOT EXISTS services_content (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                slug VARCHAR(120) NOT NULL UNIQUE,
                name VARCHAR(255) NOT NULL,
                status ENUM('draft','published') NOT NULL DEFAULT 'published',
                page_title VARCHAR(255) NOT NULL,
                seo_description TEXT NULL,
                hero_eyebrow VARCHAR(255) NULL,
                hero_secondary_button_text VARCHAR(255) NULL,
                hero_secondary_button_link VARCHAR(255) NULL,
                hero_empty_note TEXT NULL,
                overview_eyebrow VARCHAR(255) NULL,
                overview_title VARCHAR(255) NOT NULL,
                overview_body TEXT NULL,
                highlight_2_title VARCHAR(255) NULL,
                highlight_2_body TEXT NULL,
                highlight_3_title VARCHAR(255) NULL,
                highlight_3_body TEXT NULL,
                content_body LONGTEXT NULL,
                benefits_eyebrow VARCHAR(255) NULL,
                benefits_title VARCHAR(255) NULL,
                feature_1 VARCHAR(255) NULL,
                feature_2 VARCHAR(255) NULL,
                feature_3 VARCHAR(255) NULL,
                feature_4 VARCHAR(255) NULL,
                feature_5 VARCHAR(255) NULL,
                sustainable_title VARCHAR(255) NULL,
                sustainable_body_1 TEXT NULL,
                sustainable_body_2 TEXT NULL,
                process_eyebrow VARCHAR(255) NULL,
                process_title VARCHAR(255) NULL,
                process_body TEXT NULL,
                step_1_title VARCHAR(255) NULL,
                step_1_body TEXT NULL,
                step_2_title VARCHAR(255) NULL,
                step_2_body TEXT NULL,
                step_3_title VARCHAR(255) NULL,
                step_3_body TEXT NULL,
                gallery_eyebrow VARCHAR(255) NULL,
                gallery_title VARCHAR(255) NULL,
                gallery_empty_note TEXT NULL,
                cta_eyebrow VARCHAR(255) NULL,
                cta_title TEXT NULL,
                cta_body TEXT NULL,
                cta_button_text VARCHAR(255) NULL,
                cta_button_link VARCHAR(255) NULL,
                support_title VARCHAR(255) NULL,
                support_body TEXT NULL,
                phone_label VARCHAR(255) NULL,
                email_label VARCHAR(255) NULL,
                contact_eyebrow VARCHAR(255) NULL,
                contact_title VARCHAR(255) NULL,
                office_eyebrow VARCHAR(255) NULL,
                office_link_1_title VARCHAR(255) NULL,
                office_link_1_body TEXT NULL,
                office_link_2_title VARCHAR(255) NULL,
                office_link_2_body TEXT NULL,
                related_eyebrow VARCHAR(255) NULL,
                related_title VARCHAR(255) NULL,
                gallery_1 VARCHAR(255) NULL,
                gallery_2 VARCHAR(255) NULL,
                gallery_3 VARCHAR(255) NULL,
                gallery_4 VARCHAR(255) NULL,
                gallery_5 VARCHAR(255) NULL,
                gallery_6 VARCHAR(255) NULL,
                sustainable_image_1 VARCHAR(255) NULL,
                sustainable_image_2 VARCHAR(255) NULL,
                cta_image VARCHAR(255) NULL,
                contact_image VARCHAR(255) NULL,
                created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_services_content_status (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );
        $this->ensureColumns([
            'hero_eyebrow' => "ALTER TABLE services_content ADD COLUMN hero_eyebrow VARCHAR(255) NULL AFTER seo_description",
            'hero_secondary_button_text' => "ALTER TABLE services_content ADD COLUMN hero_secondary_button_text VARCHAR(255) NULL AFTER hero_eyebrow",
            'hero_secondary_button_link' => "ALTER TABLE services_content ADD COLUMN hero_secondary_button_link VARCHAR(255) NULL AFTER hero_secondary_button_text",
            'hero_empty_note' => "ALTER TABLE services_content ADD COLUMN hero_empty_note TEXT NULL AFTER hero_secondary_button_link",
            'overview_eyebrow' => "ALTER TABLE services_content ADD COLUMN overview_eyebrow VARCHAR(255) NULL AFTER hero_empty_note",
            'benefits_eyebrow' => "ALTER TABLE services_content ADD COLUMN benefits_eyebrow VARCHAR(255) NULL AFTER content_body",
            'benefits_title' => "ALTER TABLE services_content ADD COLUMN benefits_title VARCHAR(255) NULL AFTER benefits_eyebrow",
            'gallery_eyebrow' => "ALTER TABLE services_content ADD COLUMN gallery_eyebrow VARCHAR(255) NULL AFTER step_3_body",
            'gallery_title' => "ALTER TABLE services_content ADD COLUMN gallery_title VARCHAR(255) NULL AFTER gallery_eyebrow",
            'gallery_empty_note' => "ALTER TABLE services_content ADD COLUMN gallery_empty_note TEXT NULL AFTER gallery_title",
            'cta_eyebrow' => "ALTER TABLE services_content ADD COLUMN cta_eyebrow VARCHAR(255) NULL AFTER gallery_empty_note",
            'office_eyebrow' => "ALTER TABLE services_content ADD COLUMN office_eyebrow VARCHAR(255) NULL AFTER contact_title",
            'office_link_1_title' => "ALTER TABLE services_content ADD COLUMN office_link_1_title VARCHAR(255) NULL AFTER office_eyebrow",
            'office_link_1_body' => "ALTER TABLE services_content ADD COLUMN office_link_1_body TEXT NULL AFTER office_link_1_title",
            'office_link_2_title' => "ALTER TABLE services_content ADD COLUMN office_link_2_title VARCHAR(255) NULL AFTER office_link_1_body",
            'office_link_2_body' => "ALTER TABLE services_content ADD COLUMN office_link_2_body TEXT NULL AFTER office_link_2_title",
            'related_eyebrow' => "ALTER TABLE services_content ADD COLUMN related_eyebrow VARCHAR(255) NULL AFTER office_link_2_body",
            'related_title' => "ALTER TABLE services_content ADD COLUMN related_title VARCHAR(255) NULL AFTER related_eyebrow",
        ]);
    }

    private function ensureColumns(array $columnMap): void {
        foreach ($columnMap as $column => $sql) {
            $exists = $this->db->query(
                "SELECT 1
                 FROM information_schema.columns
                 WHERE table_schema = DATABASE()
                   AND table_name = 'services_content'
                   AND column_name = :column
                 LIMIT 1",
                ['column' => $column]
            )->fetch();
            if (!$exists) {
                $this->db->query($sql);
            }
        }
    }

    public function getStoredBySlug(string $slug): ?array {
        $row = $this->db->query("SELECT * FROM services_content WHERE slug = :slug LIMIT 1", ['slug' => $slug])->fetch();
        return $row ?: null;
    }

    public function getResolvedBySlug(string $slug): ?array {
        $registry = self::getRegistry();
        if (!isset($registry[$slug])) {
            return null;
        }

        $defaults = $this->buildDefaultRecord($registry[$slug]);
        $stored = $this->getStoredBySlug($slug);

        if (!$stored) {
            return $defaults + ['is_custom' => false];
        }

        return array_merge($defaults, $stored, ['is_custom' => true]);
    }

    public function getAllResolved(): array {
        $items = [];
        foreach (self::getRegistry() as $slug => $config) {
            $items[$slug] = $this->getResolvedBySlug($slug);
        }
        return $items;
    }

    public function save(string $slug, array $payload): void {
        $registry = self::getRegistry();
        if (!isset($registry[$slug])) {
            throw new InvalidArgumentException('Unknown service slug.');
        }

        $record = $this->buildDefaultRecord($registry[$slug]);
        foreach (self::getEditableColumns() as $column) {
            if (array_key_exists($column, $payload)) {
                $record[$column] = $payload[$column];
            }
        }

        $record['slug'] = $slug;
        $record['name'] = trim((string) ($record['name'] ?? '')) ?: $registry[$slug]['name'];
        $record['page_title'] = trim((string) ($record['page_title'] ?? '')) ?: $record['name'];
        $record['status'] = in_array(($record['status'] ?? ''), ['draft', 'published'], true) ? $record['status'] : 'published';

        $columns = array_merge(['slug'], self::getEditableColumns());
        $insertPlaceholders = [];
        $assignments = [];
        $params = [];
        foreach ($columns as $column) {
            $insertPlaceholders[] = ':' . $column;
            $params[$column] = $record[$column] ?? null;
            if ($column !== 'slug') {
                $assignments[] = $column . ' = VALUES(' . $column . ')';
            }
        }

        $this->db->query(
            "INSERT INTO services_content (" . implode(', ', $columns) . ")
             VALUES (" . implode(', ', $insertPlaceholders) . ")
             ON DUPLICATE KEY UPDATE " . implode(', ', $assignments),
            $params
        );
    }

    private function buildDefaultRecord(array $config): array {
        $prefix = $config['prefix'];
        $name = $config['name'];

        return [
            'id' => null,
            'slug' => $config['slug'],
            'name' => $name,
            'status' => 'published',
            'page_title' => $this->settings->get($prefix . '_page_title', $name),
            'seo_description' => '',
            'hero_eyebrow' => 'Service Expertise',
            'hero_secondary_button_text' => 'View Projects',
            'hero_secondary_button_link' => 'projects/',
            'hero_empty_note' => 'Upload a lead service image from the Service Manager to feature it here.',
            'overview_eyebrow' => 'Overview',
            'overview_title' => $this->settings->get($prefix . '_overview_title', $name),
            'overview_body' => $this->settings->get($prefix . '_overview_body', 'Simple actions make a difference. It starts and ends with each employee striving to work safer every single day so they can return.'),
            'highlight_2_title' => $this->settings->get($prefix . '_highlight_2_title', 'Contractor Service'),
            'highlight_2_body' => $this->settings->get($prefix . '_highlight_2_body', 'Simple actions make a difference. It starts and ends with each employee striving to work safer every single day so they can return.'),
            'highlight_3_title' => $this->settings->get($prefix . '_highlight_3_title', 'Onsite Supervision'),
            'highlight_3_body' => $this->settings->get($prefix . '_highlight_3_body', 'Simple actions make a difference. It starts and ends with each employee striving to work safer every single day so they can return.'),
            'content_body' => $this->settings->get($prefix . '_content_body', "There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need t.variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable. If you are going"),
            'benefits_eyebrow' => 'Key Benefits',
            'benefits_title' => $name . ' at a glance',
            'feature_1' => $this->settings->get($prefix . '_feature_1', '100 Satisfaction Guarantee'),
            'feature_2' => $this->settings->get($prefix . '_feature_2', 'Export And Profession Enginers'),
            'feature_3' => $this->settings->get($prefix . '_feature_3', 'We Are Award Winning Company'),
            'feature_4' => $this->settings->get($prefix . '_feature_4', 'Full Satisfaction Guarantee'),
            'feature_5' => $this->settings->get($prefix . '_feature_5', 'Professional Qualified'),
            'sustainable_title' => $this->settings->get($prefix . '_sustainable_title', 'The future of sustainable building practices'),
            'sustainable_body_1' => $this->settings->get($prefix . '_sustainable_body_1', "There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable. If you are going to use a passage."),
            'sustainable_body_2' => $this->settings->get($prefix . '_sustainable_body_2', "of Lorem Ipsum, you need t.variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable. If you are going"),
            'process_eyebrow' => $this->settings->get($prefix . '_process_eyebrow', 'Better process'),
            'process_title' => $this->settings->get($prefix . '_process_title', 'The process of working with us'),
            'process_body' => $this->settings->get($prefix . '_process_body', 'We specialize in a wide range of construction services, including residential, commercial, and industrial projects. From initial design to final inspection, we work closely with our clients to understand their unique needs and vision.'),
            'step_1_title' => $this->settings->get($prefix . '_step_1_title', 'Leave A Request'),
            'step_1_body' => $this->settings->get($prefix . '_step_1_body', 'Simple actions make a difference. It starts and ends with each employee striving to work safer every single day so they can return.'),
            'step_2_title' => $this->settings->get($prefix . '_step_2_title', 'Cost Calculation'),
            'step_2_body' => $this->settings->get($prefix . '_step_2_body', 'Simple actions make a difference. It starts and ends with each employee striving to work safer every single day so they can return.'),
            'step_3_title' => $this->settings->get($prefix . '_step_3_title', 'Signing Of A Contract'),
            'step_3_body' => $this->settings->get($prefix . '_step_3_body', 'Simple actions make a difference. It starts and ends with each employee striving to work safer every single day so they can return.'),
            'gallery_eyebrow' => 'Project Gallery',
            'gallery_title' => 'Selected visuals from ' . $name,
            'gallery_empty_note' => 'Upload service gallery images from the Service Manager to populate this section.',
            'cta_eyebrow' => 'Work With Us',
            'cta_title' => $this->settings->get($prefix . '_cta_title', "Let's bulid something great together!"),
            'cta_body' => $this->settings->get($prefix . '_cta_body', "Don't wait any longer to bring your construction dreams to life. Partner with TPV Construction and Services LTD and experience unparalleled service and quality."),
            'cta_button_text' => $this->settings->get($prefix . '_cta_button_text', 'Get Free Quote'),
            'cta_button_link' => $this->settings->get($prefix . '_cta_button_link', 'contact-us/'),
            'support_title' => $this->settings->get($prefix . '_support_title', 'You Still Have A Question'),
            'support_body' => $this->settings->get($prefix . '_support_body', 'if you cannot find answer to your question our FAQ, you can alwas contact us. web will answer you shortly!'),
            'phone_label' => $this->settings->get($prefix . '_phone_label', 'Call Support Center 24/7'),
            'email_label' => $this->settings->get($prefix . '_email_label', 'Write To Us'),
            'contact_eyebrow' => $this->settings->get($prefix . '_contact_eyebrow', 'Contact us'),
            'contact_title' => $this->settings->get($prefix . '_contact_title', 'Get in touch with us'),
            'office_eyebrow' => 'Office Details',
            'office_link_1_title' => 'Visit the contact page',
            'office_link_1_body' => 'See office locations, send a message, or request a callback from our team.',
            'office_link_2_title' => 'Start a project brief',
            'office_link_2_body' => 'Share your scope and we will respond with the next practical steps.',
            'related_eyebrow' => 'More Services',
            'related_title' => 'Explore related capabilities',
            'gallery_1' => $this->settings->get($prefix . '_gallery_1', 'wp-content/uploads/2024/06/service-img-1.jpg'),
            'gallery_2' => $this->settings->get($prefix . '_gallery_2', 'wp-content/uploads/2024/06/service-img-2.jpg'),
            'gallery_3' => $this->settings->get($prefix . '_gallery_3', 'wp-content/uploads/2024/06/service-img-3.png'),
            'gallery_4' => $this->settings->get($prefix . '_gallery_4', 'wp-content/uploads/2024/06/service-img-4.png'),
            'gallery_5' => $this->settings->get($prefix . '_gallery_5', 'wp-content/uploads/2024/06/service-img-5.jpg'),
            'gallery_6' => $this->settings->get($prefix . '_gallery_6', 'wp-content/uploads/2024/06/service-img-6.jpg'),
            'sustainable_image_1' => $this->settings->get($prefix . '_sustainable_image_1', 'wp-content/uploads/2024/06/company-history-img.jpg'),
            'sustainable_image_2' => $this->settings->get($prefix . '_sustainable_image_2', 'wp-content/uploads/2024/06/service-suitabilities-img-2.jpg'),
            'cta_image' => $this->settings->get($prefix . '_cta_image', 'wp-content/uploads/2024/06/cta-box-img.png'),
            'contact_image' => $this->settings->get($prefix . '_contact_image', 'wp-content/uploads/2024/06/contact-info-img.png'),
        ];
    }
}

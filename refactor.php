<?php
/**
 * Refactor admin pages to use shared includes
 * Usage: /Applications/XAMPP/xamppfiles/bin/php refactor.php
 */
$accountDir = __DIR__ . '/account';
$pages = [
    'index.php'          => ['page' => 'dashboard', 'title' => 'TPV Construction and Services LTD · Operator Dashboard'],
    'projects.php'       => ['page' => 'projects', 'title' => 'TPV Construction and Services LTD · Projects'],
    'project_stages.php' => ['page' => 'project_stages', 'title' => 'TPV Construction and Services LTD · Project Stages'],
    'daily_reports.php'  => ['page' => 'daily_reports', 'title' => 'TPV Construction and Services LTD · Daily Reports'],
    'project_budget.php' => ['page' => 'project_budget', 'title' => 'TPV Construction and Services LTD · Project Budget'],
    'invoices.php'       => ['page' => 'invoices', 'title' => 'TPV Construction and Services LTD · Invoices'],
    'payments.php'       => ['page' => 'payments', 'title' => 'TPV Construction and Services LTD · Payments'],
    'expenses.php'       => ['page' => 'expenses', 'title' => 'TPV Construction and Services LTD · Expenses'],
    'purchase_orders.php'=> ['page' => 'purchase_orders', 'title' => 'TPV Construction and Services LTD · Purchase Orders'],
    'equipment.php'      => ['page' => 'equipment', 'title' => 'TPV Construction and Services LTD · Equipment'],
    'materials.php'      => ['page' => 'materials', 'title' => 'TPV Construction and Services LTD · Materials'],
    'suppliers.php'      => ['page' => 'suppliers', 'title' => 'TPV Construction and Services LTD · Suppliers'],
    'maintenance.php'    => ['page' => 'maintenance', 'title' => 'TPV Construction and Services LTD · Maintenance'],
    'employees.php'      => ['page' => 'employees', 'title' => 'TPV Construction and Services LTD · Employees'],
    'timesheets.php'     => ['page' => 'timesheets', 'title' => 'TPV Construction and Services LTD · Timesheets'],
    'clients.php'        => ['page' => 'clients', 'title' => 'TPV Construction and Services LTD · Clients'],
    'communications.php' => ['page' => 'communications', 'title' => 'TPV Construction and Services LTD · Communications'],
    'documents.php'      => ['page' => 'documents', 'title' => 'TPV Construction and Services LTD · Documents'],
    'settings.php'       => ['page' => 'settings', 'title' => 'TPV Construction and Services LTD · Settings'],
    'profile.php'        => ['page' => 'profile', 'title' => 'TPV Construction and Services LTD · Profile'],
    'activity.php'       => ['page' => 'activity', 'title' => 'TPV Construction and Services LTD · Activity Log'],
    'test_login.php'     => ['page' => 'test_login', 'title' => 'TPV Construction and Services LTD · System Test'],
];

$count = 0;
foreach ($pages as $filename => $info) {
    $filepath = $accountDir . '/' . $filename;
    if (!file_exists($filepath)) {
        echo "SKIP: $filename not found\n";
        continue;
    }
    
    $content = file_get_contents($filepath);
    $original = $content;
    
    // 1) Replace header block: from <!doctype html> through class="content sm-gutter">
    $pattern = '/\s*<!doctype\s+html[^>]*>.*?class="content\s+sm-gutter"\s*>/is';
    $replacement = "\n" . '$' . "pageActive = '{$info['page']}';" . "\n"
                 . '$' . "pageTitle = '{$info['title']}';" . "\n"
                 . "require 'inc/admin_header.php';" . "\n";
    
    $content = preg_replace($pattern, $replacement, $content, 1, $count_h);
    
    if ($count_h === 0) {
        echo "WARN: No header match in $filename\n";
    }
    
    // 2) Replace footer block: from footer container through </html>
    $patterns2 = [
        '/\s*<div\s+class="container-fluid\s+container-fixed-lg\s+footer".*?<\/html>\s*$/is',
        '/\s*<!--\s*Scripts\s*-->\s*<script.*?<\/html>\s*$/is',
        '/\s*<div\s+id="quickview".*?<\/html>\s*$/is',
    ];
    $replacement2 = "\n<?php require 'inc/admin_footer.php'; ?>\n";
    $count_f = 0;
    
    foreach ($patterns2 as $p2) {
        $content = preg_replace($p2, $replacement2, $content, 1, $count_f);
        if ($count_f > 0) break;
    }
    
    if ($count_f === 0) {
        echo "WARN: No footer match in $filename\n";
    }
    
    if ($content !== $original) {
        file_put_contents($filepath, $content);
        echo "OK: $filename (h=$count_h, f=$count_f)\n";
        $count++;
    } else {
        echo "SKIP: $filename unchanged\n";
    }
}

echo "\nProcessed $count files.\n";

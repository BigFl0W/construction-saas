<?php
$pages = [
    'clients.php' => ['modal_id' => 'clientModal', 'table' => 'clients', 'action_field' => 'action', 'title' => 'Client', 'fields' => ['company_name', 'contact_person', 'email', 'phone', 'client_type', 'status']],
    'employees.php' => ['modal_id' => 'employeeModal', 'table' => 'employees', 'action_field' => 'action', 'title' => 'Employee', 'fields' => ['first_name', 'last_name', 'email', 'phone', 'role', 'status']],
    'equipment.php' => ['modal_id' => 'equipmentModal', 'table' => 'equipment', 'action_field' => 'action', 'title' => 'Equipment', 'fields' => ['name', 'category', 'status', 'purchase_date', 'value']],
    'materials.php' => ['modal_id' => 'materialModal', 'table' => 'materials', 'action_field' => 'action', 'title' => 'Material', 'fields' => ['name', 'unit', 'current_stock', 'reorder_level', 'status']],
    'projects.php' => ['modal_id' => 'projectModal', 'table' => 'projects', 'action_field' => 'action', 'title' => 'Project', 'fields' => ['name', 'client_id', 'start_date', 'budget_total', 'status']],
    'suppliers.php' => ['modal_id' => 'supplierModal', 'table' => 'suppliers', 'action_field' => 'action', 'title' => 'Supplier', 'fields' => ['name', 'contact_name', 'email', 'phone', 'status']],
    'timesheets.php' => ['modal_id' => 'timesheetModal', 'table' => 'timesheets', 'action_field' => 'action', 'title' => 'Timesheet', 'fields' => ['employee_id', 'project_id', 'work_date', 'hours_worked', 'status']]
];

foreach ($pages as $file => $config) {
    $filePath = __DIR__ . '/account/' . $file;
    if (!file_exists($filePath)) continue;

    $content = file_get_contents($filePath);
    if (strpos($content, 'id="' . $config['modal_id'] . '"') !== false) {
        continue;
    }

    $formHtml = "<!-- GENERATED MODAL -->\n<div class=\"modal fade\" id=\"{$config['modal_id']}\" tabindex=\"-1\">\n  <div class=\"modal-dialog modal-lg\">\n    <form method=\"POST\" action=\"\">\n      <div class=\"modal-content\">\n        <div class=\"modal-header\">\n          <h5 class=\"modal-title\">{$config['title']}</h5>\n          <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"modal\"></button>\n        </div>\n        <div class=\"modal-body\">\n          <input type=\"hidden\" name=\"csrf_token\" value=\"<?php echo \$auth->generateCsrfToken() ?? ''; ?>\">\n          <input type=\"hidden\" name=\"{$config['action_field']}\" value=\"<?php echo isset(\$editItem) ? 'update' : 'create'; ?>\">\n          <?php if(isset(\$editItem) && isset(\$editItem['id'])): ?>\n            <input type=\"hidden\" name=\"id\" value=\"<?php echo \$editItem['id']; ?>\">\n          <?php else: ?>\n            <input type=\"hidden\" name=\"id\" id=\"{$config['modal_id']}_id\" value=\"\">\n          <?php endif; ?>\n          <div class=\"row\">\n";

    foreach ($config['fields'] as $f) {
        $label = ucwords(str_replace('_', ' ', $f));
        $type = 'text';
        if ($f === 'email') $type = 'email';
        if (strpos($f, 'date') !== false) $type = 'date';
        
        if ($f === 'status') {
            $formHtml .= "            <div class=\"col-md-6 mb-3\">\n              <label class=\"form-label\">{$label}</label>\n              <select name=\"{$f}\" class=\"form-select\">\n                <option value=\"active\">Active</option>\n                <option value=\"inactive\">Inactive</option>\n              </select>\n            </div>\n";
        } else {
            $formHtml .= "            <div class=\"col-md-6 mb-3\">\n              <label class=\"form-label\">{$label}</label>\n              <input type=\"{$type}\" name=\"{$f}\" class=\"form-control\" value=\"<?php echo htmlspecialchars(\$editItem['{$f}'] ?? ''); ?>\" required>\n            </div>\n";
        }
    }

    $formHtml .= "          </div>\n        </div>\n        <div class=\"modal-footer\">\n          <button type=\"button\" class=\"btn btn-secondary\" data-bs-dismiss=\"modal\">Cancel</button>\n          <button type=\"submit\" class=\"btn btn-primary\">Save</button>\n        </div>\n      </div>\n    </form>\n  </div>\n</div>\n";

    $newContent = str_replace("<?php require 'inc/admin_footer.php';", $formHtml . "\n<?php require 'inc/admin_footer.php';", $content);
    file_put_contents($filePath, $newContent);
    echo "Injected modal for $file\n";
}

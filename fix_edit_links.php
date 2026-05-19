<?php
$dir = __DIR__ . '/account';
$files = glob($dir . '/*.php');

foreach ($files as $file) {
    if (in_array(basename($file), ['generate_modals.php'])) continue;
    $content = file_get_contents($file);
    $original = $content;

    // Remove data-bs-toggle="modal" target="..." from Edit links that have href="?edit="
    $content = preg_replace('/<a([^>]+href="\?edit=[^>]+)\s+data-bs-toggle="modal"\s+data-bs-target="#[a-zA-Z0-9_]+Modal"/i', '<a$1', $content);

    // Auto-open modal if $editItem is set
    // Find where the modal is, e.g. <div class="modal fade" id="clientModal"
    if (preg_match('/<div[^>]+id="([^"]+Modal)"/', $content, $matches)) {
        $modalId = $matches[1];
        if (strpos($content, "<?php if (isset(\$_GET['edit'])): ?>") === false) {
            $js = "\n<?php if (isset(\$_GET['edit']) && isset(\$editItem) && \$editItem): ?>\n<script>\ndocument.addEventListener('DOMContentLoaded', function() {\n  var myModal = new bootstrap.Modal(document.getElementById('$modalId'));\n  myModal.show();\n});\n</script>\n<?php endif; ?>\n";
            $content = str_replace("<?php require 'inc/admin_footer.php';", $js . "<?php require 'inc/admin_footer.php';", $content);
        }
    }

    if ($original !== $content) {
        file_put_contents($file, $content);
        echo "Fixed edit links in " . basename($file) . "\n";
    }
}

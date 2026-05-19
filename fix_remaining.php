<?php
$fixes = [
    'daily_reports.php' => [
        '<button class="btn btn-sm btn-light border view-report" title="View" data-id="<?php echo $r[\'id\']; ?>"><i class="fas fa-eye" style="width:14px;height:14px"></i></button>' => 
        '<a href="?edit=<?php echo $r[\'id\']; ?>&readonly=1" class="btn btn-sm btn-light border view-report" title="View"><i class="fas fa-eye" style="width:14px;height:14px"></i></a>',
        '<button class="btn btn-sm btn-light border edit-report" title="Edit" data-id="<?php echo $r[\'id\']; ?>"><i class="fas fa-edit" style="width:14px;height:14px"></i></button>' => 
        '<a href="?edit=<?php echo $r[\'id\']; ?>" class="btn btn-sm btn-light border edit-report" title="Edit"><i class="fas fa-edit" style="width:14px;height:14px"></i></a>'
    ],
    'project_stages.php' => [
        '<button class="btn btn-sm btn-light border edit-stage" title="Edit" data-id="<?php echo $s[\'id\']; ?>"><i class="fas fa-edit" style="width:14px;height:14px"></i></button>' => 
        '<a href="?edit=<?php echo $s[\'id\']; ?>" class="btn btn-sm btn-light border edit-stage" title="Edit"><i class="fas fa-edit" style="width:14px;height:14px"></i></a>'
    ],
    'project_budget.php' => [
        '<button class="btn btn-sm btn-light border edit-budget" title="Edit" data-id="<?php echo $b[\'id\']; ?>"><i class="fas fa-edit" style="width:14px;height:14px"></i></button>' => 
        '<a href="?edit=<?php echo $b[\'id\']; ?>" class="btn btn-sm btn-light border edit-budget" title="Edit"><i class="fas fa-edit" style="width:14px;height:14px"></i></a>'
    ],
    'blog_categories.php' => [
        '<button class="btn btn-sm btn-light border edit-cat" data-id="<?php echo $cat[\'id\']; ?>" title="Edit"><i class="fas fa-edit" style="width:14px;height:14px"></i></button>' =>
        '<a href="?edit=<?php echo $cat[\'id\']; ?>" class="btn btn-sm btn-light border edit-cat" title="Edit"><i class="fas fa-edit" style="width:14px;height:14px"></i></a>'
    ]
];

$dir = __DIR__ . '/account';
foreach ($fixes as $file => $replacements) {
    if (file_exists($dir . '/' . $file)) {
        $content = file_get_contents($dir . '/' . $file);
        foreach ($replacements as $search => $replace) {
            $content = str_replace($search, $replace, $content);
        }
        file_put_contents($dir . '/' . $file, $content);
        echo "Fixed remaining buttons in $file\n";
    }
}

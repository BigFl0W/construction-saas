<?php
// blog_categories.php - Manage blog categories
require_once '../config/config.php';
require_once '../classes/Auth.php';
require_once '../classes/Blog.php';
require_once '../classes/Functions.php';

$auth = new Auth();
$auth->requireAuth();

$blog = new Blog();
$functions = Functions::getInstance();
$db = Database::getInstance();

// Get current user for header
$currentUser = $auth->getUserData();

// Handle AJAX requests for CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    try {
        // Verify CSRF token
        if (!$auth->verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            throw new Exception('Invalid security token');
        }
        
        $action = $_POST['action'];
        $response = ['success' => false, 'message' => ''];
        
        switch ($action) {
            case 'create':
                $name = trim($_POST['name'] ?? '');
                $slug = trim($_POST['slug'] ?? '');
                $description = trim($_POST['description'] ?? '');
                $parentId = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
                $icon = trim($_POST['icon'] ?? 'fas fa-folder');
                $color = trim($_POST['color'] ?? '#1e3a5f');
                $displayOrder = (int)($_POST['display_order'] ?? 0);
                
                if (empty($name)) {
                    throw new Exception('Category name is required');
                }
                
                // Generate slug if not provided
                if (empty($slug)) {
                    $slug = $functions->createSlug($name);
                }
                
                // Check if slug exists
                $checkSql = "SELECT id FROM blog_categories WHERE slug = :slug";
                $checkStmt = $db->query($checkSql, ['slug' => $slug]);
                if ($checkStmt->rowCount() > 0) {
                    throw new Exception('Slug already exists. Please use a different slug.');
                }
                
                $sql = "INSERT INTO blog_categories (name, slug, description, parent_id, icon, color, display_order, created_at, updated_at) 
                        VALUES (:name, :slug, :description, :parent_id, :icon, :color, :display_order, NOW(), NOW())";
                
                $db->query($sql, [
                    'name' => $name,
                    'slug' => $slug,
                    'description' => $description,
                    'parent_id' => $parentId,
                    'icon' => $icon,
                    'color' => $color,
                    'display_order' => $displayOrder
                ]);
                
                $response = [
                    'success' => true,
                    'message' => 'Category created successfully',
                    'id' => $db->lastInsertId()
                ];
                break;
                
            case 'update':
                $id = (int)($_POST['id'] ?? 0);
                $name = trim($_POST['name'] ?? '');
                $slug = trim($_POST['slug'] ?? '');
                $description = trim($_POST['description'] ?? '');
                $parentId = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
                $icon = trim($_POST['icon'] ?? 'fas fa-folder');
                $color = trim($_POST['color'] ?? '#1e3a5f');
                $displayOrder = (int)($_POST['display_order'] ?? 0);
                
                if (!$id || empty($name)) {
                    throw new Exception('Invalid category data');
                }
                
                // Generate slug if not provided
                if (empty($slug)) {
                    $slug = $functions->createSlug($name);
                }
                
                // Check if slug exists for other categories
                $checkSql = "SELECT id FROM blog_categories WHERE slug = :slug AND id != :id";
                $checkStmt = $db->query($checkSql, ['slug' => $slug, 'id' => $id]);
                if ($checkStmt->rowCount() > 0) {
                    throw new Exception('Slug already exists. Please use a different slug.');
                }
                
                $sql = "UPDATE blog_categories SET 
                        name = :name,
                        slug = :slug,
                        description = :description,
                        parent_id = :parent_id,
                        icon = :icon,
                        color = :color,
                        display_order = :display_order,
                        updated_at = NOW()
                        WHERE id = :id";
                
                $db->query($sql, [
                    'id' => $id,
                    'name' => $name,
                    'slug' => $slug,
                    'description' => $description,
                    'parent_id' => $parentId,
                    'icon' => $icon,
                    'color' => $color,
                    'display_order' => $displayOrder
                ]);
                
                $response = [
                    'success' => true,
                    'message' => 'Category updated successfully'
                ];
                break;
                
            case 'delete':
                $id = (int)($_POST['id'] ?? 0);
                
                if (!$id) {
                    throw new Exception('Invalid category ID');
                }
                
                // Check if category has posts
                $checkSql = "SELECT COUNT(*) as count FROM blog_post_categories WHERE category_id = :id";
                $checkStmt = $db->query($checkSql, ['id' => $id]);
                $count = $checkStmt->fetch()['count'];
                
                if ($count > 0) {
                    throw new Exception('Cannot delete category with existing posts. Please reassign posts first.');
                }
                
                // Check if category has subcategories
                $checkSql = "SELECT COUNT(*) as count FROM blog_categories WHERE parent_id = :id";
                $checkStmt = $db->query($checkSql, ['id' => $id]);
                $count = $checkStmt->fetch()['count'];
                
                if ($count > 0) {
                    throw new Exception('Cannot delete category with subcategories. Please reassign or delete subcategories first.');
                }
                
                $sql = "DELETE FROM blog_categories WHERE id = :id";
                $db->query($sql, ['id' => $id]);
                
                $response = [
                    'success' => true,
                    'message' => 'Category deleted successfully'
                ];
                break;
                
            case 'get':
                $id = (int)($_POST['id'] ?? 0);
                
                if (!$id) {
                    throw new Exception('Invalid category ID');
                }
                
                $sql = "SELECT * FROM blog_categories WHERE id = :id";
                $stmt = $db->query($sql, ['id' => $id]);
                $category = $stmt->fetch();
                
                if (!$category) {
                    throw new Exception('Category not found');
                }
                
                $response = [
                    'success' => true,
                    'data' => $category
                ];
                break;
        }
        
        echo json_encode($response);
        exit;
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
}

// Fetch all categories for display
try {
    $sql = "SELECT c.*, 
                   p.name as parent_name,
                   (SELECT COUNT(*) FROM blog_post_categories WHERE category_id = c.id) as post_count
            FROM blog_categories c
            LEFT JOIN blog_categories p ON c.parent_id = p.id
            ORDER BY c.display_order, c.name";
    $stmt = $db->query($sql);
    $categories = $stmt->fetchAll();
    
    // Build parent options for modal
    $parentOptions = '<option value="">— None (top level) —</option>';
    foreach ($categories as $cat) {
        $parentOptions .= '<option value="' . $cat['id'] . '">' . htmlspecialchars($cat['name']) . '</option>';
    }
    
} catch (Exception $e) {
    error_log("Error fetching categories: " . $e->getMessage());
    $categories = [];
    $parentOptions = '<option value="">— None (top level) —</option>';
}

$pageActive = 'blog_categories';
$pageTitle = 'TPV Construction and Services LTD · Blog Categories';
require 'inc/admin_header.php';
?>
            <!-- breadcrumb -->
            <div data-pages="parallax">
                <div class="container-fluid p-l-15 p-r-15 sm-p-l-0 sm-p-r-0">
                    <div class="inner">
                        <ol class="breadcrumb sm-p-b-5">
                            <li class="breadcrumb-item"><a href="index.php"><i class="fas fa-home me-1"></i>Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="blog_posts.php">Blog</a></li>
                            <li class="breadcrumb-item active">Categories</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- MAIN CARD -->
            <div class="container-fluid p-l-15 p-r-15 p-t-0 p-b-25">
                <div class="card shadow-sm">
                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center bg-white py-3">
                        <div class="card-title mb-2 mb-sm-0">
                            <i class="fas fa-tags me-2"></i> Blog Categories
                        </div>
                        <div>
                            <button class="btn btn-primary btn-sm rounded-pill px-4" id="addCategoryBtn">
                                <i class="fas fa-plus me-1"></i> New Category
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- filter / search row -->
                        <div class="row filter-row m-b-20">
                            <div class="col-md-6 mb-2 mb-md-0">
                                <div class="input-group">
                                    <input type="text" class="form-control rounded-pill" id="searchBox" placeholder="Search categories...">
                                    <span class="input-group-append">
                                        <button class="btn btn-outline-secondary rounded-pill" type="button"><i class="fas fa-search"></i></button>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <span class="cat-badge"><i class="far fa-folder me-1"></i> <span id="totalCategories"><?php echo count($categories); ?></span> total</span>
                            </div>
                        </div>

                        <!-- CATEGORIES TABLE -->
                        <div class="table-responsive-wrapper">
                            <table class="table table-hover" data-table id="categoriesTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Slug</th>
                                        <th>Description</th>
                                        <th>Parent</th>
                                        <th>Icon</th>
                                        <th>Color</th>
                                        <th>Posts</th>
                                        <th>Order</th>
                                        <th data-orderable="false">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($categories as $cat): ?>
                                    <tr data-cat-id="<?php echo $cat['id']; ?>">
                                        <td><?php echo $cat['id']; ?></td>
                                        <td>
                                            <i class="<?php echo htmlspecialchars($cat['icon'] ?? 'fas fa-folder'); ?> me-2" style="color: <?php echo htmlspecialchars($cat['color'] ?? '#1e3a5f'); ?>"></i>
                                            <?php echo htmlspecialchars($cat['name']); ?>
                                        </td>
                                        <td><code><?php echo htmlspecialchars($cat['slug']); ?></code></td>
                                        <td><?php echo htmlspecialchars($functions->truncateText($cat['description'] ?? '', 50)); ?></td>
                                        <td>
                                            <?php 
                                            if ($cat['parent_id']) {
                                                echo htmlspecialchars($cat['parent_name'] ?? 'Unknown');
                                            } else {
                                                echo '—';
                                            }
                                            ?>
                                        </td>
                                        <td><code><?php echo htmlspecialchars($cat['icon'] ?? 'fas fa-folder'); ?></code></td>
                                        <td>
                                            <span class="color-preview" style="background: <?php echo htmlspecialchars($cat['color'] ?? '#1e3a5f'); ?>;"></span>
                                            <?php echo htmlspecialchars($cat['color'] ?? '#1e3a5f'); ?>
                                        </td>
                                        <td><span class="badge bg-info"><?php echo $cat['post_count']; ?></span></td>
                                        <td><?php echo $cat['display_order'] ?? 0; ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <button class="btn btn-sm btn-light border edit-cat" data-id="<?php echo $cat['id']; ?>" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-light border text-danger delete-cat" data-id="<?php echo $cat['id']; ?>" title="Delete" <?php echo $cat['post_count'] > 0 ? 'disabled' : ''; ?>>
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <p class="small hint-text m-t-15">
                            <i class="fas fa-info-circle me-1"></i> 
                            Categories with posts cannot be deleted. Reassign posts first.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- FOOTER -->
<?php require 'inc/admin_footer.php'; ?>

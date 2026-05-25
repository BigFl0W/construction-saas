<?php
require_once '../config/config.php';
require_once '../classes/Auth.php';
require_once '../classes/Functions.php';
require_once '../classes/Document.php';
require_once '../classes/Employee.php';

$auth = new Auth();
$auth->requireAuth();
$currentUser = $auth->getUserData();
$functions = Functions::getInstance();
$db = Database::getInstance();

$doc = new Document();
$employee = new Employee();

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    if (!$auth->verifyCsrfToken($_GET['csrf_token'] ?? '')) {
        $_SESSION['toast_error'] = 'Invalid security token.';
    } else {
        $doc->delete((int)$_GET['delete']);
        $_SESSION['toast_success'] = 'Document deleted successfully.';
    }
    header('Location: documents.php');
    exit;
}

// Handle category add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_category') {
    if ($auth->verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $doc->addCategory($_POST['category_name'], $_POST['category_description'] ?? null);
        $_SESSION['toast_success'] = 'Category added successfully.';
    } else {
        $_SESSION['toast_error'] = 'Invalid security token.';
    }
    header('Location: documents.php');
    exit;
}

// Handle document upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'upload' && isset($_FILES['file'])) {
    if (!$auth->verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['toast_error'] = 'Invalid security token.';
    } elseif ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['toast_error'] = 'File upload failed.';
    } else {
        $data = [
            'category_id' => $_POST['category_id'] ?: null,
            'project_id' => $_POST['project_id'] ?: null,
            'client_id' => $_POST['client_id'] ?: null,
            'employee_id' => $_POST['employee_id'] ?: null,
            'description' => $_POST['description'] ?? null,
            'version' => $_POST['version'] ?? '1.0',
            'created_by' => $currentUser['id'] ?? null
        ];
        $doc->create($data, $_FILES['file']);
        $_SESSION['toast_success'] = 'Document uploaded successfully.';
    }
    header('Location: documents.php');
    exit;
}

// Handle document metadata update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    if (!$auth->verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['toast_error'] = 'Invalid security token.';
    } else {
        $doc->update((int)$_POST['id'], [
            'category_id' => $_POST['category_id'] ?: null,
            'project_id' => $_POST['project_id'] ?: null,
            'client_id' => $_POST['client_id'] ?: null,
            'employee_id' => $_POST['employee_id'] ?: null,
            'description' => $_POST['description'] ?? null,
            'version' => $_POST['version'] ?? '1.0'
        ]);
        $_SESSION['toast_success'] = 'Document updated successfully.';
    }
    header('Location: documents.php');
    exit;
}

// Filters
$filterProject = $_GET['project_id'] ?? null;
$filterClient = $_GET['client_id'] ?? null;
$filterEmployee = $_GET['employee_id'] ?? null;
$filterCategory = $_GET['category_id'] ?? null;

$documents = $doc->getAll($filterProject, $filterClient, $filterEmployee, $filterCategory);
$categories = $doc->getCategories();
$hasDocuments = !empty($documents);

$projects = $db->query("SELECT id, name FROM projects WHERE deleted_at IS NULL ORDER BY name")->fetchAll();
$clients = $db->query("SELECT id, company_name FROM clients WHERE deleted_at IS NULL ORDER BY company_name")->fetchAll();
$employees = $employee->getAll();
$editDocument = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $editDocument = $doc->getById((int)$_GET['edit']);
}
$userName = $currentUser['first_name'] ?? $currentUser['username'] ?? 'User';

function formatFileSize($bytes) {
    if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 2) . ' GB';
    if ($bytes >= 1048576) return number_format($bytes / 1048576, 2) . ' MB';
    if ($bytes >= 1024) return number_format($bytes / 1024, 2) . ' KB';
    return $bytes . ' B';
}
$pageActive = 'documents';
$pageTitle = 'TPV Construction and Services LTD · Documents';
require 'inc/admin_header.php';
?>

                <div data-pages="parallax">
                    <div class="container-fluid p-l-25 p-r-25 sm-p-l-0 sm-p-r-0">
                        <div class="inner">
                            <ol class="breadcrumb sm-p-b-5">
                                <li class="breadcrumb-item"><a href="#">TPV Construction and Services LTD</a></li>
                                <li class="breadcrumb-item"><a href="clients.php">Clients</a></li>
                                <li class="breadcrumb-item active">Document Center</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="container-fluid p-l-25 p-r-25 p-t-0 p-b-25 sm-padding-10">
                    <div class="row g-3 mb-3">
                        <div class="col-md-9">
                            <div class="card card-default h-100">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <div class="card-title"><i class="fas fa-file-alt me-2"></i> All Documents</div>
                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#uploadModal">
                                        <i class="fas fa-upload me-1"></i> Upload
                                    </button>
                                </div>
                                <div class="card-body">
                                    <form method="GET" class="filter-row mb-3">
                                        <div class="row g-2 align-items-end">
                                            <div class="col-md-3">
                                                <label class="form-label small">Category</label>
                                                <select name="category_id" class="form-select form-select-sm">
                                                    <option value="">All</option>
                                                    <?php foreach ($categories as $cat): ?>
                                                    <option value="<?php echo $cat['id']; ?>" <?php echo $filterCategory == $cat['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['name']); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label small">Project</label>
                                                <select name="project_id" class="form-select form-select-sm">
                                                    <option value="">All</option>
                                                    <?php foreach ($projects as $p): ?>
                                                    <option value="<?php echo $p['id']; ?>" <?php echo $filterProject == $p['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($p['name']); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label small">Client</label>
                                                <select name="client_id" class="form-select form-select-sm">
                                                    <option value="">All</option>
                                                    <?php foreach ($clients as $c): ?>
                                                    <option value="<?php echo $c['id']; ?>" <?php echo $filterClient == $c['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($c['company_name']); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <button type="submit" class="btn btn-outline-secondary btn-sm w-100">Filter</button>
                                            </div>
                                        </div>
                                    </form>

                                    <div class="table-responsive">
                                        <table class="table table-hover<?php echo $hasDocuments ? '' : ' no-datatable'; ?>"<?php echo $hasDocuments ? ' data-table' : ''; ?> id="documentsTable">
                                            <thead>
                                                <tr>
                                                    <th>File</th>
                                                    <th>Category</th>
                                                    <th>Project</th>
                                                    <th>Client</th>
                                                    <th>Description</th>
                                                    <th>Size</th>
                                                    <th>Uploaded</th>
                                                    <th class="text-end">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($documents)): ?>
                                                <tr>
                                                    <td class="text-center text-muted py-4">No documents found.</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <?php else: ?>
                                                <?php foreach ($documents as $d): 
                                                    $ext = strtolower(pathinfo($d['original_filename'] ?? $d['filename'], PATHINFO_EXTENSION));
                                                    $docClass = 'doc-default';
                                                    if (in_array($ext, ['pdf'])) $docClass = 'doc-pdf';
                                                    elseif (in_array($ext, ['jpg','jpeg','png','gif','webp'])) $docClass = 'doc-image';
                                                    elseif (in_array($ext, ['doc','docx','xls','xlsx','ppt','pptx'])) $docClass = 'doc-doc';
                                                    elseif (in_array($ext, ['zip','rar','7z','tar','gz'])) $docClass = 'doc-zip';
                                                ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <div class="doc-icon <?php echo $docClass; ?>"><?php echo substr($ext ?: '?', 0, 3); ?></div>
                                                            <div>
                                                                <strong><?php echo htmlspecialchars($d['original_filename'] ?? $d['filename']); ?></strong>
                                                                <?php if (!empty($d['version']) && $d['version'] !== '1.0'): ?>
                                                                <br><small class="text-muted">v<?php echo htmlspecialchars($d['version']); ?></small>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($d['category_name'] ?? '-'); ?></td>
                                                    <td><?php echo htmlspecialchars($d['project_name'] ?? '-'); ?></td>
                                                    <td><?php echo htmlspecialchars($d['client_name'] ?? '-'); ?></td>
                                                    <td><?php echo htmlspecialchars($functions->truncateText($d['description'] ?? '', 30)); ?></td>
                                                    <td><?php echo formatFileSize((int)$d['file_size']); ?></td>
                                                    <td><?php echo htmlspecialchars($functions->timeAgo($d['created_at'])); ?></td>
                                                    <td class="text-end">
                                                        <a href="../<?php echo htmlspecialchars($d['file_path']); ?>" class="btn btn-sm btn-outline-primary btn-client-icon" target="_blank" title="Download">
                                                            <i class="fas fa-download" style="width:14px;height:14px;"></i>
                                                        </a>
                                                        <a href="?edit=<?php echo $d['id']; ?>" class="btn btn-sm btn-outline-secondary btn-client-icon" title="Edit">
                                                            <i class="fas fa-pen" style="width:14px;height:14px;"></i>
                                                        </a>
                                                        <a href="?delete=<?php echo $d['id']; ?>&csrf_token=<?php echo $auth->generateCsrfToken(); ?>" class="btn btn-sm btn-outline-danger btn-client-icon" onclick="return confirmAction(this, 'Delete this document?')" title="Delete">
                                                            <i class="fas fa-trash" style="width:14px;height:14px;"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card card-default">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <div class="card-title"><i class="fas fa-folder me-2"></i> Categories</div>
                                    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#categoryModal">
                                        <i class="fas fa-plus" style="width:14px;height:14px;"></i>
                                    </button>
                                </div>
                                <div class="card-body p-0">
                                    <div class="list-group list-group-flush">
                                        <?php foreach ($categories as $cat): ?>
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><i class="fas fa-folder" style="width:14px;height:14px;" class="me-2 text-warning"></i> <?php echo htmlspecialchars($cat['name']); ?></span>
                                            <span class="badge bg-light text-dark"><?php echo $cat['description'] ? '!' : ''; ?></span>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
            </div>
<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form method="POST" enctype="multipart/form-data">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Upload Document</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="csrf_token" value="<?php echo $auth->generateCsrfToken(); ?>">
          <input type="hidden" name="action" value="upload">
          <div class="mb-3">
            <label class="form-label">File</label>
            <input type="file" name="file" class="form-control" required>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Category</label>
              <select name="category_id" class="form-select">
                <option value="">Uncategorized</option>
                <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Version</label>
              <input type="text" name="version" class="form-control" value="1.0">
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Project</label>
              <select name="project_id" class="form-select">
                <option value="">None</option>
                <?php foreach ($projects as $p): ?>
                <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['name']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Client</label>
              <select name="client_id" class="form-select">
                <option value="">None</option>
                <?php foreach ($clients as $c): ?>
                <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['company_name']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Employee</label>
              <select name="employee_id" class="form-select">
                <option value="">None</option>
                <?php foreach ($employees as $emp): ?>
                <option value="<?php echo $emp['id']; ?>"><?php echo htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Upload</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editDocumentModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form method="POST">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Document</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="csrf_token" value="<?php echo $auth->generateCsrfToken(); ?>">
          <input type="hidden" name="action" value="update">
          <input type="hidden" name="id" value="<?php echo htmlspecialchars($editDocument['id'] ?? ''); ?>">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Category</label>
              <select name="category_id" class="form-select">
                <option value="">Uncategorized</option>
                <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat['id']; ?>" <?php echo ($editDocument['category_id'] ?? '') == $cat['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['name']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Version</label>
              <input type="text" name="version" class="form-control" value="<?php echo htmlspecialchars($editDocument['version'] ?? '1.0'); ?>">
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Project</label>
              <select name="project_id" class="form-select">
                <option value="">None</option>
                <?php foreach ($projects as $p): ?>
                <option value="<?php echo $p['id']; ?>" <?php echo ($editDocument['project_id'] ?? '') == $p['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($p['name']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Client</label>
              <select name="client_id" class="form-select">
                <option value="">None</option>
                <?php foreach ($clients as $c): ?>
                <option value="<?php echo $c['id']; ?>" <?php echo ($editDocument['client_id'] ?? '') == $c['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($c['company_name']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Employee</label>
              <select name="employee_id" class="form-select">
                <option value="">None</option>
                <?php foreach ($employees as $emp): ?>
                <option value="<?php echo $emp['id']; ?>" <?php echo ($editDocument['employee_id'] ?? '') == $emp['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($editDocument['description'] ?? ''); ?></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <a href="documents.php" class="btn btn-secondary">Cancel</a>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add Document Category</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="csrf_token" value="<?php echo $auth->generateCsrfToken(); ?>">
          <input type="hidden" name="action" value="add_category">
          <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="category_name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="category_description" class="form-control" rows="3"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </div>
    </form>
  </div>
</div>

<?php if ($editDocument): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
  new bootstrap.Modal(document.getElementById('editDocumentModal')).show();
});
</script>
<?php endif; ?>
<?php require 'inc/admin_footer.php'; ?>

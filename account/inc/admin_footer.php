        </div>
    </div>

    <div class="container-fluid container-fixed-lg footer">
        <div class="copyright sm-text-center">
            <p class="small-text pull-left">&copy;2025 TPV Construction and Services LTD &middot; Construction Management System</p>
            <p class="small pull-right"><i class="fas fa-user me-1"></i><?php echo htmlspecialchars($userName ?? 'User'); ?></p>
        </div>
    </div>
</div>

<!-- ===== CONFIRMATION MODAL ===== -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header justify-content-center border-0 pb-0">
                <div class="modal-icon" id="confirmIcon">
                    <i class="fas fa-exclamation-triangle text-warning" style="font-size:2.5rem;"></i>
                </div>
            </div>
            <div class="modal-body text-center">
                <h6 class="fw-semibold mb-1" id="confirmTitle">Are you sure?</h6>
                <p class="text-muted small mb-0" id="confirmMessage">This action cannot be undone.</p>
            </div>
            <div class="modal-footer justify-content-center border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmBtn">
                    <i class="fas fa-trash me-1"></i> Delete
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<script>
function toggleSidebar() {
    document.getElementById('mainSidebar').classList.toggle('show');
    document.getElementById('sidebarOverlay').classList.toggle('show');
}
document.getElementById('sidebarOverlay').addEventListener('click', function() {
    document.getElementById('mainSidebar').classList.remove('show');
    this.classList.remove('show');
});
function toggleSubmenu(el) {
    var menuItem = el.parentElement;
    menuItem.classList.toggle('open');
    var menuKey = menuItem.getAttribute('data-menu-key');
    if (menuKey) {
        localStorage.setItem('sidebar-menu-' + menuKey, menuItem.classList.contains('open') ? 'open' : 'closed');
    }
    return false;
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.menu-items > li[data-menu-key]').forEach(function(menuItem) {
        var menuKey = menuItem.getAttribute('data-menu-key');
        if (!menuKey) return;
        var savedState = localStorage.getItem('sidebar-menu-' + menuKey);
        if (savedState === 'closed') {
            menuItem.classList.remove('open');
        } else if (savedState === 'open') {
            menuItem.classList.add('open');
        }
    });
});

var confirmData = null;

function confirmAction(element, message, callback) {
    var modal = new bootstrap.Modal(document.getElementById('confirmModal'));
    document.getElementById('confirmMessage').textContent = message || 'This action cannot be undone.';
    var btnEl = document.getElementById('confirmBtn');
    var iconEl = document.getElementById('confirmIcon');

    if (element) {
        var icon = element.querySelector('i');
        var iconClass = icon ? icon.className : 'fas fa-trash';
        if (iconClass.indexOf('fa-trash') > -1 || iconClass.indexOf('fa-times') > -1 || iconClass.indexOf('fa-ban') > -1) {
            iconEl.innerHTML = '<i class="fas fa-exclamation-triangle text-danger" style="font-size:2.5rem;"></i>';
            btnEl.className = 'btn btn-danger';
        } else if (iconClass.indexOf('fa-check') > -1 || iconClass.indexOf('fa-approve') > -1) {
            iconEl.innerHTML = '<i class="fas fa-check-circle text-success" style="font-size:2.5rem;"></i>';
            btnEl.className = 'btn btn-success';
        } else {
            iconEl.innerHTML = '<i class="fas fa-exclamation-triangle text-warning" style="font-size:2.5rem;"></i>';
            btnEl.className = 'btn btn-warning text-dark';
        }
    }

    if (typeof callback === 'function') {
        confirmData = { callback: callback, element: element };
    } else if (element && element.getAttribute('href')) {
        var href = element.getAttribute('href');
        confirmData = { callback: function() { window.location.href = href; } };
    } else if (element && element.closest('form')) {
        var form = element.closest('form');
        confirmData = { callback: function() { form.submit(); } };
    } else {
        confirmData = { callback: null };
    }

    modal.show();
    return false;
}

document.getElementById('confirmBtn').addEventListener('click', function() {
    if (confirmData && typeof confirmData.callback === 'function') {
        confirmData.callback();
    }
    var modalEl = document.getElementById('confirmModal');
    var modal = bootstrap.Modal.getInstance(modalEl);
    if (modal) modal.hide();
});

document.getElementById('confirmModal').addEventListener('hidden.bs.modal', function() {
    confirmData = null;
});

function showToast(message, type) {
    type = type || 'success';
    var container = document.getElementById('toastContainer');
    var icons = { success: 'fa-check-circle', error: 'fa-exclamation-circle', warning: 'fa-exclamation-triangle', info: 'fa-info-circle' };
    var toast = document.createElement('div');
    toast.className = 'toast-custom ' + type;
    toast.innerHTML = '<i class="fas ' + (icons[type] || 'fa-info-circle') + ' toast-icon"></i>' +
        '<div class="toast-body">' + message + '</div>' +
        '<button class="toast-close" onclick="this.parentElement.remove()">&times;</button>';
    container.appendChild(toast);
    setTimeout(function() { if (toast.parentElement) { toast.style.animation = 'toastSlideOut 0.3s ease forwards'; setTimeout(function() { if (toast.parentElement) toast.remove(); }, 300); } }, 4000);
    toast.querySelector('.toast-close').addEventListener('click', function() { toast.remove(); });
}

$(function() {
    $('[data-bs-toggle="tooltip"]').tooltip();
    $('[data-bs-toggle="popover"]').popover();
    $('.select2').each(function() {
        var opts = { theme: 'bootstrap-5', width: '100%' };
        if ($(this).data('allow-clear')) opts.allowClear = true;
        if ($(this).data('placeholder')) opts.placeholder = $(this).data('placeholder');
        $(this).select2(opts);
    });
    function initDataTable(el) {
        if (typeof $.fn.DataTable === 'undefined') return;
        if ($(el).find('tbody tr').length === 0) return;
        var opts = {
            responsive: true,
            autoWidth: false,
            language: { search: '', searchPlaceholder: 'Search...', lengthMenu: '_MENU_ per page' }
        };
        if ($(el).data('page-length')) opts.pageLength = $(el).data('page-length');
        if ($(el).data('order')) opts.order = $(el).data('order');
        if ($(el).find('thead th').length > 6) opts.scrollX = true;
        $(el).DataTable(opts);
    }
    $('.data-table, [data-table]').each(function() { initDataTable(this); });
    setTimeout(function() { $('.alert').alert('close'); }, 5000);

    // Global ReadOnly View logic
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('readonly')) {
        $('.modal input, .modal select, .modal textarea').prop('disabled', true);
        $('.modal .modal-footer button[type="submit"]').hide();
        $('.modal .modal-title').text('View Details');
    }
});

<?php if ($toastSuccess): ?>
showToast('<?php echo addslashes($toastSuccess); ?>', 'success');
<?php endif; ?>
<?php if ($toastError): ?>
showToast('<?php echo addslashes($toastError); ?>', 'error');
<?php endif; ?>
<?php if ($toastWarning): ?>
showToast('<?php echo addslashes($toastWarning); ?>', 'warning');
<?php endif; ?>
<?php if ($toastInfo): ?>
showToast('<?php echo addslashes($toastInfo); ?>', 'info');
<?php endif; ?>
</script>

<?php if (isset($extraScripts)) echo $extraScripts; ?>
</body>
</html>

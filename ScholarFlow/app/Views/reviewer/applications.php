<?php
/**
 * Reviewer — All Applications List
 * @var array   $auth          Current user
 * @var array[] $applications  Applications with joined scholarship + user data
 * @var string  $status        Active status filter ('' | 'pending' | 'approved' | 'rejected')
 * @var array   $flash         Flash messages
 */
$pageTitle = 'All Applications';
$bodyClass  = 'app-body';
?>
<?php require ROOT . '/app/Views/layouts/header.php'; ?>

<div class="app-layout">
    <?php require ROOT . '/app/Views/layouts/sidebar.php'; ?>

    <div class="app-main">
        <header class="app-topbar">
            <button class="sidebar-toggle-btn" id="sidebarOpen"><i class="bi bi-list"></i></button>
            <div class="topbar-title">
                <h2>All Applications</h2>
                <span>Review and manage applications</span>
            </div>
        </header>

        <main class="app-content">
            <?php require ROOT . '/app/Views/layouts/flash.php'; ?>

            <!-- Status Tabs -->
            <div class="filter-bar">
                <div class="filter-search">
                    <i class="bi bi-search"></i>
                    <input type="text" id="appSearch" placeholder="Search applicant or scholarship..." class="search-input">
                </div>
                <div class="filter-badges">
                    <button class="filter-btn active" data-status="">All</button>
                    <button class="filter-btn" data-status="pending">Pending</button>
                    <button class="filter-btn" data-status="approved">Approved</button>
                    <button class="filter-btn" data-status="rejected">Rejected</button>
                </div>
            </div>

            <?php if (empty($applications)): ?>
                <div class="empty-state empty-state-lg">
                    <div class="empty-icon"><i class="bi bi-inbox-fill"></i></div>
                    <h4>No Applications Found</h4>
                    <p>There are no applications matching the selected filter.</p>
                </div>
            <?php else: ?>
                <div class="applications-table-wrap">
                    <table class="sf-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Applicant</th>
                                <th>Scholarship</th>
                                <th>Applied</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($applications as $row): ?>
                                <tr data-status="<?= $row['status'] ?>"
                                    data-search="<?= strtolower(($row['applicant_name'] ?? '') . ' ' . ($row['scholarship_name'] ?? '')) ?>">
                                    <td class="mono">
                                        <?= str_pad($row['id'], 5, '0', STR_PAD_LEFT) ?>
                                    </td>
                                    <td>
                                        <div class="applicant-cell">
                                            <?php $avatar = $row['avatar'] ?? null; ?>
                                            <?php if (!empty($avatar)): ?>
                                                <img class="mini-avatar-img"
                                                     src="<?= APP_URL . '/uploads/' . htmlspecialchars($avatar) ?>"
                                                     alt="<?= htmlspecialchars($row['applicant_name'] ?? 'Applicant') ?>">
                                            <?php else: ?>
                                                <div class="mini-avatar">
                                                    <?= strtoupper(substr($row['applicant_name'] ?? 'U', 0, 2)) ?>
                                                </div>
                                            <?php endif; ?>

                                            <div>
                                                <strong>
                                                    <?= htmlspecialchars($row['applicant_name']) ?>
                                                </strong>
                                                <small>
                                                    <?= htmlspecialchars($row['email']) ?>
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($row['scholarship_name']) ?></td>
                                    <td><?= date('M j, Y', strtotime($row['created_at'])) ?></td>
                                    <td>
                                        <span class="status-badge status-<?= $row['status'] ?>">
                                            <span class="status-dot"></span>
                                            <?= ucfirst($row['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($row['status'] === 'pending'): ?>
                                            <a href="<?= APP_URL ?>/reviewer/applications/<?= $row['id'] ?>"
                                            class="btn-table-action">
                                                Review
                                                <i class="bi bi-arrow-right"></i>
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= APP_URL ?>/reviewer/applications/<?= $row['id'] ?>/view"
                                            class="btn-table-action">
                                                View
                                                <i class="bi bi-arrow-right"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<script>
const search = document.getElementById('appSearch');
const filterBtns = document.querySelectorAll('.filter-btn');
let activeFilter = '';

function filterRows() {
    const q = search.value.toLowerCase();
    document.querySelectorAll('.sf-table tbody tr').forEach(row => {
        const matchS = !activeFilter || row.dataset.status === activeFilter;
        const matchQ = row.dataset.search.includes(q);
        row.style.display = matchS && matchQ ? '' : 'none';
    });
}

search?.addEventListener('input', filterRows);
filterBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        filterBtns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        activeFilter = btn.dataset.status;
        filterRows();
    });
});
</script>

<?php require ROOT . '/app/Views/layouts/footer.php'; ?>

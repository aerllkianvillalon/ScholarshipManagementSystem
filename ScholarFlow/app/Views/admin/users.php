<?php
/**
 * Admin — Users
 *
 * @var array        $auth   Current user (admin)
 * @var array[]      $users  Users list
 * @var string       $role   Optional role filter
 * @var array        $flash  Flash messages
 */
$pageTitle = 'Admin — Users';
$bodyClass = 'app-body';
?>

<?php require ROOT . '/app/Views/layouts/header.php'; ?>

<div class="app-layout">
    <?php require ROOT . '/app/Views/layouts/sidebar.php'; ?>

    <div class="app-main">
        <header class="app-topbar">
            <button class="sidebar-toggle-btn" id="sidebarOpen"><i class="bi bi-list"></i></button>
            <div class="topbar-title">
                <h2>Users</h2>
                <span>Manage accounts</span>
            </div>
            <div class="topbar-actions">
                <a href="<?= APP_URL ?>/admin/users/create" class="btn-topbar">
                    <i class="bi bi-plus-lg"></i> Add User
                </a>
            </div>
        </header>

        <main class="app-content">
            <?php require ROOT . '/app/Views/layouts/flash.php'; ?>

            <div class="content-card">
                <div class="filter-bar">
                    <div class="filter-search">
                        <i class="bi bi-search"></i>
                        <div class="users-search-wrap">
                            <i class="bi bi-search users-search-icon-left" aria-hidden="true"></i>
                            <input
                                type="text"
                                id="usersSearch"
                                value="<?= htmlspecialchars($q ?? '') ?>"
                                placeholder="Search by name or email..."
                                class="search-input users-search-input"
                                aria-label="Search users"
                            >
                            <a
                                href="<?= APP_URL ?>/admin/users"
                                class="users-clear-x"
                                aria-label="Clear search"
                                title="Clear"
                                <?= empty($q) ? 'style="display:none"' : '' ?>
                                id="usersClearX"
                            >
                                <i class="bi bi-x-lg" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>

                    <div class="filter-badges">
                        <button class="filter-btn active" data-role-filter="">All</button>
                        <button class="filter-btn" data-role-filter="student">Students</button>
                        <button class="filter-btn" data-role-filter="reviewer">Reviewers</button>
                        <button class="filter-btn" data-role-filter="admin">Admins</button>
                    </div>
                </div>



                <?php if (empty($users)): ?>
                    <div class="empty-state">
                        <i class="bi bi-people"></i>
                        <p>No users found.</p>
                    </div>
                <?php else: ?>
                    <div class="applications-table-wrap">
                        <table class="sf-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $u): ?>
                                    <?php $roleVal = strtolower((string)($u['role'] ?? '')); ?>
                                    <tr data-role="<?= htmlspecialchars($roleVal) ?>"
                                        data-search="<?= htmlspecialchars(strtolower(($u['name'] ?? '') . ' ' . ($u['email'] ?? ''))) ?>">
                                        <td class="mono"><?= str_pad($u['id'], 5, '0', STR_PAD_LEFT) ?></td>
                                        <td>
                                            <div class="applicant-cell">
                                                <?php if (!empty($u['avatar'])): ?>
                                                    <img class="mini-avatar-img"
                                                         src="<?= APP_URL . '/uploads/' . htmlspecialchars($u['avatar']) ?>"
                                                         alt="<?= htmlspecialchars($u['name'] ?? '') ?>">
                                                <?php else: ?>
                                                    <div class="mini-avatar">
                                                        <?= strtoupper(substr($u['name'] ?? 'U', 0, 2)) ?>
                                                    </div>
                                                <?php endif; ?>
                                                <div>
                                                    <strong><?= htmlspecialchars($u['name'] ?? '') ?></strong>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($u['email'] ?? '') ?></td>
                                        <td>
                                            <?php if ($roleVal === 'student'): ?>
                                                <span class="badge-role badge-student">Student</span>
                                            <?php elseif ($roleVal === 'reviewer'): ?>
                                                <span class="badge-role badge-reviewer">Reviewer</span>
                                            <?php elseif ($roleVal === 'admin'): ?>
                                                <span class="badge-role badge-admin">Admin</span>
                                            <?php else: ?>
                                                <span class="badge-role"><?= htmlspecialchars(ucfirst($roleVal ?: 'unknown')) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?= !empty($u['created_at'])
                                                ? htmlspecialchars(date('M j, Y', strtotime($u['created_at'])))
                                                : '-' ?>
                                        </td>
                                        <td>
                                            <div class="action-btns">
                                                <a class="btn-icon-action" href="<?= APP_URL ?>/admin/users/<?= (int)($u['id'] ?? 0) ?>/edit" title="Edit">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </a>

                                                <form method="POST"
                                                      action="<?= APP_URL ?>/admin/users/<?= (int)($u['id'] ?? 0) ?>/delete"
                                                      onsubmit="return confirm('Delete this user?')"
                                                      style="display:inline;">
                                                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf ?? '') ?>">
                                                    <button type="submit" class="btn-icon-action btn-danger-icon" title="Delete">
                                                        <i class="bi bi-trash3-fill"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>

                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

<script>
(function () {
    const search = document.getElementById('usersSearch');
    const filterBtns = document.querySelectorAll('.filter-btn[data-role-filter]');
    const rows = Array.from(document.querySelectorAll('table.sf-table tbody tr[data-role]'));

    let activeRole = '';

    function filterRows() {
        const q = (search?.value || '').trim().toLowerCase();

        rows.forEach(row => {
            const rowRole = row.dataset.role || '';
            const rowSearch = (row.dataset.search || '').toLowerCase();

            const matchRole = !activeRole || rowRole === activeRole;
            const matchQ = !q || rowSearch.includes(q);

            row.style.display = (matchRole && matchQ) ? '' : 'none';
        });
    }

    search?.addEventListener('input', filterRows);

    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            filterBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            activeRole = btn.dataset.roleFilter || '';
            filterRows();
        });
    });

    // initial paint (in case server filled q)
    filterRows();
})();
</script>

<?php require ROOT . '/app/Views/layouts/footer.php'; ?>



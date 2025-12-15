<?php
$page_title = 'Users Management';
require_once 'includes/admin-header.php';
require_once 'includes/admin-sidebar.php';

// Get all users
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$role_filter = isset($_GET['role']) ? sanitize($_GET['role']) : '';

$where = [];
$params = [];

if ($search) {
    $where[] = "(name LIKE ? OR email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($role_filter) {
    $where[] = "role = ?";
    $params[] = $role_filter;
}

$where_clause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

$stmt = $pdo->prepare("SELECT * FROM users $where_clause ORDER BY created_at DESC");
$stmt->execute($params);
$users = $stmt->fetchAll();

// Get user statistics
$stmt = $pdo->query("SELECT role, COUNT(*) as count FROM users GROUP BY role");
$user_stats = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
?>

<div class="admin-header">
    <h1>Users Management</h1>
    <div class="breadcrumb">Admin / Users</div>
</div>

<?php display_flash(); ?>

<!-- User Statistics -->
<div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
    <div class="stat-card primary">
        <h3>Total Users</h3>
        <div class="stat-value"><?php echo array_sum($user_stats); ?></div>
    </div>
    
    <div class="stat-card success">
        <h3>Regular Users</h3>
        <div class="stat-value"><?php echo $user_stats['user'] ?? 0; ?></div>
    </div>
    
    <div class="stat-card warning">
        <h3>Administrators</h3>
        <div class="stat-value"><?php echo $user_stats['admin'] ?? 0; ?></div>
    </div>
</div>

<div class="admin-table-container">
    <div class="table-header">
        <h2>All Users (<?php echo count($users); ?>)</h2>
    </div>
    
    <!-- Filters -->
    <div style="margin-bottom: 1.5rem; display: flex; gap: 1rem; flex-wrap: wrap;">
        <form method="GET" style="display: flex; gap: 0.5rem; flex: 1;">
            <input type="text" name="search" placeholder="Search by name or email..." 
                   value="<?php echo htmlspecialchars($search); ?>" 
                   style="flex: 1; max-width: 400px;">
            <button type="submit" class="btn btn-primary">Search</button>
            <?php if ($search || $role_filter): ?>
                <a href="users.php" class="btn btn-secondary">Clear</a>
            <?php endif; ?>
        </form>
        
        <div>
            <a href="users.php" class="btn btn-sm <?php echo !$role_filter ? 'btn-primary' : 'btn-secondary'; ?>">All</a>
            <a href="users.php?role=user" class="btn btn-sm <?php echo $role_filter == 'user' ? 'btn-success' : 'btn-secondary'; ?>">Users</a>
            <a href="users.php?role=admin" class="btn btn-sm <?php echo $role_filter == 'admin' ? 'btn-warning' : 'btn-secondary'; ?>">Admins</a>
        </div>
    </div>
    
    <?php if (empty($users)): ?>
        <p>No users found.</p>
    <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Phone</th>
                    <th>Joined Date</th>
                    <th>Orders</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <?php
                    // Get order count for this user
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
                    $stmt->execute([$user['id']]);
                    $order_count = $stmt->fetchColumn();
                    ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($user['name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                            <span class="badge badge-<?php echo $user['role'] == 'admin' ? 'warning' : 'success'; ?>">
                                <?php echo ucfirst($user['role']); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($user['phone'] ?? '-'); ?></td>
                        <td><?php echo format_date($user['created_at']); ?></td>
                        <td>
                            <?php if ($order_count > 0): ?>
                                <a href="../orders.php?user=<?php echo $user['id']; ?>"><?php echo $order_count; ?> orders</a>
                            <?php else: ?>
                                <span style="color: #666;">No orders</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<div class="alert alert-info" style="margin-top: 2rem;">
    <strong>Note:</strong> User management features are view-only in this MVP. Role changes and user deletion can be added as needed.
</div>

<?php require_once 'includes/admin-footer.php'; ?>

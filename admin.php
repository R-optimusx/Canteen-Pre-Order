<?php
/*
 * admin.php - STAFF page: login (session), see all orders, change status.
 * This page handles its own POST forms (login / logout / status change).
 */
require 'includes/functions.php';

$pageTitle = 'Staff';
$pageCss   = 'css/admin.css';
$active    = 'admin';

// ---- Handle form submissions BEFORE any HTML ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'login') {
        // hash_equals compares strings safely
        if (hash_equals(ADMIN_PASSWORD, $_POST['password'] ?? '')) {
            session_regenerate_id(true);          // new session id after login
            $_SESSION['is_admin'] = true;
        } else {
            set_flash('error', 'Wrong password.');
        }
    }

    if ($action === 'logout') {
        unset($_SESSION['is_admin']);
        set_flash('success', 'Logged out.');
    }

    // Only a logged-in admin may change status
    if ($action === 'status' && !empty($_SESSION['is_admin'])) {
        $orderId   = (int)($_POST['order_id'] ?? 0);
        $newStatus = $_POST['status'] ?? '';
        if (in_array($newStatus, STATUSES, true)) {
            $orders = get_orders();
            foreach ($orders as &$o) {          // & = change the real array item
                if ($o['id'] === $orderId) {
                    $o['status'] = $newStatus;
                }
            }
            unset($o);
            write_json('orders.json', $orders);
            set_flash('success', 'Status updated.');
        }
    }

    // PRG pattern: Post -> Redirect -> Get (stops "resubmit form?" on refresh)
    redirect('admin.php');
}

$isAdmin = !empty($_SESSION['is_admin']);
$orders  = $isAdmin ? array_reverse(get_orders()) : [];   // newest first

include 'includes/header.php';
?>
<h1>Staff Panel</h1>

<?php if (!$isAdmin): ?>
    <form method="post" class="login">
        <input type="hidden" name="action" value="login">
        <label for="password">Staff password</label>
        <input type="password" id="password" name="password" required>
        <button type="submit">Login</button>
    </form>
<?php else: ?>
    <form method="post" class="logout">
        <input type="hidden" name="action" value="logout">
        <button type="submit" class="ghost">Logout</button>
    </form>

    <?php if (!$orders): ?>
        <p class="none">No orders yet.</p>
    <?php endif; ?>

    <?php foreach ($orders as $o): ?>
        <div class="order">
            <div class="order-head">
                <strong><?= e($o['number']) ?></strong>
                <span><?= e($o['name']) ?> &middot; <?= e($o['created']) ?></span>
                <span class="status <?= status_class($o['status']) ?>"><?= e($o['status']) ?></span>
            </div>
            <ul>
                <?php foreach ($o['items'] as $line): ?>
                    <li><?= e($line['name']) ?> × <?= (int)$line['qty'] ?></li>
                <?php endforeach; ?>
            </ul>
            <?php if ($o['note'] !== ''): ?>
                <p class="note">Note: <?= e($o['note']) ?></p>
            <?php endif; ?>
            <p class="total">Total: <?= money($o['total']) ?></p>

            <form method="post" class="status-form">
                <input type="hidden" name="action" value="status">
                <input type="hidden" name="order_id" value="<?= (int)$o['id'] ?>">
                <select name="status">
                    <?php foreach (STATUSES as $s): ?>
                        <option value="<?= e($s) ?>" <?= $s === $o['status'] ? 'selected' : '' ?>><?= e($s) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Update</button>
            </form>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
<?php include 'includes/footer.php'; ?>

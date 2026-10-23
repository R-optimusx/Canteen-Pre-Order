<?php
/*
 * queue.php - public live queue. Anyone can see order numbers + status
 * (names are NOT shown, for privacy). Page refreshes itself every 15 sec.
 */
require 'includes/functions.php';

$pageTitle = 'Order Queue';
$pageCss   = 'css/queue.css';
$active    = 'queue';
$extraHead = '<meta http-equiv="refresh" content="15">';   // auto-reload every 15s

$orders = get_orders();

// Search by order number (GET form)
$searched = strtoupper(trim($_GET['number'] ?? ''));
$found    = null;
if ($searched !== '') {
    foreach ($orders as $o) {
        if ($o['number'] === $searched) {
            $found = $o;
        }
    }
}

// Active = not yet collected, oldest first (that is the queue order)
$active_orders = array_filter($orders, fn($o) => $o['status'] !== 'Collected');
$ready     = array_filter($active_orders, fn($o) => $o['status'] === 'Ready');
$waiting   = array_filter($active_orders, fn($o) => $o['status'] !== 'Ready');

include 'includes/header.php';
?>
<h1>Order Queue</h1>

<form method="get" action="queue.php" class="search">
    <input type="text" name="number" value="<?= e($searched) ?>" placeholder="Enter order number e.g. CN001">
    <button type="submit">Check status</button>
</form>

<?php if ($searched !== ''): ?>
    <?php if ($found): ?>
        <div class="result">
            <strong><?= e($found['number']) ?></strong> is
            <span class="status <?= status_class($found['status']) ?>"><?= e($found['status']) ?></span>
        </div>
    <?php else: ?>
        <div class="result notfound">No order found with number <?= e($searched) ?>.</div>
    <?php endif; ?>
<?php endif; ?>

<div class="columns">
    <section>
        <h2>✅ Ready for pickup</h2>
        <?php if (!$ready): ?><p class="none">Nothing ready yet.</p><?php endif; ?>
        <div class="tokens">
            <?php foreach ($ready as $o): ?>
                <span class="token ready"><?= e($o['number']) ?></span>
            <?php endforeach; ?>
        </div>
    </section>

    <section>
        <h2>⏳ In the queue</h2>
        <?php if (!$waiting): ?><p class="none">No orders waiting.</p><?php endif; ?>
        <ol class="waiting">
            <?php foreach ($waiting as $o): ?>
                <li>
                    <strong><?= e($o['number']) ?></strong>
                    <span class="status <?= status_class($o['status']) ?>"><?= e($o['status']) ?></span>
                </li>
            <?php endforeach; ?>
        </ol>
    </section>
</div>
<?php include 'includes/footer.php'; ?>

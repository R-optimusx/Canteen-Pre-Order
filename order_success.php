<?php
/*
 * order_success.php - confirmation page with order number + queue position.
 */
require 'includes/functions.php';

$pageTitle = 'Order Placed';
$pageCss   = 'css/success.css';
$active    = '';

$number = $_SESSION['last_order'] ?? null;
$order  = null;
$position = 0;

if ($number) {
    foreach (get_orders() as $o) {
        if ($o['number'] === $number) {
            $order = $o;
        }
    }
}
if (!$order) {
    redirect('index.php');
}

// Queue position = how many unfinished orders are ahead of this one (+1 for me)
$position = 1;
foreach (get_orders() as $o) {
    if ($o['id'] < $order['id'] && in_array($o['status'], ['Received', 'Preparing'], true)) {
        $position++;
    }
}

include 'includes/header.php';
?>
<div class="receipt">
    <div class="tick">✓</div>
    <h1>Order placed!</h1>
    <p>Your order number</p>
    <div class="number"><?= e($order['number']) ?></div>
    <span class="status <?= status_class($order['status']) ?>"><?= e($order['status']) ?></span>
    <p class="position">Queue position: <strong><?= $position ?></strong></p>

    <ul class="lines">
        <?php foreach ($order['items'] as $line): ?>
            <li><span><?= e($line['name']) ?> × <?= (int)$line['qty'] ?></span>
                <span><?= money($line['price'] * $line['qty']) ?></span></li>
        <?php endforeach; ?>
        <li class="sum"><span>Total</span><span><?= money($order['total']) ?></span></li>
    </ul>

    <a class="btn" href="queue.php?number=<?= e($order['number']) ?>">Track my order</a>
    <a class="link" href="index.php">Order something else</a>
</div>
<?php include 'includes/footer.php'; ?>

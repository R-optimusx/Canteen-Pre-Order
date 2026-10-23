<?php
/*
 * index.php - MENU page.
 * Shows items grouped by category with availability and an "Add" form.
 */
require 'includes/functions.php';

$pageTitle = 'Menu';
$pageCss   = 'css/index.css';
$active    = 'menu';

$menu = get_menu();
$cart = get_cart();

// Group items by category: ['Snacks' => [items...], 'Drinks' => [...]]
$grouped = [];
foreach ($menu as $item) {
    $grouped[$item['category']][] = $item;
}

include 'includes/header.php';
?>
<h1>Today's Menu</h1>
<p class="subtitle">Pick your items, pay at the counter, collect when your order is <strong>Ready</strong>.</p>

<?php foreach ($grouped as $category => $items): ?>
    <h2 class="category"><?= e($category) ?></h2>
    <div class="grid">
    <?php foreach ($items as $item): ?>
        <?php
            $inCart = $cart[$item['id']] ?? 0;
            $available = $item['stock'] > 0;
            $low = $available && $item['stock'] <= 5;
        ?>
        <div class="card <?= $available ? '' : 'soldout' ?>">
            <div class="card-top">
                <h3><?= e($item['name']) ?></h3>
                <span class="price"><?= money($item['price']) ?></span>
            </div>

            <?php if (!$available): ?>
                <span class="badge badge-out">Sold out</span>
            <?php elseif ($low): ?>
                <span class="badge badge-low">Only <?= (int)$item['stock'] ?> left</span>
            <?php else: ?>
                <span class="badge badge-ok">Available</span>
            <?php endif; ?>

            <?php if ($inCart): ?>
                <span class="in-cart">In your cart: <?= (int)$inCart ?></span>
            <?php endif; ?>

            <form method="post" action="cart_action.php" class="add-form">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                <input type="hidden" name="back" value="index.php">
                <input type="number" name="qty" value="1" min="1" max="<?= MAX_PER_ITEM ?>"
                       <?= $available ? '' : 'disabled' ?>>
                <button type="submit" <?= $available ? '' : 'disabled' ?>>Add</button>
            </form>
        </div>
    <?php endforeach; ?>
    </div>
<?php endforeach; ?>

<?php include 'includes/footer.php'; ?>

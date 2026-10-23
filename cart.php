<?php
/*
 * cart.php - shows the cart, lets user change quantities,
 * shows the calculated total, and has the "Place order" form.
 */
require 'includes/functions.php';

$pageTitle = 'Your Cart';
$pageCss   = 'css/cart.css';
$active    = 'cart';

$lines = cart_lines();
$total = cart_total($lines);

include 'includes/header.php';
?>
<h1>Your Cart</h1>

<?php if (empty($lines)): ?>
    <div class="empty">
        <p>Your cart is empty.</p>
        <a class="btn" href="index.php">Browse menu</a>
    </div>
<?php else: ?>
    <table class="cart-table">
        <thead>
            <tr><th>Item</th><th>Price</th><th>Qty</th><th>Subtotal</th><th></th></tr>
        </thead>
        <tbody>
        <?php foreach ($lines as $line): ?>
            <tr>
                <td><?= e($line['name']) ?></td>
                <td><?= money($line['price']) ?></td>
                <td>
                    <form method="post" action="cart_action.php" class="qty-form">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" value="<?= (int)$line['id'] ?>">
                        <input type="hidden" name="back" value="cart.php">
                        <input type="number" name="qty" value="<?= (int)$line['qty'] ?>" min="0" max="<?= MAX_PER_ITEM ?>">
                        <button type="submit" class="small">Update</button>
                    </form>
                </td>
                <td><?= money($line['subtotal']) ?></td>
                <td>
                    <form method="post" action="cart_action.php">
                        <input type="hidden" name="action" value="remove">
                        <input type="hidden" name="id" value="<?= (int)$line['id'] ?>">
                        <input type="hidden" name="back" value="cart.php">
                        <button type="submit" class="small danger">Remove</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr><td colspan="3">Total</td><td colspan="2" class="total"><?= money($total) ?></td></tr>
        </tfoot>
    </table>

    <div class="actions">
        <form method="post" action="cart_action.php">
            <input type="hidden" name="action" value="clear">
            <input type="hidden" name="back" value="cart.php">
            <button type="submit" class="ghost">Clear cart</button>
        </form>
    </div>

    <form method="post" action="place_order.php" class="checkout">
        <h2>Place your order</h2>
        <label for="name">Your name</label>
        <input type="text" id="name" name="name" maxlength="40" required placeholder="e.g. Rahul">
        <label for="note">Note (optional)</label>
        <input type="text" id="note" name="note" maxlength="100" placeholder="e.g. less sugar in tea">
        <button type="submit" class="btn">Submit order &middot; <?= money($total) ?></button>
    </form>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>

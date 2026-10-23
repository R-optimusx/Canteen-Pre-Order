<?php
/*
 * cart_action.php - handles cart operations: add / update / remove / clear.
 * This file prints NO HTML. It changes $_SESSION['cart'] and redirects back.
 * (So it has no css file - there is nothing to style.)
 */
require 'includes/functions.php';

// Only accept POST (forms), never plain links - safer.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

$action = $_POST['action'] ?? '';
$id     = (int)($_POST['id'] ?? 0);        // (int) forces a number -> safe
$qty    = (int)($_POST['qty'] ?? 1);

// Only allow going back to these two pages (prevents open redirects).
$back = in_array($_POST['back'] ?? '', ['index.php', 'cart.php'], true) ? $_POST['back'] : 'index.php';

$menu = menu_by_id();
$cart = get_cart();

switch ($action) {

    case 'add':
        if (!isset($menu[$id])) {
            set_flash('error', 'That item does not exist.');
            break;
        }
        $item = $menu[$id];
        if ($item['stock'] <= 0) {
            set_flash('error', $item['name'] . ' is sold out.');
            break;
        }
        $qty = max(1, $qty);
        $new = ($cart[$id] ?? 0) + $qty;           // already in cart + new qty
        if ($new > $item['stock']) {
            set_flash('error', 'Only ' . $item['stock'] . ' of ' . $item['name'] . ' available.');
        } elseif ($new > MAX_PER_ITEM) {
            set_flash('error', 'Maximum ' . MAX_PER_ITEM . ' per item allowed.');
        } else {
            $cart[$id] = $new;
            set_flash('success', $item['name'] . ' added to cart.');
        }
        break;

    case 'update':
        if (!isset($cart[$id], $menu[$id])) {
            break;
        }
        if ($qty <= 0) {
            unset($cart[$id]);                      // qty 0 = remove
            set_flash('success', 'Item removed.');
        } elseif ($qty > $menu[$id]['stock'] || $qty > MAX_PER_ITEM) {
            set_flash('error', 'That quantity is not available.');
        } else {
            $cart[$id] = $qty;
            set_flash('success', 'Quantity updated.');
        }
        break;

    case 'remove':
        unset($cart[$id]);
        set_flash('success', 'Item removed.');
        break;

    case 'clear':
        $cart = [];
        set_flash('success', 'Cart cleared.');
        break;
}

$_SESSION['cart'] = $cart;     // save the changed cart back into the session
redirect($back);

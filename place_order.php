<?php
/*
 * place_order.php - FINAL ORDER SUBMISSION (no HTML output).
 * Steps: validate -> lock -> re-check stock -> reduce stock -> save order
 *        -> empty cart -> redirect to confirmation page.
 */
require 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('cart.php');
}

// 1) Validate input
$name = trim($_POST['name'] ?? '');
$note = trim($_POST['note'] ?? '');

if ($name === '' || mb_strlen($name) > 40) {
    set_flash('error', 'Please enter your name (max 40 characters).');
    redirect('cart.php');
}
if (empty(get_cart())) {
    set_flash('error', 'Your cart is empty.');
    redirect('cart.php');
}

// 2) Lock: only ONE person at a time may run the part below.
//    Without this, two students could buy the last brownie together.
$lock = fopen(DATA_DIR . '.lock', 'c');
flock($lock, LOCK_EX);

$menu   = menu_by_id();          // read fresh stock INSIDE the lock
$orders = get_orders();
$items  = [];
$total  = 0;

// 3) Re-check every cart item against current stock
foreach (get_cart() as $id => $qty) {
    if (!isset($menu[$id]) || $menu[$id]['stock'] < $qty) {
        flock($lock, LOCK_UN);
        fclose($lock);
        $itemName = $menu[$id]['name'] ?? 'An item';
        set_flash('error', $itemName . ' is no longer available in that quantity. Please update your cart.');
        redirect('cart.php');
    }
    $menu[$id]['stock'] -= $qty;                 // reduce stock
    $items[] = [
        'id'    => $id,
        'name'  => $menu[$id]['name'],
        'price' => $menu[$id]['price'],
        'qty'   => $qty,
    ];
    $total += $menu[$id]['price'] * $qty;
}

// 4) Create the order with a new order number
$lastId = 0;
foreach ($orders as $o) {
    $lastId = max($lastId, $o['id']);
}
$newId = $lastId + 1;

$order = [
    'id'      => $newId,
    'number'  => 'CN' . str_pad((string)$newId, 3, '0', STR_PAD_LEFT),   // CN001, CN002...
    'name'    => $name,
    'note'    => $note,
    'items'   => $items,
    'total'   => $total,
    'status'  => 'Received',
    'created' => date('Y-m-d H:i:s'),
];
$orders[] = $order;

// 5) Save orders and the updated stock
write_json('orders.json', $orders);
write_json('menu.json', array_values($menu));

flock($lock, LOCK_UN);
fclose($lock);

// 6) Clear the cart and go to confirmation
$_SESSION['cart'] = [];
$_SESSION['last_order'] = $order['number'];
redirect('order_success.php');

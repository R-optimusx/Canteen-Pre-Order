<?php
/*
 * functions.php  -  shared helpers used by EVERY page.
 * Each page does:  require 'includes/functions.php';
 */

// 1) Sessions: must start BEFORE any HTML is printed.
//    $_SESSION is an array that PHP keeps for each visitor (via a cookie).
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2) Constants (settings that never change while the app runs)
define('DATA_DIR', __DIR__ . '/../data/');   // folder that holds our JSON "database"
const MAX_PER_ITEM    = 10;                  // max quantity of one item per order
const ADMIN_PASSWORD  = 'admin123';          // change this!
const STATUSES        = ['Received', 'Preparing', 'Ready', 'Collected'];

/* ---------- Output safety ---------- */
// e() = "escape". Always wrap user/text data before printing in HTML.
// Stops XSS (e.g. a name like <script>alert(1)</script>).
function e($text): string {
    return htmlspecialchars((string)$text, ENT_QUOTES, 'UTF-8');
}

function money($amount): string {
    return '₹' . number_format((float)$amount, 2);
}

/* ---------- Navigation helpers ---------- */
function redirect(string $url): void {
    header('Location: ' . $url);   // tell browser to go to another page
    exit;                          // ALWAYS stop the script after redirect
}

// Flash message = a message stored in the session that shows ONCE.
function set_flash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}
function get_flash(): ?array {
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);     // remove so it is shown only once
    return $flash;
}

/* ---------- JSON file "database" ---------- */
function read_json(string $file): array {
    $path = DATA_DIR . $file;
    if (!file_exists($path)) {
        return [];
    }
    $data = json_decode(file_get_contents($path), true);
    return is_array($data) ? $data : [];
}

function write_json(string $file, array $data): void {
    // LOCK_EX stops two requests writing the file at the same moment
    file_put_contents(DATA_DIR . $file, json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);
}

/* ---------- Menu ---------- */
function get_menu(): array {
    return read_json('menu.json');
}

// Same menu but the array KEY is the item id -> easy lookup: $menu[3]
function menu_by_id(): array {
    $result = [];
    foreach (get_menu() as $item) {
        $result[$item['id']] = $item;
    }
    return $result;
}

/* ---------- Cart (lives in $_SESSION['cart']) ---------- */
// Format:  [ item_id => quantity, ... ]   e.g. [1 => 2, 7 => 1]
function get_cart(): array {
    return $_SESSION['cart'] ?? [];
}

function cart_count(): int {
    return array_sum(get_cart());
}

// Turn the cart into full rows (name, price, qty, subtotal) using the menu.
function cart_lines(): array {
    $menu  = menu_by_id();
    $lines = [];
    foreach (get_cart() as $id => $qty) {
        if (!isset($menu[$id])) {
            continue;                       // item removed from menu -> skip
        }
        $item    = $menu[$id];
        $lines[] = [
            'id'       => $item['id'],
            'name'     => $item['name'],
            'price'    => $item['price'],
            'qty'      => $qty,
            'stock'    => $item['stock'],
            'subtotal' => $item['price'] * $qty,
        ];
    }
    return $lines;
}

function cart_total(array $lines): float {
    $total = 0;
    foreach ($lines as $line) {
        $total += $line['subtotal'];
    }
    return $total;
}

/* ---------- Orders ---------- */
function get_orders(): array {
    return read_json('orders.json');
}

function status_class(string $status): string {
    return 'status-' . strtolower($status);   // used for colour badges in CSS
}

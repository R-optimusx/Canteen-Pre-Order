<?php
/*
 * header.php - top part of every page (<head> + navigation bar).
 * The page that includes it sets these variables first:
 *   $pageTitle  -> text in the browser tab
 *   $pageCss    -> that page's OWN css file
 *   $active     -> which nav link is highlighted
 *   $extraHead  -> (optional) extra tags for <head>, e.g. auto-refresh
 * header.php itself is styled by its own file: css/nav.css
 */
$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle ?? 'Canteen') ?> | Campus Canteen</title>
    <link rel="stylesheet" href="css/nav.css">
    <link rel="stylesheet" href="<?= e($pageCss) ?>">
    <?= $extraHead ?? '' ?>
</head>
<body>
<nav class="topbar">
    <a class="brand" href="index.php">🍽️ Campus Canteen</a>
    <div class="links">
        <a href="index.php" class="<?= ($active ?? '') === 'menu'  ? 'on' : '' ?>">Menu</a>
        <a href="cart.php"  class="<?= ($active ?? '') === 'cart'  ? 'on' : '' ?>">
            Cart <span class="pill"><?= cart_count() ?></span></a>
        <a href="queue.php" class="<?= ($active ?? '') === 'queue' ? 'on' : '' ?>">Queue</a>
        <a href="admin.php" class="<?= ($active ?? '') === 'admin' ? 'on' : '' ?>">Staff</a>
        <a href="workflow.php" target="_blank" rel="noopener" class="<?= ($active ?? '') === 'workflow' ? 'on' : '' ?>">Workflow ↗</a>
    </div>
</nav>
<main class="container">
<?php if ($flash): ?>
    <div class="flash flash-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
<?php endif; ?>

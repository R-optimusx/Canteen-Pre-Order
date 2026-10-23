<?php
/*
 * workflow.php - Project ka workflow samjhane wala page (Sir ko dikhane ke liye).
 * Navbar se naye tab me khulta hai (target="_blank").
 * Is page ka apna CSS: css/workflow.css
 */
require 'includes/functions.php';

$pageTitle = 'Project Workflow';
$pageCss   = 'css/workflow.css';
$active    = 'workflow';

include 'includes/header.php';
?>
<h1>Project Workflow</h1>
<p class="subtitle">Canteen Pre-Order &amp; Queue System &mdash; PHP + Session + JSON</p>

<!-- 1. Customer flow -->
<h2>1. Customer Flow</h2>
<div class="flow">
    <div class="step"><span class="n">1</span><strong>Menu</strong><small>index.php</small><em>Items + availability dekhna</em></div>
    <div class="arrow">➜</div>
    <div class="step"><span class="n">2</span><strong>Add to Cart</strong><small>cart_action.php</small><em>$_SESSION['cart'] me save</em></div>
    <div class="arrow">➜</div>
    <div class="step"><span class="n">3</span><strong>Cart + Total</strong><small>cart.php</small><em>Update / Remove / Total</em></div>
    <div class="arrow">➜</div>
    <div class="step"><span class="n">4</span><strong>Submit Order</strong><small>place_order.php</small><em>Stock check + order save</em></div>
    <div class="arrow">➜</div>
    <div class="step"><span class="n">5</span><strong>Order Number</strong><small>order_success.php</small><em>CN001 + queue position</em></div>
    <div class="arrow">➜</div>
    <div class="step"><span class="n">6</span><strong>Track</strong><small>queue.php</small><em>Status live dekhna</em></div>
</div>

<!-- 2. Staff flow + status -->
<h2>2. Staff Flow &amp; Order Status</h2>
<div class="flow small">
    <div class="step staff"><strong>Staff Login</strong><small>admin.php</small></div>
    <div class="arrow">➜</div>
    <div class="step staff"><strong>Orders dekhna</strong><small>orders.json</small></div>
    <div class="arrow">➜</div>
    <div class="step staff"><strong>Status badalna</strong><small>admin.php</small></div>
</div>
<div class="statuses">
    <span class="status status-received">Received</span><span class="arrow">➜</span>
    <span class="status status-preparing">Preparing</span><span class="arrow">➜</span>
    <span class="status status-ready">Ready</span><span class="arrow">➜</span>
    <span class="status status-collected">Collected</span>
</div>

<!-- 3. Place order internal steps -->
<h2>3. place_order.php ke andar kya hota hai</h2>
<ol class="inner">
    <li>Naam validate karo (khali na ho)</li>
    <li>File <strong>lock</strong> karo (flock) &mdash; ek time pe ek hi order process ho</li>
    <li>Har item ka <strong>stock dobara check</strong> karo</li>
    <li>Stock <strong>kam</strong> karo (menu.json update)</li>
    <li>Naya <strong>order number</strong> banao (CN001, CN002...) &mdash; status = Received</li>
    <li>Order <code>orders.json</code> me save karo</li>
    <li>Session cart <strong>khali</strong> karo &rarr; <code>order_success.php</code> par redirect</li>
</ol>

<!-- 4. Folder structure -->
<h2>4. Folder Structure</h2>
<pre class="tree">canteen/
├── index.php          → Menu + availability        (css/index.css)
├── cart.php           → Cart, total, checkout form (css/cart.css)
├── order_success.php  → Order number + position    (css/success.css)
├── queue.php          → Public live queue          (css/queue.css)
├── admin.php          → Staff login + status       (css/admin.css)
├── workflow.php       → Ye page                    (css/workflow.css)
├── cart_action.php    → add/update/remove/clear (no HTML)
├── place_order.php    → final submission (no HTML)
├── includes/
│   ├── functions.php  → all shared helper functions
│   ├── header.php     → &lt;head&gt; + navbar            (css/nav.css)
│   └── footer.php
└── data/
    ├── menu.json      → items, prices, stock
    └── orders.json    → submitted orders</pre>

<!-- 5. Where data lives -->
<h2>5. Data kahan rehta hai?</h2>
<div class="cols">
    <div class="box"><h3>$_SESSION</h3><p>Current <strong>cart</strong> (<code>[item_id =&gt; qty]</code>), admin login, flash message, last order number.</p></div>
    <div class="box"><h3>menu.json</h3><p>Items, price, category aur <strong>stock</strong> (availability yahin se).</p></div>
    <div class="box"><h3>orders.json</h3><p>Submit hue orders: number, naam, items, total, <strong>status</strong>, time.</p></div>
</div>

<!-- 6. Concepts -->
<h2>6. PHP Concepts Used</h2>
<div class="tags">
    <span>Sessions</span><span>$_POST / $_GET</span><span>include / require</span>
    <span>Functions</span><span>Arrays</span><span>foreach / switch</span>
    <span>htmlspecialchars (XSS)</span><span>header() redirect</span>
    <span>Flash messages</span><span>PRG pattern</span><span>JSON file storage</span>
    <span>flock (file locking)</span><span>Input validation</span><span>Separate CSS per page</span>
</div>

<?php include 'includes/footer.php'; ?>

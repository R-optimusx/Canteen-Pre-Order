# Canteen Pre-Order & Queue System (PHP)

Run:  cd canteen && php -S localhost:8000   -> open http://localhost:8000
(or copy the folder to XAMPP/WAMP htdocs and open http://localhost/canteen/)

Staff password: admin123   (change ADMIN_PASSWORD in includes/functions.php)
The data/ folder must be writable (menu.json, orders.json, .lock).

Page -> CSS map
  includes/header.php + footer.php -> css/nav.css
  index.php          -> css/index.css
  cart.php           -> css/cart.css
  order_success.php  -> css/success.css
  queue.php          -> css/queue.css
  admin.php          -> css/admin.css
  cart_action.php, place_order.php -> no HTML output, so no CSS

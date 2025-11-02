<?php
include '../includes/auth.php';
include '../config/database.php';
include '../includes/functions.php';
redirectIfNotLoggedIn();

$user_id = $_SESSION['user_id'];

// Get user's orders
$stmt = $pdo->prepare("SELECT * FROM orders_tbl WHERE user_id = ? ORDER BY order_date DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - eStore</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .customer-layout {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 250px;
            background-color: #343a40;
            color: white;
            padding: 20px 0;
        }
        .sidebar .nav-brand {
            padding: 0 20px 20px;
            border-bottom: 1px solid #495057;
            margin-bottom: 20px;
            color: #007bff;
        }
        .sidebar .nav-brand h1 {
            margin: 0;
            font-size: 24px;
            color: #007bff;
        }
        .sidebar-nav {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .sidebar-nav li {
            margin-bottom: 5px;
        }
        .sidebar-nav a {
            display: block;
            padding: 12px 20px;
            color: #adb5bd;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        .sidebar-nav a:hover,
        .sidebar-nav a.active {
            background-color: #495057;
            color: white;
        }
        .sidebar-nav .user-greeting {
            padding: 12px 20px;
            color: #6c757d;
            font-weight: bold;
            border-bottom: 1px solid #495057;
            margin-bottom: 10px;
        }
        .main-content {
            flex: 1;
            padding: 20px;
            background-color: #fff;
        }
        .container {
            max-width: none;
            margin: 0;
            padding: 0;
        }
        @media (max-width: 768px) {
            .customer-layout {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
                border-right: none;
                border-bottom: 1px solid #dee2e6;
            }
        }
    </style>
</head>
<body>
    <div class="customer-layout">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="nav-brand">
                <img src="https://png.pngtree.com/png-clipart/20230816/original/pngtree-foot-logo-template-white-female-foot-vector-picture-image_10876165.png" alt="eStore Logo" style="width: 40px; height: 40px; margin-right: 10px; vertical-align: middle;">
                <h1 style="display: inline; margin: 0;">eStore</h1>
            </div>
            <div class="user-greeting">
                Welcome, <?php echo htmlspecialchars($_SESSION['first_name']); ?>!
            </div>
            <ul class="sidebar-nav">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="products.php" class="btn btn-primary">Browse Products</a></li>
                <li><a href="cart.php">View Cart
                    <?php
                    $cart_count = 0;
                    if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
                        $cart_count = array_sum($_SESSION['cart']);
                    }
                    if ($cart_count > 0) {
                        echo " (" . $cart_count . ")";
                    }
                    ?>
                </a></li>
                <li><a href="orders.php" class="active">My Orders</a></li>
                <li><a href="settings.php">Account Settings</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="container">
                <h2>My Orders</h2>

                <?php if (count($orders) > 0): ?>
                    <div class="orders-list">
                        <?php foreach ($orders as $order): ?>
                            <div class="order-card">
                                <h3>Order #<?php echo $order['order_id']; ?></h3>
                                <p><strong>Date:</strong> <?php echo date('F j, Y, g:i a', strtotime($order['order_date'])); ?></p>
                                <p><strong>Total:</strong> <?php echo formatPrice($order['total_amount']); ?></p>
                                <p><strong>Status:</strong> <span class="status status-<?php echo strtolower($order['status']); ?>"><?php echo $order['status']; ?></span></p>

                                <!-- Order Items -->
                                <?php
                                $stmt_items = $pdo->prepare("SELECT oi.*, p.product_name FROM order_items_tbl oi JOIN products_tbl p ON oi.product_id = p.product_id WHERE oi.order_id = ?");
                                $stmt_items->execute([$order['order_id']]);
                                $order_items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);
                                ?>

                                <div class="order-items">
                                    <h4>Items:</h4>
                                    <?php foreach ($order_items as $item): ?>
                                        <div class="order-item">
                                            <span><?php echo $item['product_name']; ?> (Qty: <?php echo $item['quantity']; ?>)</span>
                                            <span><?php echo formatPrice($item['price_at_purchase']); ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>You haven't placed any orders yet.</p>
                    <a href="products.php" class="btn">Start Shopping</a>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>

<?php
include '../includes/auth.php';
include '../config/database.php';
redirectIfNotLoggedIn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - eStore</title>
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
            border-bottom: 1px solid #286cafff;
            margin-bottom: 20px;
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
                <a href="../index.php" style="text-decoration: none; color: inherit; display: inline-block;">
                    <img src="https://png.pngtree.com/png-clipart/20230816/original/pngtree-foot-logo-template-white-female-foot-vector-picture-image_10876165.png" alt="eStore Logo" style="width: 40px; height: 40px; margin-right: 10px; vertical-align: middle;">
                    <h1 style="display: inline; margin: 0;">eStore</h1>
                </a>
            </div>
            <div class="user-greeting">
                Welcome, User<?php echo htmlspecialchars($_SESSION['first_name']); ?>!
            </div>
            <ul class="sidebar-nav">
                <li><a href="dashboard.php" class="active">Dashboard</a></li>
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
                <li><a href="orders.php">My Orders</a></li>
                <li><a href="settings.php">Account Settings</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="container">
                <h2>Welcome back, <?php echo htmlspecialchars($_SESSION['first_name']); ?>!</h2>
                <p>Manage your account and explore our latest products.</p>

                <div class="dashboard-grid">
                    <!-- Quick Actions -->
                    <div class="dashboard-card">
                        <h3>Quick Actions</h3>
                        <div class="dashboard-menu">
                            <a href="products.php" class="btn btn-primary">Start Shopping</a>
                        </div>
                    </div>

                    <!-- Recent Orders -->
                    <div class="dashboard-card">
                        <h3>Recent Orders</h3>
                        <?php
                        $user_id = $_SESSION['user_id'];
                        $stmt = $pdo->prepare("SELECT * FROM orders_tbl WHERE user_id = ? ORDER BY Order_Date DESC LIMIT 5");
                        $stmt->execute([$user_id]);
                        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        if (count($orders) > 0) {
                            echo "<div class='orders-list'>";
                            foreach ($orders as $order) {
                                echo "<div class='order-item'>";
                                echo "<div class='order-info'>";
                                echo "<span class='order-id'>Order #{$order['order_id']}</span>";
                                echo "<span class='order-amount'>$" . number_format($order['total_amount'], 2) . "</span>";
                                echo "</div>";
                                echo "<div class='order-status status-{$order['status']}'>{$order['status']}</div>";
                                echo "</div>";
                            }
                            echo "</div>";
                            echo "<a href='orders.php' class='btn btn-primary'>View Recent Orders</a>";
                        } else {
                            echo "<div class='no-orders'>";
                            echo "<p>You haven't placed any orders yet.</p>";
                            echo "<a href='orders.php' class='btn btn-primary'>View Recent Orders</a>";
                            echo "</div>";
                        }
                        ?>
                    </div>

                    <!-- Account Summary -->
                    <div class="dashboard-card">
                        <h3>Account Summary</h3>
                        <div class="account-info">
                            <p><strong>Name:</strong> <?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . ($_SESSION['last_name'] ?? '')); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['username']); ?></p>
                            <p><strong>Member Since:</strong>
                                <?php
                                $stmt = $pdo->prepare("SELECT DATE_FORMAT(created_at, '%M %Y') as member_since FROM users_tbl WHERE user_id = ?");
                                $stmt->execute([$user_id]);
                                $user_info = $stmt->fetch(PDO::FETCH_ASSOC);
                                echo $user_info ? htmlspecialchars($user_info['member_since']) : 'Recently';
                                ?>
                            </p>
                        </div>
                        <a href="settings.php" class="btn">Edit Profile</a>
                    </div>

                    <!-- Shopping Stats -->
                    <div class="dashboard-card">
                        <h3>Shopping Stats</h3>
                        <div class="stats-grid">
                            <?php
                            // Total orders
                            $stmt = $pdo->prepare("SELECT COUNT(*) as total_orders FROM orders_tbl WHERE user_id = ?");
                            $stmt->execute([$user_id]);
                            $total_orders = $stmt->fetch(PDO::FETCH_ASSOC)['total_orders'];

                            // Total spent
                            $stmt = $pdo->prepare("SELECT SUM(Total_Amount) as total_spent FROM orders_tbl WHERE user_id = ? AND Status = 'completed'");
                            $stmt->execute([$user_id]);
                            $total_spent = $stmt->fetch(PDO::FETCH_ASSOC)['total_spent'] ?? 0;

                            // Favorite category (most ordered)
                            $stmt = $pdo->prepare("
                                SELECT p.category, COUNT(oi.product_id) as order_count
                                FROM order_items_tbl oi
                                JOIN orders_tbl o ON oi.order_id = o.order_id
                                JOIN products_tbl p ON oi.product_id = p.product_id
                                WHERE o.user_id = ?
                                GROUP BY p.category
                                ORDER BY order_count DESC
                                LIMIT 1
                            ");
                            $stmt->execute([$user_id]);
                            $favorite_category = $stmt->fetch(PDO::FETCH_ASSOC);
                            ?>
                            <div class="stat-item">
                                <span class="stat-number"><?php echo $total_orders; ?></span>
                                <span class="stat-label">Total Orders</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number">$<?php echo number_format($total_spent, 0); ?></span>
                                <span class="stat-label">Total Spent</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number"><?php echo $favorite_category ? htmlspecialchars($favorite_category['category']) : 'None'; ?></span>
                                <span class="stat-label">Favorite Category</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>

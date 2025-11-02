<?php
include '../includes/auth.php';
include '../config/database.php';
redirectIfNotAdmin();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - eStore</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .admin-layout {
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
            .admin-layout {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
                border-right: none;
                border-bottom: 1px solid #495057;
            }
        }
        .charts-container {
            margin: 30px 0;
        }
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .chart-card {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .chart-card h4 {
            margin-bottom: 15px;
            color: #333;
            font-size: 18px;
        }
        .chart-card canvas {
            max-height: 300px;
        }
    </style>
</head>
<body>
    <div class="admin-layout">
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
                <li><a href="dashboard.php" class="active">Dashboard</a></li>
                <li><a href="products.php">Manage Products</a></li>
                <li><a href="orders.php">Manage Orders</a></li>
                <li><a href="users.php">Manage Users</a></li>
                <li><a href="../customer/logout.php">Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="container">
                <h2>Admin Dashboard</h2>

                <div class="dashboard-stats">
                    <?php
                    // Get statistics
                    $total_products = $pdo->query("SELECT COUNT(*) FROM products_tbl")->fetchColumn();
                    $total_orders = $pdo->query("SELECT COUNT(*) FROM orders_tbl")->fetchColumn();
                    $total_users = $pdo->query("SELECT COUNT(*) FROM users_tbl WHERE role = 'customer'")->fetchColumn();
                    $total_revenue = $pdo->query("SELECT COALESCE(SUM(Total_Amount), 0) FROM orders_tbl WHERE Status = 'completed'")->fetchColumn();
                    ?>

                    <div class="stat-card">
                        <h3>Total Products</h3>
                        <p><?php echo $total_products; ?></p>
                    </div>
                    <div class="stat-card">
                        <h3>Total Orders</h3>
                        <p><?php echo $total_orders; ?></p>
                    </div>
                    <div class="stat-card">
                        <h3>Total Customers</h3>
                        <p><?php echo $total_users; ?></p>
                    </div>
                    <div class="stat-card">
                        <h3>Total Revenue</h3>
                        <p>$<?php echo number_format($total_revenue, 2); ?></p>
                    </div>
                </div>

                <!-- Analytics Charts -->
                <div class="charts-container">
                    <h3>Analytics Overview</h3>
                    <div class="charts-grid">
                        <div class="chart-card">
                            <h4>Statistics Breakdown</h4>
                            <canvas id="statsPieChart"></canvas>
                        </div>
                        <div class="chart-card">
                            <h4>Revenue Trend (Last 7 Days)</h4>
                            <canvas id="revenueLineChart"></canvas>
                        </div>
                        <div class="chart-card">
                            <h4>Orders vs Customers</h4>
                            <canvas id="ordersBarChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="recent-orders">
                    <h3>Recent Orders</h3>
                    <?php
                    $stmt = $pdo->query("SELECT o.*, u.username FROM orders_tbl o JOIN users_tbl u ON o.user_id = u.user_id ORDER BY o.Order_Date DESC LIMIT 5");
                    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if (count($orders) > 0) {
                        foreach ($orders as $order) {
                            echo "<div class='order-item'>";
                            echo "<p>Order #{$order['order_id']} by {$order['username']} - {$order['total_amount']} - {$order['status']}</p>";
                            echo "</div>";
                        }
                    } else {
                        echo "<p>No recent orders</p>";
                    }
                    ?>
                </div>
            </div>
        </main>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script>
        // Pie Chart for Statistics Breakdown
        const statsPieCtx = document.getElementById('statsPieChart').getContext('2d');
        const statsPieChart = new Chart(statsPieCtx, {
            type: 'pie',
            data: {
                labels: ['Products', 'Orders', 'Customers'],
                datasets: [{
                    data: [<?php echo $total_products; ?>, <?php echo $total_orders; ?>, <?php echo $total_users; ?>],
                    backgroundColor: [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56'
                    ],
                    hoverBackgroundColor: [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });

        // Line Chart for Revenue Trend (Last 7 Days)
        const revenueLineCtx = document.getElementById('revenueLineChart').getContext('2d');
        const revenueLineChart = new Chart(revenueLineCtx, {
            type: 'line',
            data: {
                labels: ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5', 'Day 6', 'Day 7'],
                datasets: [{
                    label: 'Revenue ($)',
                    data: [<?php
                        // Get revenue for last 7 days
                        $revenue_data = [];
                        for ($i = 6; $i >= 0; $i--) {
                            $date = date('Y-m-d', strtotime("-$i days"));
                            $stmt = $pdo->prepare("SELECT COALESCE(SUM(Total_Amount), 0) FROM orders_tbl WHERE DATE(Order_Date) = ? AND Status = 'completed'");
                            $stmt->execute([$date]);
                            $revenue_data[] = $stmt->fetchColumn();
                        }
                        echo implode(',', $revenue_data);
                    ?>],
                    borderColor: '#4BC0C0',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Bar Chart for Orders vs Customers
        const ordersBarCtx = document.getElementById('ordersBarChart').getContext('2d');
        const ordersBarChart = new Chart(ordersBarCtx, {
            type: 'bar',
            data: {
                labels: ['Orders', 'Customers'],
                datasets: [{
                    label: 'Count',
                    data: [<?php echo $total_orders; ?>, <?php echo $total_users; ?>],
                    backgroundColor: [
                        '#FF6384',
                        '#36A2EB'
                    ],
                    borderColor: [
                        '#FF6384',
                        '#36A2EB'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
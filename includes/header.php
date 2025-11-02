<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eStore - Shoe E-commerce</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <header>
        <nav>
            <div class="nav-brand">
                <h1><a href="../index.php">eStore</a></h1>
            </div>
            <ul class="nav-menu">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><span>Welcome, <?php echo htmlspecialchars($_SESSION['first_name']); ?>!</span></li>

                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <li><a href="../admin/dashboard.php">Admin Dashboard</a></li>
                        <li><a href="../admin/products.php">Manage Products</a></li>
                        <li><a href="../admin/orders.php">Manage Orders</a></li>
                        <li><a href="../admin/users.php">Manage Users</a></li>
                    <?php else: ?>
                        <li><a href="../customer/products.php" class="btn btn-primary">Browse Products</a></li>
                        <li><a href="../customer/cart.php" class="btn">View Cart
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
                        <li><a href="../customer/orders.php" class="btn">My Orders</a></li>
                        <li><a href="../customer/settings.php" class="btn">Account Settings</a></li>
                    <?php endif; ?>

                    <li><a href="../customer/logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="../index.php">Home</a></li>
                    <li><a href="../login.php">Login</a></li>
                    <li><a href="../register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>

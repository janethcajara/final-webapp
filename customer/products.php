<?php
include '../includes/auth.php';
include '../config/database.php';
include '../includes/functions.php';
redirectIfNotLoggedIn();

// Add to cart functionality
if (isset($_POST['add_to_cart'])) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }

    header("Location: products.php?success=Product added to cart");
    exit();
}

// Get products
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

$query = "SELECT * FROM products_tbl WHERE stock_quantity > 0";
$params = [];

if (!empty($search)) {
    $query .= " AND (product_name LIKE ? OR description LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
}

if (!empty($category)) {
    $query .= " AND category = ?";
    $params[] = $category;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - eStore</title>
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
                <li><a href="products.php" class="active">Browse Products</a></li>
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
                <h2>Browse Products</h2>

                <!-- Search and Filter -->
                <form method="GET" class="search-form">
                    <input type="text" name="search" id="search-input" placeholder="Search products..." value="<?php echo $search; ?>">
                    <select name="category" id="category-select">
                        <option value="">All Categories</option>
                        <option value="Running" <?php echo $category == 'Running' ? 'selected' : ''; ?>>Running</option>
                        <option value="Casual" <?php echo $category == 'Casual' ? 'selected' : ''; ?>>Casual</option>
                        <option value="Sports" <?php echo $category == 'Sports' ? 'selected' : ''; ?>>Sports</option>
                    </select>
                    <button type="submit">Filter</button>
                </form>

                <?php if (isset($_GET['success'])) echo "<div class='success'>".$_GET['success']."</div>"; ?>

                <div class="products-grid" id="products-container">
                    <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <?php if (!empty($product['image'])): ?>
                            <img src="../assets/images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" style="width: 100%; height: 200px; object-fit: cover; border-radius: 8px; margin-bottom: 10px;">
                        <?php endif; ?>
                        <h3><?php echo $product['product_name']; ?></h3>
                        <p><?php echo $product['description']; ?></p>
                        <p>Category: <?php echo $product['category']; ?></p>
                        <p>Size: <?php echo $product['size']; ?></p>
                        <p>Color: <?php echo $product['color']; ?></p>
                        <p class="price"><?php echo formatPrice($product['price']); ?></p>
                        <p>Stock: <?php echo $product['stock_quantity']; ?></p>

                        <form method="POST">
                            <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                            <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['stock_quantity']; ?>">
                            <button type="submit" name="add_to_cart">Add to Cart</button>
                        </form>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </main>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script>
        // Add loading and error styles
        const style = document.createElement('style');
        style.textContent = `
            .loading {
                text-align: center;
                padding: 40px;
                font-size: 18px;
                color: #666;
            }
            .error {
                text-align: center;
                padding: 40px;
                font-size: 16px;
                color: #d9534f;
                background-color: #f2dede;
                border: 1px solid #ebccd1;
                border-radius: 4px;
                margin: 20px 0;
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>

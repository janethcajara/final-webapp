<?php
include '../includes/auth.php';
include '../config/database.php';
include '../includes/functions.php';
redirectIfNotAdmin();

// Create product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_product'])) {
    $name = sanitizeInput($_POST['product_name']);
    $description = sanitizeInput($_POST['description']);
    $category = sanitizeInput($_POST['category']);
    $size = sanitizeInput($_POST['size']);
    $color = sanitizeInput($_POST['color']);
    $price = $_POST['price'];
    $stock = $_POST['stock_quantity'];

    $stmt = $pdo->prepare("INSERT INTO products_tbl (product_name, description, category, size, color, price, stock_quantity) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $description, $category, $size, $color, $price, $stock]);

    if ($stmt->execute()) {
        header("Location: products.php?success=Product created successfully");
        exit();
    } else {
        $error = "Failed to create product";
    }
}

// Update product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    $product_id = $_POST['product_id'];
    $name = sanitizeInput($_POST['product_name']);
    $description = sanitizeInput($_POST['description']);
    $category = sanitizeInput($_POST['category']);
    $size = sanitizeInput($_POST['size']);
    $color = sanitizeInput($_POST['color']);
    $price = $_POST['price'];
    $stock = $_POST['stock_quantity'];

    $stmt = $pdo->prepare("UPDATE products_tbl SET product_name=?, description=?, category=?, size=?, color=?, price=?, stock_quantity=? WHERE product_id=?");
    $stmt->execute([$name, $description, $category, $size, $color, $price, $stock, $product_id]);

    if ($stmt->execute()) {
        header("Location: products.php?success=Product updated successfully");
        exit();
    } else {
        $error = "Failed to update product";
    }
}

// Delete product
if (isset($_GET['delete'])) {
    $product_id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM products_tbl WHERE product_id = ?");
    $stmt->execute([$product_id]);

    if ($stmt->execute()) {
        header("Location: products.php?success=Product deleted successfully");
        exit();
    } else {
        $error = "Failed to delete product";
    }
}

// Get all products
$products = $pdo->query("SELECT * FROM products_tbl ORDER BY product_id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - eStore</title>
    <link rel="stylesheet" href="../assets/css/style.css">
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
                Welcome, Admin <?php echo htmlspecialchars($_SESSION['first_name']); ?>!
            </div>
            <ul class="sidebar-nav">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="products.php" class="active">Manage Products</a></li>
                <li><a href="orders.php">Manage Orders</a></li>
                <li><a href="users.php">Manage Users</a></li>
                <li><a href="../customer/logout.php">Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="container">
                <h2>Manage Products</h2>

                <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>
                <?php if (isset($_GET['success'])) echo "<div class='success'>".$_GET['success']."</div>"; ?>

                <!-- Create Product Form -->
                <div class="form-section">
                    <h3>Add New Product</h3>
                    <form method="POST">
                        <input type="text" name="product_name" placeholder="Product Name" required>
                        <textarea name="description" placeholder="Description" required></textarea>
                        <input type="text" name="category" placeholder="Category" required>
                        <input type="text" name="size" placeholder="Size" required>
                        <input type="text" name="color" placeholder="Color" required>
                        <input type="number" step="0.01" name="price" placeholder="Price" required>
                        <input type="number" name="stock_quantity" placeholder="Stock Quantity" required>
                        <button type="submit" name="create_product" class="btn">Add Product</button>
                    </form>
                </div>

                <!-- Products List -->
                <div class="products-list">
                    <h3>All Products</h3>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($product = $products->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?php echo $product['product_id']; ?></td>
                                <td><?php echo $product['product_name']; ?></td>
                                <td><?php echo $product['category']; ?></td>
                                <td><?php echo formatPrice($product['price']); ?></td>
                                <td><?php echo $product['stock_quantity']; ?></td>
                                <td>
                                    <a href="?edit=<?php echo $product['product_id']; ?>" class="btn btn-sm">Edit</a>
                                    <a href="?delete=<?php echo $product['product_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                                </td>
                            </tr>

                            <!-- Edit Form -->
                            <?php if (isset($_GET['edit']) && $_GET['edit'] == $product['product_id']): ?>
                            <tr>
                                <td colspan="6">
                                    <form method="POST" class="edit-form">
                                        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                        <input type="text" name="product_name" value="<?php echo $product['product_name']; ?>" required>
                                        <textarea name="description" required><?php echo $product['description']; ?></textarea>
                                        <input type="text" name="category" value="<?php echo $product['category']; ?>" required>
                                        <input type="text" name="size" value="<?php echo $product['size']; ?>" required>
                                        <input type="text" name="color" value="<?php echo $product['color']; ?>" required>
                                        <input type="number" step="0.01" name="price" value="<?php echo $product['price']; ?>" required>
                                        <input type="number" name="stock_quantity" value="<?php echo $product['stock_quantity']; ?>" required>
                                        <button type="submit" name="update_product" class="btn btn-sm">Update</button>
                                        <a href="products.php" class="btn btn-sm btn-danger">Cancel</a>
                                    </form>
                                </td>
                            </tr>
                            <?php endif; ?>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>

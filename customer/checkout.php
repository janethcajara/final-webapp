<?php
include '../includes/auth.php';
include '../config/database.php';
include '../includes/functions.php';
redirectIfNotLoggedIn();

// Get user address
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users_tbl WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Calculate cart total
$total = 0;
$cart_items = [];

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        $product = getProductById($product_id, $conn);
        if ($product && $product['stock_quantity'] >= $quantity) {
            $subtotal = $product['price'] * $quantity;
            $total += $subtotal;
            $cart_items[] = [
                'product' => $product,
                'quantity' => $quantity,
                'subtotal' => $subtotal
            ];
        }
    }
}

// Process checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($cart_items)) {
    // Create order with GCash payment method
    $payment_method = 'GCash';
    $street = sanitizeInput($_POST['street'] ?? '');
    $barangay = sanitizeInput($_POST['barangay'] ?? '');
    $city = sanitizeInput($_POST['city'] ?? '');
    $province = sanitizeInput($_POST['province'] ?? '');
    $zipcode = sanitizeInput($_POST['zipcode'] ?? '');

    $pdo->beginTransaction();

    try {
        // Insert order
        $stmt = $pdo->prepare("INSERT INTO orders_tbl (total_amount, user_id, payment_method) VALUES (?, ?, ?)");
        $stmt->execute([$total, $user_id, $payment_method]);
        $order_id = $pdo->lastInsertId();

        // Insert order items and update stock
        foreach ($cart_items as $item) {
            $product_id = $item['product']['product_id'];
            $quantity = $item['quantity'];
            $price = $item['product']['price'];

            // Insert order item
            $stmt = $pdo->prepare("INSERT INTO order_items_tbl (quantity, price_at_purchase, order_id, product_id) VALUES (?, ?, ?, ?)");
            $stmt->execute([$quantity, $price, $order_id, $product_id]);

            // Update stock
            $stmt = $pdo->prepare("UPDATE products_tbl SET stock_quantity = stock_quantity - ? WHERE product_id = ?");
            $stmt->execute([$quantity, $product_id]);
        }

        $pdo->commit();
        unset($_SESSION['cart']);
        header("Location: orders.php?success=Order placed successfully with GCash");
        exit();

    } catch (Exception $e) {
        $pdo->rollback();
        $error = "Order failed: " . $e->getMessage();
    }
}

if (empty($cart_items)) {
    header("Location: cart.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - eStore</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
        <h2>Checkout</h2>
        
        <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>

        <div class="checkout-container">
            <div class="order-summary">
                <h3>Order Summary</h3>
                <?php foreach ($cart_items as $item): ?>
                <div class="order-item">
                    <p><?php echo $item['product']['product_name']; ?> x <?php echo $item['quantity']; ?></p>
                    <p><?php echo formatPrice($item['subtotal']); ?></p>
                </div>
                <?php endforeach; ?>
                <div class="order-total">
                    <h3>Total: <?php echo formatPrice($total); ?></h3>
                </div>
            </div>

            <div class="checkout-form">
                <form method="POST">
                    <h3>Shipping Address</h3>
                    <input type="text" name="street" placeholder="Street" value="<?php echo $user['street'] ?? ''; ?>" required>
                    <input type="text" name="barangay" placeholder="Barangay" value="<?php echo $user['barangay'] ?? ''; ?>" required>
                    <input type="text" name="city" placeholder="City" value="<?php echo $user['city'] ?? ''; ?>" required>
                    <input type="text" name="province" placeholder="Province" value="<?php echo $user['province'] ?? ''; ?>" required>
                    <input type="text" name="zipcode" placeholder="Zipcode" value="<?php echo $user['zipcode'] ?? ''; ?>" required>
                    
                    <h3>Payment Method</h3>
                    <p><strong>GCash</strong></p>
                    <input type="hidden" name="payment_method" value="GCash">

                    <button type="submit" class="btn btn-primary">Proceed to Checkout</button>
                </form>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>
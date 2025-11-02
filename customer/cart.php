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

// Process order placement from modals
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $payment_method = 'GCash';
    $street = sanitizeInput($_POST['street']);
    $barangay = sanitizeInput($_POST['barangay']);
    $city = sanitizeInput($_POST['city']);
    $province = sanitizeInput($_POST['province']);
    $zipcode = sanitizeInput($_POST['zipcode']);

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

// Update cart quantities
if (isset($_POST['update_cart'])) {
    $selected_items = isset($_POST['selected_items']) ? $_POST['selected_items'] : [];
    foreach ($selected_items as $product_id) {
        if (isset($_POST['quantities'][$product_id])) {
            $quantity = $_POST['quantities'][$product_id];
            if ($quantity <= 0) {
                unset($_SESSION['cart'][$product_id]);
            } else {
                $_SESSION['cart'][$product_id] = $quantity;
            }
        }
    }
    header("Location: cart.php");
    exit();
}

// Remove from cart
if (isset($_GET['remove'])) {
    $product_id = $_GET['remove'];
    unset($_SESSION['cart'][$product_id]);
    header("Location: cart.php");
    exit();
}

// Clear cart (for cancellation)
if (isset($_POST['clear_cart'])) {
    unset($_SESSION['cart']);
    header("Location: cart.php");
    exit();
}

// Process checkout directly from cart
if (isset($_POST['checkout_direct'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT * FROM users_tbl WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    // Calculate total again for checkout
    $checkout_total = 0;
    $checkout_items = [];
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $product_id => $quantity) {
            $product = getProductById($product_id, $conn);
            if ($product && $product['stock_quantity'] >= $quantity) {
                $subtotal = $product['price'] * $quantity;
                $checkout_total += $subtotal;
                $checkout_items[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal' => $subtotal
                ];
            }
        }
    }

    $conn->begin_transaction();

    try {
        // Insert order with GCash payment method
        $stmt = $conn->prepare("INSERT INTO orders_tbl (Total_Amount, user_id, Payment_Method) VALUES (?, ?, 'GCash')");
        $stmt->bind_param("di", $checkout_total, $user_id);
        $stmt->execute();
        $order_id = $conn->insert_id;

        // Insert order items and update stock
        foreach ($checkout_items as $item) {
            $product_id = $item['product']['product_id'];
            $quantity = $item['quantity'];
            $price = $item['product']['price'];

            // Insert order item
            $stmt = $conn->prepare("INSERT INTO order_items_tbl (Quantity, PriceAtPurchase, order_id, product_id) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("idii", $quantity, $price, $order_id, $product_id);
            $stmt->execute();

            // Update stock
            $stmt = $conn->prepare("UPDATE products_tbl SET stock_quantity = stock_quantity - ? WHERE product_id = ?");
            $stmt->bind_param("ii", $quantity, $product_id);
            $stmt->execute();
        }

        $conn->commit();
        unset($_SESSION['cart']);
        header("Location: orders.php?success=Order placed successfully with GCash");
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        $error = "Order failed: " . $e->getMessage();
    }
}

// Partial cancellation
if (isset($_POST['cancel_items'])) {
    $items_to_cancel = $_POST['cancel_items'];
    $reason = $_POST['cancel_reason'];
    $additional_reason = isset($_POST['other_reason']) ? $_POST['other_reason'] : '';

    foreach ($items_to_cancel as $product_id) {
        unset($_SESSION['cart'][$product_id]);
    }

    // Log cancellation reason (you can save this to database if needed)
    // For now, just redirect back
    header("Location: cart.php");
    exit();
}

// Calculate total
$total = 0;
$cart_items = [];

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        $product = getProductById($product_id, $conn);
        if ($product) {
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - eStore</title>
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
                <li><a href="products.php" class="btn btn-primary">Browse Products</a></li>
                <li><a href="cart.php" class="active">View Cart
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
                <h2>Shopping Cart</h2>

                <?php if (empty($cart_items)): ?>
                    <p>Your cart is empty</p>
                    <a href="products.php" class="btn">Continue Shopping</a>
                <?php else: ?>
                    <form method="POST">
                        <table class="cart-table">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="select-all" checked></th>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cart_items as $item): ?>
                                <tr>
                                    <td><input type="checkbox" name="selected_items[]" value="<?php echo $item['product']['product_id']; ?>" checked></td>
                                    <td><?php echo $item['product']['product_name']; ?></td>
                                    <td><?php echo formatPrice($item['product']['price']); ?></td>
                                    <td>
                                        <input type="number" name="quantities[<?php echo $item['product']['product_id']; ?>]"
                                               value="<?php echo $item['quantity']; ?>" min="1"
                                               max="<?php echo $item['product']['stock_quantity']; ?>">
                                    </td>
                                    <td><?php echo formatPrice($item['subtotal']); ?></td>
                                    <td>
                                        <a href="?remove=<?php echo $item['product']['product_id']; ?>" class="btn btn-danger">Remove</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <div class="cart-total">
                            <h3>Total: <?php echo formatPrice($total); ?></h3>
                        </div>

                        <div class="cart-actions">
                            <button type="button" id="checkout-btn" class="btn btn-primary">Proceed to Checkout</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- Checkout Confirmation Modal -->
    <div id="checkout-modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>Confirm Checkout</h3>
            <p>Are you sure you want to proceed with the checkout?</p>
            <p><strong>Total Amount: <?php echo formatPrice($total); ?></strong></p>
            <div class="modal-actions">
                <button id="confirm-checkout" class="btn btn-primary">Yes, Proceed</button>
            </div>
        </div>
    </div>

    <!-- Shipping Address Modal -->
    <div id="shipping-modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>Shipping Address</h3>
            <form id="shipping-form">
                <input type="text" name="street" placeholder="Street" value="<?php echo $user['street'] ?? ''; ?>" required>
                <input type="text" name="barangay" placeholder="Barangay" value="<?php echo $user['barangay'] ?? ''; ?>" required>
                <input type="text" name="city" placeholder="City" value="<?php echo $user['city'] ?? ''; ?>" required>
                <input type="text" name="province" placeholder="Province" value="<?php echo $user['province'] ?? ''; ?>" required>
                <input type="text" name="zipcode" placeholder="Zipcode" value="<?php echo $user['zipcode'] ?? ''; ?>" required>
                <div class="modal-actions">
                    <button type="submit" class="btn btn-primary">Proceed to Payment</button>
                </div>
            </form>
        </div>
    </div>

    <!-- GCash Payment Modal -->
    <div id="gcash-modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>Payment via GCash</h3>
            <p>Confirm payment with GCash.</p>
            <p><strong>Total Amount: <?php echo formatPrice($total); ?></strong></p>
            <div class="modal-actions">
                <button id="confirm-gcash" class="btn btn-primary">Pay with GCash</button>
            </div>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>

    <script>
        // Modal functionality
        const checkoutModal = document.getElementById('checkout-modal');
        const shippingModal = document.getElementById('shipping-modal');
        const gcashModal = document.getElementById('gcash-modal');
        const cancelModal = document.getElementById('cancel-modal');
        const checkoutBtn = document.getElementById('checkout-btn');
        const cancelBtn = document.getElementById('cancel-btn');
        const closeButtons = document.querySelectorAll('.close, .close-modal');
        const confirmCheckout = document.getElementById('confirm-checkout');
        const shippingForm = document.getElementById('shipping-form');
        const confirmGcash = document.getElementById('confirm-gcash');
        const cancelForm = document.getElementById('cancel-form');
        const otherReason = document.getElementById('other-reason');
        const otherRadio = document.querySelector('input[value="Other"]');

        // Show checkout modal
        checkoutBtn.onclick = function() {
            checkoutModal.style.display = 'block';
            setTimeout(() => {
                checkoutModal.classList.add('show');
            }, 10);
        }

        // Show cancel modal
        cancelBtn.onclick = function() {
            cancelModal.style.display = 'block';
            setTimeout(() => {
                cancelModal.classList.add('show');
            }, 10);
        }

        // Close modals
        closeButtons.forEach(btn => {
            btn.onclick = function() {
                checkoutModal.classList.remove('show');
                shippingModal.classList.remove('show');
                gcashModal.classList.remove('show');
                cancelModal.classList.remove('show');
                setTimeout(() => {
                    checkoutModal.style.display = 'none';
                    shippingModal.style.display = 'none';
                    gcashModal.style.display = 'none';
                    cancelModal.style.display = 'none';
                }, 300);
            }
        });

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target == checkoutModal) {
                checkoutModal.classList.remove('show');
                setTimeout(() => {
                    checkoutModal.style.display = 'none';
                }, 300);
            }
            if (event.target == shippingModal) {
                shippingModal.classList.remove('show');
                setTimeout(() => {
                    shippingModal.style.display = 'none';
                }, 300);
            }
            if (event.target == gcashModal) {
                gcashModal.classList.remove('show');
                setTimeout(() => {
                    gcashModal.style.display = 'none';
                }, 300);
            }
            if (event.target == cancelModal) {
                cancelModal.classList.remove('show');
                setTimeout(() => {
                    cancelModal.style.display = 'none';
                }, 300);
            }
        }

        // Confirm checkout - proceed to shipping
        confirmCheckout.onclick = function() {
            checkoutModal.classList.remove('show');
            setTimeout(() => {
                checkoutModal.style.display = 'none';
                shippingModal.style.display = 'block';
                setTimeout(() => {
                    shippingModal.classList.add('show');
                }, 10);
            }, 300);
        }

        // Shipping form submission - proceed to GCash
        shippingForm.onsubmit = function(e) {
            e.preventDefault();
            shippingModal.classList.remove('show');
            setTimeout(() => {
                shippingModal.style.display = 'none';
                gcashModal.style.display = 'block';
                setTimeout(() => {
                    gcashModal.classList.add('show');
                }, 10);
            }, 300);
        }

        // Confirm GCash payment - place order
        confirmGcash.onclick = function() {
            const formData = new FormData(shippingForm);
            formData.append('place_order', '1');

            fetch('cart.php', {
                method: 'POST',
                body: formData
            }).then(response => {
                if (response.ok) {
                    window.location.href = 'orders.php?success=Order placed successfully with GCash';
                } else {
                    alert('Order failed. Please try again.');
                }
            }).catch(error => {
                alert('Order failed. Please try again.');
            });
        }

        // Handle other reason textarea
        otherRadio.onchange = function() {
            if (this.checked) {
                otherReason.style.display = 'block';
                otherReason.required = true;
            }
        }

        // Hide textarea when other radio is not selected
        document.querySelectorAll('input[name="cancel_reason"]').forEach(radio => {
            radio.onchange = function() {
                if (this.value !== 'Other') {
                    otherReason.style.display = 'none';
                    otherReason.required = false;
                }
            }
        });

        // Handle cancel form submission
        cancelForm.onsubmit = function(e) {
            e.preventDefault();
            const selectedCancelItems = document.querySelectorAll('input[name="cancel_items[]"]:checked');
            if (selectedCancelItems.length === 0) {
                alert('Please select at least one item to cancel.');
                return;
            }
            const reason = document.querySelector('input[name="cancel_reason"]:checked').value;
            const additionalReason = otherReason.value;

            // Create form data with selected items
            const formData = new FormData();
            formData.append('cancel_items', true);
            selectedCancelItems.forEach(item => {
                formData.append('cancel_items[]', item.value);
            });
            formData.append('cancel_reason', reason);
            if (additionalReason) {
                formData.append('other_reason', additionalReason);
            }

            fetch('cart.php', {
                method: 'POST',
                body: formData
            }).then(() => {
                alert('Selected items cancelled successfully.');
                window.location.reload();
            });
        }

        // Select all functionality
        const selectAllCheckbox = document.getElementById('select-all');
        const itemCheckboxes = document.querySelectorAll('input[name="selected_items[]"]');

        selectAllCheckbox.addEventListener('change', function() {
            itemCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        itemCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const allChecked = Array.from(itemCheckboxes).every(cb => cb.checked);
                const someChecked = Array.from(itemCheckboxes).some(cb => cb.checked);
                selectAllCheckbox.checked = allChecked;
                selectAllCheckbox.indeterminate = someChecked && !allChecked;
            });
        });
    </script>

    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 90%;
            max-width: 500px;
            border-radius: 8px;
            transform: translateY(-100%);
            transition: transform 0.3s ease-out;
            max-height: 80vh;
            overflow-y: auto;
        }

        .modal.show .modal-content {
            transform: translateY(0);
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: black;
        }

        .modal-actions {
            margin-top: 20px;
            text-align: right;
        }

        .modal-actions button {
            margin-left: 10px;
        }

        .reason-options {
            margin: 20px 0;
        }

        .reason-options label {
            display: block;
            margin-bottom: 10px;
            cursor: pointer;
        }

        .cancel-items {
            margin: 20px 0;
        }

        .cancel-items h4 {
            margin-bottom: 10px;
            color: #333;
        }

        .item-checkbox {
            display: block;
            margin-bottom: 8px;
            padding: 8px;
            background-color: #f9f9f9;
            border-radius: 4px;
            cursor: pointer;
        }

        .item-checkbox:hover {
            background-color: #f0f0f0;
        }
    </style>
</body>
</html>

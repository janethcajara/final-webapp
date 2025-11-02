<?php
include '../includes/auth.php';
include '../config/database.php';
include '../includes/functions.php';
redirectIfNotLoggedIn();

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = sanitizeInput($_POST['first_name']);
    $last_name = sanitizeInput($_POST['last_name']);
    $email = sanitizeInput($_POST['email']);
    $phone = sanitizeInput($_POST['phone']);
    $street = sanitizeInput($_POST['street']);
    $barangay = sanitizeInput($_POST['barangay']);
    $city = sanitizeInput($_POST['city']);
    $province = sanitizeInput($_POST['province']);
    $zipcode = sanitizeInput($_POST['zipcode']);

    // Check if email is already taken by another user
    $email_check_stmt = $conn->prepare("SELECT user_id FROM users_tbl WHERE Email = ? AND user_id != ?");
    $email_check_stmt->bind_param("si", $email, $user_id);
    $email_check_stmt->execute();
    $email_check_result = $email_check_stmt->get_result()->fetch_assoc();

    if ($email_check_result) {
        $error = "Email already exists. Please use a different email address.";
    } else {
        // Update user information
        $stmt = $conn->prepare("UPDATE users_tbl SET First_Name = ?, Last_Name = ?, Email = ?, Phone = ?, Street = ?, Barangay = ?, City = ?, Province = ?, Zipcode = ? WHERE user_id = ?");
        $stmt->bind_param("sssssssssi", $first_name, $last_name, $email, $phone, $street, $barangay, $city, $province, $zipcode, $user_id);

        if ($stmt->execute()) {
            $_SESSION['first_name'] = $first_name;
            $success = "Profile updated successfully!";
        } else {
            $error = "No changes were made.";
        }
    }
}

// Get current user data
$stmt = $conn->prepare("SELECT * FROM users_tbl WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Check if user data was retrieved
if (!$user) {
    $error = "Unable to load user data. Please try again.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - eStore</title>
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
                <li><a href="settings.php" class="active">Account Settings</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="container">
                <h2>Account Settings</h2>

                <?php if ($success): ?>
                    <div class="success"><?php echo $success; ?></div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="error"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" class="settings-form">
                    <h3>Account Information</h3>
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['Username'] ?? ''); ?>" readonly>
                    </div>

                    <h3>Personal Information</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="first_name">First Name *</label>
                            <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['First_Name'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="last_name">Last Name *</label>
                            <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['Last_Name'] ?? ''); ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['Email'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['Phone'] ?? ''); ?>">
                    </div>

                    <h3>Address Information</h3>
                    <div class="form-group">
                        <label for="street">Street *</label>
                        <input type="text" id="street" name="street" value="<?php echo htmlspecialchars($user['Street'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="barangay">Barangay *</label>
                        <input type="text" id="barangay" name="barangay" value="<?php echo htmlspecialchars($user['Barangay'] ?? ''); ?>" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="city">City *</label>
                            <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($user['City'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="province">Province *</label>
                            <input type="text" id="province" name="province" value="<?php echo htmlspecialchars($user['Province'] ?? ''); ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="zipcode">Zipcode *</label>
                        <input type="text" id="zipcode" name="zipcode" value="<?php echo htmlspecialchars($user['Zipcode'] ?? ''); ?>" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </form>
            </div>
        </main>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>

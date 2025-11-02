<?php
include '../includes/auth.php';
include '../config/database.php';
include '../includes/functions.php';
redirectIfNotAdmin();

// Create user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_user'])) {
    $username = sanitizeInput($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $first_name = sanitizeInput($_POST['first_name']);
    $last_name = sanitizeInput($_POST['last_name']);
    $email = sanitizeInput($_POST['email']);
    $phone = sanitizeInput($_POST['phone']);
    $role = sanitizeInput($_POST['role']);

    // Check if username or email already exists
    $stmt = $pdo->prepare("SELECT user_id FROM users_tbl WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    if ($stmt->rowCount() > 0) {
        $error = "Username or email already exists";
    } else {
        $stmt = $pdo->prepare("INSERT INTO users_tbl (username, password, role, first_name, last_name, email, phone) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$username, $password, $role, $first_name, $last_name, $email, $phone]);

        if ($stmt->rowCount() > 0) {
            header("Location: users.php?success=User created successfully");
            exit();
        } else {
            $error = "Failed to create user";
        }
    }
}

// Update user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $user_id = $_POST['user_id'];
    $username = sanitizeInput($_POST['username']);
    $first_name = sanitizeInput($_POST['first_name']);
    $last_name = sanitizeInput($_POST['last_name']);
    $email = sanitizeInput($_POST['email']);
    $phone = sanitizeInput($_POST['phone']);
    $role = sanitizeInput($_POST['role']);

    // Check if username or email already exists for other users
    $stmt = $pdo->prepare("SELECT user_id FROM users_tbl WHERE (username = ? OR email = ?) AND user_id != ?");
    $stmt->execute([$username, $email, $user_id]);
    if ($stmt->rowCount() > 0) {
        $error = "Username or email already exists";
    } else {
        $stmt = $pdo->prepare("UPDATE users_tbl SET username=?, first_name=?, last_name=?, email=?, phone=?, role=? WHERE user_id=?");
        $stmt->execute([$username, $first_name, $last_name, $email, $phone, $role, $user_id]);

        if ($stmt->rowCount() > 0) {
            header("Location: users.php?success=User updated successfully");
            exit();
        } else {
            $error = "Failed to update user";
        }
    }
}

// Delete user
if (isset($_GET['delete'])) {
    $user_id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM users_tbl WHERE user_id = ?");
    $stmt->execute([$user_id]);

    if ($stmt->rowCount() > 0) {
        header("Location: users.php?success=User deleted successfully");
        exit();
    } else {
        $error = "Failed to delete user";
    }
}

// Get all users
$users = $pdo->query("SELECT * FROM users_tbl ORDER BY user_id DESC");

// Get user for editing
$edit_user = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM users_tbl WHERE user_id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_user = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - eStore</title>
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
                <li><a href="products.php">Manage Products</a></li>
                <li><a href="orders.php">Manage Orders</a></li>
                <li><a href="users.php" class="active">Manage Users</a></li>
                <li><a href="../customer/logout.php">Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="container">
                <h2>Manage Users</h2>

                <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>
                <?php if (isset($_GET['success'])) echo "<div class='success'>".$_GET['success']."</div>"; ?>

                <!-- Create/Edit User Form -->
                <div class="form-section">
                    <h3><?php echo isset($edit_user) ? 'Edit User' : 'Add New User'; ?></h3>
                    <form method="POST">
                        <?php if (isset($edit_user)): ?>
                            <input type="hidden" name="user_id" value="<?php echo $edit_user['user_id']; ?>">
                        <?php endif; ?>

                        <input type="text" name="username" placeholder="Username" value="<?php echo isset($edit_user) ? $edit_user['username'] : ''; ?>" required>
                        <?php if (!isset($edit_user)): ?>
                            <input type="password" name="password" placeholder="Password" required>
                        <?php endif; ?>
                        <input type="text" name="first_name" placeholder="First Name" value="<?php echo isset($edit_user) ? $edit_user['first_name'] : ''; ?>" required>
                        <input type="text" name="last_name" placeholder="Last Name" value="<?php echo isset($edit_user) ? $edit_user['last_name'] : ''; ?>" required>
                        <input type="email" name="email" placeholder="Email" value="<?php echo isset($edit_user) ? $edit_user['email'] : ''; ?>" required>
                        <input type="text" name="phone" placeholder="Phone" value="<?php echo isset($edit_user) ? $edit_user['phone'] : ''; ?>">
                        <select name="role" required>
                            <option value="customer" <?php echo (isset($edit_user) && $edit_user['role'] == 'customer') ? 'selected' : ''; ?>>Customer</option>
                            <option value="admin" <?php echo (isset($edit_user) && $edit_user['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                        </select>

                        <button type="submit" name="<?php echo isset($edit_user) ? 'update_user' : 'create_user'; ?>" class="btn"><?php echo isset($edit_user) ? 'Update User' : 'Add User'; ?></button>
                        <?php if (isset($edit_user)): ?>
                            <a href="users.php" class="btn btn-danger">Cancel</a>
                        <?php endif; ?>
                    </form>
                </div>

                <!-- Users List -->
                <div class="users-list">
                    <h3>All Users</h3>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($user = $users->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?php echo $user['user_id']; ?></td>
                                <td><?php echo $user['username']; ?></td>
                                <td><?php echo $user['first_name'] . ' ' . $user['last_name']; ?></td>
                                <td><?php echo $user['email']; ?></td>
                                <td><?php echo $user['role']; ?></td>
                                <td>
                                    <a href="?edit=<?php echo $user['user_id']; ?>" class="btn btn-sm">Edit</a>
                                    <a href="?delete=<?php echo $user['user_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                                </td>
                            </tr>
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

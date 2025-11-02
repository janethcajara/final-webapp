<?php
include 'config/database.php';
include 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'customer'; // Force role to customer for security
    $first_name = sanitizeInput($_POST['first_name']);
    $last_name = sanitizeInput($_POST['last_name']);
    $email = sanitizeInput($_POST['email']);
    $phone = sanitizeInput($_POST['phone']);
    $street = sanitizeInput($_POST['street']);
    $barangay = sanitizeInput($_POST['barangay']);
    $city = sanitizeInput($_POST['city']);
    $province = sanitizeInput($_POST['province']);
    $zipcode = sanitizeInput($_POST['zipcode']);

    // Check if username already exists
    $check_stmt = $pdo->prepare("SELECT user_id FROM users_tbl WHERE username = ?");
    $check_stmt->execute([$username]);
    $check_result = $check_stmt->fetch(PDO::FETCH_ASSOC);

    if ($check_result) {
        $error = "Username already exists. Please choose a different username.";
    } else {
        // Check if email already exists
        $email_check_stmt = $pdo->prepare("SELECT user_id FROM users_tbl WHERE email = ?");
        $email_check_stmt->execute([$email]);
        $email_check_result = $email_check_stmt->fetch(PDO::FETCH_ASSOC);

        if ($email_check_result) {
            $error = "Email already exists. Please use a different email address.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO users_tbl (username, password, role, first_name, last_name, email, phone, street, barangay, city, province, zipcode) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$username, $password, $role, $first_name, $last_name, $email, $phone, $street, $barangay, $city, $province, $zipcode]);

            if ($stmt->rowCount() > 0) {
                header("Location: login.php?success=Registration successful");
                exit();
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - eStore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .register-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            padding: 30px;
            width: 100%;
            max-width: 450px;
        }
        .form-control {
            font-size: 14px;
            padding: 10px;
        }
        .btn-register {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px;
            font-weight: 600;
        }
        .form-label {
            font-weight: 500;
            margin-bottom: 5px;
            font-size: 14px;
        }
        .section-title {
            font-size: 16px;
            font-weight: 600;
            margin: 20px 0 15px 0;
            padding-bottom: 8px;
            border-bottom: 2px solid #667eea;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="text-center mb-4">
            <h3 class="fw-bold text-primary">Create Account</h3>
            <p class="text-muted">Join eStore today</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form method="POST">
            <!-- Account Information -->
            <div class="mb-3">
                <label class="form-label">Username *</label>
                <input type="text" name="username" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password *</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Role *</label>
                <select name="role" class="form-control" required>
                    <option value="customer">Customer</option>
                    <option value="admin" disabled>Admin (Contact Administrator)</option>
                </select>
            </div>

            <!-- Personal Information -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">First Name *</label>
                    <input type="text" name="first_name" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Last Name *</label>
                    <input type="text" name="last_name" class="form-control" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Email *</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control">
            </div>

            <!-- Address Information -->
            <div class="section-title">Address Information</div>

            <div class="mb-3">
                <label class="form-label">Street *</label>
                <input type="text" name="street" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Barangay *</label>
                <input type="text" name="barangay" class="form-control" required>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">City *</label>
                    <input type="text" name="city" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Province *</label>
                    <input type="text" name="province" class="form-control" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Zipcode *</label>
                <input type="text" name="zipcode" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-register text-white w-100 mb-3">
                Create Account
            </button>

            <div class="text-center">
                <p class="mb-0">Already have an account? 
                    <a href="login.php" class="text-decoration-none">Login</a>
                </p>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
include 'config/database.php';

try {
    // Create users_tbl
    $pdo->exec("CREATE TABLE IF NOT EXISTS users_tbl (
        user_id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('customer', 'admin') DEFAULT 'customer',
        first_name VARCHAR(50),
        last_name VARCHAR(50),
        email VARCHAR(100) UNIQUE NOT NULL,
        phone VARCHAR(20),
        street VARCHAR(100),
        barangay VARCHAR(50),
        city VARCHAR(50),
        province VARCHAR(50),
        zipcode VARCHAR(10),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Create products_tbl
    $pdo->exec("CREATE TABLE IF NOT EXISTS products_tbl (
        product_id INT AUTO_INCREMENT PRIMARY KEY,
        product_name VARCHAR(100) NOT NULL,
        description TEXT,
        category VARCHAR(50),
        size VARCHAR(20),
        color VARCHAR(50),
        price DECIMAL(10,2) NOT NULL,
        stock_quantity INT DEFAULT 0,
        image_url VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Create orders_tbl
    $pdo->exec("CREATE TABLE IF NOT EXISTS orders_tbl (
        order_id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        total_amount DECIMAL(10,2) NOT NULL,
        status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled', 'completed') DEFAULT 'pending',
        payment_method VARCHAR(50),
        order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users_tbl(user_id) ON DELETE CASCADE
    )");

    // Create order_items_tbl
    $pdo->exec("CREATE TABLE IF NOT EXISTS order_items_tbl (
        order_item_id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL,
        price_at_purchase DECIMAL(10,2) NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders_tbl(order_id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products_tbl(product_id) ON DELETE CASCADE
    )");

    echo "All tables created successfully!\n";

} catch (PDOException $e) {
    echo "Error creating tables: " . $e->getMessage() . "\n";
}
?>

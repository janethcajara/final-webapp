<?php
include 'config/database.php';

// Clear existing products
try {
    $pdo->exec("DELETE FROM products_tbl");
    echo "Existing products cleared.\n";
} catch (PDOException $e) {
    echo "Error clearing products: " . $e->getMessage() . "\n";
}

$products = [
    // Running Shoes
    [
        'product_name' => 'Nike Air Zoom Pegasus',
        'description' => 'Responsive running shoes with Zoom Air technology for maximum cushioning',
        'category' => 'Running',
        'size' => '9',
        'color' => 'Black/White',
        'price' => 129.99,
        'stock_quantity' => 50,
        'image_url' => 'images/nike-pegasus.jpg'
    ],
    [
        'product_name' => 'Adidas Ultraboost 22',
        'description' => 'Energy-returning running shoes with Boost technology',
        'category' => 'Running',
        'size' => '10',
        'color' => 'Core Black',
        'price' => 189.99,
        'stock_quantity' => 35,
        'image_url' => 'images/adidas-ultraboost.jpg'
    ],
    [
        'product_name' => 'Saucony Ride 15',
        'description' => 'Cushioned running shoes with PWRRUN foam for all-day comfort',
        'category' => 'Running',
        'size' => '8',
        'color' => 'Blue/Silver',
        'price' => 139.99,
        'stock_quantity' => 40,
        'image_url' => 'images/saucony-ride.jpg'
    ],
    [
        'product_name' => 'ASICS Gel-Kayano 28',
        'description' => 'Stability running shoes with GEL technology for support',
        'category' => 'Running',
        'size' => '11',
        'color' => 'Midnight/Midnight',
        'price' => 159.99,
        'stock_quantity' => 30,
        'image_url' => 'images/asics-kayano.jpg'
    ],
    [
        'product_name' => 'Puma Deviate Nitro 2',
        'description' => 'Lightweight running shoes with energy-returning foam',
        'category' => 'Running',
        'size' => '9.5',
        'color' => 'Puma Black',
        'price' => 149.99,
        'stock_quantity' => 45,
        'image_url' => 'images/puma-deviate.jpg'
    ],

    // Casual Shoes
    [
        'product_name' => 'Converse Chuck Taylor All Star',
        'description' => 'Classic canvas sneakers for everyday casual wear',
        'category' => 'Casual',
        'size' => '8',
        'color' => 'Navy',
        'price' => 59.99,
        'stock_quantity' => 100,
        'image_url' => 'images/converse-chuck.jpg'
    ],
    [
        'product_name' => 'Vans Old Skool',
        'description' => 'Iconic skate shoes with canvas upper and rubber sole',
        'category' => 'Casual',
        'size' => '9',
        'color' => 'Black/White',
        'price' => 69.99,
        'stock_quantity' => 80,
        'image_url' => 'images/vans-oldskool.jpg'
    ],
    [
        'product_name' => 'Adidas Stan Smith',
        'description' => 'Timeless tennis-inspired sneakers with leather upper',
        'category' => 'Casual',
        'size' => '10',
        'color' => 'White/Green',
        'price' => 89.99,
        'stock_quantity' => 60,
        'image_url' => 'images/adidas-stan.jpg'
    ],
    [
        'product_name' => 'New Balance 574',
        'description' => 'Retro running-inspired casual shoes with suede accents',
        'category' => 'Casual',
        'size' => '8.5',
        'color' => 'Navy',
        'price' => 79.99,
        'stock_quantity' => 70,
        'image_url' => 'images/nb-574.jpg'
    ],
    [
        'product_name' => 'Crocs Classic Clog',
        'description' => 'Comfortable foam clogs with iconic Croslite material',
        'category' => 'Casual',
        'size' => '9',
        'color' => 'Navy',
        'price' => 39.99,
        'stock_quantity' => 120,
        'image_url' => 'images/crocs-classic.jpg'
    ],

    // Sports Shoes
    [
        'product_name' => 'Nike Air Force 1 \'07',
        'description' => 'Basketball-inspired shoes with durable leather upper',
        'category' => 'Sports',
        'size' => '10',
        'color' => 'White',
        'price' => 109.99,
        'stock_quantity' => 55,
        'image_url' => 'images/nike-af1.jpg'
    ],
    [
        'product_name' => 'Under Armour Curry 9',
        'description' => 'Basketball shoes with responsive cushioning and ankle support',
        'category' => 'Sports',
        'size' => '11',
        'color' => 'Black/Red',
        'price' => 149.99,
        'stock_quantity' => 25,
        'image_url' => 'images/ua-curry.jpg'
    ],
    [
        'product_name' => 'Puma Suede Classic',
        'description' => 'Basketball shoes with suede upper and rubber outsole',
        'category' => 'Sports',
        'size' => '9',
        'color' => 'Red/Black',
        'price' => 79.99,
        'stock_quantity' => 65,
        'image_url' => 'images/puma-suede.jpg'
    ],
    [
        'product_name' => 'Adidas Harden Vol. 6',
        'description' => 'Basketball shoes with Lightstrike foam for quick movements',
        'category' => 'Sports',
        'size' => '10.5',
        'color' => 'Core Black',
        'price' => 139.99,
        'stock_quantity' => 30,
        'image_url' => 'images/adidas-harden.jpg'
    ],
    [
        'product_name' => 'Nike LeBron 19',
        'description' => 'High-performance basketball shoes with Zoom Air units',
        'category' => 'Sports',
        'size' => '12',
        'color' => 'White/University Red',
        'price' => 199.99,
        'stock_quantity' => 20,
        'image_url' => 'images/nike-lebron.jpg'
    ]
];

try {
    $stmt = $pdo->prepare("INSERT INTO products_tbl (product_name, description, category, size, color, price, stock_quantity, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    foreach ($products as $product) {
        $stmt->execute([
            $product['product_name'],
            $product['description'],
            $product['category'],
            $product['size'],
            $product['color'],
            $product['price'],
            $product['stock_quantity'],
            $product['image_url']
        ]);
    }

    echo "Sample shoes inserted successfully! Total: " . count($products) . " products\n";
} catch (PDOException $e) {
    echo "Error inserting products: " . $e->getMessage() . "\n";
}
?>

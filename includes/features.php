    <section class="featured-products" id="products">
        <div class="container">
            <div class="section-title">
                <h2>Featured Products</h2>
                <p>Check out our handpicked selection of premium shoes</p>
            </div>

            <?php
            // Check if database connection is successful
            if ($pdo) {
                $stmt = $pdo->query("SELECT * FROM products_tbl WHERE stock_quantity > 0 LIMIT 3");
                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $products = [];
                error_log("Database connection failed");
            }
            ?>

            <?php if (!empty($products)): ?>
                <div class="row">
                    <?php foreach ($products as $product): ?>
                    <div class="col-md-4">
                        <div class="product-card">
                            <div class="product-image">
                                <i class="fas fa-shoe-prints"></i>
                            </div>
                            <div class="product-content">
                                <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                                <p><?php echo htmlspecialchars($product['description']); ?></p>
                                <p class="price">$<?php echo number_format($product['price'], 2); ?></p>
                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <a href="customer/products.php?product_id=<?php echo $product['product_id']; ?>" class="btn btn-primary w-100">View Product</a>
                                <?php else: ?>
                                    <a href="customer/login.php" class="btn btn-primary w-100">Login to Shop</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="text-center mt-4">
                    <a href="customer/products.php" class="btn btn-outline-primary">View All Products</a>
                </div>
            <?php else: ?>
                <div class="no-products text-center">
                    <p class="lead">No featured products available at the moment.</p>
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin'): ?>
                        <a href="admin/dashboard.php?section=products" class="btn btn-primary">Add Products</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
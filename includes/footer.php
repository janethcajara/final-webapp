    </main>

    <footer style="background: #070f11ff; color: white; padding: 2rem 0; margin-top: 3rem;">
        <div class="container">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem;">
                <div class="footer-section">
                    <h3>eStore</h3>
                    <p>Your trusted online shoe store offering premium quality footwear for every occasion.</p>
                </div>
                
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul style="list-style: none; padding: 0;">
                        <li><a href="../index.php" style="color: #ccc; text-decoration: none;">Home</a></li>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <?php if ($_SESSION['role'] === 'admin'): ?>
                                <li><a href="../admin/dashboard.php" style="color: #ccc; text-decoration: none;">Admin Dashboard</a></li>
                            <?php else: ?>
                                <li><a href="../customer/products.php" style="color: #ccc; text-decoration: none;">Products</a></li>
                                <li><a href="../customer/cart.php" style="color: #ccc; text-decoration: none;">Shopping Cart</a></li>
                            <?php endif; ?>
                        <?php else: ?>
                            <li><a href="../customer/login.php" style="color: #ccc; text-decoration: none;">Login</a></li>
                            <li><a href="../customer/register.php" style="color: #ccc; text-decoration: none;">Register</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4>Customer Service</h4>
                    <ul style="list-style: none; padding: 0;">
                        <li><a href="#" style="color: #ccc; text-decoration: none;">Contact Us</a></li>
                        <li><a href="#" style="color: #ccc; text-decoration: none;">Shipping Info</a></li>
                        <li><a href="#" style="color: #ccc; text-decoration: none;">Returns</a></li>
                        <li><a href="#" style="color: #ccc; text-decoration: none;">FAQ</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4>Contact Info</h4>
                    <p style="color: #ccc; margin: 0.5rem 0;">
                        <strong>Address:</strong><br>
                        166, Lawaan Street, Lumangbayan<br>
                        Calapan City, Oriental Mindoro
                    </p>
                    <p style="color: #ccc; margin: 0.5rem 0;">
                        <strong>Email:</strong> info@estore.com<br>
                        <strong>Phone:</strong> (043) 123-4567
                    </p>
                </div>
            </div>
            
            <div style="border-top: 1px solid #555; margin-top: 2rem; padding-top: 1rem; text-align: center;">
                <p style="color: #ccc; margin: 0;">
                    &copy; <?php echo date('Y'); ?> eStore - Shoe E-commerce Platform. All rights reserved.
                </p>
            </div>
        </div>
    </footer>

    <script src="../assets/js/script.js"></script>
</body>
</html>
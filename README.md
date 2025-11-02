# cajara-janeth-final-webapp
# eStore - Ecommerce Website

A full-featured ecommerce website built with PHP and MySQL, featuring user authentication, product management, order processing, and an admin dashboard with analytics.

## Features

- **User Authentication**: Registration, login, and logout for customers and admins
- **Product Catalog**: Browse products by categories with detailed product pages
- **Shopping Cart**: Add, remove, and manage items in the cart
- **Order Management**: Place orders, view order history, and track status
- **Admin Dashboard**: Comprehensive admin panel with statistics, charts, and management tools
- **Product Management**: Add, edit, and delete products (admin only)
- **User Management**: Manage customer accounts (admin only)
- **Order Processing**: View and update order statuses (admin only)
- **Responsive Design**: Mobile-friendly interface
- **Newsletter Signup**: Collect email addresses for marketing
- **Testimonials**: Display customer reviews and feedback
- **Analytics**: Charts and graphs for sales data and user metrics

## Technologies Used

- **Backend**: PHP 7+
- **Database**: MySQL
- **Frontend**: HTML5, CSS3, JavaScript
- **Charts**: Chart.js for data visualization
- **Styling**: Custom CSS with responsive design
- **Server**: XAMPP (Apache, MySQL, PHP)

## Installation

### Prerequisites

- XAMPP (or any web server with PHP and MySQL support)
- Web browser

### Setup Instructions

1. **Clone or Download the Project**
   ```
   Download and extract the project files to your XAMPP htdocs directory (c:/XAMPP/htdocs/ecommerce)
   ```

2. **Database Setup**
   - Start XAMPP and ensure Apache and MySQL services are running
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Create a new database named `ecommerce_db`
   - Import the database schema (if available) or create tables manually:
     - `users_tbl`: User accounts (customers and admins)
     - `products_tbl`: Product catalog
     - `orders_tbl`: Order records
     - `categories_tbl`: Product categories

3. **Configuration**
   - Update database credentials in `config/database.php` if needed
   - Default credentials:
     - Host: localhost
     - Database: ecommerce_db
     - Username: root
     - Password: (empty)

4. **Run the Application**
   - Open your web browser
   - Navigate to: `http://localhost/ecommerce`
   - Register as a new user or login with existing credentials

## Usage

### For Customers

1. **Browse Products**: View featured products and browse by categories
2. **Register/Login**: Create an account or sign in to access full features
3. **Add to Cart**: Select products and add them to your shopping cart
4. **Checkout**: Complete your purchase with order details
5. **View Orders**: Track your order history and status
6. **Settings**: Update your account information

### For Admins

1. **Login**: Access the admin dashboard at `admin/dashboard.php`
2. **Dashboard**: View statistics, charts, and recent orders
3. **Manage Products**: Add, edit, or remove products
4. **Manage Orders**: View and update order statuses
5. **Manage Users**: View and manage customer accounts

## Project Structure

```
ecommerce/
├── index.php                 # Homepage
├── login.php                 # User login page
├── register.php              # User registration page
├── logout.php                # Logout functionality
├── config/
│   └── database.php          # Database connection configuration
├── includes/                 # Reusable PHP includes
│   ├── head.php             # HTML head section
│   ├── navbar.php           # Navigation bar
│   ├── section.php          # Hero section
│   ├── features.php         # Featured products section
│   ├── categories.php       # Product categories
│   ├── featureproduct.php   # Product features
│   ├── testimonial.php      # Customer testimonials
│   ├── newsletter.php       # Newsletter signup
│   ├── footer1.php          # Footer
│   ├── auth.php             # Authentication functions
│   └── functions.php        # Utility functions
├── admin/                   # Admin panel
│   ├── dashboard.php        # Admin dashboard with analytics
│   ├── products.php         # Product management
│   ├── orders.php           # Order management
│   └── users.php            # User management
├── customer/                # Customer pages
│   ├── dashboard.php        # Customer dashboard
│   ├── products.php         # Product browsing
│   ├── cart.php             # Shopping cart
│   ├── checkout.php         # Order checkout
│   ├── orders.php           # Order history
│   ├── settings.php         # Account settings
│   └── logout.php           # Customer logout
├── assets/                  # Static assets
│   ├── css/
│   │   └── style.css        # Main stylesheet
│   ├── js/
│   │   └── script.js        # JavaScript functionality
│   └── images/              # Image assets
└── README.md                # This file
```

## Database Schema

### Key Tables

- **users_tbl**: Stores user information (user_id, username, email, password, role, etc.)
- **products_tbl**: Product catalog (product_id, name, description, price, category, image, etc.)
- **orders_tbl**: Order records (order_id, user_id, total_amount, status, order_date, etc.)
- **categories_tbl**: Product categories (category_id, name, description)

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support or questions, please contact the development team or create an issue in the repository.

---

**Note**: This is a basic ecommerce implementation. For production use, additional security measures, payment gateway integration, and testing should be implemented.

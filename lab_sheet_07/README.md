# Online Bookstore — Lab Sheet 7 Solutions (21 Questions)

A PHP + MySQL starter project covering all 21 tasks in MCA108P-AS07.

## Requirements
- PHP 8.1+ with PDO MySQL enabled
- MySQL 8+
- Apache (XAMPP/WAMP/LAMP recommended)

## Setup
1. Copy this folder into your web server directory (for XAMPP: `htdocs/Bookstore_Lab_Sheet_7_Solutions`).
2. Open phpMyAdmin or MySQL Workbench and run `database.sql`.
3. Update database credentials in `config.php`.
4. In MySQL, create an admin account by registering normally, then run:
   `UPDATE users SET role='admin' WHERE email='your-email@example.com';`
5. Visit `http://localhost/Bookstore_Lab_Sheet_7_Solutions/index.php`.

## Included
- Homepage, book grid, search/filter, pagination, details
- Registration/login using `password_hash`, `password_verify`, sessions
- Session cart with quantity updates/removal and stock validation
- Checkout with shipping information and transactional order creation
- Order history and invoice
- Admin book/inventory management, order status updates, sales report
- Reviews/ratings and wishlist
- JavaScript client-side validation and responsive custom CSS
- Prepared statements and output escaping for user/database data

## Notes
This is an educational starter, not a production-ready commerce system. Before public deployment add CSRF protection, rate limiting, email verification/password reset, HTTPS, stronger authorization auditing, and a payment provider. No real payment processing is implemented.

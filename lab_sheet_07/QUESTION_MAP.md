# Lab Sheet 7 — Question-to-file map

| Q | Requirement | Main solution files |
|---|---|---|
| 1 | Homepage with featured books, search, categories, banner | `index.php`, `assets/style.css` |
| 2 | MySQL schema for books/users/orders | `database.sql` (also `order_items`, `reviews`, `wishlist`) |
| 3 | Dynamic book grid | `index.php` |
| 4 | Book details by GET ID | `book.php?id=1` |
| 5 | Registration/login, password hashing, sessions | `register.php`, `login.php`, `logout.php`, `config.php` |
| 6 | Session cart, add/update/remove | `cart.php`, `cart_action.php` |
| 7 | Checkout and record order | `checkout.php`, `database.sql` |
| 8 | Admin add/edit books and inventory | `admin/index.php` |
| 9 | Admin view orders and fulfillment status | `admin/orders.php` |
| 10 | Search/filter by title, author, category | `index.php` |
| 11 | Pagination | `index.php` |
| 12 | Customer order history/status | `orders.php` |
| 13 | Prevent cart quantity exceeding stock | `cart_action.php`, `checkout.php` |
| 14 | Prepared statements and output escaping | `config.php` plus all query pages |
| 15 | User ratings and comments | `book.php`, `review.php`, `database.sql` |
| 16 | Invoice after successful order | `invoice.php`, `checkout.php` |
| 17 | Database wishlist | `wishlist.php`, `database.sql` |
| 18 | Client-side form validation | `assets/validation.js`, forms with `data-validate` |
| 19 | Consistent styling | `assets/style.css` |
| 20 | End-to-end journey | Follow test checklist below |
| 21 | Admin sales/revenue report | `admin/sales.php` |

## Manual end-to-end test checklist
1. Import `database.sql` and update `config.php`.
2. Register a customer and log in.
3. Search for a book and filter by category; move through pages if enough books exist.
4. Open a book detail page, add it to wishlist, write a review.
5. Add a book to the cart; attempt a quantity above stock and verify it is capped.
6. Checkout while logged in; verify the order appears in `orders.php` and an invoice opens.
7. Register a second account, then promote the intended admin in MySQL using the README command.
8. Open Admin, add/edit a book, change an order status, and inspect the sales report.
9. Test invalid forms and confirm database data is displayed escaped.

## Security reminder
Prepared statements and HTML output escaping are used throughout. This project is still an educational example: add CSRF tokens, rate limits, stronger validation, secure deployment settings and a real payment provider before production use.

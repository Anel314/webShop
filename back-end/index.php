<?php

require_once "./vendor/autoload.php";
require_once "./rest/services/authService.php";
require_once "./rest/services/cartService.php";
require_once "./rest/services/orderService.php";
require_once "./rest/services/productService.php";
require_once "./rest/services/usersService.php";
require_once "./rest/services/orderProductsService.php";


// header("Access-Control-Allow-Origin: *");
// header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
// header("Access-Control-Allow-Headers: Content-Type, Authorization");

// if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
//     http_response_code(200);
//     exit();
// }

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

Flight::register('middleware', "AuthMiddleware");

Flight::register('auth', "AuthService");
Flight::register('cart', "CartService");
Flight::register('order', "OrderService");
Flight::register('product', "ProductService");
Flight::register('users', "UsersService");
Flight::register('orderProducts', "OrderProductsService");

Flight::route("/*", function () {
    echo "<h1>Welcome to the Web Shop API</h1>";
    echo "<p>Available endpoints:</p>";
    echo "<hr>";
    echo "<ul>";
    echo "<li>GET /products - Get all products</li>";
    echo "<li>GET /products/@id - Get product by ID</li>";
    echo "<li>GET /products/category/@category - Get products by category</li>";
    echo "<li>POST /products - Add a new product</li>";
    echo "<li>PUT /products/@id - Update product by ID</li>";
    echo "<li>DELETE /products/@id - Delete product by ID</li>";
    echo "<hr>";
    echo "<li>GET /users - Get all users</li>";
    echo "<li>GET /users/@id - Get user by ID</li>";
    echo "<li>POST /users - Add new user</li>";
    echo "<li>PUT /users/@id - Update existing user</li>";
    echo "<li>DELETE /users/@id - Delete user</li>";
    echo "<hr>";
    echo "<li>POST /orders - Create new order</li>";
    echo "<li>GET /orders/@id - Get order by ID</li>";
    echo "<li>PUT /orders/@id - Update order by ID</li>";
    echo "<li>DELETE /orders/@id - Delete order by ID</li>";
    echo "<hr>";
    echo "<li>GET /cart/@user_id - Get all cart items for a user</li>";
    echo "<li>GET /cart/@user_id/total - Get total price for user's cart</li>";
    echo "<li>POST /cart - Add item to cart</li>";
    echo "<li>PUT /cart - Update item quantity in cart</li>";
    echo "<li>DELETE /cart/@user_id/@product_id - Remove item from cart</li>";
    echo "<hr>";
    echo "<li>POST /auth/register - Register a new user</li>";
    echo "<li>POST /auth/login - User login</li>";
    echo "<hr>";
    echo "<li>GET /auth/username/@username - Get user by username</li>";
    echo "<li>GET /auth/email/@email - Get user by email</li>";
    echo "<hr>";
    echo "</ul>";

    echo "<p>For more details, refer to the API documentation.</p>";
});





require_once "./rest/routes/usersRoutes.php";
require_once "./rest/routes/productRoutes.php";
require_once "./rest/routes/orderRoutes.php";
require_once "./rest/routes/cartRoutes.php";
require_once "./rest/routes/authRoutes.php";




Flight::start();
?>
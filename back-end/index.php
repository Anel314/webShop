<?php

require_once "./vendor/autoload.php";
require_once "./rest/services/authService.php";
require_once "./rest/services/cartService.php";
require_once "./rest/services/orderService.php";
require_once "./rest/services/productService.php";
require_once "./rest/services/usersService.php";


Flight::register('auth', "AuthService");
Flight::register('cart', "CartService");
Flight::register('order', "OrderService");
Flight::register('product', "ProductService");
Flight::register('users', "UsersService");










Flight::start();
?>
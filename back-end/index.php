<?php
require "./vendor/autoload.php";
require "rest/services/authService.php";
require "rest/services/cartService.php";
require "rest/services/orderService.php";
require "rest/services/productService.php";
require "rest/services/usersService.php";

Flight::register('auth', "AuthService");
Flight::register('cart', "CartService");
Flight::register('order', "OrderService");
Flight::register('product', "ProductService");
Flight::register('users', "UsersService");

Flight::route('/*', function() {
    return TRUE;
});





Flight::start();
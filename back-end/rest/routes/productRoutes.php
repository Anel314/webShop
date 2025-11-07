<?php

// PRODUCT ROUTES

// Get all products
Flight::route('GET /products', function () {
    try {
        Flight::json(Flight::product()->getAll());
    } catch (Exception $e) {
        Flight::json(["error" => $e->getMessage()], 400);
    }
});

// Get product by ID
Flight::route('GET /products/@id', function ($id) {
    try {
        $product = Flight::product()->get_product_by_id($id);
        if ($product) {
            Flight::json($product);
        } else {
            Flight::json(["error" => "Product not found"], 404);
        }
    } catch (Exception $e) {
        Flight::json(["error" => $e->getMessage()], 400);
    }
});

// Get products by category
Flight::route('GET /products/category/@category', function ($category) {
    try {
        $products = Flight::product()->get_products_by_category($category);
        if ($products) {
            Flight::json($products);
        } else {
            Flight::json(["error" => "No products found for this category"], 404);
        }
    } catch (Exception $e) {
        Flight::json(["error" => $e->getMessage()], 400);
    }
});

// Add a new product
Flight::route('POST /products', function () {
    $data = Flight::request()->data->getData();
    try {
        $product = Flight::product()->add($data);
        Flight::json($product, 201);
    } catch (Exception $e) {
        Flight::json(["error" => $e->getMessage()], 400);
    }
});

// Update product by ID
Flight::route('PUT /products/@id', function ($id) {
    $data = Flight::request()->data->getData();
    try {
        $existing = Flight::product()->get_product_by_id($id);
        if (!$existing) {
            Flight::json(["error" => "Product not found"], 404);
            return;
        }

        $updated = Flight::product()->update($data, $id);
        Flight::json($updated);
    } catch (Exception $e) {
        Flight::json(["error" => $e->getMessage()], 400);
    }
});

// Delete product by ID
Flight::route('DELETE /products/@id', function ($id) {
    try {
        $existing = Flight::product()->get_product_by_id($id);
        if (!$existing) {
            Flight::json(["error" => "Product not found"], 404);
            return;
        }

        Flight::product()->delete($id);
        Flight::json(["message" => "Product deleted successfully"]);
    } catch (Exception $e) {
        Flight::json(["error" => $e->getMessage()], 400);
    }
});

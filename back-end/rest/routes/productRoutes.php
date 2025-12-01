<?php

// PRODUCT ROUTES

/**
 * @OA\Get(
 *   path="/products",
 *   summary="Get all products",
 *   tags={"Products"},
 *   @OA\Response(
 *     response=200,
 *     description="List of all products",
 *     @OA\JsonContent(
 *       type="array",
 *       @OA\Items(
 *         type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="Wireless Mouse"),
 *         @OA\Property(property="price", type="number", format="float", example=25.99),
 *         @OA\Property(property="category", type="string", example="Accessories"),
 *         @OA\Property(property="stock_quantity", type="integer", example=100)
 *       )
 *     )
 *   ),
 *   @OA\Response(
 *     response=400,
 *     description="Error fetching products",
 *     @OA\JsonContent(
 *       @OA\Property(property="error", type="string", example="Database connection failed")
 *     )
 *   )
 * )
 */
Flight::route('GET /products', function () {
    try {
        Flight::json(Flight::product()->getAll());
    } catch (Exception $e) {
        Flight::json(["error" => $e->getMessage()], 400);
    }
});


/**
 * @OA\Get(
 *   path="/products/{id}",
 *   summary="Get a product by ID",
 *   tags={"Products"},
 *   @OA\Parameter(
 *     name="id",
 *     in="path",
 *     required=true,
 *     description="Product ID",
 *     @OA\Schema(type="integer", example=1)
 *   ),
 *   @OA\Response(
 *     response=200,
 *     description="Product details",
 *     @OA\JsonContent(
 *       type="object",
 *       @OA\Property(property="id", type="integer", example=1),
 *       @OA\Property(property="name", type="string", example="Wireless Mouse"),
 *       @OA\Property(property="price", type="number", format="float", example=25.99),
 *       @OA\Property(property="category", type="string", example="Accessories"),
 *       @OA\Property(property="stock_quantity", type="integer", example=100)
 *     )
 *   ),
 *   @OA\Response(
 *     response=404,
 *     description="Product not found",
 *     @OA\JsonContent(
 *       @OA\Property(property="error", type="string", example="Product not found")
 *     )
 *   ),
 *   @OA\Response(
 *     response=400,
 *     description="Error fetching product",
 *     @OA\JsonContent(
 *       @OA\Property(property="error", type="string", example="Invalid product ID")
 *     )
 *   )
 * )
 */
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


/**
 * @OA\Get(
 *   path="/products/category/{category}",
 *   summary="Get products by category",
 *   tags={"Products"},
 *   @OA\Parameter(
 *     name="category",
 *     in="path",
 *     required=true,
 *     description="Category name",
 *     @OA\Schema(type="string", example="Accessories")
 *   ),
 *   @OA\Response(
 *     response=200,
 *     description="List of products in the specified category",
 *     @OA\JsonContent(
 *       type="array",
 *       @OA\Items(
 *         type="object",
 *         @OA\Property(property="id", type="integer", example=3),
 *         @OA\Property(property="name", type="string", example="Mechanical Keyboard"),
 *         @OA\Property(property="price", type="number", format="float", example=89.99),
 *         @OA\Property(property="category", type="string", example="Accessories"),
 *         @OA\Property(property="stock_quantity", type="integer", example=50)
 *       )
 *     )
 *   ),
 *   @OA\Response(
 *     response=404,
 *     description="No products found in this category",
 *     @OA\JsonContent(
 *       @OA\Property(property="error", type="string", example="No products found for this category")
 *     )
 *   ),
 *   @OA\Response(
 *     response=400,
 *     description="Error fetching products",
 *     @OA\JsonContent(
 *       @OA\Property(property="error", type="string", example="Invalid category name")
 *     )
 *   )
 * )
 */
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


/**
 * @OA\Post(
 *   path="/products",
 *   summary="Add a new product",
 *   tags={"Products"},
 *   description="Creates a new product. Requires name, price, category_id, stock quantity, description, and user ID.",
 *   @OA\RequestBody(
 *     required=true,
 *     description="Product data to be added",
 *     @OA\JsonContent(
 *       required={"name", "price", "category_id", "stock_quantity", "description", "user_id"},
 *       @OA\Property(property="name", type="string", description="Product name", example="Gucci Torba"),
 *       @OA\Property(property="price", type="number", format="float", description="Product price", example=150.00),
 *       @OA\Property(property="category_id", type="integer", description="Category ID this product belongs to", example=1),
 *       @OA\Property(property="stock_quantity", type="integer", description="How many units are in stock", example=200),
 *       @OA\Property(property="description", type="string", description="Product description", example="Original Torba ba"),
 *       @OA\Property(property="user_id", type="integer", description="ID of the user who created the product", example=1)
 *     )
 *   ),
 *   @OA\Response(
 *     response=201,
 *     description="Product created successfully",
 *     @OA\JsonContent(
 *       type="object",
 *       @OA\Property(property="message", type="string", example="Product added Succesfully"),
 *       @OA\Property(
 *         property="product",
 *         type="object",
 *         @OA\Property(property="id", type="integer", example=12),
 *         @OA\Property(property="name", type="string", example="Anel Test"),
 *         @OA\Property(property="price", type="number", format="float", example=0.99),
 *         @OA\Property(property="category_id", type="integer", example=1),
 *         @OA\Property(property="stock_quantity", type="integer", example=200),
 *         @OA\Property(property="description", type="string", example="Test"),
 *         @OA\Property(property="user_id", type="integer", example=1)
 *       )
 *     )
 *   ),
 *   @OA\Response(
 *     response=400,
 *     description="Invalid input or error creating product",
 *     @OA\JsonContent(
 *       type="object",
 *       @OA\Property(property="error", type="string", example="Missing required fields or invalid data")
 *     )
 *   )
 * )
 */

Flight::route('POST /products', function () {
    $data = Flight::request()->data->getData();
    try {

        $product = Flight::product()->add($data);
        Flight::json(["message" => "Product added Succesfully", "product" => $product], 201);
    } catch (Exception $e) {
        Flight::json(["error" => $e->getMessage()], 400);
    }
});


/**
 * @OA\Get(
 * path="/products/users/{user_id}",
 * summary="Get all products by a specific user",
 * tags={"Products"},
 * description="Retrieves a list of all products associated with a given user ID.",
 * @OA\Parameter(
 * name="user_id",
 * in="path",
 * required=true,
 * description="ID of the user whose products are to be retrieved",
 * @OA\Schema(
 * type="integer",
 * example=1
 * )
 * ),
 * @OA\Response(
 * response=200,
 * description="A list of products successfully retrieved",
 * @OA\JsonContent(
 * type="array",
 * @OA\Items(
 * type="object",
 * @OA\Property(property="id", type="integer", example=12),
 * @OA\Property(property="name", type="string", example="Anel Test"),
 * @OA\Property(property="price", type="number", format="float", example=0.99),
 * @OA\Property(property="category_id", type="integer", example=1),
 * @OA\Property(property="stock_quantity", type="integer", example=200),
 * @OA\Property(property="description", type="string", example="Test"),
 * @OA\Property(property="user_id", type="integer", example=1)
 * )
 * )
 * ),
 * @OA\Response(
 * response=400,
 * description="Invalid input or error retrieving products",
 * @OA\JsonContent(
 * type="object",
 * @OA\Property(property="error", type="string", example="User not found or database error")
 * )
 * )
 * )
 */

Flight::route('GET /products/users/@user_id', function ($user_id) {
    try {
        $product = Flight::product()->get_product_by_user($user_id);
        Flight::json($product);
    } catch (Exception $e) {
        Flight::json(["error" => $e->getMessage()], 400);
    }
});








/**
 * @OA\Put(
 *   path="/products/{id}",
 *   summary="Update an existing product",
 *   tags={"Products"},
 *   description="Updates a product by its ID. The body should contain all product fields to be updated.",
 *   @OA\Parameter(
 *     name="id",
 *     in="path",
 *     required=true,
 *     description="ID of the product to update",
 *     @OA\Schema(type="integer", example=5)
 *   ),
 *   @OA\RequestBody(
 *     required=true,
 *     description="Updated product data",
 *     @OA\JsonContent(
 *       required={"name", "price", "category_id", "stock_quantity", "description", "user_id"},
 *       @OA\Property(property="name", type="string", description="Product name", example="My awesome product"),
 *       @OA\Property(property="price", type="number", format="float", description="Product price", example=150),
 *       @OA\Property(property="category_id", type="integer", description="Category ID the product belongs to", example=3),
 *       @OA\Property(property="stock_quantity", type="integer", description="Available quantity", example=13),
 *       @OA\Property(property="description", type="string", description="Product description", example="Product description"),
 *       @OA\Property(property="user_id", type="integer", description="ID of the user who created or updated the product", example=1)
 *     )
 *   ),
 *   @OA\Response(
 *     response=200,
 *     description="Product updated successfully",
 *     @OA\JsonContent(
 *       type="object",
 *       @OA\Property(property="id", type="integer", example=5),
 *       @OA\Property(property="name", type="string", example="My awesome product"),
 *       @OA\Property(property="price", type="number", format="float", example=150),
 *       @OA\Property(property="category_id", type="integer", example=3),
 *       @OA\Property(property="stock_quantity", type="integer", example=13),
 *       @OA\Property(property="description", type="string", example="Product description"),
 *       @OA\Property(property="user_id", type="integer", example=1)
 *     )
 *   ),
 *   @OA\Response(
 *     response=400,
 *     description="Invalid input or error updating product",
 *     @OA\JsonContent(
 *       type="object",
 *       @OA\Property(property="error", type="string", example="Invalid input data")
 *     )
 *   ),
 *   @OA\Response(
 *     response=404,
 *     description="Product not found",
 *     @OA\JsonContent(
 *       type="object",
 *       @OA\Property(property="error", type="string", example="Product not found")
 *     )
 *   )
 * )
 */

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


/**
 * @OA\Delete(
 *   path="/products/{id}",
 *   summary="Delete a product by ID",
 *   tags={"Products"},
 *   @OA\Parameter(
 *     name="id",
 *     in="path",
 *     required=true,
 *     description="Product ID to delete",
 *     @OA\Schema(type="integer", example=1)
 *   ),
 *   @OA\Response(
 *     response=200,
 *     description="Product deleted successfully",
 *     @OA\JsonContent(
 *       @OA\Property(property="message", type="string", example="Product deleted successfully")
 *     )
 *   ),
 *   @OA\Response(
 *     response=404,
 *     description="Product not found",
 *     @OA\JsonContent(
 *       @OA\Property(property="error", type="string", example="Product not found")
 *     )
 *   ),
 *   @OA\Response(
 *     response=400,
 *     description="Error deleting product",
 *     @OA\JsonContent(
 *       @OA\Property(property="error", type="string", example="Unable to delete product")
 *     )
 *   )
 * )
 */
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

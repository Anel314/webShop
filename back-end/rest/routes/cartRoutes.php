<?php

Flight::group('/cart', function () {

    // Get all cart items for a user
    /**
     * @OA\Get(
     *     path="/cart/{user_id}",
     *     summary="Get all cart items for a user",
     *     tags={"Cart"},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         required=true,
     *         description="The ID of the user whose cart items are retrieved",
     *         @OA\Schema(type="integer", example=42)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of cart items retrieved successfully",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 example={         
     *               "cart_id": 13,
     *               "cart_user_id": 1,
     *               "product_id": 1,
     *               "quantity": 3,
     *               "cart_created_at": "2025-10-29 15:41:56",
     *               "product_id_col": 1,
     *               "name": "Wireless Mouse",
     *               "description": "Ergonomic wireless mouse",
     *               "price": "25.99",
     *               "stock_quantity": 111,
     *               "category_id": 1,
     *               "product_created_at": "2025-10-26 00:02:06",
     *               "updated_at": "2025-10-29 15:41:56",
     *               "product_owner_id": 1
     *             }
     *                 
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error retrieving cart items",
     *         @OA\JsonContent(example={"error": "Database connection failed"})
     *     )
     * )
     */
    Flight::route('GET /@user_id', function ($user_id) {
        try {
            $items = Flight::cart()->get_all_items($user_id);
            Flight::json($items);
        } catch (Exception $e) {
            Flight::json(["error" => $e->getMessage()], 400);
        }
    });


    // Get total price for user's cart
    /**
     * @OA\Get(
     *     path="/cart/{user_id}/total",
     *     summary="Get total price of all items in a user's cart",
     *     tags={"Cart"},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         required=true,
     *         description="The ID of the user whose total cart price is calculated",
     *         @OA\Schema(type="integer", example=42)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Total price calculated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             example={"total_price": 129.97}
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error calculating total price",
     *         @OA\JsonContent(example={"error": "Some error message"})
     *     )
     * )
     */
    Flight::route('GET /@user_id/total', function ($user_id) {
        try {
            $total = Flight::cart()->get_total_item_price($user_id);
            Flight::json(["total_price" => $total]);
        } catch (Exception $e) {
            Flight::json(["error" => $e->getMessage()], 400);
        }
    });


    // Add item to cart
    /**
     * @OA\Post(
     *     path="/cart",
     *     summary="Add a new item to a user's cart",
     *     tags={"Cart"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id", "product_id", "quantity"},
     *             @OA\Property(property="user_id", type="integer", example=42),
     *             @OA\Property(property="product_id", type="integer", example=10),
     *             @OA\Property(property="quantity", type="integer", example=3)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Item added or updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             example={
     *                 "message": "Item added/updated successfully",
     *                 "item": {
     *                     "user_id": 42,
     *                     "product_id": 10,
     *                     "quantity": 3
     *                 }
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Missing or invalid fields",
     *         @OA\JsonContent(example={"error": "Missing required fields: user_id, product_id, quantity"})
     *     )
     * )
     */
    Flight::route('POST /', function () {
        $data = Flight::request()->data->getData();

        if (empty($data['user_id']) || empty($data['product_id']) || empty($data['quantity'])) {
            Flight::json(["error" => "Missing required fields: user_id, product_id, quantity"], 400);
            return;
        }

        try {
            $item = Flight::cart()->add_item(
                intval($data['user_id']),
                intval($data['product_id']),
                intval($data['quantity'])
            );
            Flight::json(["message" => "Item added/updated successfully", "item" => $item], 201);
        } catch (Exception $e) {
            Flight::json(["error" => $e->getMessage()], 400);
        }
    });


    // Update item quantity
    /**
     * @OA\Put(
     *     path="/cart",
     *     summary="Update the quantity of a product in a user's cart",
     *     tags={"Cart"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id", "product_id", "quantity"},
     *             @OA\Property(property="user_id", type="integer", example=42),
     *             @OA\Property(property="product_id", type="integer", example=10),
     *             @OA\Property(property="quantity", type="integer", example=5)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cart item updated successfully",
     *         @OA\JsonContent(
     *             example={
     *                 "message": "Cart item updated successfully",
     *                 "item": {
     *                     "user_id": 42,
     *                     "product_id": 10,
     *                     "quantity": 5
     *                 }
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Missing or invalid data",
     *         @OA\JsonContent(example={"error": "Missing required fields: user_id, product_id, quantity"})
     *     )
     * )
     */
    Flight::route('PUT /', function () {
        $data = Flight::request()->data->getData();

        if (empty($data['user_id']) || empty($data['product_id']) || !isset($data['quantity'])) {
            Flight::json(["error" => "Missing required fields: user_id, product_id, quantity"], 400);
            return;
        }

        try {
            $updated = Flight::cart()->update_item_quantity($data);
            Flight::json(["message" => "Cart item updated successfully", "item" => $updated]);
        } catch (Exception $e) {
            Flight::json(["error" => $e->getMessage()], 400);
        }
    });


    // Remove item from cart
    /**
     * @OA\Delete(
     *     path="/cart/{user_id}/{product_id}",
     *     summary="Remove a specific item from a user's cart",
     *     tags={"Cart"},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         required=true,
     *         description="The user's ID",
     *         @OA\Schema(type="integer", example=42)
     *     ),
     *     @OA\Parameter(
     *         name="product_id",
     *         in="path",
     *         required=true,
     *         description="The product's ID",
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Item removed successfully",
     *         @OA\JsonContent(example={"message": "Item removed successfully"})
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Item not found",
     *         @OA\JsonContent(example={"error": "Item not found"})
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request or database error",
     *         @OA\JsonContent(example={"error": "Some error message"})
     *     )
     * )
     */
    Flight::route('DELETE /@user_id/@product_id', function ($user_id, $product_id) {
        try {
            $deleted = Flight::cart()->remove_item(intval($user_id), intval($product_id));
            if ($deleted) {
                Flight::json(["message" => "Item removed successfully"]);
            } else {
                Flight::json(["error" => "Item not found"], 404);
            }
        } catch (Exception $e) {
            Flight::json(["error" => $e->getMessage()], 400);
        }
    });


    // Clear cart for a user
    /**
     * @OA\Delete(
     *     path="/cart/{user_id}",
     *     summary="Clear all items in a user's cart",
     *     tags={"Cart"},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         required=true,
     *         description="The ID of the user whose cart will be cleared",
     *         @OA\Schema(type="integer", example=42)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cart cleared successfully",
     *         @OA\JsonContent(example={"message": "Cart cleared successfully"})
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Failed to clear cart or invalid request",
     *         @OA\JsonContent(example={"error": "Failed to clear cart"})
     *     )
     * )
     */
    Flight::route('DELETE /@user_id', function ($user_id) {
        try {
            $cleared = Flight::cart()->clear_cart(intval($user_id));
            if ($cleared) {
                Flight::json(["message" => "Cart cleared successfully"]);
            } else {
                Flight::json(["error" => "Failed to clear cart"], 400);

            }
        } catch (Exception $e) {
            Flight::json(["error" => $e->getMessage()], 400);
        }
    });
});


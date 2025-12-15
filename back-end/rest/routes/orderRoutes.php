<?php

// ORDER ROUTES

// Get all orders
/**
 * @OA\Get(
 * path="/orders",
 * summary="Get all orders",
 * tags={"Orders"},
 * @OA\Response(
 * response=200,
 * description="A list of all orders",
 * @OA\JsonContent(
 * type="array",
 * @OA\Items(
 * type="object",
 * example={
 * "id": 1,
 * "user_id": 123,
 * "status": "pending",
 * "total_amount": 99.99,
 * "shipping_address": "123 Main St",
 * "order_date": "2025-11-12 18:00:00"
 * }
 * )
 * )
 * ),
 * @OA\Response(
 * response=400,
 * description="Bad request",
 * @OA\JsonContent(
 * type="object",
 * example={"error": "Some error message"}
 * )
 * )
 * )
 */
Flight::route('GET /orders', function () {
    Flight::middleware()->verifyToken(Flight::request()->getHeader('auth'));

    try {
        Flight::json(Flight::order()->getAll());
    } catch (Exception $e) {
        Flight::json(["error" => $e->getMessage()], 400);
    }
});

// Get order by ID
/**
 * @OA\Get(
 * path="/orders/{id}",
 * summary="Get order by ID",
 * tags={"Orders"},
 * @OA\Parameter(
 * name="id",
 * in="path",
 * required=true,
 * description="The ID of the order to retrieve",
 * @OA\Schema(type="integer", example=1)
 * ),
 * @OA\Response(
 * response=200,
 * description="Order retrieved successfully",
 * @OA\JsonContent(
 * type="object",
 * example={
 * "id": 1,
 * "user_id": 123,
 * "status": "pending",
 * "total_amount": 99.99,
 * "shipping_address": "123 Main St",
 * "order_date": "2025-11-12 18:00:00"
 * }
 * )
 * ),
 * @OA\Response(
 * response=404,
 * description="Order not found",
 * @OA\JsonContent(
 * type="object",
 * example={"error": "Order not found"}
 * )
 * ),
 * @OA\Response(
 * response=400,
 * description="Invalid request",
 * @OA\JsonContent(
 * type="object",
 * example={"error": "Some error message"}
 * )
 * )
 * )
 */
Flight::route('GET /orders/@id', function ($id) {
    Flight::middleware()->verifyToken(Flight::request()->getHeader('auth'));

    try {
        $order = Flight::order()->get_order($id);
        if ($order) {
            Flight::json($order);
        } else {
            Flight::json(["error" => "Order not found"], 404);
        }
    } catch (Exception $e) {
        Flight::json(["error" => $e->getMessage()], 400);
    }
});

// Get orders by user ID
/**
 * @OA\Get(
 * path="/orders/user/{user_id}",
 * summary="Get orders by user ID",
 * tags={"Orders"},
 * @OA\Parameter(
 * name="user_id",
 * in="path",
 * required=true,
 * description="The ID of the user whose orders to retrieve",
 * @OA\Schema(type="integer", example=123)
 * ),
 * @OA\Response(
 * response=200,
 * description="List of user's orders retrieved successfully",
 * @OA\JsonContent(
 * type="array",
 * @OA\Items(
 * type="object",
 * example={
 * "id": 1,
 * "user_id": 123,
 * "status": "pending",
 * "total_amount": 99.99,
 * "shipping_address": "123 Main St",
 * "order_date": "2025-11-12 18:00:00"
 * }
 * )
 * )
 * ),
 * @OA\Response(
 * response=404,
 * description="No orders found for this user",
 * @OA\JsonContent(
 * type="object",
 * example={"error": "No orders found for this user"}
 * )
 * ),
 * @OA\Response(
 * response=400,
 * description="Invalid request",
 * @OA\JsonContent(
 * type="object",
 * example={"error": "Some error message"}
 * )
 * )
 * )
 */
Flight::route('GET /orders/user/@user_id', function ($user_id) {
    Flight::middleware()->verifyToken(Flight::request()->getHeader('auth'));

    try {
        $orders = Flight::order()->get_orders_by_user($user_id);
        if ($orders) {
            Flight::json($orders);
        } else {
            Flight::json(["error" => "No orders found for this user"], 404);
        }
    } catch (Exception $e) {
        Flight::json(["error" => $e->getMessage()], 400);
    }
});

// Create a new order
/**
 * @OA\Post(
 *   path="/orders",
 *   summary="Create a new order from a user's cart",
 *   tags={"Orders"},
 *   @OA\RequestBody(
 *     required=true,
 *     description="User ID and shipping address are required to create an order.",
 *     @OA\JsonContent(
 *       required={"user_id", "shipping_address"},
 *       @OA\Property(property="user_id", type="integer", description="ID of the user placing the order", example=123),
 *       @OA\Property(property="shipping_address", type="string", description="Shipping address", example="123 Main St, Anytown, USA"),
 *       @OA\Property(property="status", type="string", description="Order status (optional, defaults to 'pending')", example="pending")
 *     )
 *   ),
 *   @OA\Response(
 *     response=201,
 *     description="Order created successfully",
 *     @OA\JsonContent(
 *       type="object",
 *       @OA\Property(property="message", type="string", example="Order created successfully"),
 *       @OA\Property(
 *         property="order",
 *         type="object",
 *         @OA\Property(property="id", type="integer", example=2),
 *         @OA\Property(property="user_id", type="integer", example=123),
 *         @OA\Property(property="status", type="string", example="pending"),
 *         @OA\Property(property="total_amount", type="number", format="float", example=199.98),
 *         @OA\Property(property="shipping_address", type="string", example="123 Main St, Anytown, USA"),
 *         @OA\Property(property="order_date", type="string", format="date-time", example="2025-11-12 18:01:00")
 *       )
 *     )
 *   ),
 *   @OA\Response(
 *     response=400,
 *     description="Invalid request or order creation failed",
 *     @OA\JsonContent(
 *       type="object",
 *       oneOf={
 *         @OA\Schema(
 *           @OA\Property(property="error", type="string", example="Invalid order data")
 *         ),
 *         @OA\Schema(
 *           @OA\Property(property="error", type="string", example="No items in cart to create order")
 *         ),
 *         @OA\Schema(
 *           @OA\Property(property="error", type="string", example="Product ID 45 not found.")
 *         ),
 *         @OA\Schema(
 *           @OA\Property(property="error", type="string", example="Not enough stock for product ID 45.")
 *         )
 *       }
 *     )
 *   )
 * )
 */
Flight::route('POST /orders', function () {
    Flight::middleware()->verifyToken(Flight::request()->getHeader('auth'));

    $data = Flight::request()->data->getData();

    if (empty($data['user_id']) || empty($data['shipping_address'])) {
        Flight::json(["error" => "Invalid order data"], 400);
        return;
    }

    $order_items = Flight::cart()->get_all_items($data['user_id']);
    if (!$order_items) {
        Flight::json(["error" => "No items in cart to create order"], 400);
        return;
    }

    $total_amount = Flight::cart()->get_total_item_price($data['user_id']);





    try {
        $orderData = [
            "user_id" => $data["user_id"],
            "status" => $data["status"] ?? "pending",
            "shipping_address" => $data["shipping_address"],
            "total_amount" => $total_amount,
            "order_date" => date("Y-m-d H:i:s")
        ];
        $order = Flight::order()->add($orderData);



        foreach ($order_items as $item) {
            $product = Flight::product()->get_product_by_id($item["product_id"]);
            $productId = $item["product_id"];
            if (!$product)
                throw new Exception("Product ID $productId not found.");
            if ($product["stock_quantity"] < $item["quantity"])
                throw new Exception("Not enough stock for product ID $productId.");
            if (!$productId || $item["quantity"] <= 0)
                continue;

            Flight::orderProducts()->add_product_to_order($order["id"], $item);

            Flight::product()->decrease_quantity($productId, $item["quantity"]);

            $priceAtPurchase = $product["price"] * $item["quantity"];

            $orderProductsEntity = [
                "order_id" => $order["id"],
                "product_id" => $productId,
                "quantity" => $item["quantity"],
                "price_at_purchase" => $priceAtPurchase
            ];


        }
        Flight::cart()->clear_cart($data['user_id']);
        Flight::json([
            "message" => "Order created successfully",
            "order" => $order
        ], 201);

    } catch (Exception $e) {
        Flight::json(["error" => $e->getMessage()], 400);
    }
});
//28

// Update order by ID
/**
 * @OA\Put(
 * path="/orders/{id}",
 * summary="Update order by ID",
 * tags={"Orders"},
 * @OA\Parameter(
 * name="id",
 * in="path",
 * required=true,
 * description="The ID of the order to update",
 * @OA\Schema(type="integer", example=1)
 * ),
 * @OA\RequestBody(
 * required=true,
 * description="Data to update for the order",
 * @OA\JsonContent(
 * type="object",
 * @OA\Property(property="status", type="string", description="New status of the order", example="shipped"),
 * @OA\Property(property="shipping_address", type="string", description="Updated shipping address", example="456 New Ave, Othertown, USA")
 * )
 * ),
 * @OA\Response(
 * response=200,
 * description="Order updated successfully",
 * @OA\JsonContent(
 * type="object",
 * example={
 * "id": 1,
 * "user_id": 123,
 * "status": "shipped",
 * "total_amount": 99.99,
 * "shipping_address": "456 New Ave, Othertown, USA",
 * "order_date": "2025-11-12 18:00:00"
 * }
 * )
 * ),
 * @OA\Response(
 * response=404,
 * description="Order not found",
 * @OA\JsonContent(
 * type="object",
 * example={"error": "Order not found"}
 * )
 * ),
 * @OA\Response(
 * response=400,
 * description="Invalid request",
 * @OA\JsonContent(
 * type="object",
 * example={"error": "Some error message"}
 * )
 * )
 * )
 */
Flight::route('PUT /orders/@id', function ($id) {
    Flight::middleware()->verifyToken(Flight::request()->getHeader('auth'));

    $data = Flight::request()->data->getData();
    try {
        $existing = Flight::order()->get_order($id);
        if (!$existing) {
            Flight::json(["error" => "Order not found"], 404);
            return;
        }

        $updated = Flight::order()->update($data, $id);
        Flight::json("Order updated successfully");
    } catch (Exception $e) {
        Flight::json(["error" => $e->getMessage()], 400);
    }
});

// Delete order by ID
/**
 * @OA\Delete(
 * path="/orders/{id}",
 * summary="Delete order by ID",
 * tags={"Orders"},
 * @OA\Parameter(
 * name="id",
 * in="path",
 * required=true,
 * description="The ID of the order to delete",
 * @OA\Schema(type="integer", example=1)
 * ),
 * @OA\Response(
 * response=200,
 * description="Order deleted successfully",
 * @OA\JsonContent(
 * type="object",
 * example={"message": "Order deleted successfully"}
 * )
 * ),
 * @OA\Response(
 * response=404,
 * description="Order not found",
 * @OA\JsonContent(
 * type="object",
 * example={"error": "Order not found"}
 * )
 * ),
 * @OA\Response(
 * response=400,
 * description="Invalid request",
 * @OA\JsonContent(
 * type="object",
 * example={"error": "Some error message"}
 * )
 * )
 * )
 */
Flight::route('DELETE /orders/@id', function ($id) {
    Flight::middleware()->verifyToken(Flight::request()->getHeader('auth'));

    try {
        $existing = Flight::order()->get_order($id);
        if (!$existing) {
            Flight::json(["error" => "Order not found"], 404);
            return;
        }

        Flight::order()->delete($id);
        Flight::json(["message" => "Order deleted successfully"]);
    } catch (Exception $e) {
        Flight::json(["error" => $e->getMessage()], 400);
    }
});
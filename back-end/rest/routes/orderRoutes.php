<?php

// ORDER ROUTES

// Get all orders
Flight::route('GET /orders', function () {
    try {
        Flight::json(Flight::order()->getAll());
    } catch (Exception $e) {
        Flight::json(["error" => $e->getMessage()], 400);
    }
});

// Get order by ID
Flight::route('GET /orders/@id', function ($id) {
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
Flight::route('GET /orders/user/@user_id', function ($user_id) {
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
Flight::route('POST /orders', function () {
    $data = Flight::request()->data->getData();

    if (empty($data['user_id']) || empty($data['items']) || !is_array($data['items'])) {
        Flight::json(["error" => "Invalid order data"], 400);
        return;
    }

    try {
        $orderData = [
            "user_id" => $data["user_id"],
            "status" => $data["status"] ?? "pending",
            "total_amount" => $data["total_amount"] ?? 0,
            "shipping_address" => $data["shipping_address"] ?? null,
            "order_date" => date("Y-m-d H:i:s")
        ];
        $order = Flight::order()->add($orderData);

        foreach ($data["items"] as $item) {
            $productId = $item["product_id"] ?? null;
            $quantity = $item["quantity"] ?? 0;

            if (!$productId || $quantity <= 0)
                continue;

            $product = Flight::product()->get_product_by_id($productId);
            if (!$product)
                throw new Exception("Product ID $productId not found.");
            if ($product["stock_quantity"] < $quantity)
                throw new Exception("Not enough stock for product ID $productId.");

            Flight::product()->decrease_quantity($productId, $quantity);

            $priceAtPurchase = $product["price"];
            Flight::order()->query(
                "INSERT INTO order_products (order_id, product_id, quantity, price_at_purchase) VALUES (:order_id, :product_id, :quantity, :price)",
                [
                    "order_id" => $order["id"],
                    "product_id" => $productId,
                    "quantity" => $quantity,
                    "price" => $priceAtPurchase
                ]
            );
        }

        Flight::json([
            "message" => "Order created successfully",
            "order" => $order
        ], 201);

    } catch (Exception $e) {
        Flight::json(["error" => $e->getMessage()], 400);
    }
});

// Update order by ID
Flight::route('PUT /orders/@id', function ($id) {
    $data = Flight::request()->data->getData();
    try {
        $existing = Flight::order()->get_order($id);
        if (!$existing) {
            Flight::json(["error" => "Order not found"], 404);
            return;
        }

        $updated = Flight::order()->update($data, $id);
        Flight::json($updated);
    } catch (Exception $e) {
        Flight::json(["error" => $e->getMessage()], 400);
    }
});

// Delete order by ID
Flight::route('DELETE /orders/@id', function ($id) {
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

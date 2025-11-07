<?php


// Get all cart items for a user
Flight::route('GET /cart/@user_id', function ($user_id) {
    try {
        $items = Flight::cart()->get_all_items($user_id);
        Flight::json($items);
    } catch (Exception $e) {
        Flight::json(["error" => $e->getMessage()], 400);
    }
});

// Get total price for user's cart
Flight::route('GET /cart/@user_id/total', function ($user_id) {
    try {
        $total = Flight::cart()->get_total_item_price($user_id);
        Flight::json(["total_price" => $total]);
    } catch (Exception $e) {
        Flight::json(["error" => $e->getMessage()], 400);
    }
});

// Add item to cart
Flight::route('POST /cart', function () {
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
Flight::route('PUT /cart', function () {
    $data = Flight::request()->data->getData();

    if (empty($data['user_id']) || empty($data['product_id']) || !isset($data['quantity'])) {
        Flight::json(["error" => "Missing required fields: user_id, product_id, quantity"], 400);
        return;
    }

    try {
        $updated = Flight::cart()->update_item_quantity(
            intval($data['user_id']),
            intval($data['product_id']),
            intval($data['quantity'])
        );
        Flight::json(["message" => "Cart item updated successfully", "item" => $updated]);
    } catch (Exception $e) {
        Flight::json(["error" => $e->getMessage()], 400);
    }
});

// Remove item from cart
Flight::route('DELETE /cart/@user_id/@product_id', function ($user_id, $product_id) {
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
Flight::route('DELETE /cart/@user_id', function ($user_id) {
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

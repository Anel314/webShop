<?php

// Get all users
Flight::route('GET /users', function () {
    Flight::json(Flight::users()->getAll());
});

// Get user by ID
Flight::route('GET /users/@id', function ($id) {
    try {
        $user = Flight::users()->getById($id);
        if ($user) {
            Flight::json($user);
        } else {
            Flight::json(["error" => "User not found"], 404);
        }
    } catch (Exception $e) {
        Flight::json(["error" => $e->getMessage()], 400);
    }
});

// Add new user
Flight::route('POST /users', function () {
    $data = Flight::request()->data->getData();
    try {
        $user = Flight::users()->add_user($data); // use add_user from UsersService for validation
        Flight::json($user, 201);
    } catch (Exception $e) {
        Flight::json(["error" => $e->getMessage()], 400);
    }
});

// Update existing user
Flight::route('PUT /users/@id', function ($id) {
    $data = Flight::request()->data->getData();
    try {
        $existing = Flight::users()->getById($id);
        if (!$existing) {
            Flight::json(["error" => "User not found"], 404);
            return;
        }

        $updated = Flight::users()->update($data, $id);
        Flight::json($updated);
    } catch (Exception $e) {
        Flight::json(["error" => $e->getMessage()], 400);
    }
});

// Delete user
Flight::route('DELETE /users/@id', function ($id) {
    try {
        $existing = Flight::users()->getById($id);
        if (!$existing) {
            Flight::json(["error" => "User not found"], 404);
            return;
        }

        Flight::users()->delete($id);
        Flight::json(["message" => "User deleted successfully"]);
    } catch (Exception $e) {
        Flight::json(["error" => $e->getMessage()], 400);
    }
});
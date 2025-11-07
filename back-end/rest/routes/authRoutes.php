<?php

// Get user by username
Flight::route('GET /auth/username/@username', function ($username) {
    try {
        $user = Flight::auth()->get_user_by_username($username);
        if ($user) {
            Flight::json($user);
        } else {
            Flight::json(["error" => "User not found"], 404);
        }
    } catch (Exception $e) {
        Flight::json(["error" => $e->getMessage()], 400);
    }
});

// Get user by email
Flight::route('GET /auth/email/@email', function ($email) {
    try {
        $user = Flight::auth()->get_user_by_email($email);
        if ($user) {
            Flight::json($user);
        } else {
            Flight::json(["error" => "User not found"], 404);
        }
    } catch (Exception $e) {
        Flight::json(["error" => $e->getMessage()], 400);
    }
});

// Login user
Flight::route('POST /login', function () {
    $data = Flight::request()->data->getData();

    if (empty($data['identifier']) || empty($data['password'])) {
        Flight::json(["error" => "Missing required fields: identifier, password"], 400);
        return;
    }

    try {
        $user = Flight::auth()->check_login($data);
        Flight::json(["message" => "Login successful", "user" => $user]);
    } catch (Exception $e) {
        Flight::json(["error" => $e->getMessage()], 400);
    }
});

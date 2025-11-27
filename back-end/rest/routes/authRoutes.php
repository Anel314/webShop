<?php

// Get user by username
/**
 * @OA\Get(
 *     path="/auth/username/{username}",
 *     summary="Get user by username",
 *     tags={"Authentication"},
 *     @OA\Parameter(
 *         name="username",
 *         in="path",
 *         required=true,
 *         description="The username of the user to retrieve",
 *         @OA\Schema(type="string", example="johndoe")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User retrieved successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             example={
 *                 "id": 123,
 *                 "username": "johndoe",
 *                 "email": "johndoe@example.com"
 *             }
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found",
 *         @OA\JsonContent(
 *             type="object",
 *             example={"error": "User not found"}
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid request",
 *         @OA\JsonContent(
 *             type="object",
 *             example={"error": "Some error message"}
 *         )
 *     )
 * )
 */
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
/**
 * @OA\Get(
 *     path="/auth/email/{email}",
 *     summary="Get user by email",
 *     tags={"Authentication"},
 *     @OA\Parameter(
 *         name="email",
 *         in="path",
 *         required=true,
 *         description="The email of the user to retrieve",
 *         @OA\Schema(type="string", format="email", example="johndoe@example.com")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User retrieved successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             example={
 *                 "id": 123,
 *                 "username": "johndoe",
 *                 "email": "johndoe@example.com"
 *             }
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found",
 *         @OA\JsonContent(
 *             type="object",
 *             example={"error": "User not found"}
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid request",
 *         @OA\JsonContent(
 *             type="object",
 *             example={"error": "Some error message"}
 *         )
 *     )
 * )
 */
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
/**
 * @OA\Post(
 *     path="/login",
 *     summary="Authenticate a user and log in",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"identifier", "password"},
 *             @OA\Property(property="identifier", type="string", description="Username or email", example="johndoe"),
 *             @OA\Property(property="password", type="string", format="password", example="mySecurePass123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Login successful",
 *         @OA\JsonContent(
 *             type="object",
 *             example={
 *                 "message": "Login successful",
 *                 "user": {
 *                     "id": 123,
 *                     "username": "johndoe",
 *                     "email": "johndoe@example.com"
 *                 }
 *             }
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Missing or invalid credentials",
 *         @OA\JsonContent(
 *             type="object",
 *             example={"error": "Missing required fields: identifier, password"}
 *         )
 *     )
 * )
 */
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



// Register user
/**
 * @OA\Post(
 *     path="/register",
 *     summary="Register a new user account",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"username", "email", "password", "first_name", "last_name", "address", "profile_image_url"},
 *             @OA\Property(property="username", type="string", example="johndoe"),
 *             @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="mySecurePass123"),
 *             @OA\Property(property="first_name", type="string", example="John"),
 *             @OA\Property(property="last_name", type="string", example="Doe"),
 *             @OA\Property(property="address", type="string", example="123 Main Street, NY"),
 *             @OA\Property(property="profile_image_url", type="string", example="https://example.com/images/profile.jpg")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Registration successful",
 *         @OA\JsonContent(
 *             type="object",
 *             example={
 *                 "message": "Registration successful",
 *                 "user": {
 *                     "id": 123,
 *                     "username": "johndoe",
 *                     "email": "johndoe@example.com",
 *                     "first_name": "John",
 *                     "last_name": "Doe",
 *                     "address": "123 Main Street, NY",
 *                     "profile_image_url": "https://example.com/images/profile.jpg"
 *                 }
 *             }
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid or missing registration data",
 *         @OA\JsonContent(
 *             type="object",
 *             example={"error": "Missing required fields or invalid data"}
 *         )
 *     )
 * )
 */

Flight::route('POST /register', function () {
    $data = Flight::request()->data->getData();

    try {
        $user = Flight::auth()->register($data);
        Flight::json(["message" => "Registration successful", "user" => $user]);
    } catch (Exception $e) {
        Flight::json(["error" => $e->getMessage()], 400);
    }
});


?>
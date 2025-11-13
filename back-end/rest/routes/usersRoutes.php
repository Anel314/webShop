<?php

/**
 * @OA\Get(
 * path="/users",
 * summary="Get all users",
 * tags={"Users"},
 * @OA\Response(
 * response=200,
 * description="A list of users",
 * @OA\JsonContent(
 * type="array",
 * @OA\Items(
 * type="object",
 * example={
 * "id": "1",
 * "username": "jdoe",
 * "email": "jdoe@example.com",
 * "first_name": "John",
 * "last_name": "Doe",
 * "address": "123 Maple St, Springfield",
 * "profile_image_url": "https://example.com/images/jdoe.jpg",
 * "created_at": "2023-01-12 09:21:33",
 * "updated_at": "2023-04-15 16:18:09"
 * }
 * )
 * )
 * )
 * )
 */
// Get all users
Flight::route('GET /users', function () {
    Flight::json(Flight::users()->getAll());
});

/**
 * @OA\Get(
 * path="/users/{id}",
 * summary="Get user by ID",
 * tags={"Users"},
 * @OA\Parameter(
 * name="id",
 * in="path",
 * required=true,
 * description="The ID of the user to retrieve",
 * @OA\Schema(type="string", example="1")
 * ),
 * @OA\Response(
 * response=200,
 * description="User retrieved successfully",
 * @OA\JsonContent(
 * type="object",
 * example={
 * "id": "1",
 * "username": "jdoe",
 * "email": "jdoe@example.com",
 * "first_name": "John",
 * "last_name": "Doe",
 * "address": "123 Maple St, Springfield",
 * "profile_image_url": "https://example.com/images/jdoe.jpg",
 * "created_at": "2023-01-12 09:21:33",
 * "updated_at": "2023-04-15 16:18:09"
 * }
 * )
 * ),
 * @OA\Response(
 * response=404,
 * description="User not found",
 * @OA\JsonContent(
 * type="object",
 * example={"error": "User not found"}
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

/**
 * @OA\Post(
 * path="/users",
 * summary="Add new user",
 * tags={"Users"},
 * @OA\RequestBody(
 * required=true,
 * description="User object to be added",
 * @OA\JsonContent(
 * type="object",
 * example={
 * "username": "jdoe",
 * "email": "jdoe@example.com",
 * "password": "aStrongPassword123!",
 * "first_name": "John",
 * "last_name": "Doe",
 * "address": "123 Maple St, Springfield",
 * "profile_image_url": "https://example.com/images/jdoe.jpg"
 * }
 * )
 * ),
 * @OA\Response(
 * response=201,
 * description="User created successfully",
 * @OA\JsonContent(
 * type="object",
 * example={
 * "id": "2",
 * "username": "jdoe",
 * "email": "jdoe@example.com",
 * "first_name": "John",
 * "last_name": "Doe",
 * "address": "123 Maple St, Springfield",
 * "profile_image_url": "https://example.com/images/jdoe.jpg",
 * "created_at": "2023-11-12 19:00:00",
 * "updated_at": "2023-11-12 19:00:00"
 * }
 * )
 * ),
 * @OA\Response(
 * response=400,
 * description="Invalid user data",
 * @OA\JsonContent(
 * type="object",
 * example={"error": "Validation error: Username already exists"}
 * )
 * )
 * )
 */
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

/**
 * @OA\Put(
 * path="/users/{id}",
 * summary="Update existing user",
 * tags={"Users"},
 * @OA\Parameter(
 * name="id",
 * in="path",
 * required=true,
 * description="The ID of the user to update",
 * @OA\Schema(type="string", example="1")
 * ),
 * @OA\RequestBody(
 * required=true,
 * description="User object with updated fields. Any field is optional.",
 * @OA\JsonContent(
 * type="object",
 * example={
 * "username": "jdoe_new",
 * "first_name": "Johnathan",
 * "last_name": "Doe-Smith",
 * "address": "456 Oak Ave, Springfield",
 * "profile_image_url": "https://example.com/images/jdoe_new.jpg"
 * }
 * )
 * ),
 * @OA\Response(
 * response=200,
 * description="User updated successfully",
 * @OA\JsonContent(
 * type="object",
 * example={
 * "id": "1",
 * "username": "jdoe_new",
 * "email": "jdoe@example.com",
 * "first_name": "Johnathan",
 * "last_name": "Doe-Smith",
 * "address": "456 Oak Ave, Springfield",
 * "profile_image_url": "https://example.com/images/jdoe_new.jpg",
 * "created_at": "2023-01-12 09:21:33",
 * "updated_at": "2023-11-12 19:05:00"
 * }
 * )
 * ),
 * @OA\Response(
 * response=404,
 * description="User not found",
 * @OA\JsonContent(
 * type="object",
 * example={"error": "User not found"}
 * )
 * ),
 * @OA\Response(
 * response=400,
 * description="Invalid user data",
 * @OA\JsonContent(
 * type="object",
 * example={"error": "Validation error: Username already taken"}
 * )
 * )
 * )
 */
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

/**
 * @OA\Delete(
 * path="/users/{id}",
 * summary="Delete user",
 * tags={"Users"},
 * @OA\Parameter(
 * name="id",
 * in="path",
 * required=true,
 * description="The ID of the user to delete",
 * @OA\Schema(type="string", example="1")
 * ),
 * @OA\Response(
 * response=200,
 * description="User deleted successfully",
 * @OA\JsonContent(
 * type="object",
 * example={"message": "User deleted successfully"}
 * )
 * ),
 * @OA\Response(
 * response=404,
 * description="User not found",
 * @OA\JsonContent(
 * type="object",
 * example={"error": "User not found"}
 * )
 * ),
 * @OA\Response(
 * response=400,
 * description="Error deleting user",
 * @OA\JsonContent(
 * type="object",
 * example={"error": "Some error message"}
 * )
 * )
 * )
 */
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
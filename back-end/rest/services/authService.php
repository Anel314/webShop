<?php
require_once __DIR__ . "/baseService.php";
require_once __DIR__ . "/../dao/authDao.php";
require __DIR__ . '/../../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
class AuthService extends BaseService
{
    public function __construct()
    {
        parent::__construct(new AuthDao());
    }

    public function get_user_by_username($username)
    {
        if (empty($username)) {
            throw new Exception("Username is required.");
        } else if (strlen($username) < 3) {
            throw new Exception("Username must be at least 3 characters long.");
        }

        $username = trim(strtolower($username));
        return $this->dao->get_user_by_username($username);
    }
    public function get_user_by_email($email)
    {

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format.");
        }

        $email = strtolower($email);
        return $this->dao->get_user_by_email($email);
    }

    public function check_login($data)
    {

        if (empty($data['identifier']) || empty($data['password'])) {
            throw new Exception("Username/email and password are required.");
        }

        $identifier = strtolower(trim($data['identifier']));
        $user = $this->dao->get_user_by_identifier($identifier);

        if (!$user) {
            throw new Exception("User not found.");
        }

        if ($user['password_hash'] !== md5($data['password'])) {
            throw new Exception("Incorrect password.");
        }


        $jwt_payload = [
            'user' => $user,
            'iat' => time(),
            // If this parameter is not set, JWT will be valid for life. This is not a good approach
            'exp' => time() + (60 * 60 * 24) // valid for day
        ];


        $token = JWT::encode(
            $jwt_payload,
            getenv('JWT_SECRET'),
            'HS256'
        );


        return ['success' => true, 'data' => array_merge($user, ['token' => $token])];

    }


    public function register($data)
    {
        if (empty($data['username']) || strlen($data['username']) < 3) {
            throw new Exception("Username must be at least 3 characters long.");
        }
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format.");
        }
        if (empty($data['password']) || strlen($data['password']) < 6) {
            throw new Exception("Password must be at least 6 characters long.");
        }
        if (empty($data['first_name']) || strlen($data['first_name']) < 3) {
            throw new Exception("Name must be at least 3 characters long.");
        }
        if (empty($data['last_name']) || !filter_var($data['last_name'])) {
            throw new Exception("Last name must be at least 3 characters long.");
        }
        if (empty($data['address']) || strlen($data['address']) < 6) {
            throw new Exception("Must provide a valid address.");
        }

        $data['email'] = strtolower($data['email']);
        $data['password_hash'] = md5($data['password']);
        $user_password = $data['password'];
        unset($data['password']);

        $newUser = $this->dao->register($data);
        $newUser["identifier"] = $data['username'];
        $newUser["password"] = $user_password;
        return $this->check_login($newUser);








    }

}






?>
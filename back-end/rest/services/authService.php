<?php
require_once __DIR__ . "/baseService.php";
require_once __DIR__ . "/../dao/authDao.php";
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

        if ($user['password'] !== md5($data['password'])) {
            throw new Exception("Incorrect password.");
        }

        return $user;

    }


}








?>
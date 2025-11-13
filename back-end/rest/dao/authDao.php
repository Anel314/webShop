<?php
require_once __DIR__ . "/baseDao.php";

class AuthDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct("users");

    }
    public function get_user_by_username($username)
    {
        $query = "SELECT * FROM users WHERE username = :username";
        $params = [':username' => $username];
        return $this->query_unique($query, $params);
    }
    public function get_user_by_identifier($identifier)
    {
        $query = "SELECT * FROM users WHERE email = :id OR username = :id";
        $params = [':id' => $identifier];
        return $this->query_unique($query, $params);
    }

    public function check_login($identifier)
    {
        $query = "SELECT * FROM users WHERE username = :id OR email = :id";
        $stmt = $this->query_unique($query, ["id" => $identifier]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

}


?>
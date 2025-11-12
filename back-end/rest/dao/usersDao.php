<?php
require_once __DIR__ . "/baseDao.php";

class UsersDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct("users");
    }
    public function get_user_by_id($id)
    {
        return $this->getById($id);
    }


    public function get_user_by_name($name)
    {
        $query = "SELECT * FROM users WHERE username = :username";
        $params = [':username' => $name];
        return $this->query_unique($query, $params);
    }

    public function get_user_by_email($email)
    {
        $query = "SELECT * FROM users WHERE email = :email";
        $params = [':email' => $email];
        return $this->query_unique($query, $params);
    }

    public function add_user($entity)
    {
        $this->add($entity);
    }



}





?>
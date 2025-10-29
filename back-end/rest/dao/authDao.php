<?php
require_once __DIR__ ."/baseDao.php";

class AuthDao extends BaseDao {
    public function __construct() {
        parent::__construct("users");
        
    }
    public function get_user_by_username($username) {
        $query = "SELECT * FROM users WHERE username = :username";
        $params = [':username' => $username];
        return $this->query_unique($query, $params);
    }
    public function get_user_by_email($email) {
        $query = "SELECT * FROM users WHERE email = :email";
        $params = [':email' => $email];
        return $this->query_unique($query, $params);
    }   

    public function check_login($data) {
        $user = $this->get_user_by_username($data['username']);
        if (!$user) {
            $user = $this->get_user_by_email($data["email"]); 
        }
        if (!$user) {
            return false;
        }
        if($user["password"] == md5(md5($data["password"]))) {
            return true;
        }

        return false;
    }

}


?>
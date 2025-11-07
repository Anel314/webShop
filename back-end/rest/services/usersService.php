<?php
require_once __DIR__ . "/baseService.php";
require_once __DIR__ . "/../dao/usersDao.php";
class UsersService extends BaseService
{
    public function __construct()
    {
        parent::__construct(new UsersDao());
    }

    public function add_user($entity)
    {
        if ($this->get_user_by_email($entity["email"])) {
            throw new Exception("Email already exists.");
        }


        if (strlen($entity["password"]) < 6) {
            throw new Exception("Password must be at least 6 characters.");
        }


        if ($this->get_user_by_name($entity["name"])) {
            throw new Exception("Username already exists.");
        }

        $entity["password"] = md5($entity["password"]);

        return $this->dao->add($entity);
    }

    public function get_user_by_email($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format.");
        }
        return $this->dao->get_user_by_email($email);
    }


    public function get_user_by_name($name)
    {
        if (strlen($name) < 3) {
            throw new Exception("Username must be at least 3 characters.");
        }
        return $this->dao->get_user_by_name($name);
    }
    public function get_user_by_id($id)
    {
        return $this->dao->get_user_by_id($id);
    }

}






?>
<?php
require_once __DIR__."./baseService.php";
require_once __DIR__."/../dao/authDao.php";
class AuthService extends BaseService{
    public function __construct(){
        parent::__construct(new AuthDao());
    }

    public function add($entity) {
        $email_exists = $this->dao->get_user_by_email($entity['email']);
        $username_exists = $this->dao->get_user_by_name($entity['username']);
        if ($email_exists || $username_exists) {
            return [];
        }
        return parent::add($entity);
    }

    public function getAll(){
        return $this->dao->getAll();
    }
    public function update($entity, $id, $id_column = "id") {
        $email_exists = $this->dao->get_user_by_email($entity['email']);
        if(!$email_exists){
            return [];
        }
        return parent::update($entity,$id, $id_column='id');
    }



}








?>
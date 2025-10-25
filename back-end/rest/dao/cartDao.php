<?php
require_once __DIR__ ."/baseDao.php";

class CartDao extends BaseDao{
    public function __construct(){
        parent::__construct("cart");
    }

    public function get_all_items($user_id){
        $query = "SELECT * FROM cart JOIN products ON products.id = cart.product_id WHERE cart.user_id = :user_id";
        $params = [':user_id' => $user_id];
        return $this->query($query, $params);
    }

    public function get_total_item_price($user_id){
        $query = "SELECT SUM(price * quantity) AS total_price From cart JOIN products ON products.id = cart.product_id WHERE cart.user_id = :user_id;";
        $params = [':user_id' => $user_id];
        $result = $this->query_unique($query, $params);
        return $result['total_price'] ?? 0;
    }
}

?>
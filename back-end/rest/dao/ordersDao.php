<?php
require_once __DIR__ . '/baseDao.php';

class OrdersDao extends BaseDao {
    public function __construct() {
        parent::__construct("orders");
    }
    public function get_order($order_id) {
        $query = "SELECT * FROM orders WHERE id = :order_id";
        $params = [':order_id' => $order_id];
        return $this->query_unique($query, $params);
    }

    public function get_orders_by_user($user_id) {
    $query = "SELECT * FROM orders JOIN order_products ON orders.id = order_products.order_id WHERE  orders.user_id = :user_id";
        $params = [':user_id' => $user_id];
        return $this->query($query, $params);
    }

 
 
 
}

?>
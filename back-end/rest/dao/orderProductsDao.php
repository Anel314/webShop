<?php
require_once __DIR__ . '/baseDao.php';

class OrderProductsDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct("order_products");
    }
    public function get_products_by_order($order_id)
    {
        return $this->query("SELECT * FROM order_products WHERE order_id = :order_id", [':order_id' => $order_id]);
    }
    public function add_product_to_order($order_id, $entity)
    {
        $query = "INSERT INTO order_products (order_id, product_id, quantity, price_at_purchase) VALUES (:order_id, :product_id, :quantity, :price)";
        $params = [
            ':order_id' => $order_id,
            ':product_id' => $entity["product_id"],
            ':quantity' => $entity["quantity"],
            ':price' => $entity["price"]
        ];
        return $this->query($query, $params);
    }




}

?>
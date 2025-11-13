<?php
require_once __DIR__ . "/baseService.php";
require_once __DIR__ . "/../dao/orderProductsDao.php";

class OrderProductsService extends BaseService
{
    public function __construct()
    {
        parent::__construct(new OrderProductsDao());

    }

    public function get_products_by_order($order_id)
    {
        return $this->dao->get_products_by_order("SELECT * FROM order_products WHERE order_id = :order_id", [':order_id' => $order_id]);
    }
    public function add_product_to_order($order_id, $entity)
    {

        return $this->dao->add_product_to_order($order_id, $entity);
    }



}


?>
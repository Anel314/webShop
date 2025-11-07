<?php
require_once __DIR__ . "/baseService.php";
require_once __DIR__ . "/../dao/ordersDao.php";

class OrderService extends BaseService
{
    public function __construct()
    {
        parent::__construct(new OrdersDao());

    }

    public function get_order($order_id)
    {
        return $this->dao->get($order_id);
    }

    public function get_orders_by_user($user_id)
    {
        return $this->dao->get_by_user($user_id);
    }



}


?>
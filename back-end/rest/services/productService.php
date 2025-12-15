<?php
require_once __DIR__ . "/baseService.php";
require_once __DIR__ . "/../dao/productsDao.php";
class ProductService extends BaseService
{
    public function __construct()
    {
        parent::__construct(new ProductsDao());

    }
    public function get_product_by_id($id)
    {
        return $this->dao->get_product_by_id($id);
    }

    public function get_products_by_category($category)
    {
        return $this->dao->get_products_by_category($category);
    }

    public function decrease_quantity($product_id, $quantity)
    {
        $product = $this->get_product_by_id($product_id);
        if (!$product) {
            throw new Exception("Product not found.");
        }

        if ($product['stock_quantity'] < $quantity) {
            throw new Exception("Insufficient stock.");
        }

        return $this->dao->decrease_quantity($product_id, $quantity);
    }
    public function get_product_by_user($user_id)
    {

        return $this->dao->get_product_by_user($user_id);
    }
}

?>
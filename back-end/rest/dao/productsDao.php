<?php
require_once __DIR__ . "/baseDao.php";

class ProductsDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct("products");

    }
    public function get_product_by_user($user_id)
    {
        $query = "SELECT * FROM products WHERE user_id = :user_id";
        $params = [':user_id' => $user_id];
        return $this->query($query, $params);
    }
    public function get_product_by_id($id)
    {
        $query = "SELECT * FROM products WHERE id = :id";
        $params = [':id' => $id];
        return $this->query_unique($query, $params);
    }
    public function get_products_by_category($category)
    {



        $query = "SELECT p.id AS product_id, p.name AS product_name, p.description AS product_description, p.price, p.stock_quantity, p.category_id, p.created_at AS product_created_at, p.updated_at AS product_updated_at, p.user_id, c.id AS category_id, c.name AS category_name, c.description AS category_description, c.created_at AS category_created_at FROM products p JOIN categories c ON p.category_id = c.id WHERE c.name = :category;";



        $params = [':category' => $category];
        return $this->query($query, $params);
    }
    public function decrease_quantity($product_id, $quantity)
    {
        $query = "UPDATE products SET stock_quantity = stock_quantity - :quantity WHERE id = :product_id";
        $params = [':product_id' => $product_id, ':quantity' => $quantity];
        $stmt = $this->connection->prepare($query);
        return $stmt->execute($params);
    }


}



?>
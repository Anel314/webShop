<?php
require_once __DIR__ ."/baseDao.php";

class ProductsDao extends BaseDao {
    public function __construct() {
        parent::__construct("products");
        
    }
    public function get_product_by_id($id) {
        $query = "SELECT * FROM products WHERE id = :id";
        $params = [':id' => $id];
        return $this->query_unique($query, $params);
    }
    public function get_products_by_category($category) {
        $query = "SELECT * FROM products JOIN categories ON products.category_id = categories.id WHERE categories.name = :category";
        $params = [':category' => $category];
        return $this->query($query, $params);
    }
    public function decrease_quantity($product_id, $quantity) {
        $query = "UPDATE products SET stock_quantity = stock_quantity - :quantity WHERE id = :product_id";
        $params = [':product_id' => $product_id, ':quantity' => $quantity];
        $stmt = $this->connection->prepare($query);
        return $stmt->execute($params);
    }
        
    
}

?>
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
    
}

$db = new ProductsDao();
echo "Products in Electronics category:\n";
$products = $db->get_products_by_category('Electronics');
print_r($products);

?>
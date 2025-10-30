
<?php
require_once __DIR__ . "/./baseService.php";
require_once __DIR__ ."/../dao/cartDao.php";
require_once __DIR__ ."/../dao/productService.php";

class OrderService extends BaseService{
    public function __construct(){
        parent::__construct(new OrdersDao());
        
    }
    public function add($data){
        $this->dao->add($data);
        $product = new ProductService();
        $this->dao->add($data);
        $product->decrease_quantity($data["product_id"],$data["quantity"]);
    }



}


?>

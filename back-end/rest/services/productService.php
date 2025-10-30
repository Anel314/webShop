
<?php
require_once __DIR__ . "/./baseService.php";
require_once __DIR__ ."/../dao/productsDao.php";
class ProductService extends BaseService{
    public function __construct(){
        parent::__construct(new ProductsDao());
        
    }
    public function add($data){
        $this->dao->add($data);
    }

    public function decrease_quantity($id, $quantity){
        $product = $this->dao->get($id);
        if($product["stock_quantyty"]< $quantity){
            return [];
        }
        return $this->dao->decrease_quantity($id, $quantity);
    }


}


?>

<?php
require_once __DIR__ . "/./baseService.php";
require_once __DIR__ ."/../dao/cartDao.php";
// require_once __DIR__ ."/../dao/productsDao.php";

class CartService extends BaseService{
    public function __construct(){
        parent::__construct(new CartDao());
        
    }

    public function add($entity){
        $data = array(
            'user_id' => $entity['user_id'],
            'product_id' => $entity['product_id'],
            'quantity' => $entity['quantity']
        );

        if ($entity['quantity'] <= 0) {
            return [];
        }
        $productsDao = new ProductsDao();
        // $productsDao->decrease_quantity($entity['product_id'], $entity['quantity']);
    
        return parent::add($data);
   }
   public function delete($entity){
    $data = array(
        'user_id' => $entity['user_id'],
        'product_id' => $entity['product_id'],
        'quantity'=> $entity['quantity']
    );
    // $productsDao = new ProductsDao();
    // $productsDao->decrease_quantity($entity['product_id'], $entity['quantity']*-1);
    return parent::delete($data);

   }
}


?>
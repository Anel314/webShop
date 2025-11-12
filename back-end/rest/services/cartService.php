<?php
require_once __DIR__ . "/./baseService.php";
require_once __DIR__ . "/../dao/cartDao.php";
// require_once __DIR__ ."/../dao/productsDao.php";

class CartService extends BaseService
{
    public function __construct()
    {
        parent::__construct(new CartDao());

    }

    public function get_all_items($user_id)
    {
        return $this->dao->get_all_items($user_id);
    }

    public function get_total_item_price($user_id)
    {
        $total = $this->dao->get_total_item_price($user_id);
        return $total ?? 0.0;
    }



    public function add_item(int $user_id, int $product_id, int $quantity = 1): array
    {
        $existingItem = $this->dao->get_cart_item($user_id, $product_id);

        if ($existingItem) {
            $newQuantity = $existingItem['quantity'] + $quantity;
            return $this->dao->update_cart_item($user_id, $product_id, $newQuantity);
        } else {
            return $this->dao->add_cart_item([
                'user_id' => $user_id,
                'product_id' => $product_id,
                'quantity' => $quantity
            ]);
        }
    }

    public function remove_item(int $user_id, int $product_id): bool
    {
        return $this->dao->delete_cart_item($user_id, $product_id);
    }

    public function update_item_quantity($data): array
    {
        if ($data["quantity"] <= 0) {
            $this->remove_item($data["user_id"], $data["product_id"]);
            return [];
        }
        return $this->dao->update_cart_item($data);
    }

    public function clear_cart(int $user_id): bool
    {
        return $this->dao->clear_cart($user_id);
    }
}


?>
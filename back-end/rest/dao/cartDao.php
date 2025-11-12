<?php
require_once __DIR__ . "/baseDao.php";

class CartDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct("cart");
    }

    public function get_all_items($user_id)
    {

        $query = "SELECT cart.id AS cart_id, cart.user_id AS cart_user_id, cart.product_id, cart.quantity, cart.created_at AS cart_created_at, products.id AS product_id_col, products.name, products.description, products.price, products.stock_quantity, products.category_id, products.created_at AS product_created_at, products.updated_at, products.user_id AS product_owner_id FROM cart JOIN products ON products.id = cart.product_id WHERE cart.user_id = :user_id;";
        $params = [':user_id' => $user_id];
        // print_r($user_id);
        return $this->query($query, $params);
    }

    public function get_total_item_price($user_id)
    {
        $query = "SELECT SUM(price * quantity) AS total_price From cart JOIN products ON products.id = cart.product_id WHERE cart.user_id = :user_id;";
        $params = [':user_id' => $user_id];
        $result = $this->query_unique($query, $params);
        return $result['total_price'] ?? 0;
    }


    public function get_cart_item($user_id, $product_id)
    {
        $query = "SELECT * FROM cart WHERE user_id = :user_id AND product_id = :product_id";
        $params = [
            ':user_id' => $user_id,
            ':product_id' => $product_id
        ];
        return $this->query_unique($query, $params);
    }

    public function add_cart_item(array $data)
    {
        return $this->add($data); // BaseDao add method
    }

    // Update a cart item by its cart id
    public function update_cart_item($entity)
    {
        $params = [":user_id" => $entity["user_id"], ":product_id" => $entity["product_id"]];

        $cart_id = $this->query_unique("SELECT id FROM cart WHERE user_id=:user_id AND product_id = :product_id", $params)["id"];
        return $this->update($entity, $cart_id, 'id');
    }


    public function delete_cart_item($user_id, $product_id): bool
    {
        $stmt = $this->connection->prepare(
            "DELETE FROM cart WHERE user_id = :user_id AND product_id = :product_id"
        );
        return $stmt->execute([
            ':user_id' => $user_id,
            ':product_id' => $product_id
        ]);
    }

    public function clear_cart($user_id): bool
    {
        $stmt = $this->connection->prepare("DELETE FROM cart WHERE user_id = :user_id");
        return $stmt->execute([':user_id' => $user_id]);
    }

}

?>
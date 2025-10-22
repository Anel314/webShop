<?php
require_once __DIR__ . "/../config.php";


class BaseDao{
    protected $connection;
    private $table_name;

    public function __construct($table_name){
        $this->table_name = $table_name;
        try{
            $this->connection = new PDO(
                "mysql:host=" . Config::DB_HOST() . ";dbname=" . Config::DB_NAME() . ";port=" . Config::DB_PORT(),
                Config::DB_USER(),
                Config::DB_PASSWORD(),
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ]
                );
                echo "Connection Established\n";
            }
        catch(PDOException $e){
            $this->connection = null;
            throw new Exception("". $e->getMessage());

        }
    }
    public function showDatabases(){
        $stmt = $this->connection->prepare("Show databases;");   
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);     
        
    }
 public function getAll(): array {
    $sql = "SELECT * FROM " . $this->table_name . ";";
    try {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "SQL Error: " . $e->getMessage();
        return [];
    }


}



}
$db = new BaseDao("users");
foreach ($db->getAll() as $row) {
    echo "------------------------------------------------------------\n";
    foreach($row as $key => $value) {
        echo"". $key .": ". $value ."\n";
    }
}
?>

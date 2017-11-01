<?php
/**
 * Created by PhpStorm.
 * User: rk521
 * Date: 24/10/17
 * Time: 01:09 PM
 */

require_once __DIR__ . '/../app/DB.php';

class Historial
{

    private $db;

    /**
     * Historial constructor.
     */
    public function __construct()
    {
        $this->db = DB::init()->getDB();
    }

    public function getHistorial($product_id) {
        $query = "SELECT * FROM dw_historial WHERE product_id=:product_id ORDER BY created_at ASC;";
        $stm = $this->db->prepare($query);
        $stm->bindValue(":product_id", $product_id, PDO::PARAM_INT);
        $stm->execute();

        $historial = $stm->fetchAll(PDO::FETCH_ASSOC);

        return json_encode(['status' => 1, 'historial' => $historial]);
    }
}

if (isset($_POST['get'])) {
    $get = $_POST['get'];
    $h = new Historial();

    switch ($get) {
        case 'historial':
            echo $h->getHistorial($_POST['product_id']);
            break;
        default:
            echo json_encode(['status' => 0, 'message' => 'Invalid Request']);
    }
}
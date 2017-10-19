<?php
/**
 * Created by PhpStorm.
 * User: rk521
 * Date: 19/10/17
 * Time: 12:28 AM
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/DB.php';

use GuzzleHttp\Client;

class Proxy
{
    private $db;

    /**
     * Proxy constructor.
     * @param $db
     */
    public function __construct()
    {
        $this->db = DB::init()->getDB();
    }

    /**
     * Devuelve todos los proxy de la BD
     *
     * @return array
     */
    public function getProxies() {
        $query = "SELECT * FROM dw_proxy;";
        $stm = $this->db->prepare($query);
        $stm->execute();

        $response = $stm->fetchAll(PDO::FETCH_ASSOC);
        return $response;
    }

    /**
     * Crea un nuevo proxy
     *
     * @param $data
     * @return string
     */
    public function addProxy($data) {
        $response = ["status" => 0];

        try {
            $query = "INSERT INTO dw_proxy (ip, puerto) VALUES (:ip, :puerto)";
            $stm = $this->db->prepare($query);
            $stm->bindValue(":ip", $data["ip"], PDO::PARAM_STR);
            $stm->bindValue(":puerto", $data["puerto"], PDO::PARAM_STR);
            $stm->execute();

            $response["status"] = 1;
        } catch (Exception $e) {
            $response["message"] = $e->getMessage();
        }

        return json_encode($response);
    }

    /**
     * Elimina un proxy
     *
     * @param $proxy_id
     * @return string
     */
    public function removeProxy($proxy_id) {
        $response = ["status" => 0];

        try {
            $query = "DELETE FROM dw_proxy WHERE id = :id;";
            $stm = $this->db->prepare($query);
            $stm->bindValue(":id", $proxy_id, PDO::PARAM_INT);
            $stm->execute();

            $response["status"] = 1;
        } catch (Exception $e) {
            $response["message"] = $e->getMessage();
        }

        return json_encode($response);
    }

    /**
     * Devuelve el primer proxy disponible
     *
     * @return string
     */
    public function getProxy() {
        $response = ["status" => 0];

        try {
            $query = "SELECT * FROM dw_proxy;";
            $stm = $this->db->prepare($query);
            $stm->execute();

            $client = new Client();

            $response = $stm->fetchAll(PDO::FETCH_ASSOC);
            foreach ($response as $proxy) {
                $client->request('GET', '/', ['proxy' => ['http' => 'tcp://localhost:8125']]);
                //print_r($client);
            }

            $response["status"] = 1;
        } catch (Exception $e) {
            $response["message"] = $e->getMessage();
        }

        return json_encode($response);
    }
}

$p = new Proxy();
echo $p->getProxy();
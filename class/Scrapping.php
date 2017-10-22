<?php
/**
 * Created by PhpStorm.
 * User: rk521
 * Date: 18/10/17
 * Time: 12:47 AM
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/DB.php';
require_once __DIR__ . '/../traits/SchemaTrait.php';

use Goutte\Client;
use GuzzleHttp\Exception\ClientException;
use Symfony\Component\DomCrawler\Crawler;

class Scrapping
{
    use SchemaTrait;
    private $client;
    private $db;

    /**
     * Scrapping constructor.
     * @param $goutte
     */
    public function __construct()
    {
        $this->client = new Client();
        $this->db = DB::init()->getDB();
    }


    /**
     * Devuelve una lista de todas las páginas a las que se hará Scrapping de la Base de datos
     *
     * @return array
     */
    public function getPages()
    {
        $query = "SELECT * FROM dw_paginas";
        $pdo = $this->db->prepare($query);
        $pdo->execute();

        $response = $pdo->fetchAll(PDO::FETCH_ASSOC);
        return $response;
    }


    /**
     * Inicia el scrapping a una lista de paginas
     */
    public function init() {
        //$pages = $this->getPages();
        $pages = ["http://www.bestbuy.com.mx/c/productos/c3"];

        foreach ($pages as $page) {
            // TODO si la tienda nos bloquea, hacemos la conexión a través de proxy
            if ($this->isBlocked($page)) {

            } else {
                $crawler = $this->client->request('GET', $page);

                $nodeValues = $crawler->filter('li')->each(function (Crawler $node, $i) {
                    return $node->text();
                });

                //print_r($nodeValues);
                echo $this->client->getResponse();
            }
        }
    }

    public function isBlocked($url) {
        try {
            $client = new GuzzleHttp\Client();
            $res = $client->request('GET', $url);
            $block = ($res->getStatusCode() == 200) ? false : true;
        } catch (ClientException $e) {
            $block = true;
        }

        return $block;
    }

    /**
     * Obtiene todos los productos de la BD para hacer scrapping
     */
    public function getAllReviews() {
        $query = "SELECT * FROM wp_pwgb_posts
                    WHERE post_type = 'reviews' 
                    AND post_status = 'publish';";
        $stm = $this->db->prepare($query);
        $stm->execute();

        $response = $stm->fetchAll(PDO::FETCH_ASSOC);
        foreach ($response as $review) {
            $metadata = $this->getMetaData($review["ID"]);
            $links = $this->productsLink();
            foreach ($links as $link) {
                if (isset($metadata[$link])) {
                    // TODO hacer scrapping, obtener el precio y comparar
                }
            }
        }

        return $response;
    }


    /**
     * Obtiene la metadata de un producto
     *
     * @param $post_id
     * @return array
     */
    public function getMetaData($post_id) {
        $query = "SELECT * FROM wp_pwgb_postmeta
                    WHERE post_id = :post_id;";
        $stm = $this->db->prepare($query);
        $stm->bindValue(":post_id", $post_id, PDO::PARAM_INT);
        $stm->execute();

        $response = $stm->fetchAll(PDO::FETCH_ASSOC);
        return $response;
    }
}

$s = new Scrapping();
$s->getAllReviews();
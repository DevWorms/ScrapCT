<?php
/**
 * Created by PhpStorm.
 * User: rk521
 * Date: 18/10/17
 * Time: 12:47 AM
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/DB.php';

use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

class Scrapping
{
    private $goutte;
    private $db;

    /**
     * Scrapping constructor.
     * @param $goutte
     */
    public function __construct()
    {
        $this->goutte = new Client();
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
            $crawler = $this->goutte->request('GET', $page);

            $nodeValues = $crawler->filter('li')->each(function (Crawler $node, $i) {
                return $node->text();
            });

            print_r($nodeValues);
        }
    }
}

$s = new Scrapping();
$s->init();
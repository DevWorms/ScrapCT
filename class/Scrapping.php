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
require_once __DIR__ . '/../traits/PriceTrait.php';

use Goutte\Client;
use GuzzleHttp\Exception\ClientException;
use Symfony\Component\DomCrawler\Crawler;

class Scrapping
{
    use SchemaTrait, PriceTrait;
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

    /**
     * Devuelve el precio obtenido haciendo scrapping de la url de un producto en Sanborns
     *
     * @param $url
     * @return int
     */
    public function getSanbornsPrice($url) {
        $price = null;

        if ($this->isBlocked($url)) {
            echo "URL Bloqueada";
        } else {
            $crawler = $this->client->request('GET', $url);

            // Precio actual
            $elements = $crawler->filter('.actual')->each(function($node){
                return $node->text();
            });

            if (count($elements) < 1) {
                // Precio regular
                $elements = $crawler->filter('.regular')->each(function($node){
                    return $node->text();
                });
            }

            $price = $this->cleanPrice($elements[0]);
        }

        return $price;
    }

    /**
     * Devuelve el precio obtenido haciendo scrapping de la url de un producto en BestBuy
     *
     * @param $url
     * @return null|string
     */
    public function getBestBuyPrice($url) {
        $price = null;

        if ($this->isBlocked($url)) {
            echo "URL Bloqueada";
        } else {
            $crawler = $this->client->request('GET', $url);

            // Precio actual
            $elements = $crawler->filter('.pb-hero-price')->each(function($node){
                return $node->text();
            });

            if (count($elements) < 1) {
                // Precio regular
                $elements = $crawler->filter('.pb-regular-price')->each(function($node){
                    return $node->text();
                });
            }

            $price = $this->cleanPrice($elements[0]);
        }

        return $price;
    }

    /**
     * Devuelve el precio obtenido haciendo scrapping de la url de un producto en BestBuy
     *
     * @param $url
     * @return null|string
     */
    public function getClaroShopPrice($url) {
        $price = null;

        if ($this->isBlocked($url)) {
            echo "URL Bloqueada";
        } else {
            $crawler = $this->client->request('GET', $url);

            // Precio actual
            $elements = $crawler->filter('.total')->each(function($node){
                return $node->text();
            });

            if (count($elements) < 1) {
                // Precio regular
                $elements = $crawler->filter('.antes')->each(function($node){
                    return $node->text();
                });
            }

            $price = $this->cleanPrice($elements[0]);
        }

        return $price;
    }

    /**
     * Devuelve el precio obtenido haciendo scrapping de la url de un producto en Coppel
     * @param $url
     * @return null|string
     */
    public function getCoppelPrice($url) {
        $price = null;

        if ($this->isBlocked($url)) {
            echo "URL Bloqueada";
        } else {
            $crawler = $this->client->request('GET', $url);

            // Precio actual
            $elements = $crawler->filter('.pcontado')->each(function($node){
                return $node->text();
            });

            if (count($elements) < 1) {
                // Precio regular
                $elements = $crawler->filter('.p_oferta')->each(function($node){
                    return $node->text();
                });
            }

            $price = $this->cleanPrice($elements[0]);
        }

        return $price;
    }

    /**
     * Devuelve el precio obtenido haciendo scrapping de la url de un producto en Sears
     * @param $url
     * @return null|string
     */
    public function getSearsPrice($url) {
        $price = null;

        if ($this->isBlocked($url)) {
            echo "URL Bloqueada";
        } else {
            $crawler = $this->client->request('GET', $url);

            // Precio actual
            $elements = $crawler->filter('.precio')->each(function($node){
                return $node->text();
            });

            if (count($elements) < 1) {
                // Precio regular
                $elements = $crawler->filter('.precio_secundario')->each(function($node){
                    return $node->text();
                });
            }

            $price = $this->cleanPrice($elements[0]);
        }

        return $price;
    }

    /**
     * Devuelve el precio obtenido haciendo scrapping de la url de un producto en CyberPuerta
     * @param $url
     * @return null|string
     */
    public function getCyberPuertaPrice($url) {
        $price = null;

        if ($this->isBlocked($url)) {
            echo "URL Bloqueada";
        } else {
            $crawler = $this->client->request('GET', $url);

            // Precio actual
            $elements = $crawler->filter('.detailsInfo_right_pricebox_border_left_price_price')->each(function($node){
                return $node->text();
            });

            if (count($elements) < 1) {
                // Precio regular
                $elements = $crawler->filter('.detailsInfo_right_pricebox_border_left_price_price')->each(function($node){
                    return $node->text();
                });
            }

            $price = $this->cleanPrice($elements[0]);
        }

        return $price;
    }

    /**
     * Devuelve el precio obtenido haciendo scrapping de la url de un producto en RadioShack
     * @param $url
     * @return null|string
     */
    public function getRadioShackPrice($url) {
        $price = null;

        if ($this->isBlocked($url)) {
            echo "URL Bloqueada";
        } else {
            $crawler = $this->client->request('GET', $url);

            // Precio actual
            $elements = $crawler->filter('.big-price')->each(function($node){
                return $node->text();
            });

            if (count($elements) < 1) {
                // Precio regular
                $elements = $crawler->filter('.pricebefore')->each(function($node){
                    return $node->text();
                });
            }

            $price = $this->cleanPrice($elements[0]);
        }

        return $price;
    }

    /**
     * TODO error
     *
     * @param $url
     * @return null|string
     */
    public function getWalMartPrice($url) {
        $price = null;

        if ($this->isBlocked($url)) {
            echo "URL Bloqueada";
        } else {
            $crawler = $this->client->request('GET', $url);

            // Precio actual
            $elements = $crawler->filter('.pricesPDP')->each(function($node){
                return $node->text();
            });

            if (count($elements) < 1) {
                // Precio regular
                $elements = $crawler->filter('.lstPrc')->each(function($node){
                    return $node->text();
                });
            }

            $price = $this->cleanPrice($elements[0]);
        }

        return $price;
    }

    /**
     * TODO error
     * @param $url
     * @return null|string
     */
    public function getSamsPrice($url) {
        $price = null;

        if ($this->isBlocked($url)) {
            echo "URL Bloqueada";
        } else {
            $crawler = $this->client->request('GET', $url);

            // Precio actual
            $elements = $crawler->filter('.prod-price-actual')->each(function ($node) {
                return $node->text();
            });

            if (count($elements) < 1) {
                // Precio regular
                $elements = $crawler->filter('.prod-price-actual')->each(function($node){
                    return $node->text();
                });
            }

            $price = $this->cleanPrice($elements[0]);
        }

        return $price;
    }

    /**
     * TODO error
     *
     * @param $url
     * @return null|string
     */
    public function getLiverpoolPrice($url) {
        $price = null;

        if ($this->isBlocked($url)) {
            echo "URL Bloqueada";
        } else {
            $crawler = $this->client->request('GET', $url);

            // Precio actual
            $elements = $crawler->filter('.precio-promocion');

            if (count($elements) < 1) {
                // Precio regular
                $elements = $crawler->filter('.precio-especial')->each(function($node){
                    return $node->text();
                });
            }

            $price = $this->cleanPrice($elements[0]);
        }

        return $price;
    }
}

$s = new Scrapping();
//echo $s->getSanbornsPrice("https://www.sanborns.com.mx/Paginas/Producto.aspx?ean=50644691195");
//echo $s->getBestBuyPrice("http://www.bestbuy.com.mx/p/sony-pantalla-de-40-led-1080p-smart-tv-hdtv-negro/1000198293");
//echo $s->getClaroShopPrice("http://wwvv.claroshop.com/producto/493261/teclado-iluv-bluetooth-portatil/");
//echo $s->getCoppelPrice("http://www.coppel.com/pantalla-led-sony-40-pulgadas-full-hd-smart-tv-kdl-40w650d-la1-pm-2245913");
//echo $s->getSearsPrice("http://www.sears.com.mx/producto/573080/led-sony-40-full-hd-smart-kdl40w650d/");
//echo $s->getCyberPuertaPrice("https://www.cyberpuerta.mx/index.php?cl=details&anid=df765142eaee513e0d53a10c1f434d58&gclid=CjwKCAjw9O3NBRB3EiwAK6wPT2cix-udis_dzQ0jbOJ0JmuaI7JOb3K3hLJwPv4Ma0mIstS9MTf3_xoCm8UQAvD_BwE");
//echo $s->getRadioShackPrice("https://www.radioshack.com.mx/store/radioshack/en/Categor%C3%ADa/Todas/Gadgets-y-Drones/Drones-y-Radio-Control/Helic%C3%B3pteros/HELICOPTERO-RADIO-CONTROL-VICA-X-ZERO/p/70965");


//echo $s->getWalMartPrice("https://www.walmart.com.mx/Celulares/Smartphones/Celulares-Desbloqueados/Smartphone-Samsung-Galaxy-J7-Pro-16GB-Negro-Desbloqueado_00880608881303");
//echo $s->getLiverpoolPrice("https://www.liverpool.com.mx/tienda/smartphone-samsung-s8-5-8-pulgadas-negro-at-t/1057898018?skuId=1057898018");
//echo $s->getSamsPrice("https://www.sams.com.mx/microondas-y-hornos-electricos/horno-de-microondas-lg-1-5-pies-cubicos/000189096");
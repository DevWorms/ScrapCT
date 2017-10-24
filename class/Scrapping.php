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

    /**
     * Si una conexión esta bloqueada por ip
     *
     * @param $url
     * @return bool
     */
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

        $query = "SELECT * FROM wp_pwgb_posts WHERE post_type = 'reviews' AND post_status = 'publish' LIMIT 0, 2;";
        $stm = $this->db->prepare($query);
        $stm->execute();

        $response = $stm->fetchAll(PDO::FETCH_ASSOC);
        foreach ($response as $review) {
            echo "Producto: " . $review["post_title"] . "<br>";
            // Obtiene los metadatos del producto
            $metadata = $this->getMetaData($review["ID"]);
            // Nombres de las columnas de las diferentes tiendas
            $links = $this->productsLink();
            foreach ($links as $link) {
                foreach ($metadata as $mdata) {
                    // Valida que exista el precio de esa tienda en la BD
                    if (isset($mdata['meta_key']) && $mdata['meta_key'] == $link) {
                        // Si tiene una url del producto en las tiendas...
                        if (isset($mdata['meta_value']) && !empty($mdata['meta_value'])) {
                            // Obtiene el precio, haciendo scrapping
                            $price = $this->linkToScrap($link, $mdata['meta_value']);
                            // Valida si el precio del producto cambio
                            echo $mdata['meta_value'] . "<br><strong>Precio: </strong>" . $price;
                            if ($price != null && $price != "" && !empty($price)) {
                                $hasChanged = $this->priceHasChanged($metadata, $link, $price);
                                if ($hasChanged["change"]) {
                                    if ($hasChanged["action"] == 'update') {
                                        $oldPrice = $hasChanged['oldPrice'];
                                        echo " El precio cambio, precio original: " . $oldPrice;
                                        $this->updatePrice($link, $price, $mdata['post_id']);
                                    } else {
                                        echo " El precio no existe, hay que crearlo";
                                        $this->createPrice($link, $price, $mdata['post_id']);
                                    }
                                }
                            }
                            echo "<br>";
                            break;
                        }
                    }
                }
            }
            echo "<br><br>";
        }

        return $response;
    }

    /**
     * Convierte el link de una tienda, al precio de una tienda
     *
     * @param $link
     * @return null|string
     */
    public function linkToPriceIndex($link) {
        switch ($link) {
            case 'sanborns_pl':
                return 'price_sanborns';
                break;
            case 'claroshop_pl':
                return 'price_claroshop';
                break;
            case 'coppel_pl':
                return 'price_coppel';
                break;
            case 'sears_pl':
                return 'price_sears';
                break;
            case 'sams_pl':
                return 'price_sams';
                break;
            case 'bestbuy_pl':
                return 'price_bestbuy';
                break;
            case 'walmart_pl':
                return 'price_walmart';
                break;
            case 'liverpool_pl':
                return 'price_liverpool';
                break;
            case 'office_max_pl':
                return 'price_office_max';
                break;
            case 'office_depot_pl':
                return 'price_office_depot';
                break;
            case 'palacio_pl':
                return 'price_palacio';
                break;
            case 'soriana_pl':
                return 'price_soriana';
                break;
            case 'elektra_pl':
                return 'price_elektra';
                break;
            case 'sony_pl':
                return 'price_sony';
                break;
            case 'costco_pl':
                return 'price_costco';
                break;
            case 'radioshack_pl':
                return 'price_radioshack';
                break;
            default:
                return null;
        }
    }

    /**
     * Crea el precio para una tiende
     *
     * @param $tag_store
     * @param $price
     * @param $post_id
     */
    public function createPrice($tag_store, $price, $post_id) {
        $tag_price = $this->linkToPriceIndex($tag_store);

        $query = "INSERT INTO wp_pwgb_postmeta (post_id, meta_key, meta_value) VALUES (:post_id, :meta_key, :price);";
        $stm = $this->db->prepare($query);
        $stm->bindValue(":post_id", $post_id, PDO::PARAM_INT);
        $stm->bindValue(":meta_key", $tag_price, PDO::PARAM_STR);
        $stm->bindValue(":price", $price, PDO::PARAM_INT);
        $stm->execute();
    }

    /**
     * Actualiza el precio para una tiende
     *
     * @param $tag_store
     * @param $price
     * @param $post_id
     */
    public function updatePrice($tag_store, $price, $post_id) {
        $tag_price = $this->linkToPriceIndex($tag_store);

        $query = "UPDATE wp_pwgb_postmeta SET meta_value=:price WHERE post_id=:post_id AND meta_key=:meta_key;";
        $stm = $this->db->prepare($query);
        $stm->bindValue(":post_id", $post_id, PDO::PARAM_INT);
        $stm->bindValue(":meta_key", $tag_price, PDO::PARAM_STR);
        $stm->bindValue(":price", $price, PDO::PARAM_INT);
        $stm->execute();
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
     * Devuelve el precio de un producto de acuerdo al link y tienda que se envia
     *
     * @param $link
     * @param $url
     * @return null|string
     */
    public function linkToScrap($link, $url) {
        switch ($link) {
            case 'sanborns_pl':
                return $this->getSanbornsPrice($url);
                break;
            case 'claroshop_pl':
                return $this->getClaroShopPrice($url);
                break;
            case 'coppel_pl':
                return $this->getCoppelPrice($url);
                break;
            case 'sears_pl':
                return $this->getSearsPrice($url);
                break;
            case 'sams_pl':
                return $this->getSamsPrice($url);
                break;
            case 'bestbuy_pl':
                return $this->getBestBuyPrice($url);
                break;
            case 'walmart_pl':
                return $this->getWalMartPrice($url);
                break;
            case 'liverpool_pl':
                return $this->getLiverpoolPrice($url);
                break;
            case 'office_max_pl':
                return $this->getOfficeMaxPrice($url);
                break;
            case 'office_depot_pl':
                return $this->getOfficeDepotPrice($url);
                break;
            case 'palacio_pl':
                return $this->getPalacioPrice($url);
                break;
            case 'soriana_pl':
                return $this->getSorianaPrice($url);
                break;
            case 'elektra_pl':
                return $this->getElektraPrice($url);
                break;
            case 'sony_pl':
                return $this->getSonyPrice($url);
                break;
            case 'costco_pl':
                return $this->getCostcoPrice($url);
                break;
            case 'radioshack_pl':
                return $this->getRadioShackPrice($url);
                break;
            default:
                return null;
        }
    }

    /**
     * Devuelve true si el precio de un producto cambio o no se encontró
     *
     * @param $meta_id
     * @param $price
     * @return array
     */
    public function priceHasChanged($metadata, $tag_link, $price) {
        $res = ['change' => true, 'action' => 'create'];

        foreach ($metadata as $mdata) {
            $tagPrice = $this->linkToPriceIndex($tag_link);
            if (isset($mdata['meta_key']) && $mdata['meta_key'] == $tagPrice) {
                $res['action'] = 'update';
                // Si tiene una url del producto en las tiendas...
                if (isset($mdata['meta_value']) && !empty($mdata['meta_value'])) {
                    $res['oldPrice'] = $mdata['meta_value'];
                    if ($mdata['meta_value'] == $price) {
                        $res['change'] = false;
                        break;
                    }
                }
            }
        }

        return $res;
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
     * Devuelve el precio obtenido haciendo scrapping de la url de un producto en Costco
     * @param $url
     * @return null|string
     */
    public function getCostcoPrice($url) {
        $price = null;

        if ($this->isBlocked($url)) {
            echo "URL Bloqueada";
        } else {
            $crawler = $this->client->request('GET', $url);

            // Precio actual
            $elements = $crawler->filter('.productdetail_inclprice')->each(function($node){
                return $node->text();
            });

            if (count($elements) < 1) {
                // Precio regular
                $elements = $crawler->filter('.productdetail_exclprice')->each(function($node){
                    return $node->text();
                });
            }

            $price = $this->specialCostcoPrice($elements[0]);
        }

        return $price;
    }

    /**
     * Devuelve el precio obtenido haciendo scrapping de la url de un producto en Sony
     * @param $url
     * @return null|string
     */
    public function getSonyPrice($url) {
        $price = null;

        if ($this->isBlocked($url)) {
            echo "URL Bloqueada";
        } else {
            $crawler = $this->client->request('GET', $url);

            // Precio actual
            $elements = $crawler->filter('.skuBestPrice')->each(function($node){
                return $node->text();
            });

            if (count($elements) < 1) {
                // Precio regular
                $elements = $crawler->filter('.skuListPrice')->each(function($node){
                    return $node->text();
                });
            }

            $price = $this->cleanPrice($elements[0]);
        }

        return $price;
    }

    /**
     * Devuelve el precio obtenido haciendo scrapping de la url de un producto en Elektra
     * @param $url
     * @return null|string
     */
    public function getElektraPrice($url) {
        $price = null;

        if ($this->isBlocked($url)) {
            echo "URL Bloqueada";
        } else {
            $crawler = $this->client->request('GET', $url);

            // Precio actual
            $elements = $crawler->filter('.skuBestPrice')->each(function($node){
                return $node->text();
            });

            if (count($elements) < 1) {
                // Precio regular
                $elements = $crawler->filter('.skuListPrice')->each(function($node){
                    return $node->text();
                });
            }

            $price = $this->cleanPrice($elements[0]);
        }

        return $price;
    }

    /**
     * Devuelve el precio obtenido haciendo scrapping de la url de un producto en Soriana
     * @param $url
     * @return null|string
     */
    public function getSorianaPrice($url) {
        $price = null;

        if ($this->isBlocked($url)) {
            echo "URL Bloqueada";
        } else {
            $crawler = $this->client->request('GET', $url);

            // Precio actual
            $elements = $crawler->filter('.sale-price')->each(function($node){
                return $node->text();
            });

            if (count($elements) < 1) {
                // Precio regular
                $elements = $crawler->filter('.original-price')->each(function($node){
                    return $node->text();
                });
            }

            if (count($elements) < 1) {
                // Precio regular
                $elements = $crawler->filter('.big-price')->each(function($node){
                    return $node->text();
                });
            }

            $price = $this->cleanPrice($elements[0]);
        }

        return $price;
    }

    /**
     * Devuelve el precio obtenido haciendo scrapping de la url de un producto en Palacio de Hierro
     * @param $url
     * @return null|string
     */
    public function getPalacioPrice($url) {
        $price = null;

        if ($this->isBlocked($url)) {
            echo "URL Bloqueada";
        } else {
            $crawler = $this->client->request('GET', $url);

            // Precio actual
            $elements = $crawler->filter('.special-price')->each(function($node){
                return $node->text();
            });

            if (count($elements) < 1) {
                // Precio regular
                $elements = $crawler->filter('.price')->each(function($node){
                    return $node->text();
                });
            }

            $price = $this->cleanPrice($elements[0]);
        }

        return $price;
    }

    /**
     * Devuelve el precio obtenido haciendo scrapping de la url de un producto en Office Depot
     * @param $url
     * @return null|string
     */
    public function getOfficeDepotPrice($url) {
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
                $elements = $crawler->filter('.discountedPrice')->each(function($node){
                    return $node->text();
                });
            }

            if (count($elements) < 1) {
                // Precio regular
                $elements = $crawler->filter('.pricebefore ')->each(function($node){
                    return $node->text();
                });
            }

            $price = $this->cleanPrice($elements[0]);
        }

        return $price;
    }

    /**
     * Devuelve el precio obtenido haciendo scrapping de la url de un producto en Office Max
     * @param $url
     * @return null|string
     */
    public function getOfficeMaxPrice($url) {
        $price = null;

        if ($this->isBlocked($url)) {
            echo "URL Bloqueada";
        } else {
            $crawler = $this->client->request('GET', $url);

            // Precio actual
            $elements = $crawler->filter('.skuBestPrice')->each(function($node){
                return $node->text();
            });

            if (count($elements) < 1) {
                // Precio regular
                $elements = $crawler->filter('.skuListPrice')->each(function($node){
                    return $node->text();
                });
            }

            $price = $this->cleanPrice($elements[0]);
        }

        return $price;
    }

    /**
     * Devuelve el precio obtenido haciendo scrapping de la url de un producto en Office Walmart
     *
     * @param $url
     * @return null|string
     */
    public function getWalMartPrice($url) {
        $price = null;
        $getUrl = "https://www.walmart.com.mx/WebControls/hlGetProductDetail.ashx?upc=" . $url;
        $text = utf8_decode(file_get_contents($getUrl));
        $text = str_replace("var detailInfo = ", "", $text);
        $text = str_replace(";", "", $text);
        $text = stripslashes(html_entity_decode($text));
        $json = json_decode($text);

        if (isset($json->offers) && count($json->offers) > 0) {
            $price = $json->offers[0]->price;
        } else {
            $index = "_" . $url;
            $price = $this->cleanPrice($json->c->facets->$index->p);
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
//$s->getAllReviews();

//echo $s->getSanbornsPrice("https://www.sanborns.com.mx/Paginas/Producto.aspx?ean=50644691195");
//echo $s->getBestBuyPrice("http://www.bestbuy.com.mx/p/sony-pantalla-de-40-led-1080p-smart-tv-hdtv-negro/1000198293");
//echo $s->getClaroShopPrice("http://wwvv.claroshop.com/producto/493261/teclado-iluv-bluetooth-portatil/");
//echo $s->getCoppelPrice("http://www.coppel.com/pantalla-led-sony-40-pulgadas-full-hd-smart-tv-kdl-40w650d-la1-pm-2245913");
//echo $s->getSearsPrice("http://www.sears.com.mx/producto/573080/led-sony-40-full-hd-smart-kdl40w650d/");
//echo $s->getCyberPuertaPrice("https://www.cyberpuerta.mx/index.php?cl=details&anid=df765142eaee513e0d53a10c1f434d58&gclid=CjwKCAjw9O3NBRB3EiwAK6wPT2cix-udis_dzQ0jbOJ0JmuaI7JOb3K3hLJwPv4Ma0mIstS9MTf3_xoCm8UQAvD_BwE");
//echo $s->getRadioShackPrice("https://www.radioshack.com.mx/store/radioshack/en/Categor%C3%ADa/Todas/Gadgets-y-Drones/Drones-y-Radio-Control/Helic%C3%B3pteros/HELICOPTERO-RADIO-CONTROL-VICA-X-ZERO/p/70965");
//echo $s->getCostcoPrice("http://www.costco.com.mx/view/p/lg-led-55-smart-tv-ultra-hd-641445");
//echo $s->getCostcoPrice("http://www.costco.com.mx/view/p/lg-barra-de-sonido-bluetooth-con-subwoofer-integrado-641437?utm_source=homepage&utm_medium=desktop_fy18p2w3&utm_campaign=buyerspick&utm_term=lg%2Bbarra%2Bde%2Bsonido%2B641437&utm_content=pos3");
//echo $s->getSonyPrice("https://store.sony.com.mx/mhc-v90dw/p");
//echo $s->getSonyPrice("https://store.sony.com.mx/xperia-touch/p");
//echo $s->getElektraPrice("https://www.elektra.com.mx/pantalla-led-lg-55-4k-smart-55uh7650-1006936/p");
//echo $s->getElektraPrice("https://www.elektra.com.mx/pantalla-led-hkpro-32-hd-smart-hkp32sm4sm5-1003802/p");
//echo $s->getSorianaaPrice("https://www.soriana.com/soriana/es/c/Electronica/Pantallas/Pantallas/Pantalla-LED-SmartTV-LG-de-55%22-UHD,-4K/p/11202913");
//echo $s->getSorianaaPrice("https://www.soriana.com/soriana/es/c/Electronica/Pantallas/Pantallas/Pantalla-LED-Smart-TV-LG-55%22-UHD,-4K-/p/11176387");
//echo $s->getPalacioPrice("https://www.elpalaciodehierro.com/tecnologia/tecnologia-celulares/r9-apple-iphone-8-plus-silv-64gb-lae-esp.html");
//echo $s->getPalacioPrice("https://www.elpalaciodehierro.com/tecnologia/tecnologia-celulares/38127805-r9-samsung-lte-sma720f-galaxy-a7-17-neg.html");
//echo $s->getOfficeDepotPrice("https://www.officedepot.com.mx/officedepot/en/Categor%C3%ADa/Todas/Electr%C3%B3nica/Pantallas/Pantallas/PANTALLA-SAMSUNG-32-PULGADAS-SMART-HD/p/66020");
//echo $s->getOfficeDepotPrice("https://www.officedepot.com.mx/officedepot/en/Categor%C3%ADa/Todas/Electr%C3%B3nica/Pantallas/PANTALLA-LG-55%22-%28SUHD%2C-SMART-TV%29/p/79661");
//echo $s->getOfficeMaxPrice("http://www.officemax.com.mx/pantalla-jvc-32--smart-tv/p");
//echo $s->getOfficeMaxPrice("http://www.officemax.com.mx/cable-hdmi-general-electric-73580-de-3-pies--69535/p");
//echo $s->getWalMartPrice("00880608881303");
//echo $s->getWalMartPrice("00690144309192");

//echo $s->getLiverpoolPrice("https://www.liverpool.com.mx/tienda/smartphone-samsung-s8-5-8-pulgadas-negro-at-t/1057898018?skuId=1057898018");
//echo $s->getSamsPrice("https://www.sams.com.mx/microondas-y-hornos-electricos/horno-de-microondas-lg-1-5-pies-cubicos/000189096");
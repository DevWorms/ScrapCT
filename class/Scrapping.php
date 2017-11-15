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
require_once __DIR__ . '/AmazonConnection.php';

use Goutte\Client;
use GuzzleHttp\Exception\ClientException;
use Symfony\Component\DomCrawler\Crawler;
error_reporting(E_ALL);
class Scrapping
{
    use SchemaTrait, PriceTrait;
    private $client;
    private $db;
    private $block;

    /**
     * Scrapping constructor.
     * @param $goutte
     */
    public function __construct()
    {
        $this->block = 10;
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
    public function getAllReviews($response, $hasta, $total,$shop) {
        echo "<div style='color:blue;'>Actualizando " . $hasta . " de " . $total . " articulos.</div><br><br>";
        $contador = 1;
        foreach ($response as $review) {
            $porcentaje = ($contador * 100) / count($response);
            echo "<div style='color: blue'>" . $contador . " de " . count($response) . " - " . $porcentaje. "% Producto: " . $review["post_title"] . "</div><br>";
            // Obtiene los metadatos del producto
            $metadata = $this->getMetaData($review["ID"]);

            // Imprime el asin
            $asin = $this->getAsin($metadata);
            echo "<strong>Asin: </strong>" . $asin . "<br>";

            // Nombres de las columnas de las diferentes tiendas
            $links = $this->productsLink();
            //foreach ($links as $link) {
                $link = $shop;
                foreach ($metadata as $mdata) {
                    // Valida que exista el precio de esa tienda en la BD
                    if (isset($mdata['meta_key']) && $mdata['meta_key'] == $link) {
                        // Si tiene una url del producto en las tiendas...
                        if (isset($mdata['meta_value']) && !empty($mdata['meta_value'])) {
                            // Obtiene el precio, haciendo scrapping
                            if ($link == 'amazon_affiliate_link' || $link == 'amazon_pl') {
                                $price = $this->linkToScrap($link, $asin);
                            } else {
                                $price = $this->linkToScrap($link, $mdata['meta_value']);
                            }
                            // Valida si el precio del producto cambio
                            // Imprime el link
                            echo $mdata['meta_value'];
                            // Imprime el precio
                            echo "<br><strong>Precio: </strong>";
                            echo (isset($price) && is_numeric($price) ? $price : "<div style='color: red'>El producto ya no se encuentra disponible</div>");

                            if ($price != null && $price != "" && !empty($price)) {
                                $hasChanged = $this->priceHasChanged($metadata, $link, $price);
                                if ($hasChanged["change"]) {
                                    if ($hasChanged["action"] == 'update') {
                                        $oldPrice = $hasChanged['oldPrice'];
                                        echo " El precio cambio, precio original: " . $oldPrice;
                                        $this->updatePrice($link, $price, $mdata['post_id']);
                                    } else {
                                        echo " El precio no existe, se creo en la base de datos";
                                        $this->createPrice($link, $price, $mdata['post_id']);
                                    }
                                }
                            }
                            echo "<br>";
                            break;
                        }
                    }
                }
            //}
            echo "<br><br>";

            // ACtualiza el mejor precio y mejor tienda para del producto
            $this->setBestPrice($review["ID"]);

            $contador += 1;
        }

        return $response;
    }
    
    public function init($categoria,$hasta,$total,$shop) {
        $limites = " LIMIT " . $hasta . " , " . $total;

        $query = "SELECT p.* from  wp_pwgb_term_taxonomy as tx
                    inner join wp_pwgb_term_relationships as tr ON tx.term_taxonomy_id = tr.term_taxonomy_id
                    INNER JOIN wp_pwgb_posts as p ON tr.object_id = p.ID "; 
        if($categoria == "allCategories"){
            $query .= " WHERE  p.post_type = 'reviews'
                    AND p.post_status = 'publish'";
        }else{
            $query .= " WHERE tx.term_taxonomy_id = :tax 
                    AND p.post_type = 'reviews'
                    AND p.post_status = 'publish' ";
        }

        $query .= $limites;
        $stm3 = $this->db->prepare($query);
        if($categoria != "allCategories"){
          $stm3->bindParam(":tax", $categoria);  
        }
        $stm3->execute();
        $response = $stm3->fetchAll(PDO::FETCH_ASSOC);
        $this->getAllReviews($response, $hasta, $total,$shop);
    }

    public function updateScraping($current_position, $total) {
        if ($current_position == $this->block + 1) {
            $query = "INSERT INTO dw_scraping (current_position, total, started_at, finished) VALUES (:cu, :t, NOW(), FALSE );";
            $stm = $this->db->prepare($query);
            $stm->bindValue(":cu", $current_position, PDO::PARAM_INT);
            $stm->bindValue(":t", $total, PDO::PARAM_INT);
            $stm->execute();
        } else {
            $query = "UPDATE dw_scraping SET current_position=:cu;";
            $stm = $this->db->prepare($query);
            $stm->bindValue(":cu", $current_position, PDO::PARAM_INT);
            $stm->execute();
        }
    }

    public function finishScraping() {
        $query = "UPDATE dw_scraping SET finished_at=now(), finished=TRUE;";
        $stm = $this->db->prepare($query);
        $stm->execute();
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
            case 'amazon_affiliate_link':
                return 'price_amazon';
                break;
            case 'amazon_pl':
                return 'price_amazon';
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
     * Actualiza el precio para una tienda
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
            case 'liverpool_pl':
                return $this->getLiverpoolPrice($url);
                break;
            case 'linio_pl':
                return $this->getLinioPrice($url);
                break;
            case 'linio_affiliate_link':
                return $this->getLinioPrice($url);
                break;
            case 'amazon_affiliate_link':
                return $this->getAmazonPrice($url);
                break;
            case 'amazon_pl':
                return $this->getAmazonPrice($url);
                break;
                

            case 'walmart_pl':
                return $this->getWalMartPrice($url);
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
    public function getAsin($metadata) {
        foreach ($metadata as $mdata) {
            if (isset($mdata['meta_key']) && $mdata['meta_key'] == "asin") {
                // Si tiene una url del producto en las tiendas...
                if (isset($mdata['meta_value']) && !empty($mdata['meta_value'])) {
                    return $mdata['meta_value'];
                }
            }
        }

        return null;
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
     * Actualiza el mejor precio y mejor tienda de un producto
     *
     * @param $product_id
     */
    public function setBestPrice($product_id) {
        $query = "SELECT * FROM wp_pwgb_postmeta WHERE post_id=:product_id;";
        $stm = $this->db->prepare($query);
        $stm->bindValue(":product_id", $product_id, PDO::PARAM_INT);
        $stm->execute();

        $response = $stm->fetchAll(PDO::FETCH_ASSOC);
        $tag_prices = $this->productPrice();
        $theBest = ['price' => 0];
        $otherPrices = [];
        $hasChanged = false;

        // Obtiene el mejor precio anterior
        foreach ($response as $metadata) {
            // Mejor precio
            if (isset($metadata['meta_key']) && $metadata['meta_key'] == 'price_best') {
                $theBest['price'] = $metadata['meta_value'];
            }

            // Mejor tienda
            if (isset($metadata['meta_key']) && $metadata['meta_key'] == 'best_shop') {
                $theBest['shop'] = $metadata['meta_value'];
            }

            // Obtiene el precio de otras tiendas
            foreach ($tag_prices as $price) {
                if (isset($metadata['meta_key']) && $metadata['meta_key'] == $price) {
                    $otherPrices[$price] = (int) $metadata['meta_value'];
                }
            }
        }

        $oldPrice = $theBest['price'];
        // Obtiene el nuevo mejor precio
        foreach ($otherPrices as $store => $price) {
            if (($price > 0) && ($price < $theBest['price'])) {
                $theBest['price'] = $price;
                $theBest['shop'] = $store;
                $hasChanged = true;
            }
        }

        // Si el mejor precio cambio, lo actualiza y lo guarda en el historial
        if ($hasChanged) {
            $query = "INSERT INTO dw_historial (product_id, price_old, price_new, created_at) VALUES 
                        (:product_id, :price_old, :price_new, NOW());";
            $stm2 = $this->db->prepare($query);
            $stm2->bindValue(":product_id", $product_id, PDO::PARAM_INT);
            $stm2->bindValue(":price_old", $oldPrice, PDO::PARAM_INT);
            $stm2->bindValue(":price_new", $theBest['price'], PDO::PARAM_INT);
            $stm2->execute();

            $query = "UPDATE wp_pwgb_postmeta SET meta_value=:price_new WHERE post_id=:product_id AND meta_key='price_best';";
            $stm3 = $this->db->prepare($query);
            $stm3->bindValue(":product_id", $product_id, PDO::PARAM_INT);
            $stm3->bindValue(":price_new", $theBest['price'], PDO::PARAM_INT);
            $stm3->execute();

            $query = "UPDATE wp_pwgb_postmeta SET meta_value=:best_shop WHERE post_id=:product_id AND meta_key='best_shop';";
            $stm4 = $this->db->prepare($query);
            $stm4->bindValue(":product_id", $product_id, PDO::PARAM_INT);
            $stm4->bindValue(":best_shop", $theBest['shop'], PDO::PARAM_STR);
            $stm4->execute();
        }
    }

    /**
     * Devuelve el precio obtenido haciendo scrapping de la url de un producto en Sanborns
     *
     * @param $url
     * @return int
     */
    public function getSanbornsPrice($url) {
        try {
            $price = null;

            if ($this->isBlocked($url)) {
                echo "<div style='color: red'>URL no disponible</div><br>";
            } else {
                $crawler = $this->client->request('GET', $url);

                // Precio actual
                $elements = $crawler->filter('.actual')->each(function ($node) {
                    return $node->text();
                });

                if (count($elements) < 1) {
                    // Precio regular
                    $elements = $crawler->filter('.regular')->each(function ($node) {
                        return $node->text();
                    });
                }

                $price = (isset($elements[0])) ? $this->cleanPrice($elements[0]) : null;
            }

            return $price;
        } catch (Exception $ex) {
            echo "<br><div style='color: red;'>" . $ex->getMessage() . " " . $ex->getLine() . " " . $ex->getFile() .  "</div><br>";
        }
    }

    /**
     * Devuelve el precio obtenido haciendo scrapping de la url de un producto en BestBuy
     *
     * @param $url
     * @return null|string
     */
    public function getBestBuyPrice($url) {
        try {
            $price = null;

            if ($this->isBlocked($url)) {
                echo "<div style='color: red'>URL no disponible</div><br>";
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

                $price = (isset($elements[0])) ? $this->cleanPrice($elements[0]) : null;
            }

            return $price;
        } catch (Exception $ex) {
            echo "<br><div style='color: red;'>" . $ex->getMessage() . " " . $ex->getLine() . " " . $ex->getFile() .  "</div><br>";
        }
    }

    /**
     * Devuelve el precio obtenido haciendo scrapping de la url de un producto en BestBuy
     *
     * @param $url
     * @return null|string
     */
    public function getClaroShopPrice($url) {
        try {
            $price = null;

            if ($this->isBlocked($url)) {
                echo "<div style='color: red'>URL no disponible</div><br>";
            } else {
                $crawler = $this->client->request('GET', $url);

                // Precio actual
                $elements = $crawler->filter('.total')->each(function ($node) {
                    return $node->text();
                });

                if (count($elements) < 1) {
                    // Precio regular
                    $elements = $crawler->filter('.antes')->each(function ($node) {
                        return $node->text();
                    });
                }

                $price = (isset($elements[0])) ? $this->cleanPrice($elements[0]) : null;
            }

            return $price;
        } catch (Exception $ex) {
            echo "<br><div style='color: red;'>" . $ex->getMessage() . " " . $ex->getLine() . " " . $ex->getFile() .  "</div><br>";
        }
    }

    /**
     * Devuelve el precio obtenido haciendo scrapping de la url de un producto en Coppel
     * @param $url
     * @return null|string
     */
    public function getCoppelPrice($url) {
        try {
            $price = null;

            if ($this->isBlocked($url)) {
                echo "<div style='color: red'>URL no disponible</div><br>";
            } else {
                $crawler = $this->client->request('GET', $url);

                // Precio actual
                $elements = $crawler->filter('.pcontado')->each(function ($node) {
                    return $node->text();
                });

                if (count($elements) < 1) {
                    // Precio regular
                    $elements = $crawler->filter('.p_oferta')->each(function ($node) {
                        return $node->text();
                    });
                }

                $price = (isset($elements[0])) ? $this->cleanPrice($elements[0]) : null;
            }

            return $price;
        } catch (Exception $ex) {
            echo "<br><div style='color: red;'>" . $ex->getMessage() . " " . $ex->getLine() . " " . $ex->getFile() .  "</div><br>";
        }
    }

    /**
     * Devuelve el precio obtenido haciendo scrapping de la url de un producto en Sears
     * @param $url
     * @return null|string
     */
    public function getSearsPrice($url) {
        try {
            $price = null;

            if ($this->isBlocked($url)) {
                echo "<div style='color: red'>URL no disponible</div><br>";
            } else {
                $crawler = $this->client->request('GET', $url);

                // Precio actual
                $elements = $crawler->filter('.precio')->each(function ($node) {
                    return $node->text();
                });

                if (count($elements) < 1) {
                    // Precio regular
                    $elements = $crawler->filter('.precio_secundario')->each(function ($node) {
                        return $node->text();
                    });
                }

                $price = (isset($elements[0])) ? $this->cleanPrice($elements[0]) : null;
            }

            return $price;
        } catch (Exception $ex) {
            echo "<br><div style='color: red;'>" . $ex->getMessage() . " " . $ex->getLine() . " " . $ex->getFile() .  "</div><br>";
        }
    }

    /**
     * Devuelve el precio obtenido haciendo scrapping de la url de un producto en CyberPuerta
     * @param $url
     * @return null|string
     */
    public function getCyberPuertaPrice($url) {
        try {
            $price = null;

            if ($this->isBlocked($url)) {
                echo "<div style='color: red'>URL no disponible</div><br>";
            } else {
                $crawler = $this->client->request('GET', $url);

                // Precio actual
                $elements = $crawler->filter('.detailsInfo_right_pricebox_border_left_price_price')->each(function ($node) {
                    return $node->text();
                });

                if (count($elements) < 1) {
                    // Precio regular
                    $elements = $crawler->filter('.detailsInfo_right_pricebox_border_left_price_price')->each(function ($node) {
                        return $node->text();
                    });
                }

                $price = (isset($elements[0])) ? $this->cleanPrice($elements[0]) : null;
            }

            return $price;
        } catch (Exception $ex) {
            echo "<br><div style='color: red;'>" . $ex->getMessage() . " " . $ex->getLine() . " " . $ex->getFile() .  "</div><br>";
        }
    }

    /**
     * Devuelve el precio obtenido haciendo scrapping de la url de un producto en RadioShack
     * @param $url
     * @return null|string
     */
    public function getRadioShackPrice($url) {
        try {
            $price = null;

            if ($this->isBlocked($url)) {
                echo "<div style='color: red'>URL no disponible</div><br>";
            } else {
                $crawler = $this->client->request('GET', $url);

                // Precio actual
                $elements = $crawler->filter('.big-price')->each(function ($node) {
                    return $node->text();
                });

                if (count($elements) < 1) {
                    // Precio regular
                    $elements = $crawler->filter('.pricebefore')->each(function ($node) {
                        return $node->text();
                    });
                }

                $price = (isset($elements[0])) ? $this->cleanPrice($elements[0]) : null;
            }

            return $price;
        } catch (Exception $ex) {
            echo "<br><div style='color: red;'>" . $ex->getMessage() . " " . $ex->getLine() . " " . $ex->getFile() .  "</div><br>";
        }
    }

    /**
     * Devuelve el precio obtenido haciendo scrapping de la url de un producto en Costco
     * @param $url
     * @return null|string
     */
    public function getCostcoPrice($url) {
        try {
            $price = null;

            if ($this->isBlocked($url)) {
                echo "<div style='color: red'>URL no disponible</div><br>";
            } else {
                $crawler = $this->client->request('GET', $url);

                // Precio actual
                $elements = $crawler->filter('.productdetail_inclprice')->each(function ($node) {
                    return $node->text();
                });

                if (count($elements) < 1) {
                    // Precio regular
                    $elements = $crawler->filter('.productdetail_exclprice')->each(function ($node) {
                        return $node->text();
                    });
                }

                $price = $this->specialCostcoPrice($elements[0]);
            }

            return $price;
        } catch (Exception $ex) {
            echo "<br><div style='color: red;'>" . $ex->getMessage() . " " . $ex->getLine() . " " . $ex->getFile() .  "</div><br>";
        }
    }

    /**
     * Devuelve el precio obtenido haciendo scrapping de la url de un producto en Sony
     * @param $url
     * @return null|string
     */
    public function getSonyPrice($url) {
        try {
            $price = null;

            if ($this->isBlocked($url)) {
                echo "<div style='color: red'>URL no disponible</div><br>";
            } else {
                $crawler = $this->client->request('GET', $url);

                // Precio actual
                $elements = $crawler->filter('.skuBestPrice')->each(function ($node) {
                    return $node->text();
                });

                if (count($elements) < 1) {
                    // Precio regular
                    $elements = $crawler->filter('.skuListPrice')->each(function ($node) {
                        return $node->text();
                    });
                }

                $price = (isset($elements[0])) ? $this->cleanPrice($elements[0]) : null;
            }

            return $price;
        } catch (Exception $ex) {
            echo "<br><div style='color: red;'>" . $ex->getMessage() . " " . $ex->getLine() . " " . $ex->getFile() .  "</div><br>";
        }
    }

    /**
     * Devuelve el precio obtenido haciendo scrapping de la url de un producto en Elektra
     * @param $url
     * @return null|string
     */
    public function getElektraPrice($url) {
        try {
            $price = null;

            if ($this->isBlocked($url)) {
                echo "<div style='color: red'>URL no disponible</div><br>";
            } else {
                $crawler = $this->client->request('GET', $url);

                // Precio actual
                $elements = $crawler->filter('.skuBestPrice')->each(function ($node) {
                    return $node->text();
                });

                if (count($elements) < 1) {
                    // Precio regular
                    $elements = $crawler->filter('.skuListPrice')->each(function ($node) {
                        return $node->text();
                    });
                }

                $price = (isset($elements[0])) ? $this->cleanPrice($elements[0]) : null;
            }

            return $price;
        } catch (Exception $ex) {
            echo "<br><div style='color: red;'>" . $ex->getMessage() . " " . $ex->getLine() . " " . $ex->getFile() .  "</div><br>";
        }
    }

    /**
     * Devuelve el precio obtenido haciendo scrapping de la url de un producto en Soriana
     * @param $url
     * @return null|string
     */
    public function getSorianaPrice($url) {
        try {
            $price = null;

            if ($this->isBlocked($url)) {
                echo "<div style='color: red'>URL no disponible</div><br>";
            } else {
                $crawler = $this->client->request('GET', $url);

                // Precio actual
                $elements = $crawler->filter('.sale-price')->each(function ($node) {
                    return $node->text();
                });

                if (count($elements) < 1) {
                    // Precio regular
                    $elements = $crawler->filter('.original-price')->each(function ($node) {
                        return $node->text();
                    });
                }

                if (count($elements) < 1) {
                    // Precio regular
                    $elements = $crawler->filter('.big-price')->each(function ($node) {
                        return $node->text();
                    });
                }

                $price = (isset($elements[0])) ? $this->cleanPrice($elements[0]) : null;
            }

            return $price;
        } catch (Exception $ex) {
            echo "<br><div style='color: red;'>" . $ex->getMessage() . " " . $ex->getLine() . " " . $ex->getFile() .  "</div><br>";
        }
    }

    /**
     * Devuelve el precio obtenido haciendo scrapping de la url de un producto en Palacio de Hierro
     * @param $url
     * @return null|string
     */
    public function getPalacioPrice($url) {
        try {
            $price = null;

            if ($this->isBlocked($url)) {
                echo "<div style='color: red'>URL no disponible</div><br>";
            } else {
                $crawler = $this->client->request('GET', $url);

                // Precio actual
                $elements = $crawler->filter('.special-price')->each(function ($node) {
                    return $node->text();
                });

                if (count($elements) < 1) {
                    // Precio regular
                    $elements = $crawler->filter('.price')->each(function ($node) {
                        return $node->text();
                    });
                }

                $price = (isset($elements[0])) ? $this->cleanPrice($elements[0]) : null;
            }

            return $price;
        } catch (Exception $ex) {
            echo "<br><div style='color: red;'>" . $ex->getMessage() . " " . $ex->getLine() . " " . $ex->getFile() .  "</div><br>";
        }
    }

    /**
     * Devuelve el precio obtenido haciendo scrapping de la url de un producto en Office Depot
     * @param $url
     * @return null|string
     */
    public function getOfficeDepotPrice($url) {
        try {
            $price = null;

            if ($this->isBlocked($url)) {
                echo "<div style='color: red'>URL no disponible</div><br>";
            } else {
                $crawler = $this->client->request('GET', $url);

                // Precio actual
                $elements = $crawler->filter('.big-price')->each(function ($node) {
                    return $node->text();
                });

                if (count($elements) < 1) {
                    // Precio regular
                    $elements = $crawler->filter('.discountedPrice')->each(function ($node) {
                        return $node->text();
                    });
                }

                if (count($elements) < 1) {
                    // Precio regular
                    $elements = $crawler->filter('.pricebefore ')->each(function ($node) {
                        return $node->text();
                    });
                }

                $price = (isset($elements[0])) ? $this->cleanPrice($elements[0]) : null;
            }

            return $price;
        } catch (Exception $ex) {
            echo "<br><div style='color: red;'>" . $ex->getMessage() . " " . $ex->getLine() . " " . $ex->getFile() .  "</div><br>";
        }
    }

    /**
     * Devuelve el precio obtenido haciendo scrapping de la url de un producto en Office Max
     * @param $url
     * @return null|string
     */
    public function getOfficeMaxPrice($url) {
        try {
            $price = null;

            if ($this->isBlocked($url)) {
                echo "<div style='color: red'>URL no disponible</div><br>";
            } else {
                $crawler = $this->client->request('GET', $url);

                // Precio actual
                $elements = $crawler->filter('.skuBestPrice')->each(function ($node) {
                    return $node->text();
                });

                if (count($elements) < 1) {
                    // Precio regular
                    $elements = $crawler->filter('.skuListPrice')->each(function ($node) {
                        return $node->text();
                    });
                }

                $price = (isset($elements[0])) ? $this->cleanPrice($elements[0]) : null;
            }

            return $price;
        } catch (Exception $ex) {
            echo "<br><div style='color: red;'>" . $ex->getMessage() . " " . $ex->getLine() . " " . $ex->getFile() .  "</div><br>";
        }
    }

    /**
     * Devuelve el precio obtenido haciendo scrapping de la url de un producto en Office Walmart
     *
     * @param $url
     * @return null|string
     */
    public function getWalMartPrice($url) {
        try {
            $product_id = substr($url, -14);

            $price = null;
            $getUrl = "https://www.walmart.com.mx/WebControls/hlGetProductDetail.ashx?upc=" . $product_id;
            $text = utf8_decode(file_get_contents($getUrl));
            $text = str_replace("var detailInfo = ", "", $text);
            $text = str_replace(";", "", $text);
            $text = stripslashes(html_entity_decode($text));
            $json = json_decode($text);

            if (isset($json->offers) && count($json->offers) > 0) {
                $price = $json->offers[0]->price;
            } else {
                $index = "_" . $product_id;
                $price = $this->cleanPrice($json->c->facets->$index->p);
            }

            return $price;
        } catch (Exception $ex) {
            echo "<br><div style='color: red;'>" . $ex->getMessage() . " " . $ex->getLine() . " " . $ex->getFile() .  "</div><br>";
        }
    }

    /**
     * TODO error
     * @param $url
     * @return null|string
     */
    public function getSamsPrice($url) {
        try {
            $price = null;

            if ($this->isBlocked($url)) {
                echo "<div style='color: red'>URL no disponible</div><br>";
            } else {
                $crawler = $this->client->request('GET', $url);

                // Precio actual
                $elements = $crawler->filter('.prod-price-actual')->each(function ($node) {
                    return $node->text();
                });

                if (count($elements) < 1) {
                    // Precio regular
                    $elements = $crawler->filter('.prod-price-actual')->each(function ($node) {
                        return $node->text();
                    });
                }

                $price = (isset($elements[0])) ? $this->cleanPrice($elements[0]) : null;
            }

            return $price;
        } catch (Exception $ex) {
            echo "<br><div style='color: red;'>" . $ex->getMessage() . " " . $ex->getLine() . " " . $ex->getFile() .  "</div><br>";
        }
    }

    /**
     * Devuelve el precio obtenido haciendo scrapping de la url de un producto en Office Liverpool
     *
     * @param $url
     * @return null|string
     */
    public function getLiverpoolPrice($url) {
        try {
            $price = null;

            $text = utf8_decode(file_get_contents($url));

            foreach (preg_split("/((\r?\n)|(\r\n?))/", $text) as $line) {
                if (strpos($line, "requiredlistprice") !== false) {
                    $price = $this->cleanPrice($line);
                }

                if (strpos($line, "requiredsaleprice") !== false) {
                    $price = $this->cleanPrice($line);
                }
            }

            return $price;
        } catch (Exception $ex) {
            echo "<br><div style='color: red;'>" . $ex->getMessage() . " " . $ex->getLine() . " " . $ex->getFile() .  "</div><br>";
        }
    }

    public function getLinioPrice($url) {
        try {
            $price = null;

            if ($this->isBlocked($url)) {
                echo "<div style='color: red'>URL no disponible</div><br>";
            } else {
                $crawler = $this->client->request('GET', $url);

                // Precio actual
                $elements = $crawler->filter('.price-main')->each(function ($node) {
                    return $node->text();
                });

                if (count($elements) < 1) {
                    // Precio regular
                    $elements = $crawler->filter('.original-price')->each(function ($node) {
                        return $node->text();
                    });
                }

                $price = (isset($elements[0])) ? $this->cleanPrice($elements[0]) : null;
            }

            return $price;
        } catch (Exception $ex) {
            echo "<br><div style='color: red;'>" . $ex->getMessage() . " " . $ex->getLine() . " " . $ex->getFile() .  "</div><br>";
        }
    }

    public function getAmazonPrice($asin) {
        try {
            $a = new AmazonConnection();

            return $a->getPriceAmazonApi($asin);
        } catch (Exception $ex) {
            echo "<br><div style='color: red;'>" . $ex->getMessage() . " " . $ex->getLine() . " " . $ex->getFile() .  "</div><br>";
        }
    }

    public function getLastID(){
        $query = "SELECT MAX(ID) as ultimo FROM wp_pwgb_posts";
        $pdo = $this->db->prepare($query);
        $pdo->execute();
        return $result[0]['ultimo'];
    }

    public function ids() {
        $query = "SELECT ID FROM wp_pwgb_posts WHERE post_type = 'reviews' 
                    AND post_status = 'publish';";
        
        $stm = $this->db->prepare($query);
        $stm->execute();
        $response = $stm->fetchAll(PDO::FETCH_ASSOC);

        $i = 0;
        foreach ($response as $id) {
            //print_r($id);
            echo $id["ID"] . ", ";
            $i++;
            if ($i%50 == 0) {
                echo "<br><br>";
            }
        }
    }

}

if (isset($_POST['prueba'])) {
    $post = $_POST['prueba'];
    $s = new Scrapping();
    switch ($post) {
        case 'precios':
            $s->init($_POST['categoria'],$_POST['inicio'] , $_POST['fin'] , $_POST['shop']);
            break;
        default:
            echo "default";
            //header("Location: 404.php");
            break;
    }
}

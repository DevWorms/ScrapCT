<?php
/**
 * Created by PhpStorm.
 * User: rk521
 * Date: 1/11/17
 * Time: 06:38 AM
 */

require_once __DIR__ . '/../app/DB.php';

class Csv
{
    private $db;

    /**
     * Scrapping constructor.
     * @param $goutte
     */
    public function __construct()
    {
        $this->db = DB::init()->getDB();
    }

    public function init() {
        $query = "SELECT ID, post_title as Nombre, p.post_name as URL FROM wp_pwgb_posts p WHERE post_type = 'reviews' AND post_status = 'publish';";
        $pdo = $this->db->prepare($query);
        $pdo->execute();

        $productos = $pdo->fetchAll(PDO::FETCH_ASSOC);

        $precios = [
            'Precio Amazon' => 'price_amazon',
            'Precio Linio' => 'price_linio',
            'Precio Liverpool' => 'price_liverpool',
            'Precio Sanborns' => 'price_sanborns',
            'Precio Claroshop' => 'price_claroshop',
            'Precio SamsClub' => 'price_sams',
            'Precio Sears' => 'price_sears',
            'Precio BestBuy' => 'price_bestbuy',
            'Precio Coppel' => 'price_coppel',
            'Precio Cyberpuerta' => 'price_cyberpuerta',
            'Precio Walmart' => 'price_walmart',
            'Precio Office Max' => 'price_office_max',
            'Precio Office Depot' => 'price_office_depot',
            'Precio Palacio de Hierro' => 'price_palacio',
            'Precio Soriana' => 'price_soriana',
            'Precio Elektra' => 'price_elektra',
            'Precio Sony' => 'price_sony',
            'Precio Costco' => 'price_costco',
            'Precio RadioShack' => 'price_radioshack',
        ];

        echo "<strong>Fecha</strong>,<strong>Fabricante</strong>,<strong>Modelo</strong>,<strong>Nombre</strong>,<strong>ASIN</strong>,<strong>URL</strong>,<strong>Mejor Precio</strong>";
        foreach ($precios as $precio => $key) {
            echo ",<strong>" . $precio . "</strong>";
        }
        echo "<br>";
        foreach ($productos as $producto) {
            $query = "SELECT * FROM wp_pwgb_postmeta WHERE post_id = :post_id;";
            $pdo2 = $this->db->prepare($query);
            $pdo2->bindValue(":post_id", $producto["ID"], PDO::PARAM_INT);
            $pdo2->execute();

            $metadata = $pdo2->fetchAll(PDO::FETCH_ASSOC);
            echo date("d/m/Y") . "," . $this->getFabricante($metadata) . "," . $this->getModelo($metadata) . "," . $producto["Nombre"] . "," . $this->getAsin($metadata) . "," . "http://www.tec-check.com.mx/reviews/" . $producto["URL"] . "," . $this->getBestPrice($metadata);
            foreach ($precios as $precio => $key) {
                echo "," . $this->getPrice($metadata, $key);
            }
            echo "<br>";
        }
    }

    public function getFabricante($metadata) {
        foreach ($metadata as $mdata) {
            if (isset($mdata['meta_key']) && $mdata['meta_key'] == "company") {
                // Si tiene una url del producto en las tiendas...
                if (isset($mdata['meta_value']) && !empty($mdata['meta_value'])) {
                    return $mdata['meta_value'];
                }
            }
        }

        return null;
    }

    public function getModelo($metadata) {
        foreach ($metadata as $mdata) {
            if (isset($mdata['meta_key']) && $mdata['meta_key'] == "model") {
                // Si tiene una url del producto en las tiendas...
                if (isset($mdata['meta_value']) && !empty($mdata['meta_value'])) {
                    return $mdata['meta_value'];
                }
            }
        }

        return null;
    }

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

    public function getBestPrice($metadata) {
        foreach ($metadata as $mdata) {
            if (isset($mdata['meta_key']) && $mdata['meta_key'] == "price_best") {
                // Si tiene una url del producto en las tiendas...
                if (isset($mdata['meta_value']) && !empty($mdata['meta_value'])) {
                    return $mdata['meta_value'];
                }
            }
        }

        return null;
    }

    public function getPrice($metadata, $key) {
        foreach ($metadata as $mdata) {
            if (isset($mdata['meta_key']) && $mdata['meta_key'] == $key) {
                // Si tiene una url del producto en las tiendas...
                if (isset($mdata['meta_value']) && !empty($mdata['meta_value'])) {
                    return $mdata['meta_value'];
                }
            }
        }

        return "No disponible";
    }
}

$c = new Csv();
$c->init();
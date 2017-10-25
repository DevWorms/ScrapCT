<?php
/**
 * Created by PhpStorm.
 * User: rk521
 * Date: 24/10/17
 * Time: 02:09 PM
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/DB.php';
require_once __DIR__ . '/../traits/SchemaTrait.php';
use Goutte\Client;

class Search
{
    use SchemaTrait;

    private $db;
    private $client;

    /**
     * Search constructor.
     */
    public function __construct()
    {
        $this->client = new Client();
        $this->db = DB::init()->getDB();
    }

    public function init($name, $model, $company) {
        $shops = $this->shops();

    }

    public function searchOnLiverpool($name, $model, $company, $shop_url) {
        $url = $shop_url . $model;
        $crawler = $this->client->request("GET", $url);

        // Los primeros productos
        $productos = $crawler->filter('.product-cell ');

        if (count($productos) > 0) {
            // Busca el modelo del producto en los resultados
            foreach ($productos as $producto) {
                if ($this->searchTextOnResult($model, $producto)) {
                    echo "Encontrado por modelo: ";
                    print_r($producto);
                    break;
                } elseif ($this->searchNameOnResult($producto, $name)) {
                    echo "Encontrado por nombre";
                    print_r($producto);
                    break;
                }
            }
        }
    }

    public function searchOnWalmart($name, $model, $company) {
        $url = "https://www.walmart.com.mx/WebControls/hlSearch.ashx?search=" . $model . "%20AND%20price=%5B0%20TO%20100000%5D&start=0&rows=25&facet=true&ffield=price";
        $data = utf8_decode(file_get_contents($url));
        $data = str_replace('"IsPreorderable":"False",', "", $data);
        $json = json_decode($data);

        foreach ($json->docs as $product) {
            if ($this->searchTextOnResult($model, $product->n)) {
                echo "Encontrado por modelo: ";
                print_r($product);
                break;
            } elseif ($this->searchNameOnResult($product->n, $name)) {
                echo "Encontrado por nombre";
                print_r($product);
                break;
            }
        }

        //echo $data;
    }

    public function searchOnCyberPuerta($name, $model, $company, $shop_url) {
        $url = $shop_url . $model;
        $crawler = $this->client->request("GET", $url);

        // Los primeros productos
        $productos = $crawler->filter('.productData ');

        if (count($productos) > 0) {
            // Busca el modelo del producto en los resultados
            foreach ($productos as $producto) {
                if ($this->searchTextOnResult($model, $producto)) {
                    echo "Encontrado por modelo: ";
                    print_r($producto);
                    break;
                } elseif ($this->searchNameOnResult($producto, $name)) {
                    echo "Encontrado por nombre";
                    print_r($producto);
                    break;
                }
            }
        }
    }

    public function searchOnRadioShack($name, $model, $company, $shop_url) {
        $producto = [];
        $url = $shop_url . $model;
        $crawler = $this->client->request('GET', $url);

        // Los primeros productos
        $productos = $crawler->filter('.productGridItem ')->each(function ($node) {
            return $node->text();
        });

        if (count($productos) > 0) {
            // Busca el modelo del producto en los resultados
            foreach ($productos as $producto) {
                if ($this->searchTextOnResult($model, $producto)) {
                    echo "Encontrado por modelo: ";
                    print_r($producto);
                    break;
                } elseif ($this->searchNameOnResult($producto, $name)) {
                    echo "Encontrado por nombre";
                    print_r($producto);
                    break;
                }
            }
        }
    }

    /**
     * Busca el modelo en el titulo del producto
     *
     * @param $result
     * @param $text
     * @return bool
     */
    public function searchTextOnResult($result, $text) {
        foreach (preg_split("/((\r?\n)|(\r\n?))/", $result) as $line) {
            if (strpos($line, $text) !== false) {
                return true;
            }
        }
    }

    /**
     * Busca un producto por noombre en una cadena de texto
     *
     * @param $result
     * @param $name
     * @return bool
     */
    public function searchNameOnResult($result, $name) {
        $words = explode(" ", $name);
        $percent_max = count($words);
        $percent_min = (int) ((65 * $percent_max) / 100);
        $percent = 0;

        foreach ($words as $word) {
            if (strpos($result, $word) !== false) {
                $percent++;
            }
        }

        return ($percent >= $percent_min) ? true : false;
    }

    public function spacesToDash($string) {
        return str_replace(" ", "-", $string);
    }
}

$s = new Search();
//$s->searchOnRadioShack("LG UF6400 43 pulgadas 4K Ultra HD Smart TV", "LG UF6400", "LG", "https://www.radioshack.com.mx/store/radioshack/en/search/?category=0-0-0-0&text=");
//$s->searchOnCyberPuerta("Monitor Gamer LG 24MP59G-P LED 23.8''", "LG", "LG", "https://www.cyberpuerta.mx/index.php?stoken=71865D4F&lang=0&cl=search&searchparam=");
//$s->searchOnWalmart("Barra de Sonido Curva Samsung 2.1 Canales HW-J6000R", "HW-J6000R/ZX", "Samsung");
$s->searchOnLiverpool("Smartphone Samsung S8 5.8 pulgadas Negro AT&T", "S8", "Samsung", "https://www.liverpool.com.mx/tienda/?s=");
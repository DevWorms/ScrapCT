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
use GuzzleHttp\Client as GuzzleClient;

class Search
{
    use SchemaTrait;

    private $db;
    private $client;
    private $gClient;

    /**
     * Search constructor.
     */
    public function __construct()
    {
        $this->client = new Client();
        $this->gClient = new GuzzleClient();
        $this->db = DB::init()->getDB();
    }

    /**
     * @param $name
     * @param $model
     * @param $company
     */
    public function init($name, $model, $company) {

    }

    /**
     * Busca un producto en Liverpool
     *
     * @param $name
     * @param $model
     */
    public function searchOnLiverpool($name, $model) {
        $url = "https://www.liverpool.com.mx/tienda/?s=" . $this->spacesToPlus($model);
        $crawler = $this->client->request("GET", $url);

        // Los primeros productos
        $productos = $crawler->filter('.product-cell ')->each(function ($node) {
            // Extrae el nombre del producto
            $tmpProducto['name'] =$node->filter(".gtmProdName")->first()->attr('value');

            // Extrae el enlace del producto
            //$tmpProducto['url'] = $node->filter("#actionPathId_1061927251")->first()->attr('value');

            return $tmpProducto;
        });

        foreach ($productos as $producto) {
            if ($this->searchTextOnResult($model, $producto['name'])) {
                return $producto;
            } elseif ($this->searchNameOnResult($producto['name'], $name)) {
                return $producto;
            }
        }

        return [];
    }

    /**
     * Busca un producto en BestBuy
     *
     * @param $name
     * @param $model
     * @param $company
     * @param $shop_url
     */
    public function searchOnBestBuy($name, $model) {
        $url = "http://www.bestbuy.com.mx/catalogsearch/result/?q=" . $this->spacesToPlus($model) . "&_dyncharset=UTF-8&id=pcat17071&type=page&sc=Global&cp=1&nrp=&sp=&qp=&list=n&af=true&iht=y&usc=All+Categories&ks=960&keys=keys";
        $crawler = $this->client->request("GET", $url);

        // Los primeros productos
        $productos = $crawler->filter('.item')->each(function ($node) {

            // Extrae el nombre del producto
            $tmpProducto['name'] = $node->filter("a")->first()->attr('title');

            // Extrae el enlace del producto
            $tmpProducto['url'] = $node->filter("a")->first()->attr('href');

            return $tmpProducto;
        });

        foreach ($productos as $producto) {
            if ($this->searchTextOnResult($model, $producto['name'])) {
                return $producto;
            } elseif ($this->searchNameOnResult($producto['name'], $name)) {
                return $producto;
            }
        }

        if (count($productos) > 0) {
            return $productos[0];
        }

        return null;

    }

    /**
     * Busca un producto en Walmart
     *
     * @param $name
     * @param $model
     */
    public function searchOnWalmart($name, $model) {
        $url = "https://www.walmart.com.mx/WebControls/hlSearch.ashx?search=" . $model . "%20AND%20price=%5B0%20TO%20100000%5D&start=0&rows=25&facet=true&ffield=price";
        $data = utf8_decode(file_get_contents($url));
        $data = str_replace('"IsPreorderable":"False",', "", $data);
        $json = json_decode($data);

        foreach ($json->docs as $product) {
            if ($this->searchTextOnResult($model, $product->n)) {
                return ['name' => $product->n, 'url' => 'https://www.walmart.com.mx' . $product->u];
            } elseif ($this->searchNameOnResult($product->n, $name)) {
                return ['name' => $product->n, 'url' => 'https://www.walmart.com.mx' . $product->u];
                break;
            }
        }

        return null;
    }

    /**
     * Busca un producto en Cyberpuerta
     *
     * @param $name
     * @param $model
     */
    public function searchOnCyberPuerta($name, $model) {
        $url = "https://www.cyberpuerta.mx/index.php?stoken=71865D4F&lang=0&cl=search&searchparam=" . $this->spacesToPlus($model);
        $crawler = $this->client->request("GET", $url);

        // Los primeros productos
        $productos = $crawler->filter('.productData ')->each(function ($node) {
            // Extrae el nombre del producto
            $tmpProducto['name'] =$node->filter(".emproduct_right_title")->first()->attr('title');

            // Extrae el enlace del producto
            $tmpProducto['url'] = $node->filter(".emproduct_right_title")->first()->attr('href');

            return $tmpProducto;
        });

        foreach ($productos as $producto) {
            if ($this->searchTextOnResult($model, $producto['name'])) {
                return $producto;
            } elseif ($this->searchNameOnResult($producto['name'], $name)) {
                return $producto;
            }
        }

        return null;
    }

    /**
     * Busca un producto en RadioShack
     *
     * @param $name
     * @param $model
     * @param $company
     * @param $shop_url
     */
    public function searchOnRadioShack($name, $model) {
        $url = "https://www.radioshack.com.mx/store/radioshack/en/search/?category=0-0-0-0&text=" . $model;
        $crawler = $this->client->request('GET', $url);

        // Los primeros productos
        $productos = $crawler->filter('.productGridItem ')->each(function ($node) {
            // Extrae el nombre del producto
            $tmpName = $node->filter(".details")->first()->text();
            $tmpProducto['name'] = explode("SKU", $tmpName)[0];

            // Extrae el enlace del producto
            $tmpProducto['url'] = "https://www.radioshack.com.mx" . $node->filter(".productMainLink")->first()->attr('href');

            return $tmpProducto;
        });

        foreach ($productos as $producto) {
            if ($this->searchTextOnResult($model, $producto['name'])) {
                return $producto;
            } elseif ($this->searchNameOnResult($producto['name'], $name)) {
                return $producto;
            }
        }

        return null;
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
     * Busca un producto por nombre en una cadena de texto
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

    /**
     * Convierte espacios a guiones
     *
     * @param $string
     * @return mixed
     */
    public function spacesToDash($string) {
        return str_replace(" ", "-", $string);
    }

    /**
     * @param $string
     * @return mixed
     */
    public function spacesToPlus($string) {
        return str_replace(" ", "+", $string);
    }

    /**
     * @param $name
     * @param $model
     * @return null
     */
    public function searchOnOfficeMax($name, $model) {
        $url = "http://www.officemax.com.mx/" . $model . "?&utmi_p=_&utmi_pc=BuscaFullText&utmi_cp=" . $model;
        $crawler = $this->client->request('GET', $url);

        // Los primeros productos
        $productos = $crawler->filter('.product-item')->each(function ($node) {
            // Extrae el nombre del producto
            $tmpProducto['name'] = $node->filter(".contenedor-img")->first()->attr("title");

            // Extrae el enlace del producto
            $tmpProducto['url'] = $node->filter(".contenedor-img")->first()->attr('href');

            return $tmpProducto;
        });

        foreach ($productos as $producto) {
            if ($this->searchTextOnResult($model, $producto['name'])) {
                return $producto;
            } elseif ($this->searchNameOnResult($producto['name'], $name)) {
                return $producto;
            }
        }

        return null;
    }

    /**
     * @param $name
     * @param $model
     * @return array|null
     */
    public function searchOnOfficeDepot($name, $model) {
        $url = "https://www.officedepot.com.mx/officedepot/en/search/autocomplete/SearchBox?term=" . $this->spacesToPlus($model);
        $response = file_get_contents($url);
        $json = json_decode($response);
        if (count($json->products) > 0) {
            return ['name' => $json->products[0]->name, 'url' => "https://www.officedepot.com.mx" . $json->products[0]->url];
        } else {
            $url = "https://www.officedepot.com.mx/officedepot/en/search/autocomplete/SearchBox?term=" . $this->spacesToPlus($name);
            $response = file_get_contents($url);
            $json = json_decode($response);

            if (count($json->products) > 0) {
                return ['name' => $json->products[0]->name, 'url' => "https://www.officedepot.com.mx" . $json->products[0]->url];
            }
        }

        return null;
    }

    /**
     * @param $name
     * @param $model
     * @return null
     */
    public function searchOnPalacio($name, $model) {
        $url = "https://www.elpalaciodehierro.com/catalogsearch/result/?q=" . $this->spacesToPlus($model);
        $crawler = $this->client->request('GET', $url);

        // Los primeros productos
        $productos = $crawler->filter('.scroll_image_js')->each(function ($node) {
            // Extrae el nombre del producto
            $names = $node->filter(".product-name")->each(function ($n) {
                return $n->text();
            });
            $tmpProducto['name'] = $names[0];

            // Extrae el enlace del producto
            $urls = $node->filter(".product-image")->each(function ($n) {
                return $n->attr('href');
            });
            $tmpProducto['url'] = "https://www.elpalaciodehierro.com" . $urls[0];

            return $tmpProducto;
        });

        foreach ($productos as $producto) {
            if ($this->searchTextOnResult($model, $producto['name'])) {
                return $producto;
            } elseif ($this->searchNameOnResult($producto['name'], $name)) {
                return $producto;
            }
        }

        if (count($productos) > 0) {
            return $productos[0];
        }

        return null;
    }

    /**
     * @param $name
     * @param $model
     * @return array|null
     */
    public function searchOnSoriana($name, $model) {
        $url = "https://www.soriana.com/soriana/es/search/autocomplete/SearchBox?term=" . $this->spacesToPlus($model);
        $response = file_get_contents($url);
        $json = json_decode($response);
        if (count($json->products) > 0) {
            return ['name' => $json->products[0]->name, 'url' => "https://www.soriana.com" . $json->products[0]->url];
        } else {
            $url = "https://www.soriana.com/soriana/es/search/autocomplete/SearchBox?term=" . $this->spacesToPlus($name);
            $response = file_get_contents($url);
            $json = json_decode($response);

            if (count($json->products) > 0) {
                return ['name' => $json->products[0]->name, 'url' => "https://www.soriana.com" . $json->products[0]->url];
            }
        }
        return null;
    }

    /**
     * @param $name
     * @param $model
     * @return array|null
     */
    public function searchOnElektra($name, $model) {
        $url = "https://www.elektra.com.mx/buscaautocomplete/?maxRows=10&productNameContains=" . $this->spacesToPlus($model) . "&suggestionsStack=";
        $response = file_get_contents($url);
        $json = json_decode($response);

        if (count($json->itemsReturned) > 0) {
            return ['name' => $json->itemsReturned[0]->name, 'url' => $json->itemsReturned[0]->href];
        } else {
            $url = "https://www.elektra.com.mx/buscaautocomplete/?maxRows=10&productNameContains=" . $this->spacesToPlus($name) . "&suggestionsStack=";
            $response = file_get_contents($url);
            $json = json_decode($response);

            if (count($json->itemsReturned) > 0) {
                return ['name' => $json->itemsReturned[0]->name, 'url' => $json->itemsReturned[0]->href];
            }
        }

        return null;
    }

    /**
     * @param $name
     * @param $model
     * @return array|null
     */
    public function searchOnSony($name, $model) {
        $url = "https://store.sony.com.mx/api/catalog_system/pub/products/search/" . $model . "?_from=0&_to=9";
        $response = file_get_contents($url);
        $json = json_decode($response);

        if (count($json) > 0) {
            return ['name' => $json[0]->productName, 'url' => $json[0]->link];
        } else {
            $url = "https://store.sony.com.mx/api/catalog_system/pub/products/search/" . $name . "?_from=0&_to=9";
            $response = file_get_contents($url);
            $json = json_decode($response);

            if (count($json) > 0) {
                return ['name' => $json[0]->productName, 'url' => $json[0]->link];
            }
        }

        return null;
    }

    /**
     * @param $name
     * @param $model
     * @return array|null
     */
    public function searchOnCostco($name, $model) {
        $url = "http://www.costco.com.mx/view/search.json?term=" . $this->spacesToPlus($model);
        $response = file_get_contents($url);
        $json = json_decode($response);

        if (count($json) > 0) {
            return ['name' => $json[0]->label, 'url' => "http://www.costco.com.mx" . $json[0]->url];
        } else {
            $url = "http://www.costco.com.mx/view/search.json?term=" . $this->spacesToPlus($name);
            $response = file_get_contents($url);
            $json = json_decode($response);

            if (count($json) > 0) {
                return ['name' => $json[0]->label, 'url' => "http://www.costco.com.mx" . $json[0]->url];
            }
        }

        return null;
    }

    /**
     * @param $name
     * @param $model
     * @return null
     */
    public function searchOnSears($name, $model) {
        $url = "http://www.sears.com.mx/buscar/autocomplete_ajax.php?data=" . $model . "&id=c&null";
        $crawler = $this->client->request('GET', $url);

        // Los primeros productos
        $productos = $crawler->filter('li')->each(function ($node) {
            // Extrae el nombre del producto
            $tmpProducto['name'] = $node->filter("a")->first()->text();
            //$tmpProducto['name'] = explode("SKU", $tmpName)[0];

            // Extrae el enlace del producto
            $tmpProducto['url'] = "http://www.sears.com.mx/" . $node->filter("a")->first()->attr('href');
            //$tmpProducto['url'] = "https://www.radioshack.com.mx" . $node->filter(".productMainLink")->first()->attr('href');

            return $tmpProducto;
        });

        foreach ($productos as $producto) {
            if ($this->searchTextOnResult($model, $producto['name'])) {
                return $producto;
            } elseif ($this->searchNameOnResult($producto['name'], $name)) {
                return $producto;
            }
        }

        if (count($productos) > 0) {
            return $productos[0];
        }

        return null;
    }

    /**
     * @param $name
     * @param $model
     * @return array
     */
    public function searchOnCoppel($name, $model) {
        $url = "http://www.coppel.com/SearchDisplay?categoryId=&storeId=12761&catalogId=10001&langId=-5&sType=SimpleSearch&resultCatEntryType=2&showResultsPage=true&searchSource=Q&pageView=&beginIndex=0&pageSize=18&searchTerm=" . $this->spacesToPlus($model);
        $crawler = $this->client->request('GET', $url);

        // Los primeros productos
        $productos = $crawler->filter('.1')->each(function ($node) {
            // Extrae el nombre del producto
            $names = $node->filter(".m0")->each(function ($n) {
                return $n->text();
            });

            $tmpProducto['name'] = $names[0];
            //$tmpProducto['name'] = $node->filter(".m0")->first()->text();

            // Extrae el enlace del producto
            $tmpProducto['url'] = $node->filter("a")->first()->attr('href');

            return $tmpProducto;
        });

        foreach ($productos as $producto) {
            if ($this->searchTextOnResult($model, $producto['name'])) {
                return $producto;
            } elseif ($this->searchNameOnResult($producto['name'], $name)) {
                return $producto;
            }
        }

        if (count($productos) > 0) {
            return $productos[0];
        }

        return $productos;
    }

    /**
     * @param $name
     * @param $model
     * @return null
     */
    public function searchOnClaroShop($name, $model) {
        $url = "https://w3.claroshop.com/buscador/" . base64_encode($model) . "/1/";
        $crawler = $this->client->request('GET', $url);

        // Los primeros productos
        $productos = $crawler->filter('.productbox')->each(function ($node) {
            // Extrae el nombre del producto
            $names = $node->filter(".descrip")->each(function ($n) {
                return $n->text();
            });
            $tmpProducto['name'] = explode("Vendido", $names[0])[0];

            // Extrae el enlace del producto
            $tmpProducto['url'] = "https://w3.claroshop.com" . $node->filter(".descrip")->first()->attr('href');

            return $tmpProducto;
        });

        foreach ($productos as $producto) {
            if ($this->searchTextOnResult($model, $producto['name'])) {
                return $producto;
            } elseif ($this->searchNameOnResult($producto['name'], $name)) {
                return $producto;
            }
        }

        if (count($productos) > 0) {
            return $productos[0];
        }

        return null;
    }

    /**
     * @param $name
     * @param $model
     * @return null
     */
    public function searchOnSanborns($name, $model) {
        $url = "http://buscador.sanborns.com.mx/search?client=Sanborns&output=xml_no_dtd&proxystylesheet=Sanborns&sort=date:D:L:d1&oe=UTF-8&ie=UTF-8&ud=1&exclude_apps=1&site=Sanborns&ulang=es&access=p&entqr=3&entqrm=0&filter=0&getfields=*&q=" . $model;
        $data = file_get_contents($url);

        $doc = new DOMDocument();
        $doc->loadHTML($data);
        $summary = $doc->getElementById('title_2');

        print_r($summary->textContent);
        /*
        // Los primeros productos
        $productos = $crawler->filter('.l')->each(function ($node) {
            // Extrae el nombre del producto
            $names = $node->filter(".Pay_pal")->each(function ($n) {
                return $n->text();
            });
            $tmpProducto['name'] = explode("Vendido", $names[0])[0];

            // Extrae el enlace del producto
            //$tmpProducto['url'] = "https://w3.claroshop.com" . $node->filter(".descrip")->first()->attr('href');

            return $tmpProducto;
        });

        /*
        foreach ($productos as $producto) {
            if ($this->searchTextOnResult($model, $producto['name'])) {
                return $producto;
            } elseif ($this->searchNameOnResult($producto['name'], $name)) {
                return $producto;
            }
        }

        if (count($productos) > 0) {
            return $productos[0];
        }*/

        return null;
    }

    /**
     * @param $name
     * @param $model
     * @return null
     */
    public function searchOnLinio($name, $model) {
        $url = "https://www.linio.com.mx/search?q=" . $this->spacesToPlus($model);
        $crawler = $this->client->request('GET', $url);

        // Los primeros productos
        $productos = $crawler->filter('.catalogue-product')->each(function ($node) {
            // Extrae el nombre del producto
            $tmpProducto['name'] = $node->filter(".title-section")->first()->text();

            // Extrae el enlace del producto
            $tmpProducto['url'] = $node->filter(".title-section")->first()->attr('href');

            return $tmpProducto;
        });

        foreach ($productos as $producto) {
            if ($this->searchTextOnResult($model, $producto['name'])) {
                return $producto;
            } elseif ($this->searchNameOnResult($producto['name'], $name)) {
                return $producto;
            }
        }

        if (count($productos) > 0) {
            return $productos[0];
        }

        return null;
    }
}

$s = new Search();
//print_r($s->searchOnRadioShack("MICROCOMPONENTE LG CM2460", "LG CM2460"));
//print_r($s->searchOnCyberPuerta("Monitor Gamer LG 24MP59G-P LED 23.8", "LG 24MP59G"));
//print_r($s->searchOnWalmart("Barra de Sonido Curva Samsung 2.1 Canales HW-J6000R", "HW-J6000R/ZX"));
//print_r($s->searchOnLiverpool("Smartphone Samsung S8 5.8 pulgadas Negro AT&T", "S8"));
//print_r($s->searchOnOfficeMax("Desktop Lenovo AIO 510 23.5 8GB 1TB Core i5 Blanco", "Lenovo AIO 510"));
//print_r($s->searchOnOfficeDepot("Desktop Lenovo AIO 510 23.5 8GB 1TB Core i5 Blanco", "Lenovo AIO 510"));
//print_r($s->searchOnPalacio("Desktop Lenovo AIO 510 23.5 8GB 1TB Core i5 Blanco", "Lenovo AIO 510"));
//print_r($s->searchOnSoriana("Desktop Lenovo AIO 510 23.5 8GB 1TB Core i5 Blanco", "Lenovo AIO 510"));
//print_r($s->searchOnElektra("Desktop Lenovo AIO 510 23.5 8GB 1TB Core i5 Blanco", "Lenovo AIO 510"));
//print_r($s->searchOnSony("Xperia™ XA1", "XA1"));
//print_r($s->searchOnCostco("Samsung LED 49 Smart Tv FHD 60MR", "FHD 60MR"));
//print_r($s->searchOnSears("Celular Samsung A320 A3 17", "a320"));
//print_r($s->searchOnSears("Celular Samsung A320 A3 17", "a320"));
//print_r($s->searchOnCoppel("AT&T Samsung S8 Plus Negro", "S8"));
//print_r($s->searchOnClaroShop("Samsung Sm-G955F 64Gb Color Negro Galaxy S8 Plus", "S8"));
//print_r($s->searchOnSanborns("Samsung Sm-G955F 64Gb Color Negro Galaxy S8 Plus", "S8")); // TODO
//print_r($s->searchOnBestBuy("ACER - LAPTOP PREDATOR G9-593-78QJ DE 15.6", "ACER G9-593-78QJ"));
//print_r($s->searchOnLinio("Samsung Galaxy S8+ Dual Sim 64GB", "S8"));
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
    public function init($name, $model, $post_id) {
        foreach ($this->productsLink() as $shop) {
            switch ($shop) {
                case 'sanborns_pl':
                    $result = $this->searchOnSanborns($name, $model);
                    if ($result) {
                        $this->saveProducto($shop, $post_id, $result['url']);
                    }
                    break;
                case 'linio_pl':
                    $result = $this->searchOnLinio($name, $model);
                    if ($result) {
                        $this->saveProducto($shop, $post_id, $result['url']);
                    }
                    break;
                case 'claroshop_pl':
                    $result = $this->searchOnClaroShop($name, $model);
                    if ($result) {
                        $this->saveProducto($shop, $post_id, $result['url']);
                    }
                    break;
                case 'coppel_pl':
                    $result = $this->searchOnCoppel($name, $model);
                    if ($result) {
                        $this->saveProducto($shop, $post_id, $result['url']);
                    }
                    break;
                case 'sears_pl':
                    $result = $this->searchOnSears($name, $model);
                    if ($result) {
                        $this->saveProducto($shop, $post_id, $result['url']);
                    }
                    break;
                    /*
                case 'sams_pl':
                    $result = $this->searchOnSams($name, $model);
                    if ($result) {
                        $this->saveProducto($shop, $post_id, $result['url']);
                    }
                    break;
                    */
                case 'bestbuy_pl':
                    $result = $this->searchOnBestBuy($name, $model);
                    if ($result) {
                        $this->saveProducto($shop, $post_id, $result['url']);
                    }
                    break;
                case 'walmart_pl':
                    $result = $this->searchOnWalmart($name, $model);
                    if ($result) {
                        $this->saveProducto($shop, $post_id, $result['url']);
                    }
                    break;
                case 'liverpool_pl':
                    $result = $this->searchOnLiverpool($name, $model);
                    if ($result) {
                        $this->saveProducto($shop, $post_id, $result['url']);
                    }
                    break;
                case 'office_max_pl':
                    $result = $this->searchOnOfficeMax($name, $model);
                    if ($result) {
                        $this->saveProducto($shop, $post_id, $result['url']);
                    }
                    break;
                case 'office_depot_pl':
                    $result = $this->searchOnOfficeDepot($name, $model);
                    if ($result) {
                        $this->saveProducto($shop, $post_id, $result['url']);
                    }
                    break;
                case 'palacio_pl':
                    $result = $this->searchOnPalacio($name, $model);
                    if ($result) {
                        $this->saveProducto($shop, $post_id, $result['url']);
                    }
                    break;
                case 'soriana_pl':
                    $result = $this->searchOnSoriana($name, $model);
                    if ($result) {
                        $this->saveProducto($shop, $post_id, $result['url']);
                    }
                    break;
                case 'elektra_pl':
                    $result = $this->searchOnElektra($name, $model);
                    if ($result) {
                        $this->saveProducto($shop, $post_id, $result['url']);
                    }
                    break;
                case 'sony_pl':
                    $result = $this->searchOnSony($name, $model);
                    if ($result) {
                        $this->saveProducto($shop, $post_id, $result['url']);
                    }
                    break;
                case 'costco_pl':
                    $result = $this->searchOnCostco($name, $model);
                    if ($result) {
                        $this->saveProducto($shop, $post_id, $result['url']);
                    }
                    break;
                case 'radioshack_pl':
                    $result = $this->searchOnRadioShack($name, $model);
                    if ($result) {
                        $this->saveProducto($shop, $post_id, $result['url']);
                    }
                    break;
            }
        }
    }

    public function all($inicio, $fin) {
        $query = "SELECT p.*, m.* FROM wp_pwgb_posts p
                    INNER JOIN wp_pwgb_postmeta m
                      ON p.ID=m.post_id
                    WHERE p.post_type = 'reviews' 
                    AND p.post_status = 'publish'
                    AND m.meta_key='model'
                    AND p.ID BETWEEN :inicio AND :fin;";
        $stm2 = $this->db->prepare($query);
        $stm2->bindParam(":inicio", $inicio);
        $stm2->bindParam(":fin", $fin);
        $stm2->execute();
        $productos = $stm2->fetchAll(PDO::FETCH_ASSOC);
        echo "Total: " . count($productos) . "<br><br>";

        foreach ($productos as $producto) {
            $this->init($producto["post_title"], $producto["meta_value"], $producto["ID"]);
        }
    }

    public function saveProducto($shop, $post_id, $url) {
        try {
            $query = "INSERT INTO wp_pwgb_postmeta (post_id, meta_key, meta_value) VALUES (:post_id, :shop, :url);";
            $stm = $this->db->prepare($query);
            $stm->bindValue(":post_id", $post_id, PDO::PARAM_INT);
            $stm->bindValue(":shop", $shop, PDO::PARAM_STR);
            $stm->bindValue(":url", $url, PDO::PARAM_STR);
            $stm->execute();

            echo "Producto encontrado: " . $url . " Post: " . $post_id . "<br><br>";

        } catch (Exception $ex) {
            echo "Error: " . $ex->getMessage() . "<br><br>";
        }
    }

    /**
     * Busca un producto en Liverpool
     *
     * @param $name
     * @param $model
     */
    public function searchOnLiverpool($name, $model) {
        try {
            $url = "https://www.liverpool.com.mx/tienda/?s=" . $this->spacesToPlus($model);
            $crawler = $this->client->request("GET", $url);

            // Los primeros productos
            $productos = $crawler->filter('.product-cell ')->each(function ($node) {
                // Extrae el nombre del producto
                $tmpProducto['name'] = $node->filter(".gtmProdName")->first()->attr('value');

                // Extrae el enlace del producto TODO
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
        } catch (Exception $ex) {
            return null;
        }
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
        try {
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
        } catch (Exception $ex) {
            return null;
        }
    }

    /**
     * @param $name
     * @param $model
     * @return array|null
     */
    public function searchOnOfficeDepot($name, $model) {
        try {
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
        } catch (Exception $ex) {
            return null;
        }
    }

    /**
     * @param $name
     * @param $model
     * @return null
     */
    public function searchOnPalacio($name, $model) {
        try {
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
        } catch (Exception $ex) {
            return null;
        }
    }

    /**
     * @param $name
     * @param $model
     * @return array|null
     */
    public function searchOnSoriana($name, $model) {
        try {
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
        } catch (Exception $ex) {
            return null;
        }
    }

    /**
     * @param $name
     * @param $model
     * @return array|null
     */
    public function searchOnElektra($name, $model) {
        try {
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
        } catch (Exception $ex) {
            return null;
        }
    }

    /**
     * @param $name
     * @param $model
     * @return array|null
     */
    public function searchOnSony($name, $model) {
        try {
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
        } catch (Exception $ex) {
            return null;
        }
    }

    /**
     * @param $name
     * @param $model
     * @return array|null
     */
    public function searchOnCostco($name, $model) {
        try {
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
        } catch (Exception $ex) {
            return null;
        }
    }

    /**
     * @param $name
     * @param $model
     * @return null
     */
    public function searchOnSears($name, $model) {
        try {
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
        } catch (Exception $ex) {
            return null;
        }
    }

    /**
     * @param $name
     * @param $model
     * @return array
     */
    public function searchOnCoppel($name, $model) {
        try {
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
        } catch (Exception $ex) {
            return null;
        }
    }

    /**
     * @param $name
     * @param $model
     * @return null
     */
    public function searchOnClaroShop($name, $model) {
        try {
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
        } catch (Exception $ex) {
            return null;
        }
    }

    /**
     * @param $name
     * @param $model
     * @return null
     */
    public function searchOnSanborns($name, $model) {
        /*
        $url = "http://buscador.sanborns.com.mx/search?client=Sanborns&output=xml_no_dtd&proxystylesheet=Sanborns&sort=date:D:L:d1&oe=UTF-8&ie=UTF-8&ud=1&exclude_apps=1&site=Sanborns&ulang=es&access=p&entqr=3&entqrm=0&filter=0&getfields=*&q=" . $model;
        $data = file_get_contents($url);

        $doc = new DOMDocument();
        $doc->loadHTML($data);
        $summary = $doc->getElementById('title_2');

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
        try {
            $url = "https://www.linio.com.mx/search?q=" . $this->spacesToPlus($model);
            $crawler = $this->client->request('GET', $url);

            // Los primeros productos
            $productos = $crawler->filter('.catalogue-product')->each(function ($node) {
                // Extrae el nombre del producto
                $tmpProducto['name'] = $node->filter(".title-section")->first()->text();

                // Extrae el enlace del producto
                $tmpProducto['url'] = "https://www.linio.com.mx" . $node->filter(".title-section")->first()->attr('href');

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
        } catch (Exception $ex) {
            return null;
        }
    }

    public function getLastID(){
        $query = "SELECT MAX(ID) as ultimo FROM wp_pwgb_posts";
        $pdo = $this->db->prepare($query);
        $pdo->execute();
        $result = $pdo->fetchAll();
        return $result[0]['ultimo'];
    }

}


if (isset($_POST['post'])) {
    $post = $_POST['post'];
    $search = new Search();
    switch ($post) {
        case 'ultimoId':
            echo $search->getLastID();
            break;
        case 'all':
            $search->all($_POST['inicio'] , $_POST['fin']);
            break;
        default:
            header("Location: 404.php");
            break;
    }
}
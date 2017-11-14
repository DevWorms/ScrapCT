<?php
/**
 * Created by PhpStorm.
 * User: rk521
 * Date: 1/11/17
 * Time: 06:38 AM
 */

require_once __DIR__ . '/../app/DB.php';

class TablaPost
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
        $query = "SELECT DISTINCT post_id FROM `wp_pwgb_postmeta`";
        $pdo = $this->db->prepare($query);
        $pdo->execute();

        $productos = $pdo->fetchAll(PDO::FETCH_ASSOC);

        echo '
        <table>
		  <tr>
		    <th>ID</th>
		    <th>Categoría</th>
		    <th>Fabricante</th>
		    <th>Modelo</th>
		    <th>Producto</th>
		    <th>ASIN</th>
		    <th>Img_URL</th>
		    <th>Affiliate Amazon</th>
		    <th>Affiliate Best Buy</th>
		    <th>Affiliate Linio</th>
		  </tr>
		';

        foreach ($productos as $producto) {
        	$categoria = $this->categoria($producto['post_id']);
        	$company = $this->company($producto['post_id']);
        	$model = $this->model($producto['post_id']);
        	$asin = $this->asin($producto['post_id']);
        	$img = $this->img($producto['post_id']);
        	$affiliateAmazon = $this->affiliateAmazon($producto['post_id']);
        	$affiliateBestBuy = $this->affiliateBestBuy($producto['post_id']);
        	$affiliateLinio = $this->affiliateLinio($producto['post_id']);
        	echo 

        	'<tr><td>' . $producto['post_id'] . "</td>" .
        	'<td>' . $categoria['name'] . "</td>" . 
        	'<td>' . $company['meta_value'] . "</td>" . 
        	'<td>' . $model['meta_value'] . "</td>" .
        	'<td>' . $company['meta_value'] . ' ' . $model['meta_value'] . "</td>" .
        	'<td>' . $asin['meta_value'] . "</td>" .
        	'<td>' . $img['meta_value'] . "</td>" .
        	'<td>' . $affiliateAmazon['meta_value'] . "</td>" .
        	'<td>' . $affiliateBestBuy['meta_value'] . "</td>" .
        	'<td>' . $affiliateLinio['meta_value'] . "</td></tr>";
        }

        echo '</table>';
    }

    public function categoria($id){
    	$query = "SELECT terms.name FROM (
					(wp_pwgb_terms AS terms INNER JOIN wp_pwgb_term_taxonomy AS taxo ON terms.term_id = taxo.term_id)
    				INNER JOIN wp_pwgb_term_relationships AS relations ON taxo.term_taxonomy_id = relations.term_taxonomy_id)
					WHERE taxo.taxonomy = 'category' AND relations.object_id = :post_id";
    	$pdo = $this->db->prepare($query);
        $pdo->bindValue(":post_id", $id, PDO::PARAM_INT);
    	$pdo->execute();	

    	$categoria = $pdo->fetch(PDO::FETCH_ASSOC);

    	return $categoria;
    }

    public function company($id){
    	$query = "SELECT meta_value FROM `wp_pwgb_postmeta` WHERE meta_key LIKE 'company' AND post_id = :post_id";
    	$pdo = $this->db->prepare($query);
        $pdo->bindValue(":post_id", $id, PDO::PARAM_INT);
    	$pdo->execute();	

    	$company = $pdo->fetch(PDO::FETCH_ASSOC);

    	return $company;
    }

    public function model($id){
    	$query = "SELECT meta_value FROM `wp_pwgb_postmeta` WHERE meta_key LIKE 'model' AND post_id = :post_id";
    	$pdo = $this->db->prepare($query);
        $pdo->bindValue(":post_id", $id, PDO::PARAM_INT);
    	$pdo->execute();	

    	$model = $pdo->fetch(PDO::FETCH_ASSOC);

    	return $model;
    }

    public function asin($id){
    	$query = "SELECT meta_value FROM `wp_pwgb_postmeta` WHERE meta_key LIKE 'asin' AND post_id = :post_id";
    	$pdo = $this->db->prepare($query);
        $pdo->bindValue(":post_id", $id, PDO::PARAM_INT);
    	$pdo->execute();	

    	$asin = $pdo->fetch(PDO::FETCH_ASSOC);

    	return $asin;
    }

    public function img($id){
    	$query = "SELECT meta_value FROM `wp_pwgb_postmeta` WHERE meta_key LIKE 'picture' AND post_id = :post_id";
    	$pdo = $this->db->prepare($query);
        $pdo->bindValue(":post_id", $id, PDO::PARAM_INT);
    	$pdo->execute();	

    	$img = $pdo->fetch(PDO::FETCH_ASSOC);

    	return $img;
    }

    public function affiliateAmazon($id){
    	$query = "SELECT meta_value FROM `wp_pwgb_postmeta` WHERE meta_key LIKE 'amazon_affiliate_link' AND post_id = :post_id";
    	$pdo = $this->db->prepare($query);
        $pdo->bindValue(":post_id", $id, PDO::PARAM_INT);
    	$pdo->execute();	

    	$affiliateAmazon = $pdo->fetch(PDO::FETCH_ASSOC);

    	return $affiliateAmazon;
    }

    public function affiliateBestBuy($id){
    	$query = "SELECT meta_value FROM `wp_pwgb_postmeta` WHERE meta_key LIKE 'bestbuy_affiliate_link' AND post_id = :post_id";
    	$pdo = $this->db->prepare($query);
        $pdo->bindValue(":post_id", $id, PDO::PARAM_INT);
    	$pdo->execute();	

    	$affiliateBestBuy = $pdo->fetch(PDO::FETCH_ASSOC);

    	return $affiliateBestBuy;
    }

    public function affiliateLinio($id){
    	$query = "SELECT meta_value FROM `wp_pwgb_postmeta` WHERE meta_key LIKE 'linio_affiliate_link' AND post_id = :post_id";
    	$pdo = $this->db->prepare($query);
        $pdo->bindValue(":post_id", $id, PDO::PARAM_INT);
    	$pdo->execute();	

    	$affiliateLinio = $pdo->fetch(PDO::FETCH_ASSOC);

    	return $affiliateLinio;
    }
}

$c = new TablaPost();
$c->init();
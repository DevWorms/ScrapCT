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

        $query = "SELECT DISTINCT(post_id) FROM `wp_pwgb_postmeta` WHERE meta_key LIKE '%price%' ORDER BY post_id";
        $pdo = $this->db->prepare($query);
        $pdo->execute();

        $productos = $pdo->fetchAll(PDO::FETCH_ASSOC);

        foreach ($productos as $producto) {
            $update = $this->update($producto['post_id']);
            echo $producto['post_id'] . ' | ' . $update[0] . ' ' . $update[1] . '<br>';

            $precio = $update[1];
            $id = $producto['post_id'];

            $precio = intval($precio);
            $mejor = number_format($precio,2);

            $tienda = "";


            if($update[0] == 'price_amazon')
                $tienda = 'Amazon';
            else if($update[0] == 'price_linio')
                $tienda = 'Linio';
            else if($update[0] == 'price_liverpool')
                $tienda = 'Liverpool';
            else if($update[0] == 'shop')
                $tienda = 'Claroshop';
            else if($update[0] == 'price_coppel')
                $tienda = 'Coppel';
            else if($update[0] == 'price_sanborns')
                $tienda = 'Sanborns';
            else if($update[0] == 'price_sams')
                $tienda = 'Sams';
            else if($update[0] == 'price_sears')
                $tienda = 'Sears';
            else if($update[0] == 'price_cyberpuerta')
                $tienda = 'Cyberpuerta';
            else if($update[0] == 'price_bestbuy')
                $tienda = 'BestBuy';


            $query1 = "UPDATE `wp_pwgb_postmeta` SET meta_value = :mejor WHERE post_id = :id 
                        AND meta_key = 'price_best'";
            $pdo1 = $this->db->prepare($query1);
            $pdo1->bindValue(":mejor", $mejor, PDO::PARAM_INT);
            $pdo1->bindValue(":id", $id, PDO::PARAM_INT);
            $pdo1->execute(); 


            $query2 = "UPDATE `wp_pwgb_postmeta` SET meta_value = 'field_582e2c7b07a6d' WHERE post_id = :id 
                        AND meta_key = '_price_best'";
            $pdo2 = $this->db->prepare($query2);
            $pdo2->bindValue(":id", $id, PDO::PARAM_INT);
            $pdo2->execute(); 


            $query3 = "UPDATE `wp_pwgb_postmeta` SET meta_value = :tienda WHERE post_id = :id 
                        AND meta_key = 'best_shop'";
            $pdo3 = $this->db->prepare($query3);
            $pdo3->bindValue(":tienda", $tienda, PDO::PARAM_INT);
            $pdo3->bindValue(":id", $id, PDO::PARAM_INT);
            $pdo3->execute(); 

        }

    }

    public function update($id){
        $query = "SELECT meta_key, meta_value FROM `wp_pwgb_postmeta` WHERE `meta_key` LIKE '%price%' AND post_id = :post_id AND meta_key NOT LIKE ('_price_best') AND meta_key NOT LIKE ('price_best') AND meta_key LIKE ('price%') AND meta_value > 0 AND meta_key NOT LIKE '%performance%' ORDER BY CAST(`wp_pwgb_postmeta`.`meta_value` AS signed) ASC";
        $pdo = $this->db->prepare($query);
        $pdo->bindValue(":post_id", $id, PDO::PARAM_INT);
        $pdo->execute();    

        $update = $pdo->fetch(PDO::FETCH_ASSOC);

        return array($update['meta_key'], $update['meta_value']);
    }

    /*
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
    */
}

$c = new TablaPost();
$c->init();
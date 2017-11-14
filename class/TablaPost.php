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
		    <th>Fabricante</th>
		    <th>Modelo</th>
		    <th>Producto</th>
		    <th>ASIN</th>
		    <th>Img_URL</th>
		    <th>Affiliate</th>
		  </tr>
		';

        foreach ($productos as $producto) {
        	$company = $this->company($producto['post_id']);
        	$model = $this->model($producto['post_id']);
        	$asin = $this->asin($producto['post_id']);
        	$img = $this->model($producto['post_id']);
        	$affiliate = $this->model($producto['post_id']);
        	echo 

        	'<tr><td>' . $producto['post_id'] . "</td>" .
        	'<td>' . $company['meta_value'] . "</td>" . 
        	'<td>' . $model['meta_value'] . "</td>" .
        	'<td>' . $asin['meta_value'] . "</td>" .
        	'<td>' . $img['meta_value'] . "</td>" .
        	'<td>' . $affiliate['meta_value'] . "</td></tr>";
        }

        echo '</table>';
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

    public function affiliate($id){
    	$query = "SELECT meta_value FROM `wp_pwgb_postmeta` WHERE meta_key LIKE '%affiliate%' AND post_id = :post_id AND meta_value NOT LIKE 'field%'";
    	$pdo = $this->db->prepare($query);
        $pdo->bindValue(":post_id", $id, PDO::PARAM_INT);
    	$pdo->execute();	

    	$affiliate = $pdo->fetch(PDO::FETCH_ASSOC);

    	return $affiliate;
    }
}

$c = new TablaPost();
$c->init();
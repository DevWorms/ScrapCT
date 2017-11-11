<?php 
	require_once __DIR__ . '/../app/DB.php';
	/**
	* Clase para lso custom post
	*/
	error_reporting(E_ALL);
	class CustomPost  {

		function __construct(){
	        $this->pdo = DB::init()->getDB();
    	}

    	/**
    	 * Buscador de productos para modifica manualmente
    	 * @param  [type] $criterio [description]
    	 * @return [type]           [description]
    	 */
    	public function buscador($criterio){
    		$busqueda = '%'.$criterio.'%';
    		$respuesta = ['estado' => 0, 'mensaje' => ''];
	        try {
	            $query = "SELECT 
						    p.ID, p.post_name, p.post_title, p.guid
						FROM
						    wp_pwgb_posts AS p
						        INNER JOIN
						    wp_pwgb_postmeta AS m ON p.ID = m.post_id
						WHERE
						    (p.post_title LIKE ?
						        OR p.post_name LIKE ?
						        OR m.meta_value LIKE ?)
						        AND p.post_type = 'reviews'
						        AND m.meta_key = 'asin'
						GROUP BY p.ID";

	            $sentencia = $this->pdo->prepare($query);
	            $sentencia->bindParam(1,$busqueda);
	            $sentencia->bindParam(2,$busqueda);
	            $sentencia->bindParam(3,$busqueda);
	            $sentencia->execute();
	            $resultado = $sentencia->fetchAll();
	            $respuesta['productos'] = $resultado;
	            $respuesta['estado'] = 1;
	            $respuesta['mensaje'] = 'Productos encontrados';
	        } catch (Exception $e) {
	            $respuesta['estado'] = 0;
	            $respuesta['mensaje'] = $e->getMessage();
	        }
	        return json_encode($respuesta);
    	}

    	public function getProducto($producto){
    		$respuesta = ['estado' => 0, 'mensaje' => ''];
    		try{

				$query = "SELECT 
						    p.ID,
						    p.post_name,
						    p.post_title,
						    m.meta_key,
						    m.meta_value
						FROM
						    wp_pwgb_posts AS p
						        INNER JOIN
						    wp_pwgb_postmeta AS m ON p.ID = m.post_id
						WHERE
						    p.ID = :producto AND m.meta_key IN('model','company','picture','amazon_pl','asin','linio_pl','sanborns_pl','liverpool_pl','claroshop_pl','coppel_pl','sears_pl','sams_pl','bestbuy_pl','walmart_pl','amazon_affiliate_link','linio_affiliate_link','elektra_pl','price_sams','price_sears','price_cyberpuerta','price_linio','price_amazon','price_claroshop','price_coppel','price_bestbuy','price_sanborns','price_best','best_shop','price_liverpool');";

			    $sentencia = $this->pdo->prepare($query);
	            $sentencia->bindParam(":producto",$producto);
	            $sentencia->execute();
	            $resultado = $sentencia->fetchAll();
	            $respuesta['producto'] = $resultado;
	            $respuesta['estado'] = 1;
	            $respuesta['mensaje'] = 'Producto encontrado';

    		}catch (Exception $e) {
	            $respuesta['estado'] = 0;
	            $respuesta['mensaje'] = $e->getMessage();
	        }
	        return json_encode($respuesta);
    	}

    	public function modificar($post){
    		$respuesta = ['estado' => 0, 'mensaje' => ''];
    		try{
    			$query  = "UPDATE wp_pwgb_posts SET post_name = :post_name , post_title = :post_title WHERE ID = :ID";
    			$sentencia = $this->pdo->prepare($query);
	            $sentencia->bindParam(":post_name",$post['post_name']);
	            $sentencia->bindParam(":post_title",$post['post_title']);
	            $sentencia->bindParam(":ID",$post['ID']);
	            if($sentencia->execute()){
	            	
	            	foreach ($post as $key => $value) {
	            		if($key != "post_name" AND $key !="post_title" AND $key != "post" AND $key != "ID"){
	            			$queryMeta = "UPDATE wp_pwgb_postmeta SET meta_value= :meta_value WHERE post_id = :ID AND meta_key = :meta_key";
            				$sentencia = $this->pdo->prepare($queryMeta);
				            $sentencia->bindParam(":meta_value",$value);
				            $sentencia->bindParam(":ID",$post['ID']);
		            		$sentencia->bindParam(":meta_key",$key);
		            		$sentencia->execute();
	            		}
	            	}
         		
	            }else{
	            	$respuesta['estado'] = 0;
	            	$respuesta['mensaje'] = 'No se puedo actualizar master';
	            }
	            
	            $respuesta['estado'] = 1;
	            $respuesta['mensaje'] = 'Se actualizo correctamente';
    		}catch(Exception $ex){
    			$respuesta['estado'] = 0;
	            $respuesta['mensaje'] = 'No se puedo actualizar exception ' . $ex->getMessage();
    		}

    		return json_encode($respuesta);
    	}

    	public function eliminar($id){
        $respuesta = ['estado' => 0, 'mensaje' => ''];
        try {
            $query = "DELETE FROM wp_pwgb_postmeta WHERE post_id = :id";

            $sentencia = $this->pdo->prepare($query);
            $sentencia->bindParam(':id', $id);

            if ($sentencia->execute()) {
            	$query = "DELETE FROM wp_pwgb_posts WHERE ID = :id";
	            $sentencia = $this->pdo->prepare($query);
	            $sentencia->bindParam(':id', $id);
	            if($sentencia->execute()){
					$respuesta['estado'] = 1;
	                $respuesta['mensaje'] = 'Se eliminó el producto correctamente master y detalle';
	            }else{
	            	$respuesta['estado'] = 0;
                	$respuesta['mensaje'] = 'No se pudo eliminar el  producto correctamente master';
	            }
                
            } else {
                $respuesta['estado'] = 0;
                $respuesta['mensaje'] = 'No se pudo eliminar el   producto correctamente detalle';
            }


        } catch (Exception $e) {
            $respuesta['estado'] = 0;
            $respuesta['mensaje'] = $e->getMessage();

        }

        return json_encode($respuesta);
    }
	}

	if (isset($_POST['post'])) {
    $post = $_POST['post'];
    $custom = new CustomPost();
    switch ($post) {
        case 'buscador':
            echo $custom->buscador($_POST['criterio']);
            break;
        case 'getProducto':
            echo $custom->getProducto($_POST['id']);
            break;
        case 'modificar':
            echo $custom->modificar($_POST);
            break;
         case 'eliminar':
            echo $custom->eliminar($_POST['id']);
            break;
        default:
            header("Location: 404.php");
            break;
    }
}
 ?>
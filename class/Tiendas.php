<?php 
	require_once dirname(__FILE__) . '/../app/DB.php';
	/**
	* 
	*/
	class Tiendas {

		private $pdo;

		public function __construct(){
			//inicializamos conexion a la bd
			$this->pdo = DB::init()->getDB();
		}

		public function getTiendas(){
			$respuesta = ['estado' => 0,'mensaje' => '' ];
			try {
				$query = "SELECT *from dw_tiendas";
				$sentencia = $this->pdo->prepare($query);
		        $sentencia->execute();
		        $resultado = $sentencia->fetchAll();
		        $respuesta['tiendas'] = $resultado;
		        $respuesta['estado'] = 1;
				$respuesta['mensaje'] = 'Tiendas encontradas';
			} catch (Exception $e) {
				$respuesta['estado'] = 0;
				$respuesta['mensaje'] = $e->getMessage();

			}

			return json_encode($respuesta);
		}

		public function getTienda($id){
			$respuesta = ['estado' => 0,'mensaje' => '' ];
			try {
				$query = "SELECT *from dw_tiendas WHERE id= :id";
				$sentencia = $this->pdo->prepare($query);
				$sentencia->bindParam(':id',$id);
		        $sentencia->execute();
		        $resultado = $sentencia->fetchAll();
		        $respuesta['tienda'] = $resultado;
		        $respuesta['estado'] = 1;
				$respuesta['mensaje'] = 'Tiendas encontradas';
			} catch (Exception $e) {
				$respuesta['estado'] = 0;
				$respuesta['mensaje'] = $e->getMessage();

			}

			return json_encode($respuesta);
		}

		public function crearTienda($nombre, $url, $clase){
			$respuesta = ['estado' => 0,'mensaje' => '' ];
			try {
				$query = "INSERT INTO dw_tiendas (tienda,url,clase) VALUES(:tienda,:url,:clase)";
				$sentencia = $this->pdo->prepare($query);
				$sentencia->bindParam(':tienda', $nombre);
				$sentencia->bindParam(':url', $url);
				$sentencia->bindParam(':clase', $clase);
		        if($sentencia->execute()){
		        	$respuesta['estado'] = 1;
					$respuesta['mensaje'] = 'Tienda creada correctamente';
		        }else{
		        	$respuesta['estado'] = 0;
					$respuesta['mensaje'] = 'No se pudo crear la tienda correctamente';
		        }
		        
			} catch (Exception $e) {
				$respuesta['estado'] = 0;
				$respuesta['mensaje'] = $e->getMessage();
			}

			return json_encode($respuesta);
		}

		public function updateTienda($id,$nombre,$url,$clase){
			$respuesta = ['estado' => 0,'mensaje' => '' ];
			try {
				$query = "UPDATE dw_tiendas SET tienda= :tienda,url= :url,clase= :clase WHERE id= :id";
				$sentencia = $this->pdo->prepare($query);
				$sentencia->bindParam(':tienda', $nombre);
				$sentencia->bindParam(':url', $url);
				$sentencia->bindParam(':clase', $clase);
				$sentencia->bindParam(':id', $id);
		        if($sentencia->execute()){
		        	$respuesta['estado'] = 1;
					$respuesta['mensaje'] = 'Tienda modificada correctamente';
		        }else{
		        	$respuesta['estado'] = 0;
					$respuesta['mensaje'] = 'No se pudo actualizar la tienda correctamente';
		        }
		        
			} catch (Exception $e) {
				$respuesta['estado'] = 0;
				$respuesta['mensaje'] = $e->getMessage();
			}

			return json_encode($respuesta);
		}

		public function deleteTienda($id){
			$respuesta = ['estado' => 0,'mensaje' => '' ];
			try {
				$query = "DELETE FROM dw_tiendas WHERE id = :id";

				$sentencia = $this->pdo->prepare($query);
				$sentencia->bindParam(':id', $id);

		        if($sentencia->execute()){
		        	$respuesta['estado'] = 1;
					$respuesta['mensaje'] = 'Se eliminó la tienda correctamente';
		        }else{
		        	$respuesta['estado'] = 0;
					$respuesta['mensaje'] = 'No se pudo eliminar la tienda correctamente';
		        }
		        
		        
			} catch (Exception $e) {
				$respuesta['estado'] = 0;
				$respuesta['mensaje'] = $e->getMessage();

			}

			return json_encode($respuesta);
		}
	}

	/**
	 * RECEPCION DE PETICIONES
	 */
	
	if(isset($_POST['post'])){
		$post= $_POST['post'];
		$tienda = new Tiendas();
		switch ($post) {
			case 'getTiendas':
				echo $tienda->getTiendas();
				break;
			case 'getTienda':
				echo $tienda->getTienda($_POST['id']);
				break;
			case 'crearTienda':
				echo $tienda->crearTienda($_POST['nombre'],$_POST['url'],$_POST['clase']);
				break;
			case 'updateTienda':
				echo $tienda->updateTienda($_POST['id_tienda'],$_POST['u-nombre'],$_POST['u-url'],$_POST['u-clase']);
				break;
			case 'deleteTienda':
				echo $tienda->deleteTienda($_POST['id']);
				break;
			default:
				header("Location: 404.php");
				break;
		}
	}
 ?>
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
			default:
				header("Location: 404.php");
				break;
		}
	}
 ?>
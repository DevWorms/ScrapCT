<?php 
	require_once dirname(__FILE__) . '/../app/DB.php';
	/**
	* Clase para las utilidades del usuario
	*/
	class User {

		private $pdo;

		public function __construct(){
			//inicializamos conexion a la bd
			$this->pdo = DB::init()->getDB();
		}

		/**
		 * Inicio de sesion a la aplicacion
		 * @param  [string] $usuario    
		 * @param  [string] $contrasena 
		 * @return [json]             
		 */
		public function inicarSesion($correo , $contrasena){

			$respuesta = ['estado' => 0,'mensaje' => '' ];

			$contrasenaCifrada = hash('sha256', $contrasena);
			//buscamos al usuario
			$consultaSesion = "SELECT * FROM dw_usuarios  WHERE correo = ? AND contrasena = ?";
	        $sentencia = $this->pdo->prepare($consultaSesion);
	        $sentencia->bindParam(1, $correo);
	        $sentencia->bindParam(2, $contrasenaCifrada);
	        $sentencia->execute();
	        $resultado = $sentencia->fetchAll();

	        // Si existe el usuario
	        if(count($resultado) > 0){
	        	$resultado = $resultado[0];
	        	//Generamos la sesion
	        	session_start();
	        	$_SESSION['id'] = $resultado['id'];
	        	$_SESSION['usuario'] = $resultado['usuario'] . " " .$resultado['apellido'];
	        	$_SESSION['correo'] = $resultado['correo'];
	        	session_write_close();

	        	$respuesta['estado'] = 1;
            	$respuesta['mensaje'] = "Bienvenido " . $resultado['usuario'] . " " . $resultado['apellido'] ;
	        }else{
	        	//session_destroy();
	        	//si no existe
	        	$respuesta['estado'] = 0;
            	$respuesta['mensaje'] = "Usuario o contraseña incorrecta ";
	        }
	        //devolvemos una respuesta json
	        return json_encode($respuesta);
		}

		/**
		 * cerrar session
		 * @return none
		 */
		public function cerrarSesion(){
			if (session_status() == PHP_SESSION_NONE) {
        		session_start();
		    }
			session_destroy();

			header("Location: " . '../');
		}

		public function getUsuarios(){
			$respuesta = ['estado' => 0,'mensaje' => '' ];
			try {
				$query = "SELECT usuario,apellido,correo,id from dw_usuarios";
				$sentencia = $this->pdo->prepare($query);
		        $sentencia->execute();
		        $resultado = $sentencia->fetchAll();
		        $respuesta['usuarios'] = $resultado;
		        $respuesta['estado'] = 1;
				$respuesta['mensaje'] = 'Usuarios encontrados';
			} catch (Exception $e) {
				$respuesta['estado'] = 0;
				$respuesta['mensaje'] = $e->getMessage();

			}

			return json_encode($respuesta);
		}

		public function createUsuario($nombre,$apellido,$correo,$contrasena){
			$respuesta = ['estado' => 0,'mensaje' => '' ];
			try {

				$contrasena= hash('sha256', $contrasena);

				$query = "INSERT INTO dw_usuarios (usuario,apellido,correo,contrasena) VALUES(:usuario,:apellido,:correo,:contrasena)";

				$sentencia = $this->pdo->prepare($query);
				$sentencia->bindParam(':usuario', $nombre);
				$sentencia->bindParam(':apellido', $apellido);
				$sentencia->bindParam(':correo', $correo);
				$sentencia->bindParam(':contrasena', $contrasena);

		        if($sentencia->execute()){
		        	$respuesta['estado'] = 1;
					$respuesta['mensaje'] = 'Usuario creado correctamente';
		        }else{
		        	$respuesta['estado'] = 0;
					$respuesta['mensaje'] = 'No se pudo crear el usuario correctamente';
		        }
		        
		        
			} catch (Exception $e) {
				$respuesta['estado'] = 0;
				$respuesta['mensaje'] = $e->getMessage();

			}

			return json_encode($respuesta);
		}

		public function deleteUsuario($id){
			$respuesta = ['estado' => 0,'mensaje' => '' ];
			try {
				$query = "DELETE FROM dw_usuarios WHERE id = :id";

				$sentencia = $this->pdo->prepare($query);
				$sentencia->bindParam(':id', $id);

		        if($sentencia->execute()){
		        	$respuesta['estado'] = 1;
					$respuesta['mensaje'] = 'Se eliminó el usuario correctamente';
		        }else{
		        	$respuesta['estado'] = 0;
					$respuesta['mensaje'] = 'No se pudo eliminar el usuario correctamente';
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
		$usuario = new User();
		switch ($post) {
			case 'login':
				echo  $usuario->inicarSesion($_POST['correo'], $_POST['contrasena']);
				break;
			case 'logout':
				echo  $usuario->cerrarSesion();
				break;
			case 'getUsuarios':
				echo $usuario->getUsuarios();
				break;
			case 'crearUsuario':
				echo $usuario->createUsuario($_POST['nombre'], $_POST['apellido'], $_POST['correo'], $_POST['contrasena']);
				break;
			case 'deleteUsuario':
				echo $usuario->deleteUsuario($_POST['id']);
				break;
			default:
				header("Location: 404.php");
				break;
		}
	}
 ?>
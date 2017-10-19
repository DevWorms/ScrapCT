<?php 
	require_once dirname(__FILE__) . '/../app/DB.php';
	/**
	* User class
	*/
	class User {

		private $pdo;

		public function __construct(){
			$this->pdo = ConexionBD::obtenerInstancia()->obtenerBD();
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
			$consultaSesion = "SELECT * FROM usuarios  WHERE correo = ? AND contrasena = ?";
	        $sentencia = $this->pdo->prepare($consultaSesion);
	        $sentencia->bindParam(1, $correo);
	        $sentencia->bindParam(2, $contrasenaCifrada);
	        $sentencia->execute();
	        $resultado = $sentencia->fetchAll();

	        // Si existe el usuario
	        if(count($resultado) > 0){
	        	//Generamos la sesion
	        	session_start();
	        	$_SESSION['id'] = $resultado['id'];
	        	$_SESSION['usuario'] = $resultado['usuario'] . $resultado['apellido'];
	        	$_SESSION['correo'] = $resultado['correo'];
	        	session_write_close();

	        	$respuesta['estado'] = 1;
            	$respuesta['mensaje'] = "Bienvenido " . $resultado['usuario'] . $resultado['apellido'] ;
	        }else{
	        	//si no existe
	        	$respuesta['estado'] = 0;
            	$respuesta['mensaje'] = "Usuario o contraseña incorrecta ";
	        }

	        return json_encode($respuesta);
		}


	}
 ?>
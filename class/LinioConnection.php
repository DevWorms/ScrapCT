<?php 
	/**
	* Clase para la conexion con el api de LINIO
	*/
	class LinioConnection
	{
		/**
		 * CONSTANTES - CLAVES DE ACCESO
		 */
		const NETWORK_TOKEN_APIKEY = '2619d93329d6d12aeb131abe6e8e43c12b413cf3f004512764bf1681f52ead87'; 
		const NETWORK_ID = 'linio';

		function __construct(){

		}
		/**
		 * Metodo que genera la construccion y peticion a la api
		 * @return respuesta associative array
		 */
		public function apiCall(){
			//devolveremos esto
			$respuesta = ['estado' => 0,'mensaje' => '' , 'resultado' => null];  
			//construimos el url
			//AUTENTICACION
			$apiCallURL = "https://" . LinioConnection::NETWORK_ID . ".api.hasoffers.com/Apiv3/json?";
			$apiCallURL .= "NetworkToken=". LinioConnection::NETWORK_TOKEN_APIKEY;
			//TARGET
			$apiCallURL .= "&Target=Offer";
			//METODO
			$apiCallURL .= "&Method=findAll";
			//CAMPOS PARA RECIBIR
			//FILTROS
			$apiCallURL .= "&filters[status]=active";
			$apiCallURL .= "&filters[currency]=MXN";
			//ORDEN
			//LIMITE
			$apiCallURL .= "&limit=10";

			// INICIALIZAMOS EL CURL Y HACEMOS LA PETICION
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $apiCallURL);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$resultado = curl_exec($ch);
			curl_close($ch);

			// Revertimos el json a arreglo asociativo para poder manipularlo en PHP
			$respuesta['resultado']=  json_decode($resultado, true);

			// Verficamos si no hubo problema con la respuesta JSON para no generar una malformacion
			if(json_last_error()!==JSON_ERROR_NONE){
					$respuesta['estado'] = 0;
					$respuesta['mensaje'] = "El json obtenido de la peticion esta mal formado";
					$respuesta['resultado'] = null;
			}else{
				//ahora si el json esta correcto verificamos si la peticion fue exitosa
				if(isset($respuesta['resultado']['response']['status']) 
					&& $respuesta['resultado']['response']['status']===1){
					$respuesta['estado'] = 1;
					$respuesta['mensaje'] = "Peticion a la api EXITOSA";
					$respuesta['resultado'] = $respuesta['resultado']['response']['data'];
				}else{
					// si la peticion devolvio un error 
					$respuesta['estado'] = 0;
					$respuesta['mensaje'] =(isset($respuesta['resultado']['response']['errorMessage'])?' ('.$respuesta['resultado']['response']['errorMessage'].')':'');
					$respuesta['resultado'] = "Sin resultados";
					
				}
				//FIN
			}
			//FIN
			
			return json_encode($respuesta);
		}
	}

	$linio = new LinioConnection();
	echo $linio->apiCall();
 ?>
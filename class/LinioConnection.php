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
		 * @return response JSON
		 */
		public function apiCall(){
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
			//ORDEN
			//LIMITE
			$apiCallURL .= "&limit=10";
			
			// INICIALIZAMOS EL CURL Y HACEMOS LA PETICION
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $apiCallURL);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($ch);
			curl_close($ch);

			// Revertimos el json a arreglo asociativo para poder manipularlo en PHP
			$response = json_decode($response, true);

			// Verficamos si no hubo problema con la respeusta JSON para no generar una malformacion
			if(json_last_error()!==JSON_ERROR_NONE){
				throw new RuntimeException(
					'API response not well-formed (json error code: '.json_last_error().')'
				);
			}

			// IMPRECION DE PRUEBA
			if(isset($response['response']['status']) && $response['response']['status']===1){
				echo 'API call successful';
				echo PHP_EOL;
				// EL atributo dara contiene mi lista de productos
				echo 'Response Data: <pre>'.print_r($response['response']['data'], true).'';
				echo PHP_EOL;
			}else{
				//SI EXISTE UN ERROR
				echo 'API call failed'.(isset($response['response']['errorMessage'])?' ('.$response['response']['errorMessage'].')':'').'';
				echo PHP_EOL;
				echo 'Errors: <pre>'.print_r($response['response']['errors'], true).'';
				echo PHP_EOL;
			}
		}


	}

	$linio = new LinioConnection();
	$linio->apiCall();
 ?>
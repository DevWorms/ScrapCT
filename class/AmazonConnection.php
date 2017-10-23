<?php 
	/**
	* Clase para la conexion con el api de Amazon
	*/
	class AmazonConnection{
		/**
		 * CONSTANTES
		 */
		const AWS_API_KEY= 'AKIAISNMOFVRONHVLIBA';
		const AWS_API_SECRET_KEY= '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';
		const AWS_ASSOCIATE_TAG= 'tecchec-20';

		function __construct(){

		}

		private function getPeticionAmazon($region, $params, $public_key, $private_key, $associate_tag=NULL, $version='2011-08-01'){
		    $method = 'GET';
		    $host = 'webservices.amazon.'.$region;
		    $uri = '/onca/xml';
		    
		    $params['Service'] = 'AWSECommerceService';
		    $params['AWSAccessKeyId'] = $public_key;
		    $params['Timestamp'] = gmdate('Y-m-d\TH:i:s\Z');
		    $params['Version'] = $version;
		    if ($associate_tag !== NULL) {
		        $params['AssociateTag'] = $associate_tag;
		    }
		    ksort($params);

		    $canonicalized_query = array();
		    foreach ($params as $param=>$value)
		    {
		        $param = str_replace('%7E', '~', rawurlencode($param));
		        $value = str_replace('%7E', '~', rawurlencode($value));
		        $canonicalized_query[] = $param.'='.$value;
		    }
		    $canonicalized_query = implode('&', $canonicalized_query);
		    $string_to_sign = $method."\n".$host."\n".$uri."\n".$canonicalized_query;
		    $signature = base64_encode(hash_hmac('sha256', $string_to_sign, $private_key, TRUE));
		    $signature = str_replace('%7E', '~', rawurlencode($signature));
		    $request = 'http://'.$host.$uri.'?'.$canonicalized_query.'&Signature='.$signature;
		    
		    return $request;
		}

		public function apiCall(){
			//devolveremos esto
			$respuesta = ['estado' => 0,'mensaje' => '' , 'resultado' => null];  
			//Establecemos las cabeceras
			if (isset($_SERVER['HTTP_ORIGIN'])) {

			    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
			    header('Access-Control-Allow-Credentials: true');
			    header('Access-Control-Max-Age: 86400');    // cache for 1 day
			}

			//Obtenemos los parametros
			$json = urldecode($_GET["json"]);
			$json = json_decode($json);

			$asin = $json->asin;
			$page = $json->page;
			$keywords = $json->keywords;

			//OBTENEMOS LA PETICION GENERADA
			$request = $this->getPeticionAmazon('com.mx', array(
					'Operation' => 'ItemSearch',
					'Condition' => 'All',
					'ItemPage'=>"$page",
					'SearchIndex' => 'Electronics',
					'BrowseNode' => $asin,
					'ResponseGroup'=>'Offers'), 
					AmazonConnection::AWS_API_KEY,
					AmazonConnection::AWS_API_SECRET_KEY,
					AmazonConnection::AWS_ASSOCIATE_TAG);

			echo "$request" . "<br>";
		

			// traempos el contenido que devuelve la peticion
			$response = @file_get_contents($request);


			if ($response === FALSE) {
				$respuesta['estado'] = 0;
				$respuesta['mensaje'] = "No se pudo obtener la respuesta";
				$respuesta['resultado'] = null;
			} else {
				$pxml = simplexml_load_string($response);
				$respuesta['estado'] = 1;
				$respuesta['mensaje'] = "Resultados obtenidos";
				$respuesta['resultado'] = $pxml->Items;
				json_encode($pxml->Items);
			}

			return json_encode($respuesta);
		}
	}

	$o = new AmazonConnection();
	echo $o->apiCall();
 ?>
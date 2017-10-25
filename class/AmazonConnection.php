<?php
		require_once __DIR__ . '/../app/DB.php';
		error_reporting(E_ALL);
	/**
	* Clase para la conexion con el api de Amazon
	*/
	class AmazonConnection
	{
		//CONSTANTES
		const AWS_API_KEY= 'AKIAISNMOFVRONHVLIBA';
		const AWS_API_SECRET_KEY= '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';
		const AWS_ASSOCIATE_TAG= 'tecchec-20';
		const AWS_SERVICIO = 'AWSECommerceService';

		public $nodosBase;
		public $allNodes;

		function __construct(){
			$this->db = DB::init()->getDB();
			$this->nodosBase = array(
				'videojuegos' => '9482691011',
				'electronicos' => '9482559011'
				);
			$this->allNodes = array("");
		}

		/**
		 * Valida si un producto existe en la BD usando el ASIN de amazon
		 *
		 * @param $amazon_asin
		 * @return bool
		 */
		public function exists($amazon_asin) {
				$query = "SELECT * FROM wp_pwgb_postmeta WHERE meta_key='asin' AND meta_value=:asin;";
				$pdo = $this->db->prepare($query);
				$pdo->bindValue(":asin", $amazon_asin, PDO::PARAM_INT);
				$pdo->execute();

				$response = $pdo->fetchAll(PDO::FETCH_ASSOC);
				return (count($response) > 0) ? true : false;
		}


		/**
		 * Valida si el precio de un producto de amazon cambio de precio
		 *
		 * @param $amazon_asin
		 * @param $price
		 * @return bool
		 */
		public function priceHasChanged($amazon_asin, $price) {
				// Valida que el producto exista
				$query = "SELECT * FROM wp_pwgb_postmeta WHERE meta_key='asin' AND meta_value=:asin;";
				$pdo = $this->db->prepare($query);
				$pdo->bindValue(":asin", $amazon_asin, PDO::PARAM_INT);
				$pdo->execute();

				$response = $pdo->fetchAll(PDO::FETCH_ASSOC);
				// El producto existe
				if (count($response) > 0) {
						// Obtiene el precio de amazon
						$post_id = $response[0]["post_id"];
						$query = "SELECT * FROM wp_pwgb_postmeta WHERE meta_key='price_amazon' AND post_id=:post_id;";
						$pdo2 = $this->db->prepare($query);
						$pdo2->bindValue(":post_id", $post_id, PDO::PARAM_INT);
						$pdo2->execute();
						$response2 = $pdo2->fetchAll(PDO::FETCH_ASSOC);

						// El precio de amazon existe
						if (count($response2) > 0) {
								if ($price == $response[0]["price_amazon"]) {
										// El precio es el mismo
										return false;
								}
						}
				}

				return true;
		}

		/**
		 * Actualiza el precio de un producto de amazon, usando su ASIN
		 *
		 * @param $amazon_asin
		 * @param $price
		 * @return array
		 */
		public function updateAmazonPrice($amazon_asin, $price) {
				try {
						$query = "SELECT post_id FROM wp_pwgb_postmeta WHERE meta_key='asin' AND meta_value=:asin;";
						$pdo = $this->db->prepare($query);
						$pdo->bindValue(":asin", $amazon_asin, PDO::PARAM_INT);
						$pdo->execute();

						$response = $pdo->fetchAll(PDO::FETCH_ASSOC);
						if (count($response) > 0) {
								$query = "UPDATE wp_pwgb_postmeta SET meta_value=:price WHERE meta_key='price_amazon' AND post_id=:post_id;";
								$pdo2 = $this->db->prepare($query);
								$pdo2->bindValue(":price", $price, PDO::PARAM_INT);
								$pdo2->bindValue(":post_id", $response[0][0], PDO::PARAM_INT);
								$pdo2->execute();

								return ["status" => 1, "message" => "success"];
						} else {
								// TODO Crear el producto
						}
				} catch (Exception $e) {
						return ["status" => 0, "message" => $e->getMessage()];
				}
		}


		/**
		 * Crea un producto, junto a sus metadatos
		 *
		 * @param $producto
		 * @param $precio
		 * @param $asin
		 * @param $link
		 * @param string $descripcion
		 * @param string $modelo
		 * @param string $fabricante
		 * @return array
		 */
		public function insertProduct($producto, $precio, $asin, $link, $descripcion = '', $modelo = '', $fabricante = '') {
				try {
						$slug = $this->slugify($producto);
						$guid = "http://www.tec-check.com.mx/reviews/" . $slug;
						$query = "INSERT INTO wp_pwgb_posts (post_author, post_date, post_date_gmt, post_content, post_title
													post_excerpt, post_status, comment_status, ping_status, post_password, post_name, 
													to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, 
													menu_order, post_type, post_mime_type, comment_count) VALUES (
													8, NOW(), NOW(), :descripcion, :producto, '', 'publish', 'open', 'open', '', :slug, '', '', 
													NOW(), NOW(), '', '0', :guid, '0', 'reviews', '', '0')";
						$pdo = $this->db->prepare($query);
						$pdo->bindValue(":descripcion", $descripcion, PDO::PARAM_STR);
						$pdo->bindValue(":producto", $producto, PDO::PARAM_STR);
						$pdo->bindValue(":slug", $slug, PDO::PARAM_STR);
						$pdo->bindValue(":guid", $guid, PDO::PARAM_STR);
						$pdo->execute();

						$post_id = $this->db->lastInsertId();

						$query_metadata = "INSERT INTO wp_pwgb_postmeta (meta_value, meta_key, post_id) VALUES (:asin, 'asin', :post_id);";
						$pdo2 = $this->db->prepare($query_metadata);
						$pdo2->bindValue(":asin", $asin, PDO::PARAM_STR);
						$pdo2->bindValue(":post_id", $post_id, PDO::PARAM_INT);
						$pdo2->execute();

						$query_metadata = "INSERT INTO wp_pwgb_postmeta (meta_value, meta_key, post_id) VALUES (:model, 'model', :post_id);";
						$pdo3 = $this->db->prepare($query_metadata);
						$pdo3->bindValue(":model", $modelo, PDO::PARAM_STR);
						$pdo3->bindValue(":post_id", $post_id, PDO::PARAM_INT);
						$pdo3->execute();

						$query_metadata = "INSERT INTO wp_pwgb_postmeta (meta_value, meta_key, post_id) VALUES (:pl, 'amazon_affiliate_link', :post_id);";
						$pdo4 = $this->db->prepare($query_metadata);
						$pdo4->bindValue(":pl", $link, PDO::PARAM_STR);
						$pdo4->bindValue(":post_id", $post_id, PDO::PARAM_INT);
						$pdo4->execute();

						$query_metadata = "INSERT INTO wp_pwgb_postmeta (meta_value, meta_key, post_id) VALUES (:company, 'company', :post_id);";
						$pdo5 = $this->db->prepare($query_metadata);
						$pdo5->bindValue(":company", $fabricante, PDO::PARAM_STR);
						$pdo5->bindValue(":post_id", $post_id, PDO::PARAM_INT);
						$pdo5->execute();

						$query_metadata = "INSERT INTO wp_pwgb_postmeta (meta_value, meta_key, post_id) VALUES (:price, 'price_amazon', :post_id);";
						$pdo6 = $this->db->prepare($query_metadata);
						$pdo6->bindValue(":price", $precio, PDO::PARAM_STR);
						$pdo6->bindValue(":post_id", $post_id, PDO::PARAM_INT);
						$pdo6->execute();
				} catch (Exception $e) {
						return ["status" => 0, "message" => $e->getMessage()];
				}
		}

		/**
		 * Convierte un texto, en una url
		 *
		 * @param $text
		 * @return mixed|string
		 */
		public function slugify($text)
		{
				$text = preg_replace('~[^\pL\d]+~u', '-', $text);
				$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
				$text = preg_replace('~[^-\w]+~', '', $text);
				$text = trim($text, '-');
				$text = preg_replace('~-+~', '-', $text);
				$text = strtolower($text);

				if (empty($text)) {
						return 'n-a';
				}

				return $text;
		}


        /**
         * Busca un producto de Amazon en las demás tiendas, en base a su nombre, modelo y fabricante
         *
         * @param $name
         * @param $model
         * @param $company
         */
        public function searchProduct($name, $model, $company) {

        }

		/**
		 * Metodo que construye la peticion base y el query de busqueda
		 * @param  string $region  [ejemplo com.mx]
		 * @param  assoc_array $params  [parametros de busqueda]
		 * @param  string $version [version de la api]
		 * @return [string]  $request 
		 */
		private function construirPeticion($region, $params, $version='2011-08-01'){
			/*** PARAMETROS OBLIGATORIOS ***/
			//Metodo
			$method = 'GET';
			//punto de conexion
			$host = 'webservices.amazon.'.$region;
			//estilo de respuesta
			$uri = '/onca/xml';
			//servicio a consultar
			$params['Service'] = AmazonConnection::AWS_SERVICIO;
			//Api key id
			$params['AWSAccessKeyId'] = AmazonConnection::AWS_API_KEY;
			// tiempo actual de consulta
			$params['Timestamp'] = gmdate('Y-m-d\TH:i:s\Z');
			//version a utilizar
			$params['Version'] = $version;
			// tag de socio para comision por click
			$params['AssociateTag'] = AmazonConnection::AWS_ASSOCIATE_TAG;
			//Queremos solo electronicos
			$params['SearchIndex'] = "Electronics";
			//ordenamos los parametros
			ksort($params);
			$url_query = array();
			//generamos el query de peticion o busqueda
			foreach ($params as $param=>$value)
			{
					$param = str_replace('%7E', '~', rawurlencode($param));
					$value = str_replace('%7E', '~', rawurlencode($value));
					$url_query[] = $param.'='.$value;
			}
			//separamos por and person los elementos del arreglo
			$url_query = implode('&', $url_query);
			//generamos la peticion base y de busqueda
			$str_query_peticion = $method."\n".$host."\n".$uri."\n".$url_query;
			//Agregamos la llave privada al final
			$signature = base64_encode(hash_hmac('sha256', $str_query_peticion,
			 AmazonConnection::AWS_API_SECRET_KEY, TRUE));
			$signature = str_replace('%7E', '~', rawurlencode($signature));
			$request = 'http://'.$host.$uri.'?'.$url_query.'&Signature='.$signature;
			
			return $request;
		}

		/**
		 * Metodo que habilita cabeceras de origen
		 * @return none
		 */
		private function verifyHTTPorigins(){

			if (isset($_SERVER['HTTP_ORIGIN'])) {
				header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
				header('Access-Control-Allow-Credentials: true');
				header('Access-Control-Max-Age: 86400');    // cache for 1 day
			}
		}


		/**
		 * Metodo que genera la funcion BrowseNodeLookup de amazon
		 * este busca categorias o browsenode hijo y ancenstros del nodo que recibe
		 * @param  [string] $browseNodeId [Nodo padre]
		 * @return [associative] $response
		 */
		public function browseNodeLookup($browseNodeId){
			$amazon = $this;
			$amazon->verifyHTTPorigins();

			$parametros = array('Operation' => 'BrowseNodeLookup' ,
							 			'BrowseNodeId' => $browseNodeId);

			$peticion = $amazon->construirPeticion("com.mx",$parametros);

			$response= ['estado' => 0,'mensaje' => '' , 'resultado' => null];
			//parseamos el contenido a un string
			$xmlContent = @file_get_contents($peticion);
			
			if($xmlContent === FALSE){
				// si no se pudo obtener limpiamos todo
				$response['estado'] = 0;
				$response['mensaje'] = 'No se pudo obtener respuesta a esta peticion';
				$response['resultado'] = null;
				return $response;
			}

			$pxml = simplexml_load_string($xmlContent);

			//devovlemos la respuesta structurada
			$response['estado'] = 1;
			$response['mensaje'] = 'Peticion exitosa';
			$response['resultado'] = $pxml->BrowseNodes;

			return $response;

		}

		/**
		 * Regresa todos los hijos y nietos de los nodo semilla que se pase
		 * @param  [array] $semilla [Nodos principales Electronics y Videjojuegos]
		 * @return [array]          [Todos los nodos]
		 */
		public function getAllNodes($semilla){
			try{
				//PADRES
				foreach ($semilla as  $nodo) {
					array_push($this->allNodes, $nodo); 
					$resultado = $this->browseNodeLookup($nodo)['resultado'];
					if($resultado != null ){
						//HIJOS
						$hijos = $resultado->BrowseNode->Children->BrowseNode;
						if(count($hijos)){
							foreach ($hijos as $nodoHijo) {
								array_push($this->allNodes, $nodoHijo->BrowseNodeId);
								$resultado = $this->browseNodeLookup($nodoHijo->BrowseNodeId)['resultado'];
								if($resultado != null ){
									//NIETOS
									$nietos = $resultado->BrowseNode->Children->BrowseNode;
									if(count($nietos)){
										foreach ($nietos as $nodoNieto) {
											array_push($this->allNodes, $nodoNieto->BrowseNodeId);
										}
									}
									//FIN NIETOS
								}
							}
						}
						//FIN HIJOS
					}
				}
				// FIN PADRES

			} catch (Exception $e) {

				return json_encode(array("status" => 0, "message" => $e->getMessage()));
			}

			return $this->allNodes;
		}
		
		/**
		 * itemSearch Metodo que devuelve 10 producto de la pagina indicada y del nodo indicado
		 * @param  [string] $nodo   [categoria]
		 * @param  [string] $pagina  pagina de resultados solo del 1 al 10
		 * @return [type]         [description]
		 */
		public function itemSearch($nodo,$pagina){
			$amazon = $this;
			$parametros = array('Operation' => 'ItemSearch',
										'ItemPage'=>"$pagina",
										'Condition' => 'All',
										'BrowseNode' => "$nodo", 
										'ResponseGroup' => 'Medium');

			$peticion= $amazon->construirPeticion("com.mx", $parametros);


			$response= ['estado' => 0,'mensaje' => '' , 'resultado' => null];
			//parseamos el contenido a un string
			$xmlContent = @file_get_contents($peticion);
			
			if($xmlContent === FALSE){
				// si no se pudo obtener limpiamos todo
				$response['estado'] = 0;
				$response['mensaje'] = 'No se pudo obtener respuesta a esta peticion';
				$response['resultado'] = null;
				return $response;
			}

			$pxml = simplexml_load_string($xmlContent);

			//devovlemos la respuesta structurada
			$response['estado'] = 1;
			$response['mensaje'] = 'Peticion exitosa';
			$response['resultado'] = $pxml->Items;

			return $response;
		}

		
		/**
		 * Seccionamos los nodos en 20 partes para optimizar la busqueda 
		 * @return aassoc_array arreglo con los nodos seccionados
		 */
		public function seccionarNodos(){
			//arreglo de las cantidades para cada una de las 20 secciones
			$cantidad_nodos_seccion = array();
			// obtengo los nodos(categorias) de amazon
			$nodos = $this->getAllNodes($this->nodosBase);
			// elimino nodos duplicados
			$nodos = array_unique($nodos);
			//obtengo la cantidad total de nodos unicos
			$total_nodos = count($nodos);
			// entero de nodos que contendra cada seccion al menos 1 
			$nodos_seccion = 1;
			// variable de nodos sobrantes
			$nodos_sobrantes = 0;
			// deivison de nodos que tencan a cada seccion sin contar sobrantes
			$division = 0;
			// si se obtuvieron mas de 20
			if($total_nodos > 20){
				// el total de nodos entre las 20 secciones
				$division = $total_nodos / 20;
				// le quitamos lso decinales a la division y estos son los nodos minimos que tendra cada seccion
				$nodos_seccion = floor($division);
				// los nodos minimos por seccion obtenidos arroba * 20 nos dara menos del total, esta multiplicacion se la restamos al total y seran los nodos sobrantes
				$nodos_sobrantes = $total_nodos - ($nodos_seccion * 20);
				//recorremos 20 secciones
				for($cont = 1; $cont<=20 ;$cont++){
					// si hay nodos sobrantes
					if($nodos_sobrantes > 0){
						// distribumos de a un nodo sobrante entre las secciones
						$nodos_sobrantes--;
						$cantidad_nodos_seccion["seccion_" . $cont] =  $nodos_seccion + 1;
					}else{
						// si ya no hay nodos sobrantes le toca el minimo de nodos por seccion
						$cantidad_nodos_seccion["seccion_" . $cont] =  $nodos_seccion;
					}
					
				}
			}else{
				// si los nodos obtenidos son menos de 20
				// los nodos por seccion son 1
				$nodos_seccion = 1;
				for($cont = 1; $cont<=20 ;$cont++){
					// se distribuye de auno a las secciones y guardamos en el arreglo
					$cantidad_nodos_seccion["seccion_" . $cont] =  $nodos_seccion;
				}
			}
			// arreglo para guardar los nodos seccionados en relacion a la cantidad que le toca a cada seccion
			$nodos_seccionados = array();
			// indice actual
			$current_index = 0;
			// limite a recorrer
			$limite = 0;
			// obtenemos las cantidades que le tocan a cada seccion
			foreach ($cantidad_nodos_seccion as $seccion => $cantidad) {
				// arregl de nodos de la seccion actual
				$nodos_save = array();
				// el liminite sera la cantidad actual
				$limite += $cantidad;
				//recorremos del indice acutal al imite actual
				for($it = $current_index ; $it<= ($limite-1) ; $it++){
					// si hay nodo lo guardamos
					if(isset($nodos[$it])){
						$nodos_save[] = $nodos[$it];
					}
					
				}
				// el indice se mueve al ultimo limite obtenido
				$current_index = $limite;
				//asignamos lso nodos guardados a la seccion
				$nodos_seccionados[$seccion] = $nodos_save;
			}
			// impresion de prueba
			foreach ($nodos_seccionados as $key => $value) {
				echo " $key --> " .implode(" , ", $value) . " <br> <br>";
			}

	
		}
				
	}

	$amazon = new AmazonConnection();
	$amazon->seccionarNodos();


	
?>
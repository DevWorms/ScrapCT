<?php
		require_once __DIR__ . '/../app/DB.php';

	/**
	* Clase para la conexion con el api de Amazon
	*/
	class AmazonConnection
	{
		//CONSTANTES
		const AWS_API_KEY= 'AKIAISNMOFVRONHVLIBA';
		const AWS_API_SECRET_KEY= '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';
		const AWS_ASSOCIATE_TAG= 'tecchec-20';
		CONST AWS_SERVICIO = 'AWSECommerceService';

		function __construct(){
			$this->db = DB::init()->getDB();
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
		 * Devuelve en un objeto de la clase SimpleXML el resultado de una peticion
		 * @param  [string] $request [peticion]
		 * @return asocc array   $response [objeto de response]
		 */
		private function getResponse($request){
			$response= ['estado' => 0,'mensaje' => '' , 'resultado' => null];
			//parseamos el contenido a un string
			$xmlContent = @file_get_contents($request);
			
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

		public function browseNodeLookup($browseNodeId){
			$amazon = $this;
			$amazon->verifyHTTPorigins();

			$parametros = array('Operation' => 'BrowseNodeLookup' ,
							 			'BrowseNodeId' => $browseNodeId);

			$peticion = $amazon->construirPeticion("com.mx",$parametros);

			return $amazon->getResponse($peticion);
		}
				

	}

	$amazon = new AmazonConnection();
	echo json_encode($amazon->browseNodeLookup("9687950011"));
?>
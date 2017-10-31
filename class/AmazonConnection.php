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
		public function insertProduct($producto, $precio, $asin, $link, $descripcion = '', $modelo = '', $fabricante = '', $img_url,$datos,$nodo) {
				try {
						$slug = $this->slugify($producto);
						$guid = "http://www.tec-check.com.mx/reviews/" . $slug;
						$query = "INSERT INTO wp_pwgb_posts (post_author, post_date, post_date_gmt, post_content, post_title,post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count) VALUES (8, NOW(), NOW(), :descripcion, :producto, '', 'publish', 'open', 'open', '', :slug, '', '', NOW(), NOW(), '', '0', :guid, '0', 'reviews', '', '0')";
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

						$query_metadata = "INSERT INTO wp_pwgb_postmeta (meta_value, meta_key, post_id) VALUES (:img_url, 'picture', :post_id);";

						$pdo7 = $this->db->prepare($query_metadata);
						$pdo7->bindValue(":img_url", $img_url, PDO::PARAM_STR);
						$pdo7->bindValue(":post_id", $post_id, PDO::PARAM_INT);
						$pdo7->execute();

						$query_metadata = "INSERT INTO wp_pwgb_postmeta (meta_value, meta_key, post_id) VALUES (:datos, 'esp_tecnica', :post_id);";

						$pdo8 = $this->db->prepare($query_metadata);
						$pdo8->bindValue(":datos", $datos, PDO::PARAM_STR);
						$pdo8->bindValue(":post_id", $post_id, PDO::PARAM_INT);
						$pdo8->execute();

						// generamos relacion con categorias
						$query_relate = "INSERT INTO wp_pwgb_term_relationships (object_id,term_taxonomy_id,term_order) VALUES (:object_id,:term_taxonomy_id,:term_order)";

						$taxonomy_id= $this->getTaxonomiId($nodo);

						$pdo9 = $this->db->prepare($query_relate);
						$pdo9->bindValue(":object_id", $post_id);
						$pdo9->bindValue(":term_taxonomy_id", $taxonomy_id);
						$pdo9->bindValue(":term_order", 0);
						$pdo9->execute();

				} catch (Exception $e) {
						echo json_encode(["status" => 0, "message" => $e->getMessage()])."<br>";
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
			//para otras peticiones si se pasa el id o ASIN no es requerido el indice de electronicos
			if(!isset($params['ItemId'])){
				$params['SearchIndex'] = "Electronics";
			}
						
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
		private function browseNodeLookup($browseNodeId){
			$amazon = $this;
			$amazon->verifyHTTPorigins();

			$parametros = array('Operation' => 'BrowseNodeLookup' ,
							 			'BrowseNodeId' => $browseNodeId);

			$peticion = $amazon->construirPeticion("com.mx",$parametros);

			$response= ['estado' => 0,'mensaje' => '' , 'resultado' => null];
			//parseamos el contenido a un string
			echo "<br> consola > <b>Obteniendo las categorias para el nodo : " . $browseNodeId . "</b>";
			$xmlContent = @file_get_contents($peticion);
			
			if($xmlContent === FALSE){
				// si no se pudo obtener limpiamos todo
				$response['estado'] = 0;
				$response['mensaje'] = 'No se pudo obtener respuesta a esta peticion';
				$response['resultado'] = null;
				echo "<br> consola > <span style='color:red'>Las Categorias para el nodo: " . $browseNodeId . " no puedieron ser resueltas</span>";
				return $response;
			}

			$pxml = simplexml_load_string($xmlContent);

			//devovlemos la respuesta structurada
			$response['estado'] = 1;
			$response['mensaje'] = 'Peticion exitosa';
			$response['resultado'] = $pxml->BrowseNodes;
			echo "<br> consola > <span style='color:green'>Categorias para el nodo: " . $browseNodeId . " obtenidas</span>";
			return $response;
		}

		/**
		 * Regresa todos los hijos y nietos de los nodo semilla que se pase
		 * @param  [array] $semilla [Nodos principales Electronics y Videjojuegos]
		 * @return [array]          [Todos los nodos]
		 */
		public function getAllNodes($semilla){
			echo "<br> consola > <b>Semilla inicial de nodos Electronicos y Videjojuegos ...</b>";
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
								if(!$this->existCategoria($nodoHijo->BrowseNodeId)){
									$this->insertCategorias($nodoHijo->Name , $nodoHijo->BrowseNodeId);
								}
								array_push($this->allNodes, $nodoHijo->BrowseNodeId);
								$resultado = $this->browseNodeLookup($nodoHijo->BrowseNodeId)['resultado'];
								if($resultado != null ){
									//NIETOS
									$nietos = $resultado->BrowseNode->Children->BrowseNode;
									if(count($nietos)){
										foreach ($nietos as $nodoNieto) {
											if(!$this->existCategoria($nodoNieto->BrowseNodeId)){
												$this->insertCategorias($nodoNieto->Name , $nodoNieto->BrowseNodeId);
											}
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
				echo "<br> consola > <span style='color:red'>Exception : ".$e->getMessage() ."</span>";
				return json_encode(array("status" => 0, "message" => $e->getMessage()));
			}

			return $this->allNodes;
		}
		
		/**
		 * itemSearch Metodo que devuelve 10 producto de la pagina indicada y del nodo indicado
		 * @param  [string] $nodo   [categoria]
		 * @param  [string] $pagina  pagina de resultados solo del 1 al 10
		 * @return [assoc array]  $response
		 */
		private function itemSearch($nodo,$pagina){
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
			$total_secciones= 10;
			//arreglo de las cantidades para cada una de las 20 secciones
			$cantidad_nodos_seccion = array();
			// obtengo los nodos(categorias) de amazon
			$nodos = $this->getAllNodes($this->nodosBase);
			echo "<br> consola > <b>Categorias obtenidas</b>";
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
			echo "<br> consola > <b>Calculando secciones ...</b>";
			// si se obtuvieron mas de 10
			if($total_nodos > $total_secciones){
				// el total de nodos entre las 10 secciones
				$division = $total_nodos / $total_secciones;
				// le quitamos lso decinales a la division y estos son los nodos minimos que tendra cada seccion
				$nodos_seccion = floor($division);
				// los nodos minimos por seccion obtenidos arroba * 10 nos dara menos del total, esta multiplicacion se la restamos al total y seran los nodos sobrantes
				$nodos_sobrantes = $total_nodos - ($nodos_seccion * $total_secciones);
				//recorremos 10 secciones
				for($cont = 1; $cont<=$total_secciones ;$cont++){
					echo "<br> consola > <b>Determinando las cantidad de categorias por seccion ...</b>";
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
				for($cont = 1; $cont<=$total_secciones ;$cont++){
					echo "<br> consola > <b>CAsignando las categorias a cada seccion ...</b>";
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
			// cargamos a la base de datos los nodos por seccion
			echo "<br> consola > <b>Almacenando las secciones de categorias en la base de datos...</b>";
			$query = "UPDATE dw_secciones_nodos SET nodos = :nodos,conjunto_paginas= :conjunto_paginas WHERE seccion = :seccion";
			$sentencia = $this->db->prepare($query);
			foreach ($nodos_seccionados as $key => $value) {
				$str_nodos = implode(",", $value);
				$sentencia->bindValue(":nodos", $str_nodos);
				$sentencia->bindValue(":conjunto_paginas", 0);
				$sentencia->bindValue(":seccion", $key);
				$sentencia->execute();
			}

			echo "<br> consola > <b>Completado, <span style='color:blue'>Oprime Ejecutar proceso Amazon</span>...</b>";
		}

		/**
		 * Obtiene los productos de amazon de una seccion de nodos y conjunto de paginas dada
		 * y los inserta en la base de datos
		 * @param  [array] $nodos            [arreglo de nodos]
		 * @param  [int] $conjunto_paginas [conjumto 1 (1-5) , conjunto 2 (6-10)]
		 * @return [type]                   [description]
		 */
		private function getProductosByNodos($nodos,$conjunto_paginas){
			echo "<br> consola > Calculando el intervalo de paginas";
			$pag_ini= 0;
			$pag_fin = 0;
			// obtenemos el invervalo de pagians segun el conjunto dado
			if($conjunto_paginas == 1){
				$pag_ini = 1;
				$pag_fin = 5;
			}else if($conjunto_paginas == 2){
				$pag_ini = 6;
				$pag_fin = 10;
			}
			// iteramos los nodos dados
			foreach ($nodos as  $nodo) {
				// reccoremos el invervalo de pagias
				for ($pag=$pag_ini; $pag <=$pag_fin ; $pag++) { 
					//obtenemos los productos del nodo y pagina corrientes
					echo "<br> consola > <span style='color:blue'>Obteniendo productos del de la pagina $pag para el nodo $nodo...</span>";
					$response = $this->itemSearch($nodo, $pag);
					$response = $response['resultado'];
					// si la peticion tuvo resultadi
					if($response != null){
						// si contiene items (productos)
						if(isset($response->Item)){
							$items = $response->Item;
							// iteramso cada producto
							$nuevos = 0;
							$actualizados = 0;
							foreach ($items as $item) {
								// el nombre es el atrivuto title
								$producto = $item->ItemAttributes->Title;
								// si el precio nuevo desde existe
								if(isset($item->OfferSummary->LowestNewPrice->Amount)){
									$precio = ($item->OfferSummary->LowestNewPrice->Amount / 100);
									$precio= floor($precio);
								}else if(isset($item->ItemAttributes->ListPrice->Amount)){
									// si el no esta el precio mas bajo, entonces usamos el precio de lista
									$precio = ($item->ItemAttributes->ListPrice->Amount / 100);
									$precio= floor($precio);
								}
								//obtenemos los demas valores de manera directa
								$asin= $item->ASIN;
								$link = $item->DetailPageURL;
								$descripcion = $item->ItemAttributes->Feature;
								$modelo = $item->ItemAttributes->Model;
								$fabricante = $item->ItemAttributes->Manufacturer;
								$img_url = $item->LargeImage->URL;
								//especificaciones tecnicas
								$atributos =(array) $item->ItemAttributes;
								// todo menos lo que ya se almaceno y datos
								// inecesarios
								unset($atributos['Binding']);
								unset($atributos['CatalogNumberList']);
								unset($atributos['EAN']);
								unset($atributos['EANList']);
								unset($atributos['Feature']);
								unset($atributos['Manufacturer']);
								unset($atributos['Model']);
								unset($atributos['Title']);
								unset($atributos['MPN']);
								unset($atributos['UPC']);
								unset($atributos['UPCList']);
								$datos_tecnicos = json_encode($atributos);

								//si ya existe el producto en la base
								//vemos si su precio cambio y solo actualziamos esto
								if($this->exists($asin)){
									if($this->priceHasChanged($asin, $precio)){
										$actualizados++;
										$this->updateAmazonPrice($asin, $precio);
									}
									
								}else{
									// si no existe es un producto nuevo y se isnerta con toda su informacion
									$nuevos++;
									$this->insertProduct($producto, $precio, $asin, $link, $descripcion, $modelo, $fabricante, $img_url,$datos_tecnicos,$nodo);
								}
									
							}
							echo "<br> consola > <span style='color:green'> $nuevos productons nuevos y $actualizados productos cambiaron de precio para le nodo $nodo</span>";
						}else{
							echo "<br> consola > <span style='color:red'>No tenia items el nodo  $nodo...</span>";
						}
					}else{
						echo "<br> consola > <span style='color:red'>No se pudieron obtener los productos para el $nodo...</span>";
					}
					
				}
				
			}
		}

		/**
		 * Obtiene la seccion de nodos y conjunto de paginas de los cuales se obtendran 
		 * y cargaran los productos , es el proceso principa que se encarga de ejecutar
		 * las demas partes
		 * @return None
		 */
		public function cargarProductos(){
			// obtenemos los nodos de las secciones
			echo "<br> consola > <span style='color:blue'>Obteniendo las categorias de la base de datos ...</span>";
			$query = "SELECT * FROM dw_secciones_nodos WHERE seccion = :seccion ";
			$sentencia = $this->db->prepare($query);
			$nombre_seccion = "";
			$total_secciones = 10;
			$nodos = null;
			$conjunto_paginas = 0;
			$ultima = 0;
			// itenramos por seccion
			for($i=1; $i<=$total_secciones  ; $i++){
				$nombre_seccion = "seccion_".$i;
				$sentencia->bindValue(":seccion", $nombre_seccion);
				$sentencia->execute();
				$datos = $sentencia->fetchAll();
				// si la seccion corriente no tiene conjunto de pagians recorrido
				// o le falta el conjunto 2 (solo dos conjuntos 1 y 2)
				if($datos[0]["conjunto_paginas"] == 0 || $datos[0]["conjunto_paginas"] == 1){
					echo "<br> consola > <span style='color:blue'>Obteniendo las categorias de la $nombre_seccion</span>";
					// le sumamos 1 al conjunt corriente
					$conjunto_paginas = $datos[0]["conjunto_paginas"] + 1;
					//obtenemos los nodos de esta seccion
					$nodos = explode(",", $datos[0]["nodos"]);
					//actualizamso los datos de conjunto de pagas que se recorreran
					echo "<br> consola > <span style='color:green'>Actualizando el conjunto de paginas</span>";
					$queryPaginas = "UPDATE dw_secciones_nodos SET conjunto_paginas = :conjunto WHERE seccion = :seccion";
					$sentencia = $this->db->prepare($queryPaginas);
					$sentencia->bindValue(":seccion", $nombre_seccion);
					$sentencia->bindValue(":conjunto", $conjunto_paginas);
					$sentencia->execute();

					break;
				}
				// si llego hasta la seccion 10 la ultima, ha terminado todas las secciones
				// en sus dos conjuntos de paginas
				if($i == $total_secciones){
					// refrescamos la tabla para los procesos del dia siguiente
					$refreshQuery = "UPDATE dw_secciones_nodos SET conjunto_paginas = :conjunto , nodos = :nodos";
					$sentencia = $this->db->prepare($refreshQuery);
					$sentencia->bindValue(":conjunto", 0);
					$sentencia->bindValue(":nodos", "");
					$sentencia->execute();
					$ultima = $i;
					echo "<br> consola > <span style='color:blue'>Limpiando la tabla de las secciones</span>";
					break;
				}

			}
			// si NO es la ultima seccion en su ultimo conjunto ejecutamos el proceso de carga de producto
			if($ultima != $total_secciones AND $conjunto_paginas!=2){
				$this->getProductosByNodos($nodos,$conjunto_paginas);
			}	

		}

		/**
		 * Obtiene el precion de un producto especifico pro ASIN
		 * @param  [String] $asin [asin del producto]
		 * @return [int]       $precio
		 */
		public function getPriceAmazonApi($asin){
			$amazon = $this;
			$precio = null;
			// realizamos la peticion esta vez por ASIN y obtenemos
			// la informacion de las ofertas (precios)
			$parametros = array('Operation' => 'ItemLookup',
								'ItemId' => $asin,
								'MechantId' => 'All',
    							'Condition' => 'All',
    							'ResponseGroup' => 'OfferFull');



			$peticion = $amazon->construirPeticion("com.mx",$parametros);

			$xmlContent = @file_get_contents($peticion);
			
			if($xmlContent === FALSE){
				$precion = null;
			}else{
				$pxml = simplexml_load_string($xmlContent);
			}
			// obtenemos el item
			$item = $pxml->Items->Item;
			// si el precio mas bajo no esta
			if(isset($item->OfferSummary->LowestNewPrice->Amount)){
				$division = ($item->OfferSummary->LowestNewPrice->Amount) / 100;
				$precio = floor($division);
				//si no ontenemos e precio de lista
			}else if(isset($item->Offers->Offer->OfferListing->Price->Amount)){
				// para los dos casos usamos el precio sin formato
				// pero lo dividmos entre 100 ya que todos los precion traen dos decimales
				// sin importar que sea 00 y el api lo devuelve sin punto es decir 100 veces mas grande
				$division = ($item->Offers->Offer->OfferListing->Price->Amount) / 100;
				$precio = floor($division);
			}else{
				$precio ="Sin precion obtenido";
			}			
			// regresamso el precio
			return $precio;
		}

		public function infoAmazon (){
			$query = "SELECT COUNT(*) as cuantos FROM wp_pwgb_postmeta WHERE meta_key = :key";
			$pdo = $this->db->prepare($query);
			$pdo->bindValue(":key", 'asin');
			$pdo->execute();
			$result = $pdo->fetchAll();
			return $result[0]['cuantos'];
		}

		private function insertCategorias($nombre,$browseNodeId){
			// se insertan los terms
			$slug = $this->slugify($nombre);
			$query = "INSERT INTO wp_pwgb_terms(name,slug,term_group,browse_node_amazon) VALUES (:name,:slug,:term_group,:browse_node_amazon)";
			$pdo = $this->db->prepare($query);
			$pdo->bindValue(":name", $nombre);
			$pdo->bindValue(":slug", $slug);
			$pdo->bindValue(":term_group", 0);
			$pdo->bindValue(":browse_node_amazon", $browseNodeId);
			$pdo->execute();
			$term_id = $this->db->lastInsertId();

			$query_tax = "INSERT INTO wp_pwgb_term_taxonomy (term_id,taxonomy,description,parent,count) VALUES (:term_id,:taxonomy,:description,:parent,:count)";
			$pdox = $this->db->prepare($query_tax);
			$pdox->bindValue(":term_id", $term_id);
			$pdox->bindValue(":taxonomy", 'category');
			$pdox->bindValue(":description", '');
			$pdox->bindValue(":parent", 0);
			$pdox->bindValue(":count", 0);
			$pdox->execute();

			 
		}

		private function existCategoria($browseNodeId){
			$query = "SELECT * FROM wp_pwgb_terms WHERE browse_node_amazon=:browse_node_amazon";
			$pdo = $this->db->prepare($query);
			$pdo->bindValue(":browse_node_amazon", $browseNodeId);
			$pdo->execute();
			$response = $pdo->fetchAll(PDO::FETCH_ASSOC);
			return (count($response) > 0) ? true : false;
		}

		private function getTaxonomiId ($browseNodeId){
			$query = "SELECT term_taxonomy_id  FROM wp_pwgb_terms AS term RIGHT JOIN wp_pwgb_term_taxonomy AS tax ON tax.term_id = term.term_id WHERE term.browse_node_amazon = :browseNode";
			$pdo = $this->db->prepare($query);
			$pdo->bindValue(":browseNode", $browseNodeId);
			$pdo->execute();
			$response = $pdo->fetchAll(PDO::FETCH_ASSOC);
			return $response[0]['term_taxonomy_id'];
		}
				
	}

	if(isset($_POST['post'])){
		$post= $_POST['post'];
		$amazon = new AmazonConnection();
		switch ($post) {
			case 'obtenerCategorias':
				$amazon->seccionarNodos();
				break;
			case 'cargarProductos':
				$amazon->cargarProductos();
				break;
			case 'infoAmazon':
				echo $amazon->infoAmazon();
			break;
			default:
				header("Location: 404.php");
			break;
		}
	}

?>
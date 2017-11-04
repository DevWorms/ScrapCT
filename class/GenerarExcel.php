<?php 
	require_once __DIR__ . '/../app/DB.php';
	require_once __DIR__  . '/../libs/Excel/PHPExcel.php';
	/**
	* Clase para descargar los productos a un EXCEL
	*/
	class GenerarExcel 
	{
		private $db;

		function __construct(){
			$this->db = DB::init()->getDB();
			//PHPExcel_Shared_Font::setAutoSizeMethod(PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);
			if(ini_get('max_execution_time') < 600){
				ini_set('max_execution_time', 600);
			}
			
		}

		public function writeExcel(){
			// Create new PHPExcel object
			$objPHPExcel = new PHPExcel();

			// Set properties
		
			$objPHPExcel->getProperties()->setCreator("Teccheck System");
			$objPHPExcel->getProperties()->setLastModifiedBy("Teccheck system");
			$objPHPExcel->getProperties()->setTitle("Descarga Productos de tecnologia");
			$objPHPExcel->getProperties()->setSubject("Descarga Porductos de tecnologia");
			$objPHPExcel->getProperties()->setDescription("Descarga de los productos de tecnologias en la base de datos");
			
			// write cells product
			$this->writeDataProduct($objPHPExcel);
					
			// Save Excel 2007 file
			//date('H:i:s') 
			$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
			$descarga = 'descarga/' . "productos_teccheck_".date("Y-m-d").".xlsx";
			$destino = __DIR__ . '/../descarga/' . "productos_teccheck_".date("Y-m-d").".xlsx";
			$objWriter->save($destino);

			return $descarga;
		
		}

		private function getCategoria($post_id){
			$query = "SELECT term.name as categoria FROM wp_pwgb_terms AS term LEFT JOIN wp_pwgb_term_taxonomy AS tax ON tax.term_id = term.term_id LEFT JOIN wp_pwgb_term_relationships as rel ON tax.term_taxonomy_id = rel.term_taxonomy_id WHERE rel.object_id = :post_id";
	    	$pdo = $this->db->prepare($query);
			$pdo->bindValue(":post_id", $post_id);
			$pdo->execute();
			$response = $pdo->fetchAll(PDO::FETCH_ASSOC);
			if(isset($response[0]['categoria'])){
				return $response[0]['categoria'];
			}
			
			return 'Sin categoria';

		}

		private function writeDataProduct($objPHPExcel){
			// Add some data
			$fecha = date("d/m/Y");
			$row = 2;
			$hoja = 0;
			$productos = $this->getMaster();
			// formato de cabeceras
			$objPHPExcel->setActiveSheetIndex(0);
			$nombre_hoja = date("Ymd");
			$objPHPExcel->getActiveSheet()->setTitle($nombre_hoja);
			foreach ($productos as  $producto) {
				
				// encabezados
				$this->encabezados($objPHPExcel);
				// ajustamos el width al contenido
				foreach(range('A','Z') as $columnID) {
				    $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
				}
				// categoria y dato master
				$objPHPExcel->getActiveSheet()->SetCellValue('A'.$row, $fecha);
				$objPHPExcel->getActiveSheet()->SetCellValue('AA'.$row, $this->getCategoria($producto['post_id']));
				$objPHPExcel->getActiveSheet()->SetCellValue('D'.$row, $producto['post_nombre']);
				// detalles
				$detalles = $this->getMeta($producto['post_id']);
				foreach ($detalles as $deta) {
					switch ($deta['meta_key']) {
						case 'company':
							$objPHPExcel->getActiveSheet()->SetCellValue('B'.$row, $deta['meta_value']);
							break;
						case 'model':
							$objPHPExcel->getActiveSheet()->SetCellValue('C'.$row, $deta['meta_value']);
							break;
						case 'asin':
							$objPHPExcel->getActiveSheet()->SetCellValue('E'.$row, $deta['meta_value']);	
							break;
						case 'amazon_affiliate_link':
							$objPHPExcel->getActiveSheet()->SetCellValue('F'.$row, $deta['meta_value']);
							break;
						case 'price_best':
							$objPHPExcel->getActiveSheet()->SetCellValue('G'.$row, $deta['meta_value']);
							break;
						case 'price_amazon':
							$objPHPExcel->getActiveSheet()->SetCellValue('H'.$row, $deta['meta_value']);
							break;
						case 'price_linio':
							$objPHPExcel->getActiveSheet()->SetCellValue('I'.$row, $deta['meta_value']);
							break;
						case 'price_liverpool':
							$objPHPExcel->getActiveSheet()->SetCellValue('J'.$row, $deta['meta_value']);
							break;
						case 'price_sanborns':
							$objPHPExcel->getActiveSheet()->SetCellValue('K'.$row, $deta['meta_value']);
							break;
						case 'price_claroshop':
							$objPHPExcel->getActiveSheet()->SetCellValue('L'.$row, $deta['meta_value']);
							break;
						case 'price_sams':
							$objPHPExcel->getActiveSheet()->SetCellValue('M'.$row, $deta['meta_value']);
							break;
						case 'price_sears':
							$objPHPExcel->getActiveSheet()->SetCellValue('N'.$row, $deta['meta_value']);
							break;
						case 'price_bestbuy':
							$objPHPExcel->getActiveSheet()->SetCellValue('O'.$row, $deta['meta_value']);
							break;
						case 'price_coppel':
							$objPHPExcel->getActiveSheet()->SetCellValue('P'.$row, $deta['meta_value']);
							break;
						case 'price_cyberpuerta':
							$objPHPExcel->getActiveSheet()->SetCellValue('Q'.$row, $deta['meta_value']);
							break;
						case 'price_walmart':
							$objPHPExcel->getActiveSheet()->SetCellValue('R'.$row, $deta['meta_value']);
							break;
						case 'price_office_max':
							$objPHPExcel->getActiveSheet()->SetCellValue('S'.$row, $deta['meta_value']);
							break;
						case 'price_office_depot':
							$objPHPExcel->getActiveSheet()->SetCellValue('T'.$row, $deta['meta_value']);
							break;
						case 'price_palacio':
							$objPHPExcel->getActiveSheet()->SetCellValue('U'.$row, $deta['meta_value']);
							break;
						case 'price_soriana':
							$objPHPExcel->getActiveSheet()->SetCellValue('V'.$row, $deta['meta_value']);
							break;
						case 'price_elektra':
							$objPHPExcel->getActiveSheet()->SetCellValue('W'.$row, $deta['meta_value']);
							break;
						case 'price_sony':
							$objPHPExcel->getActiveSheet()->SetCellValue('X'.$row, $deta['meta_value']);
							break;
						case 'price_costco':
							$objPHPExcel->getActiveSheet()->SetCellValue('Y'.$row, $deta['meta_value']);
							break;
						case 'price_radioshack':
							$objPHPExcel->getActiveSheet()->SetCellValue('Z'.$row, $deta['meta_value']);
							break;
						default:
							break;
					}
				}

				
				$row++;
				
			}

		}

		private function encabezados($objPHPExcel){
			$style = array( 'font' => array('size' => 14,'bold' => true) );
			$objPHPExcel->getActiveSheet()->SetCellValue('AA1','Categoria');
			$objPHPExcel->getActiveSheet()->SetCellValue('A1','Fecha');
			$objPHPExcel->getActiveSheet()->SetCellValue('B1','Fabricante');
			$objPHPExcel->getActiveSheet()->SetCellValue('C1','Modelo');
			$objPHPExcel->getActiveSheet()->SetCellValue('D1','Nombre');
			$objPHPExcel->getActiveSheet()->SetCellValue('E1','ASIN');
			$objPHPExcel->getActiveSheet()->SetCellValue('F1','URL en Teccheck');
			$objPHPExcel->getActiveSheet()->SetCellValue('G1','Mejor precio');
			$objPHPExcel->getActiveSheet()->SetCellValue('H1','Precio Amazon');
			$objPHPExcel->getActiveSheet()->SetCellValue('I1','Precio Linio');
			$objPHPExcel->getActiveSheet()->SetCellValue('J1','Precio Liverpool');
			$objPHPExcel->getActiveSheet()->SetCellValue('K1','Precio Sanborns');
			$objPHPExcel->getActiveSheet()->SetCellValue('L1','Precio Claroshop');
			$objPHPExcel->getActiveSheet()->SetCellValue('M1','Precio SamsClub');
			$objPHPExcel->getActiveSheet()->SetCellValue('N1','Precio Sears');
			$objPHPExcel->getActiveSheet()->SetCellValue('O1','Precio BestBuy');
			$objPHPExcel->getActiveSheet()->SetCellValue('P1','Precio Coppel');
			$objPHPExcel->getActiveSheet()->SetCellValue('Q1','Precio Cyberpuerta');
			$objPHPExcel->getActiveSheet()->SetCellValue('R1','Precio Walmart');
			$objPHPExcel->getActiveSheet()->SetCellValue('S1','Precio OfficeMax');
			$objPHPExcel->getActiveSheet()->SetCellValue('T1','Precio OfficeDepot');
			$objPHPExcel->getActiveSheet()->SetCellValue('U1','Precio Palacio');
			$objPHPExcel->getActiveSheet()->SetCellValue('V1','Precio Soriana');
			$objPHPExcel->getActiveSheet()->SetCellValue('W1','Precio Eelektra');
			$objPHPExcel->getActiveSheet()->SetCellValue('X1','Precio Sony');
			$objPHPExcel->getActiveSheet()->SetCellValue('Y1','Precio Costco');
			$objPHPExcel->getActiveSheet()->SetCellValue('Z1','Precio RadioShack');
			$objPHPExcel->getActiveSheet()->getStyle('A1:AA1')->applyFromArray($style);
			
		}
		private function getMaster(){

			$query = "SELECT post.ID as post_id, post.post_title as post_nombre FROM wp_pwgb_posts AS post INNER JOIN wp_pwgb_postmeta AS meta ON meta.post_id = post.ID WHERE meta.meta_key = 'asin' GROUP BY meta.post_id";

			$pdo = $this->db->prepare($query);
			$pdo->execute();
			$response = $pdo->fetchAll(PDO::FETCH_ASSOC);

			return $response;

		}

		private function getMeta($post_id){
		
			$query = "SELECT meta_key,meta_value FROM wp_pwgb_postmeta WHERE  post_id = :post_id";

			$pdo = $this->db->prepare($query);
			$pdo->bindParam(':post_id', $post_id);
			$pdo->execute();

			$response = $pdo->fetchAll(PDO::FETCH_ASSOC);

			return $response;
		}
	}

	if(isset($_POST['post'])){
		$post= $_POST['post'];
		$excel = new GenerarExcel();
		switch ($post) {
			case 'genera_excel':
				echo $excel->writeExcel();
				break;
			default:
				echo "No function";
		}
	}
	
	
 ?>
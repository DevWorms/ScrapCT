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

			// ponemos las cabeceras
			$objPHPExcel->getActiveSheet()->SetCellValue('A1','Categoria');
			$objPHPExcel->getActiveSheet()->SetCellValue('B1','Nombre');
			$objPHPExcel->getActiveSheet()->SetCellValue('C1','ASIN');
			$objPHPExcel->getActiveSheet()->SetCellValue('D1','Modelo');
			$objPHPExcel->getActiveSheet()->SetCellValue('E1','URL afiliado');
			$objPHPExcel->getActiveSheet()->SetCellValue('F1','Fabricante');
			$objPHPExcel->getActiveSheet()->SetCellValue('G1','Precio amazon');
			$objPHPExcel->getActiveSheet()->SetCellValue('H1','URL imagen');
			$objPHPExcel->getActiveSheet()->SetCellValue('I1','Esp tecnica');
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
			$row = 2;
			$hoja = 0;
			$productos = $this->getMaster();
			foreach ($productos as  $producto) {
				$objPHPExcel->setActiveSheetIndex($hoja);
				// categoria y dato master
				$objPHPExcel->getActiveSheet()->SetCellValue('A'.$row, $this->getCategoria($producto['post_id']));
				$objPHPExcel->getActiveSheet()->SetCellValue('B'.$row, $producto['post_nombre']);
				// detalles
				$detalles = $this->getMeta($producto['post_id']);
				foreach ($detalles as $deta) {
					switch ($deta['meta_key']) {
						case 'asin':
							$objPHPExcel->getActiveSheet()->SetCellValue('C'.$row, $deta['meta_value']);	
							break;
						case 'model':
							$objPHPExcel->getActiveSheet()->SetCellValue('D'.$row, $deta['meta_value']);
							break;
						case 'amazon_affiliate_link':
							$objPHPExcel->getActiveSheet()->SetCellValue('E'.$row, $deta['meta_value']);
							break;
						case 'company':
							$objPHPExcel->getActiveSheet()->SetCellValue('F'.$row, $deta['meta_value']);
							break;
						case 'price_amazon':
							$objPHPExcel->getActiveSheet()->SetCellValue('G'.$row, $deta['meta_value']);
							break;
						case 'picture':
							$objPHPExcel->getActiveSheet()->SetCellValue('H'.$row, $deta['meta_value']);
							break;
						case 'esp_tecnica':
							$objPHPExcel->getActiveSheet()->SetCellValue('I'.$row, $deta['meta_value']);
							break;
						default:
							break;
					}
				}

				$objPHPExcel->getActiveSheet()->setTitle('pro_'.$hoja);
				$row++;
				if($row >1002){
					$row = 0;
					$hoja++;
					$objPHPExcel->createSheet();
				}
			}	
		}

		private function getMaster(){

			$query = "SELECT post.ID as post_id, post.post_title as post_nombre FROM wp_pwgb_posts AS post INNER JOIN wp_pwgb_postmeta AS meta ON meta.post_id = post.ID WHERE meta.meta_key = 'asin' GROUP BY meta.post_id";

			$pdo = $this->db->prepare($query);
			$pdo->execute();
			$response = $pdo->fetchAll(PDO::FETCH_ASSOC);

			return $response;

		}

		private function getMeta($post_id){
		
			$query = "SELECT meta_key,meta_value FROM wp_pwgb_postmeta where meta_key in('asin','model','amazon_affiliate_link','company','price_amazon','picture','esp_tecnica') AND post_id = :post_id";

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
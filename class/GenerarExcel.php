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
			echo " ";
			$objPHPExcel = new PHPExcel();

			// Set properties
			echo date('H:i:s') . " Set properties\n";
			$objPHPExcel->getProperties()->setCreator("Teccheck System");
			$objPHPExcel->getProperties()->setLastModifiedBy("Teccheck system");
			$objPHPExcel->getProperties()->setTitle("Descarga Productos de tecnologia");
			$objPHPExcel->getProperties()->setSubject("Descarga Porductos de tecnologia");
			$objPHPExcel->getProperties()->setDescription("Descarga de los productos de tecnologias en la base de datos");


			// Add some data

			$objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'x');
			$objPHPExcel->getActiveSheet()->SetCellValue('B2', 'x!');
			$objPHPExcel->getActiveSheet()->SetCellValue('C1', 'x');
			$objPHPExcel->getActiveSheet()->SetCellValue('D2', 'x!');

			// Rename sheet

			$objPHPExcel->getActiveSheet()->setTitle('TecCheck');

					
			// Save Excel 2007 file
			//date('H:i:s') 
			$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
			$destino = __DIR__ . '/../descarga/' . "productos_teccheck_".date("Y-m-d").".xlsx";
			echo $destino ."<br>";
			$objWriter->save($destino);

			// Echo done
			echo date('H:i:s') . " Done writing file.\r\n";
		}

		private function getData($excel){
			
		}
	}

	$excel = new GenerarExcel();
	$excel->writeExcel();
 ?>
<?php 
	require_once __DIR__ . '/app/DB.php';
	require_once __DIR__ . '/class/AmazonConnection.php';
	$amazon = new AmazonConnection();
	$db = DB::init()->getDB();
	$query = "SELECT p.ID,m.meta_value FROM wp_pwgb_posts as p 
			INNER JOIN wp_pwgb_postmeta as m ON p.ID = m.post_id 
			WHERE m.meta_key = 'asin'  LIMIT 0,278";

	$pdo = $db->prepare($query);
    $pdo->execute();
    $response = $pdo->fetchAll(PDO::FETCH_ASSOC);

    foreach ($response as $post) {
    	$precio = $amazon->getPriceAmazonApi($post['meta_value']);
    	if(!is_numeric($precio)){
    		$precio="No disponible";
    	}
    	// algo hace el scrapping que tira el precio de amazon, debemos quitar la obtencion del precio de amazon en el scraping , esta parte de update no se hara solo es para corregir lo que pide max
    	echo "UPDATE wp_pwgb_postmeta SET meta_value = '". $precio . "' WHERE post_id = " . $post['ID'] ." AND meta_key = 'price_amazon'"."<br>";

    	// ESTO SI HAY QUE IMPLEMENTARLO
    	// actualizar mejor precioe y cambiar lo del mejor precio que se tiene  a lo que pondre abajo obvio ahi si se ejcutarian los querys aqui los imprimo 
    	$queryPrecios = "SELECT post_id,meta_key,meta_value FROM wp_pwgb_postmeta WHERE meta_key 
    	in('price_amazon','price_sanborns','price_linio','price_claroshop','price_coppel','price_sears','price_bestbuy','price_walmart','price_liverpool','price_office_max','price_office_depot','price_palacio','price_soriana','price_elektra','price_sony','price_costco','price_radioshack')
    		 AND post_id = :id";
	    $pdo = $db->prepare($queryPrecios);
	    $pdo->bindParam(":id",$post['ID']);
	    $pdo->execute();
	    $precios = $pdo->fetchAll(PDO::FETCH_ASSOC);

		$mejor_precio = 0;
		$price_shop = '';


	    foreach ($precios as $precio) {
	    	$precio['meta_value'] = str_replace(',','',$precio['meta_value']);
	    	$precio['meta_value'] = str_replace('$','',$precio['meta_value']);
	    	$precio['meta_value'] = explode(".", $precio['meta_value'] )[0];
	    	if(is_numeric($precio['meta_value'])){
	    		$mejor_precio =  $precio['meta_value'];
	    		break;
	    	}
	    }
	    foreach ($precios as $precio) {
	    	$precio['meta_value'] = str_replace(',','',$precio['meta_value']);
	    	$precio['meta_value'] = str_replace('$','',$precio['meta_value']);
	    	$precio['meta_value'] = explode(".", $precio['meta_value'] )[0];

	    	if(is_numeric($precio['meta_value'])){

	    		if($precio['meta_value'] <= $mejor_precio ){
	    			$mejor_precio = $precio['meta_value']; 
	    			$price_shop = $precio['meta_key'];
	    		}
	    		
	    	}
	    }

	    
	   

	    
	    echo "UPDATE wp_pwgb_postmeta SET meta_value = '". $mejor_precio . "' WHERE post_id = " . $post['ID'] ." AND meta_key = 'price_best'"."<br>";

	    $shop = '';
	    switch ($price_shop) {
	    	case 'price_amazon':
                	$shop = "Amazon";
                    break;
                case 'price_sanborns':
                	$shop = "Sanborns";
                    break;
                case 'price_linio':
                	$shop = "Linio";
                    break;
                case 'price_claroshop':
                	$shop = "Claroshop";
                    break;
                case 'price_coppel':
                	$shop = "Coppel";
                    break;
                case 'price_sears':
                	$shop = "Sears";
                    break;
                case 'price_bestbuy':
                  	$shop = "Bestbuy";
                    break;
                case 'price_walmart':
                    $shop = "Walmart";
                    break;
                case 'price_liverpool':
                    $shop = "Liverpool";
                    break;
                case 'price_office_max':
                   	$shop = "Officemax";
                    break;
                case 'price_office_depot':
                    $shop = "Officedepot";
                    break;
                case 'price_palacio':
                	$shop = "Palacio";
                    break;
                case 'price_soriana':
    				$shop = "Soriana";
                    break;
                case 'price_elektra':
                    $shop = "Elektra";
                    break;
                case 'price_sony':
                    $shop = "Sony";
                    break;
                case 'price_costco':
                    $shop = "Costco";
                    break;
                case 'price_radioshack':
                    $shop = "Radioshack";
                    break;
            }

        echo "UPDATE wp_pwgb_postmeta SET meta_value = '". $shop . "' WHERE post_id = " . $post['ID'] ." AND meta_key = 'best_shop'"."<br><br>";
    }

 ?>
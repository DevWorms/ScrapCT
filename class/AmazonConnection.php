<?php
    require_once __DIR__ . '/../app/DB.php';

	/**
	* Clase para la conexion con el api de Amazon
	*/
	class AmazonConnection
	{
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
	}

	/*
	$a = new AmazonConnection();
	$a->updateAmazonPrice("123", "999");
	*/

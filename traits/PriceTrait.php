<?php
/**
 * Created by PhpStorm.
 * User: rk521
 * Date: 22/10/17
 * Time: 01:36 PM
 */

trait PriceTrait
{
    public function cleanPrice($price) {
        // Remueve decimales
        if (strpos($price, ".") !== false) {
            $price = substr($price, 0, strpos($price, "."));
        }

        // Remueve simbolo de pesos
        $price = str_replace("$", "", $price);

        // Remueve el simbolo de coma
        $price = str_replace(",", "", $price);

        // Remueve caracteres NO numéricos
        $price = preg_replace("/[^0-9,.]/", "", $price);

        // Elimina espacios
        return trim($price);
    }

    public function specialCostcoPrice($price) {
        foreach (preg_split("/((\r?\n)|(\r\n?))/", $price) as $line) {
            if (strpos($line, "Precio Final") !== false) {
                return $this->cleanPrice($line);
            }
        }

        return $this->cleanPrice($price);
    }
}
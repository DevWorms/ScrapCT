<?php
/**
 * Created by PhpStorm.
 * User: rk521
 * Date: 22/10/17
 * Time: 03:32 PM
 */

trait SchemaTrait
{
    public function productsLink() {
        return [
            'sanborns_pl',
            'linio_pl',
            'amazon_pl',
            'claroshop_pl',
            'coppel_pl',
            'sears_pl',
            'sams_pl',
            'bestbuy_pl',
            'walmart_pl',
            'amazon_affiliate_link',
            'linio_affiliate_link',

            'liverpool_pl',
            'office_max_pl',
            'office_depot_pl',
            'palacio_pl',
            'soriana_pl',
            'elektra_pl',
            'sony_pl',
            'costco_pl',
            'radioshack_pl',
        ];
    }

    public function productPrice() {
        return [
            'price_cyberpuerta',
            'price_walmart',
            'price_bestbuy',
            'price_sears',
            'price_sams',
            'price_coppel',
            'price_claroshop',
            'price_sanborns',
            'price_linio',
            'price_amazon',

            'price_liverpool',
            'price_office_max',
            'price_office_depot',
            'price_palacio',
            'price_soriana',
            'price_elektra',
            'price_sony',
            'price_costco',
            'price_radioshack',
        ];
    }

    public function shops() {
        return [
            'Radioshack' => 'https://www.radioshack.com.mx/store/radioshack/en/search/?category=0-0-0-0&text=',
            'Cyberpuerta' => 'https://www.cyberpuerta.mx/',
            'Walmart' => 'https://www.walmart.com.mx/',
            'Bestbuy' => 'http://www.bestbuy.com.mx/',
            'Sears' => 'http://www.sears.com.mx/',
            'Sams' => 'https://www.sams.com.mx/',
            'Coppel' => 'http://www.coppel.com/',
            'Claroshop' => 'http://wwvv.claroshop.com/',
            'Liverpool' => 'https://www.liverpool.com.mx/tienda/',
            'Sanborns' => 'https://www.sanborns.com.mx/Paginas/',
            'Office Max' => 'http://www.officemax.com.mx/',
            'Office Depot' => 'https://www.officedepot.com.mx/',
            'Palacio de Hierro' => 'https://www.elpalaciodehierro.com/',
            'Soriana' => 'https://www.soriana.com/',
            'Elektra' => 'https://www.elektra.com.mx/',
            'Sony' => 'https://store.sony.com.mx/',
            'Costco' => 'http://www.costco.com.mx/',
        ];
    }
}
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
            'price_radioshack',
            'price_cyberpuerta',
            'price_walmart',
            'price_bestbuy',
            'price_sears',
            'price_sams',
            'price_coppel',
            'price_claroshop',
            'price_liverpool',
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
}
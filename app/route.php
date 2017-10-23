<?php

/**
 * Created by PhpStorm.
 * User: @HackeaMesta
 * Date: 16.01.17
 * Time: 00:30
 */
class Route {
    /**
     * Arreglo de Rutas permitidas
     *
     * @var array $_listUri
     */
    private $_listUri = array();

    /**
     * Arreglo de callbacks (En este caso archivos a los que apunta)
     *
     * @var array $_listCall
     */
    private $_listCall = array();

    /**
     * wide to clean
     *
     * @var string $_trim
     */
    private $_trim = '/\^$';

    /**
     * Agrega nuevas rutas permitidas
     *
     * @param string $uri
     * @param object $function Callback (Archivo en este caso)
     */
    public function add($uri, $function) {
        $uri = trim($uri, $this->_trim);
        $this->_listUri[] = $uri;
        $this->_listCall[] = $function;
    }

    /**
     * submit Map routing
     */
    public function submit() {
        $uri = isset($_REQUEST['uri']) ? $_REQUEST['uri'] : '/';
        $uri = trim($uri, $this->_trim);

        $replacementValues = array();

        /**
         * Lista las uris
         */
        foreach ($this->_listUri as $listKey => $listUri) {
            /**
             * Si la uri actual es valida
             */
            if (preg_match("#^$listUri$#", $uri)) {
                /**
                 * Reemplaza valores
                 * Beta
                 */
                $realUri = explode('/', $uri);
                $fakeUri = explode('/', $listUri);

                /**
                 * Reemplaza .* por valor
                 * Beta
                 */
                foreach ($fakeUri as $key => $value) {
                    if ($value == '.+') {
                        $replacementValues[] = $realUri[$key];
                    }
                }

                /**
                 * Ejecuta callback
                 */
                //call_user_func_array($this->_listCall[$listKey], $replacementValues);
                // Incluye el archivo
                ob_start();
                include(dirname(__FILE__) . "/../" . $this->_listCall[$listKey]);
            }

        }

    }
}
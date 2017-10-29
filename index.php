<?php

include 'app/route.php';

$route = new Route();

$route->add('/', 'login.php');
$route->add('/error', '404.php');
$route->add('/tiendas', 'home.php');
$route->add('/usuarios', 'usuarios.php');
$route->add('/amazon', 'amazon.php');

$route->submit();

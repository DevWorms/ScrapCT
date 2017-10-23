<?php

include 'app/route.php';

$route = new Route();

$route->add('/', 'login.php');
$route->add('/404', '404.php');
$route->add('/home', 'home.php');



$route->submit();

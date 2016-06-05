<?php
/**
 * Created by PhpStorm.
 * User: szabacsik
 * Date: 2016. 06. 05.
 * Time: 15:30
 */

require_once ('inc/interfaces.php');
require_once ('inc/route.php');

use improwerk\implement\mvcd as mvcd;

$route = new mvcd\route();


ob_start();
var_dump($route->getRoute()) ;
$dump = ob_get_contents();
ob_end_clean();
echo "<pre> $dump </pre>";

ob_start();
var_dump($route->getDomain()) ;
$dump = ob_get_contents();
ob_end_clean();
echo "<pre> $dump </pre>";

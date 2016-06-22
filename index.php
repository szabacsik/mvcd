<?php
/**
 * Created by PhpStorm.
 * User: szabacsik
 * Date: 2016. 06. 05.
 * Time: 15:30
 */

require_once('core/interfaces.php');
require_once('core/filesystem.php');
require_once('core/route.php');
require_once('core/dispatcher.php');

use improwerk\implement\mvcd as mvcd;

$filesystem = new mvcd\filesystem ();
$route = new mvcd\route ( $filesystem );
$dispatcher = new mvcd\dispatcher ( $route );

print ($filesystem->working_directory."<br>");
print ($route -> url . "<br>");

ob_start();
var_dump ( $route -> sense );
$dump = ob_get_contents ();
ob_end_clean ();
echo "<pre> $dump </pre>";


/*ob_start();
var_dump($route->sense);
$dump = ob_get_contents();
ob_end_clean();
echo "<pre> $dump </pre>";*/

/*echo "<hr>";
ob_start();
var_dump($_SERVER);
$dump = ob_get_contents();
ob_end_clean();
echo "<pre> $dump </pre>";
echo "<hr>";
ob_start();
var_dump($_REQUEST);
$dump = ob_get_contents();
ob_end_clean();
echo "<pre> $dump </pre>";*/

<?php
$startScriptTime=microtime(TRUE);
/**
 * Created by PhpStorm.
 * User: szabacsik
 * Date: 2016. 06. 05.
 * Time: 15:30
 */

require_once('core/constants.php');
require_once('core/interfaces.php');
require_once('core/configuration.php');
require_once('core/filesystem.php');
require_once('core/route.php');
require_once('core/dispatcher.php');

use improwerk\implement\mvcd as mvcd;

$configuration = new mvcd\configuration ();
$filesystem = new mvcd\filesystem ();
$route = new mvcd\route ( $configuration, $filesystem );
$dispatcher = new mvcd\dispatcher ( $configuration, $filesystem, $route );

//$filesystem -> debug ();
//$route -> debug ();
$endScriptTime=microtime(TRUE);
$totalScriptTime=$endScriptTime-$startScriptTime;
echo '<br><br><hr>Load time: '.number_format($totalScriptTime, 4).' seconds<hr>';
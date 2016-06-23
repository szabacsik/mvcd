<?php
/**
 * Created by PhpStorm.
 * User: andra
 * Date: 2016. 06. 20.
 * Time: 14:47
 */

namespace improwerk\implement\mvcd;


class configuration implements ibasic
{
    public $subdomains_relative_folder = 'subdomains';
    public $controllers_relative_folder = 'controllers';
    public $security_private_controller_enabled = false;
    public $default_controller = 'front.php';
    public function __construct()
    {
    }

    public function __destruct()
    {
        // TODO: Implement __destruct() method.
    }
}
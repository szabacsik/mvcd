<?php
/**
 * Created by PhpStorm.
 * User: szabacsik
 * Date: 2016. 06. 05.
 * Time: 15:31
 */

namespace improwerk\implement\mvcd;

interface ibasic
{
    public function __construct ();
    public function __destruct ();
}


interface iadvanced
{
    public function __construct ( $object );
    public function __destruct ();
}

interface interface_controller
{
    public function __construct ( $route );
    public function __destruct();
}

interface interface_dispatcher
{
    public function __construct ( $configuration, $filesystem, $route );
    public function __destruct();
}

interface interface_route
{
    public function __construct ( $configuration, $filesystem );
    public function __destruct();
}

interface interface_routing_point
{
    public function __construct ();
    public function __destruct ();
    public function is_common_application ();
    public function is_private_application ();
    public function is_subdomain ();
}
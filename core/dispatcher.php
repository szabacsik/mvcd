<?php
/**
 * Created by PhpStorm.
 * User: szabacsik
 * Date: 2016. 06. 05.
 * Time: 15:39
 */

namespace improwerk\implement\mvcd;


class dispatcher implements interface_dispatcher
{
    private $route;
    private $configuration;
    private $filesystem;
    private $controller_instance;

    public function __construct ( $configuration, $filesystem, $route )
    {

        $this -> configuration = $configuration;
        $this -> filesystem = $filesystem;
        $this -> route = $route;

        if ( !$this -> route -> get_application () )
            $this -> route -> set_application ( $this -> configuration -> core [ "default_application" ], $this -> filesystem -> getcwd () . "/" . $this -> configuration -> filesystem [ "relative_folders" ] [ "common_applications" ] . "/" . $this -> configuration -> core [ "default_application" ] );

        if ( !$this -> route -> get_controller () )
                $this -> route -> set_controller ( $this -> configuration -> core [ "default_controller" ], $this -> route -> get_application_filepath () . "/" . $this -> configuration -> core [ "default_controller" ] . ".php" );

        $this -> delegate ();

    }

    private function delegate ()
    {
        $classname = $this -> configuration -> core [ "namespace" ] . $this -> route -> get_controller_name ();
        require_once ( $this -> route -> get_controller_filepath () );
        $this -> controller_instance = new $classname ( $this -> route );
    }

    public function __destruct()
    {
        // TODO: Implement __destruct() method.
    }
}
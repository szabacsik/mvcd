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

    private $controller_class_name = "";
    private $controller_class_path = "";
    private $controller_instance = false;

    private $application_data = '';
    private $controller_data = '';
    private $application_model = false;
    private $application_view = false;
    private $application_controller = false;

    public function __construct ( $configuration, $filesystem, $route )
    {

        $this -> configuration = $configuration;
        $this -> filesystem = $filesystem;
        $this -> route = $route;
        $this -> controller_class_name = $this -> configuration -> core [ "default_controller" ];
        $this -> controller_class_path = $this -> filesystem -> getcwd () . "/" . $this -> configuration -> filesystem [ "relative_folders" ] [ "controllers" ] . "/" . $this -> controller_class_name . ".php";

        $this -> route -> debug ();

        $this -> route -> set_application ( "Default", "path", "folder", "common" );

        print ("<br><hr><hr><br>");

        $this -> route -> debug ();

/*
        var_dump ( $this -> route -> get_controller () );
        print ("<hr>");
        var_dump ( $this -> route -> get_application () );
        print ("<hr>");
        var_dump ( $this -> route -> get_subdomain () );

        if ( is_array ( $this -> route -> get_controller () ) )
        {
            $this -> application_data = $this -> route -> get_application ();
            $this -> controller_data = $this -> route -> get_controller ();
        }
        else
        {
            if ( is_array ( $this -> route -> get_application () ) )
            {
                //$this -> route -> set_controller ();
            }
        }*/

/*        if ( $this -> route -> get_route () )
        {
            foreach ( $this -> route -> get_route () as $pathindex => $pathitem )
            {
                if ( $pathitem [ "type" ] == "controller" && $pathitem [ "properties" ][ "mode" ] == "file" )
                {
                    if ( $pathitem [ "properties" ][ "owner" ] == "common" )
                    {
                        $this -> controller_class_name = $pathitem [ "name" ];
                        $this -> controller_class_path = $pathitem [ "filepath" ];
                        break;
                    }
                    elseif ( $pathitem [ "properties" ][ "owner" ] == "private" && $this -> configuration -> security [ "private_controllers" ] == enabled )
                        {
                            //TODO: If private controllers are enabled, delegate
                        }
                }
            }
        }*/

        $this -> delegate ();

    }

    private function delegate ()
    {
        //$classname = $this -> configuration -> core [ "namespace" ] . $this -> controller_class_name;
        //require_once ( $this -> controller_class_path );
        //$this -> controller_instance = new $classname ( $this -> route );
    }

    public function __destruct()
    {
        // TODO: Implement __destruct() method.
    }
}
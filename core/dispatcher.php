<?php
/**
 * Created by PhpStorm.
 * User: szabacsik
 * Date: 2016. 06. 05.
 * Time: 15:39
 */

namespace improwerk\implement\mvcd;


class dispatcher implements iadvanced
{
    private $route;

    private $engine_namespace = "";
    private $controller_class_name = "";
    private $controller_class_path = "";
    private $controller_instance = false;

    public function __construct ( $route )
    {

        $this -> route = $route;
        $this -> engine_namespace = 'improwerk\implement\mvcd\\';
        $this -> controller_class_name = 'front';
        $this -> controller_class_path = $this -> route -> filesystem -> getcwd () . "/controllers/" . $this -> controller_class_name . ".php";

        if ( $this -> route -> getRoute () )
        {
            foreach ( $this -> route -> getRoute () as $pathindex => $pathitem )
            {
                if ( $pathitem [ "type" ] == "controller" && $pathitem [ "properties" ][ "mode" ] == "file" )
                {
                    if ( $pathitem [ "properties" ][ "owner" ] == "common" )
                    {
                        $this -> controller_class_name = $pathitem [ "name" ];
                        $this -> controller_class_path = $pathitem [ "filepath" ];
                        break;
                    }
                    elseif ( $pathitem [ "properties" ][ "owner" ] == "private" )
                        {
                            //TODO: If private controllers are enabled, delegate
                        }
                }
            }
        }

        $this -> delegate ();

    }

    private function delegate ()
    {
        $classname = $this -> engine_namespace . $this -> controller_class_name;
        require_once ( $this -> controller_class_path );
        $this -> controller_instance = new $classname ( $this -> route );
    }

    public function __destruct()
    {
        // TODO: Implement __destruct() method.
    }
}
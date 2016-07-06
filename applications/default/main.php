<?php
/**
 * Created by PhpStorm.
 * User: andra
 * Date: 2016. 07. 05.
 * Time: 18:41
 */

namespace improwerk\implement\mvcd;


class main implements interface_controller
{

    public function __construct ( $route )
    {
        echo ("<br><br><hr><br><strong>Hello World! This is the main controller of the default application.</strong><br><br>");
        echo ("You are here:<br>");
        foreach ( $route -> get_route () as $key => $item )
        {
            //if ( $route -> get_subdomain () )
            echo ( "(". $item [ "type" ] . ") <strong>" . $item [ "name" ] . "</strong> url: " . $item [ "url" ] . "<br>" );
        }
        echo ("<br><br><hr><br><br>");
    }

    public function __destruct()
    {
        // TODO: Implement __destruct() method.
    }

}
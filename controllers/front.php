<?php
/**
 * Created by PhpStorm.
 * User: andra
 * Date: 2016. 06. 23.
 * Time: 16:34
 */

namespace improwerk\implement\mvcd;


class front implements interface_controller
{
    private $route;
    public function __construct ( $route )
    {
        $this -> route = $route;
        Print ( "This is the default controller.<br>You are here: home" );
        foreach ( $this -> route -> getRoute() as $pathitem )
        {
            Print ( " | " . $pathitem [ "name" ] );
        }
        Print ("<br>");
        Print ("The primary domain is: " . $this -> route -> getDomain () );
        Print ("<br>");
        Print ("Subdomain: " );
        $subdomains = $this -> route -> getSubdomains ();
        if ( $subdomains )
        {
            foreach ( $subdomains as $subdomain )
            {
                Print ( "/" . $subdomain [ "name" ] );
            }
        }
        else
        {
            Print ( "none" );
        }

        Print ("<br>");
        Print ("Parameters: " );
        $parameters = $this -> route -> getParameters ();
        if ( $parameters )
        {
            foreach ( $parameters as $parameter )
            {
                Print ( "/" . $parameter [ "name" ] );
            }
        }
        else
        {
            Print ( "none" );
        }
    }

    public function __destruct ()
    {
    }
}

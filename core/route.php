<?php
/**
 * Created by PhpStorm.
 * User: szabacsik
 * Date: 2016. 06. 05.
 * Time: 15:37
 */

namespace improwerk\implement\mvcd;


class route implements interface_route
{
    //$_SERVER
    private $DOCUMENT_ROOT;
    private $GATEWAY_INTERFACE;
    private $HTTP_ACCEPT;
    private $HTTP_ACCEPT_ENCODING;
    private $HTTP_ACCEPT_LANGUAGE;
    private $HTTP_CACHE_CONTROL;
    private $HTTP_CONNECTION;
    private $HTTP_DNT;
    private $HTTP_HOST;
    private $HTTP_UPGRADE_INSECURE_REQUESTS;
    private $HTTP_USER_AGENT;
    private $PATH;
    private $QUERY_STRING;
    private $REDIRECT_STATUS;
    private $REMOTE_ADDR;
    private $REMOTE_PORT;
    private $REQUEST_METHOD;
    private $REQUEST_URI;
    private $SCRIPT_FILENAME;
    private $SCRIPT_NAME;
    private $SERVER_ADDR;
    private $SERVER_ADMIN;
    private $SERVER_NAME;
    private $SERVER_PORT;
    private $SERVER_PROTOCOL;
    private $SERVER_SIGNATURE;
    private $SERVER_SOFTWARE;
    private $PHP_SELF;
    private $REQUEST_TIME;
    private $argv;
    private $argc;
    //$_GET, $_POST, $_COOKIE
    private $REQUEST;

    public $sense = array ( "domain" => "", "base" => "", "pathitem" => array (), "variables" => array (), "anchor" => "", "protocol" => "", "port" => "" );
    public $url = "";
    private $configuration;
    private $filesystem;

    function __construct ( $configuration, $filesystem )
    {
        $this->configuration = $configuration;
        $this->filesystem = $filesystem;
        $this->initStatic();
        $this->initDynamic();
        //$this->processRoute ();
        $this->reverse_process ();
    }

    private function initStatic ()
    {
        $this -> HTTP_HOST                      = $_SERVER [ 'HTTP_HOST' ];
        $this -> SERVER_NAME                    = $_SERVER [ 'SERVER_NAME' ];
        $this -> REQUEST_URI                    = $_SERVER [ 'REQUEST_URI' ];
        $this -> SCRIPT_NAME                    = $_SERVER [ 'SCRIPT_NAME' ];
        $this -> SCRIPT_FILENAME                = $_SERVER [ 'SCRIPT_FILENAME' ];
        $this -> DOCUMENT_ROOT                  = $_SERVER [ 'DOCUMENT_ROOT' ];
        $this -> GATEWAY_INTERFACE              = $_SERVER [ 'GATEWAY_INTERFACE' ];
        $this -> HTTP_ACCEPT                    = $_SERVER [ 'HTTP_ACCEPT' ];
        $this -> HTTP_ACCEPT_ENCODING           = $_SERVER [ 'HTTP_ACCEPT_ENCODING' ];
        $this -> HTTP_ACCEPT_LANGUAGE           = $_SERVER [ 'HTTP_ACCEPT_LANGUAGE' ];
        $this -> HTTP_CACHE_CONTROL             = $_SERVER [ 'HTTP_CACHE_CONTROL' ];
        $this -> HTTP_CONNECTION                = $_SERVER [ 'HTTP_CONNECTION' ];
        $this -> HTTP_DNT                       = $_SERVER [ 'HTTP_DNT' ];
        $this -> HTTP_HOST                      = $_SERVER [ 'HTTP_HOST' ];
        $this -> HTTP_UPGRADE_INSECURE_REQUESTS = $_SERVER [ 'HTTP_UPGRADE_INSECURE_REQUESTS' ];
        $this -> HTTP_USER_AGENT                = $_SERVER [ 'HTTP_USER_AGENT' ];
        $this -> PATH                           = $_SERVER [ 'PATH' ];
        $this -> QUERY_STRING                   = $_SERVER [ 'QUERY_STRING' ];
        $this -> REDIRECT_STATUS                = $_SERVER [ 'REDIRECT_STATUS' ];
        $this -> REMOTE_ADDR                    = $_SERVER [ 'REMOTE_ADDR' ];
        $this -> REMOTE_PORT                    = $_SERVER [ 'REMOTE_PORT' ];
        $this -> REQUEST_METHOD                 = $_SERVER [ 'REQUEST_METHOD' ];
        $this -> REQUEST_URI                    = $_SERVER [ 'REQUEST_URI' ];
        $this -> SCRIPT_FILENAME                = $_SERVER [ 'SCRIPT_FILENAME' ];
        $this -> SCRIPT_NAME                    = $_SERVER [ 'SCRIPT_NAME' ];
        $this -> SERVER_ADDR                    = $_SERVER [ 'SERVER_ADDR' ];
        $this -> SERVER_ADMIN                   = $_SERVER [ 'SERVER_ADMIN' ];
        $this -> SERVER_NAME                    = $_SERVER [ 'SERVER_NAME' ];
        $this -> SERVER_PORT                    = $_SERVER [ 'SERVER_PORT' ];
        $this -> SERVER_PROTOCOL                = $_SERVER [ 'SERVER_PROTOCOL' ];
        $this -> SERVER_SIGNATURE               = $_SERVER [ 'SERVER_SIGNATURE' ];
        $this -> SERVER_SOFTWARE                = $_SERVER [ 'SERVER_SOFTWARE' ];
        $this -> PHP_SELF                       = $_SERVER [ 'PHP_SELF' ];
        $this -> REQUEST_TIME                   = $_SERVER [ 'REQUEST_TIME' ];
        $this -> argv                           = $_SERVER [ 'argv' ];
        $this -> argc                           = $_SERVER [ 'argc' ];
        $this -> REQUEST                        = $_REQUEST;

        $this -> sense [ "variables" ]   = $this -> REQUEST;
        $this -> sense [ "port" ]        = $this -> SERVER_PORT;
        $this -> sense [ "protocol" ]    = $this -> SERVER_PROTOCOL;
    }

    private function initDynamic ()
    {
        $this -> url = $this -> HTTP_HOST . $this -> REQUEST_URI;
        if ( strpos ( $this -> REQUEST_URI, "?" ) === false )
            $temp [ 0 ] = $this -> REQUEST_URI;
        else
            $temp = explode ( "?", $this -> REQUEST_URI );
        $this -> sense [ "domain" ] = explode ( ':', $this -> HTTP_HOST );
        $this -> sense [ "domain" ] = $this -> sense [ "domain" ] [ 0 ];
        $this -> sense [ "base" ] = str_replace ( basename ( $this -> SCRIPT_FILENAME ), '', $this -> SCRIPT_NAME );
        $path_abstract = str_replace ( $this -> sense [ "base" ], '', str_replace ( basename ( $this -> SCRIPT_FILENAME ), '', $temp [ 0 ] ) );
        unset ( $temp );
        $path_abstract = preg_replace('~/+~', '/', $path_abstract );
        $path_abstract = ltrim ( $path_abstract, '/' );
        $path_abstract = rtrim ( $path_abstract, '/' );
        if ( $path_abstract === "/" || $path_abstract === "" )
        {
            $this -> sense [ "pathitem" ] = array ( 0 => "/" );
        }
        else
        {
            $this -> sense [ "pathitem" ] = explode( "/", $path_abstract );
            array_unshift ( $this -> sense [ "pathitem" ], "/" );
        }

/*
        $executed_file_path = $_SERVER [ "SCRIPT_NAME" ];
        $break = explode ( '/', $executed_file_path );
        $executed_file_name = $break [ count ( $break ) - 1 ];
        $this -> working_directory = rtrim ( str_replace ( $executed_file_name, "", $_SERVER [ 'SCRIPT_FILENAME' ] ), "/" );
*/

    }

    function getRoute ()
    {
        return $this -> sense [ "pathitem" ];
    }
    
    function getDomain ()
    {
        return $this -> sense [ "domain" ];
    }

    function getSubdomains ()
    {
        $result = false;
        foreach ( $this -> sense [ "pathitem" ] as $pathitem )
        {
            if ( $pathitem [ "type" ] ==  "subdomain" )
            {
                $result [] = $pathitem;
            }
            else
            {
                break;
            }
        }

        return $result;

    }

    function getParameters ()
    {
        $result = false;
        foreach ( $this -> sense [ "pathitem" ] as $pathitem )
        {
            if ( $pathitem [ "type" ] ==  "parameter" )
            {
                $result [] = $pathitem;
            }
            else
            {
               // break;
            }
        }

        return $result;

    }


    function getPort()
    {
        // TODO: Implement getPort() method.
    }

    private function reverse_process ()
    {
        $pathitem_relative_path = "/";
        $full_path_extended_properties = array ();
        $path_item_properties = array ( "name" => "", "type" => "", "properties" => array (), "filepath" => "" );
        $path_item_application_index = false;
        print ("<br><hr>");


        foreach ( array_reverse ( $this -> sense [ "pathitem" ], true ) as $path_item_index => $path_item_name )
        {
            if ( $this -> is_application ( $path_item_name ) )
            {
                $path_item_application_index = $path_item_index;
            }
            else
            {
                if ( $path_item_application_index !== false )
                {

                }
            }
        }

        print ("<br><hr><br><br>");
    }

    private function is_application ( $path_item_name )
    {
        $filesystem_target = $this -> filesystem -> getcwd () . "/" . $this -> configuration -> filesystem [ "relative_folders" ] [ "applications" ] . "/" . $path_item_name;
        if ( is_dir ( $filesystem_target ) )
        {
            $result = array ( "type" => "application", "properties" => array ( "mode" => "folder", "owner" => "common" ), "filepath" => $filesystem_target );
            return $result;
        }
        else
            return false;
    }

    function processRoute ()
    {
        $pathitem_relative_path = "/";
        $full_path_extended_properties = array ();
        $pathitem_properties = array ( "name" => "", "type" => "", "properties" => array (), "filepath" => "" );
        foreach ( $this -> sense [ "pathitem" ] as $pathitem_index => $pathitem_name )
        {
            $pathitem_relative_path .= $pathitem_name . "/";
            $pathitem_relative_path = preg_replace('~/+~', '/', $pathitem_relative_path );
            #print ( "<br>relative: " . $pathitem_relative_path . "<br>");
            $pathitem_properties [ "name" ] = $pathitem_name;
            //$pathitem_properties [ "filepath" ] = $pathitem_relative_path;
            //print ( "<br> -> " . $pathitem_properties [ "filepath" ] . " <- <br>" );
            if ( $pathitem_index > 0 )
            {
                $isSubdomain = $this -> isSubdomain ( $pathitem_relative_path );
                if ( is_array ( $isSubdomain ) )
                {
                    $pathitem_properties [ "type" ] = "subdomain";
                    $pathitem_properties [ "properties" ] = $isSubdomain [ "properties" ];
                    $pathitem_properties [ "filepath" ] = $isSubdomain [ "filepath" ];
                }
                else
                {
                    $isApplication = $this -> isApplication ( $pathitem_relative_path );
                    if ( is_array($isApplication))
                    {
                        $pathitem_properties [ "type" ] = "application";
                        $pathitem_properties [ "properties" ] = $isApplication [ "properties" ];
                        $pathitem_properties [ "filepath" ] = $isApplication [ "filepath" ];
                    }
                    else
                    {
                        $isController = $this -> isController ( $pathitem_relative_path );
                        if ( is_array ( $isController ) )
                        {
                            $pathitem_properties [ "type" ] = "controller";
                            $pathitem_properties [ "properties" ] = $isController [ "properties" ];
                            $pathitem_properties [ "filepath" ] = $isController [ "filepath" ];
                        }
                        else
                        {
                            $pathitem_properties [ "type" ] = "parameter";
                            $pathitem_properties [ "properties" ] = false;
                            $pathitem_properties [ "filepath" ]  = false;
                            //break;
                        }
                    }

                }
                $full_path_extended_properties [] = $pathitem_properties;
            }
        }

        $this -> sense ["pathitem"] = $full_path_extended_properties;

    }

    private function isApplication ( $pathitem_relative_path )
    {
        $pathitem_name = end ( explode ( "/", $pathitem_relative_path ) );
        //$pathitem_name = substr ( $pathitem_relative_path, strrpos ( $pathitem_relative_path, '/' ) + 1 );
        if ( !$pathitem_name )
        {
            $pathitem_name = end ( explode ( "/", rtrim ( $pathitem_relative_path, '/' ) ) );
            //$pathitem_name = substr ( $pathitem_relative_path, strrpos ( rtrim ( $pathitem_relative_path, '/' ), '/' ) + 1 );
        }

        $filesystem_target = $this -> filesystem -> getcwd () . "/" . $this -> configuration -> filesystem [ "relative_folders" ] [ "applications" ] . "/" . $pathitem_name;
        Print ( "<br>looking for: " . $filesystem_target . "<br>" );

        if ( is_dir ( $filesystem_target ) )
        {
            $result = array ( "type" => "application", "properties" => array ( "mode" => "folder", "owner" => "private" ), "filepath" => $filesystem_target );
            return $result;
        }
        else
            return false;

    }

    function isSubdomain ( $pathitem_relative_path )
    {
        $filesystem_target = $this -> filesystem -> getcwd () . "/" . $this -> configuration -> filesystem [ "relative_folders" ] [ "subdomains" ] . $pathitem_relative_path;
        if ( is_dir ( $filesystem_target ) )
        {
            $result = array ( "type" => "subdomain", "properties" => array ( "mode" => "folder" ), "filepath" => $filesystem_target );
            return $result;
        }
        else //TODO: Logical subdomain without folder.
            return false;
    }

    function isController ( $pathitem_relative_path )
    {
        $pathitem_name = end ( explode ( "/", $pathitem_relative_path ) );
        //$pathitem_name = substr ( $pathitem_relative_path, strrpos ( $pathitem_relative_path, '/' ) + 1 );
        if ( !$pathitem_name )
        {
            $pathitem_name = end ( explode ( "/", rtrim ( $pathitem_relative_path, '/' ) ) );
            //$pathitem_name = substr ( $pathitem_relative_path, strrpos ( rtrim ( $pathitem_relative_path, '/' ), '/' ) + 1 );
        }

        $filesystem_target = $this -> filesystem -> getcwd () . "/controllers/" . $pathitem_name . ".php";
        if ( is_file ( $filesystem_target ) )
        {
            $result = array ( "type" => "controller", "properties" => array ( "mode" => "file", "owner" => "common" ), "filepath" => $filesystem_target );
            return $result;
        }
        else //TODO: Logical controller without file.
        {
            $filesystem_target = $this -> filesystem -> getcwd () . "/subdomains" . rtrim ( $pathitem_relative_path, $pathitem_name . "/" ) . "/" . $pathitem_name . ".php";
            if ( is_file ( $filesystem_target ) )
            {
                $result = array ( "type" => "controller", "properties" => array ( "mode" => "file", "owner" => "private" ), "filepath" => $filesystem_target );
                return $result;
            }
            else
            {
                return false;
            }
        }
    }

    function debug ()
    {
        print ("<hr>");
        print ( "url: " . $this -> url . "<br>");
        print ( '<br>$route -> sense');
        ob_start();
        var_dump ( $this -> sense );
        $dump = ob_get_contents ();
        ob_end_clean ();
        $dump = str_replace ( '["', '<span style="color:red; font-weight: bold;">["', $dump );
        $dump = str_replace ( '"]', '"]</span>', $dump );
        echo "<pre> $dump </pre>";
    }

    function __destruct()
    {
        // TODO: Implement __destruct() method.
    }
}

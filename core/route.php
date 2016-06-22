<?php
/**
 * Created by PhpStorm.
 * User: szabacsik
 * Date: 2016. 06. 05.
 * Time: 15:37
 */

namespace improwerk\implement\mvcd;


class route implements iadvanced
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

    //public $working_directory = "";
    public $sense = array ( "domain" => "", "base" => "", "path" => array (), "variables" => array (), "anchor" => "", "protocol" => "", "port" => "" );
    public $url = "";
    private $filesystem;

    function __construct ( $filesystem )
    {
        $this->filesystem = $filesystem;
        $this->initStatic();
        $this->initDynamic();
        $this->processRoute ();
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

        $this -> sense [ "variables" ] = $this -> REQUEST;
        $this->sense [ "port" ]        = $this -> SERVER_PORT;
        $this->sense [ "protocol" ]    = $this -> SERVER_PROTOCOL;
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
            $this -> sense [ "path" ] = array ( 0 => "/" );
        }
        else
        {
            $this -> sense [ "path" ] = explode( "/", $path_abstract );
            array_unshift ( $this -> sense [ "path" ], "/" );
        }

/*        $executed_file_path = $_SERVER [ "SCRIPT_NAME" ];
        $break = explode ( '/', $executed_file_path );
        $executed_file_name = $break [ count ( $break ) - 1 ];
        $this -> working_directory = rtrim ( str_replace ( $executed_file_name, "", $_SERVER [ 'SCRIPT_FILENAME' ] ), "/" );*/

    }

    function getRoute ()
    {
        return $this -> sense [ "path" ];
    }
    
    function getDomain ()
    {
        return $this -> sense [ "domain" ];
    }

    function getPort()
    {
        // TODO: Implement getPort() method.
    }

    function processRoute ()
    {
        $filesystem_target = $this->filesystem->getcwd();
        $path_extended = array ();
        $pathitem_properties = array ( "name" => "", "type" => "", "filepath" => "" );
        foreach ( $this -> sense [ "path" ] as $pathitem_index => $pathitem_name )
        {
            $pathitem_properties [ "name" ] = $pathitem_name;
            if ( $pathitem_index > 0 )
            {
                $filesystem_target .= "/" . $pathitem_name;
                $isSubdomain = $this -> isSubdomain ( $filesystem_target );
                if ( is_array ( $isSubdomain ) )
                {
                    $pathitem_properties [ "type" ] = "subdomain";
                    $pathitem_properties [ "properties" ] = $isSubdomain [ "properties" ];
                    $pathitem_properties [ "filepath" ] = $filesystem_target;
                }
                else
                {
                    $filesystem_target = $this -> working_directory . "/controllers/" . $pathitem_name . ".php";
                    $isController = $this -> isController ( $filesystem_target );
                    if ( is_array ( $isController ) )
                    {
                        $pathitem_properties [ "type" ] = "controller";
                        $pathitem_properties [ "properties" ] = $isController [ "properties" ];
                        $pathitem_properties [ "filepath" ] = $filesystem_target;
                    }
                    else
                    {
                        $pathitem_properties [ "type" ] = false;
                        $pathitem_properties [ "properties" ] = false;
                        $pathitem_properties [ "filepath" ]  = false;
                        break;
                    }
                }
                $path_extended [] = $pathitem_properties;
            }
        }

        $this -> sense ['path'] = $path_extended;

    }

    function isSubdomain ( $filesystem_target )
    {
        if ( is_dir ( $filesystem_target ) )
        {
            $result = array ( "type" => "subdomain", "properties" => array ( "mode" => "folder" ) );
            return $result;
        }
        else //TODO: Logical subdomain is lehet. Saját könyvtár nélkül.
            return false;
    }

    function isController ( $filesystem_target )
    {
        if ( is_file ( $filesystem_target ) )
        {
            $result = array ( "type" => "controller", "properties" => array ( "mode" => "file", "owner" => "common" ) );
            return $result;
        }
        else //TODO: Logikai controller -nek van értelme? Nem létezik hozzá php file? Azt viszont ki kell dolgozni, hogy owner = private és a file a subdomain könyvtárban van
        {
            return false;
        }

    }

    function __destruct()
    {
        // TODO: Implement __destruct() method.
    }
}

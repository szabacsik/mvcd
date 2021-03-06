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
    private $REQUEST; //$_GET, $_POST, $_COOKIE

    public $sense = array("domain" => "", "base" => "", "pathitem" => array(), "variables" => array(), "anchor" => "", "protocol" => "", "port" => "");
    public $url = "";
    private $controller_index;
    private $application_index;
    private $subdomain_index;
    private $configuration;
    private $filesystem;

    public $point_list;

    function __construct($configuration, $filesystem)
    {
        $this->configuration = $configuration;
        $this->filesystem = $filesystem;
        $this->init_static();
        $this->init_dynamic();
        $this->reverse_process();
        //$this -> process ();
    }

    private function init_static()
    {
        $this->HTTP_HOST = $_SERVER ['HTTP_HOST'];
        $this->SERVER_NAME = $_SERVER ['SERVER_NAME'];
        $this->REQUEST_URI = $_SERVER ['REQUEST_URI'];
        $this->SCRIPT_NAME = $_SERVER ['SCRIPT_NAME'];
        $this->SCRIPT_FILENAME = $_SERVER ['SCRIPT_FILENAME'];
        $this->DOCUMENT_ROOT = $_SERVER ['DOCUMENT_ROOT'];
        $this->GATEWAY_INTERFACE = $_SERVER ['GATEWAY_INTERFACE'];
        $this->HTTP_ACCEPT = $_SERVER ['HTTP_ACCEPT'];
        $this->HTTP_ACCEPT_ENCODING = $_SERVER ['HTTP_ACCEPT_ENCODING'];
        $this->HTTP_ACCEPT_LANGUAGE = $_SERVER ['HTTP_ACCEPT_LANGUAGE'];
        $this->HTTP_CACHE_CONTROL = $_SERVER ['HTTP_CACHE_CONTROL'];
        $this->HTTP_CONNECTION = $_SERVER ['HTTP_CONNECTION'];
        $this->HTTP_DNT = $_SERVER ['HTTP_DNT'];
        $this->HTTP_HOST = $_SERVER ['HTTP_HOST'];
        $this->HTTP_UPGRADE_INSECURE_REQUESTS = $_SERVER ['HTTP_UPGRADE_INSECURE_REQUESTS'];
        $this->HTTP_USER_AGENT = $_SERVER ['HTTP_USER_AGENT'];
        $this->PATH = $_SERVER ['PATH'];
        $this->QUERY_STRING = $_SERVER ['QUERY_STRING'];
        $this->REDIRECT_STATUS = $_SERVER ['REDIRECT_STATUS'];
        $this->REMOTE_ADDR = $_SERVER ['REMOTE_ADDR'];
        $this->REMOTE_PORT = $_SERVER ['REMOTE_PORT'];
        $this->REQUEST_METHOD = $_SERVER ['REQUEST_METHOD'];
        $this->REQUEST_URI = $_SERVER ['REQUEST_URI'];
        $this->SCRIPT_FILENAME = $_SERVER ['SCRIPT_FILENAME'];
        $this->SCRIPT_NAME = $_SERVER ['SCRIPT_NAME'];
        $this->SERVER_ADDR = $_SERVER ['SERVER_ADDR'];
        $this->SERVER_ADMIN = $_SERVER ['SERVER_ADMIN'];
        $this->SERVER_NAME = $_SERVER ['SERVER_NAME'];
        $this->SERVER_PORT = $_SERVER ['SERVER_PORT'];
        $this->SERVER_PROTOCOL = $_SERVER ['SERVER_PROTOCOL'];
        $this->SERVER_SIGNATURE = $_SERVER ['SERVER_SIGNATURE'];
        $this->SERVER_SOFTWARE = $_SERVER ['SERVER_SOFTWARE'];
        $this->PHP_SELF = $_SERVER ['PHP_SELF'];
        $this->REQUEST_TIME = $_SERVER ['REQUEST_TIME'];
        $this->argv = $_SERVER ['argv'];
        $this->argc = $_SERVER ['argc'];
        $this->REQUEST = $_REQUEST;

        $this->sense ["variables"] = $this->REQUEST;
        $this->sense ["port"] = $this->SERVER_PORT;
        $this->sense ["protocol"] = $this->SERVER_PROTOCOL;
    }

    private function init_dynamic()
    {

        $this->url = $this->HTTP_HOST . $this->REQUEST_URI;
        if (strpos($this->REQUEST_URI, "?") === false)
            $temp [0] = $this->REQUEST_URI;
        else
            $temp = explode("?", $this->REQUEST_URI);
        $this->sense ["domain"] = explode(':', $this->HTTP_HOST);
        $this->sense ["domain"] = $this->sense ["domain"] [0];
        $this->sense ["base"] = str_replace(basename($this->SCRIPT_FILENAME), '', $this->SCRIPT_NAME);
        $this->sense ["base"] = preg_replace('~/+~', '', $this->sense ["base"]);
        $path_abstract = str_replace($this->sense ["base"], '', str_replace(basename($this->SCRIPT_FILENAME), '', $temp [0]));
        unset ($temp);
        $path_abstract = preg_replace('~/+~', '/', $path_abstract);
        $path_abstract = ltrim($path_abstract, '/');
        $path_abstract = rtrim($path_abstract, '/');
        if ($path_abstract === "/" || $path_abstract === "") {
            $this->sense ["pathitem"] = array(0 => "/");
        } else {
            $this->sense ["pathitem"] = explode("/", $path_abstract);
            array_unshift($this->sense ["pathitem"], "/");
        }

        $this->point_list = $this->sense ["pathitem"];

    }

    function get_route()
    {
        return $this->sense ["pathitem"];
    }

    public function get_controller()
    {
        return $this->sense ["pathitem"][$this->controller_index];
    }

    public function get_controller_name()
    {
        return $this->sense ["pathitem"][$this->controller_index]["name"];
    }

    public function get_controller_filepath()
    {
        return $this->sense ["pathitem"][$this->controller_index]["filepath"];
    }

    public function get_application()
    {
        return $this->sense ["pathitem"][$this->application_index];
    }

    public function get_application_filepath()
    {
        return $this->sense ["pathitem"][$this->application_index]["filepath"];
    }

    function get_subdomain()
    {
        return array_slice($this->sense ["pathitem"], 1, $this->subdomain_index);
    }

    public function set_application($name, $filepath, $mode = "folder")
    {

        $pathitem [] = array("name" => $name, "type" => "application", "url" => "set_application needs to set", "filepath" => $filepath, "properties" => array("mode" => $mode, "owner" => "master"));
        if (is_array($this->get_application())) {
            $this->sense ["pathitem"] [$this->application_index] = $pathitem;
        } else {
            $this->application_index = $this->subdomain_index + 1;
            array_splice($this->sense ["pathitem"], $this->application_index, 0, $pathitem);
        }

        //$application = $this -> get_application ();
        //$filepath = $application ["filepath"] . "/" . $this -> configuration -> core [ "default_controller" ] . ".php";
        //$this -> set_controller ( $this -> configuration -> core [ "default_controller" ], $filepath );

        $this->update($this->application_index);

    }

    public function set_controller($name, $filepath, $mode = "file")
    {

        $pathitem [] = array("name" => $name, "type" => "controller", "url" => "set_controller needs to set", "filepath" => $filepath, "properties" => array("mode" => $mode, "owner" => "master"));
        if (is_array($this->get_controller())) {
            $this->sense ["pathitem"] [$this->controller_index] = $pathitem;
        } else {
            $this->controller_index = $this->application_index + 1;
            array_splice($this->sense ["pathitem"], $this->controller_index, 0, $pathitem);
        }

        $this->update($this->controller_index);

    }

    private function update($path_item_index_from = 0)
    {
        $controller_index = 0;
        for ($index = $path_item_index_from; $index < count($this->sense ['pathitem']); $index++) {
            if ($this->sense ['pathitem'] [$index] ["type"] === "controller") {
                $controller_index = $index;
            }

            if ($index > $controller_index && $controller_index > 0 && $this->sense ['pathitem'] [$index] ["type"] === "faulty") {
                $this->sense ['pathitem'] [$index] ["type"] = "parameter";
            }

        }
    }

    public function get_domain()
    {
        return $this->sense ["domain"];
    }

    public function get_base()
    {
        return $this->sense ["base"];
    }

    public function get_parameters()
    {
        $result = false;
        foreach ($this->sense ["pathitem"] as $pathitem) {
            if ($pathitem ["type"] == "parameter") {
                $result [] = $pathitem;
            } else {
                // break;
            }
        }

        return $result;

    }

    public function get_port()
    {
        return $this->sense ["port"];
    }

    private function reverse_process()
    {
        $pathitem_url = $this->get_domain() . "/" . $this->get_base();

        if (count($this->sense ["pathitem"]) > 1 && $this->sense ["pathitem"] != "/") {

            $full_path_extended_properties = array();

            foreach (array_reverse($this->sense ["pathitem"], true) as $path_item_index => $path_item_name) {
                $is_master_application = $this->is_master_application($path_item_name);
                $sub_path_items = array_slice($this->sense ["pathitem"], 0, $path_item_index + 1);
                $is_private_application = $this->is_private_application($sub_path_items);

                if (is_array($is_master_application) || is_array($is_private_application)) {
                    $path_item_application_index = $path_item_index;
                    $this->application_index = $path_item_index;
                    if (is_array($is_master_application)) {
                        $pathitem_url .= "/" . $path_item_name;

                        $is_master_application ["url"] = $pathitem_url;
                        $full_path_extended_properties [$path_item_index] = $is_master_application;
                        $parent = $is_master_application;
                    } else {
                        $is_private_application ["url"] = $pathitem_url;
                        $full_path_extended_properties [$path_item_index] = $is_private_application;
                        $parent = $is_private_application;
                    }

                    $sub_path_items = array_slice($this->sense ["pathitem"], $path_item_application_index + 1, count($this->sense ["pathitem"]) - 2);
                    $sub_path_properties = $this->pathalyzer($parent, $sub_path_items, $path_item_application_index + 1);
                    $sub_path_item_index = 0;

                    foreach ($sub_path_properties as $sub_path_item_properties) {
                        $sub_path_item_index++;
                        //$sub_path_item_properties [ "url" ] = "sub path url needs to set";
                        $full_path_extended_properties [$path_item_index + $sub_path_item_index] = $sub_path_item_properties;
                    }

                } else {
                    $sub_path_items = array_slice($this->sense ["pathitem"], 0, $path_item_index + 1);
                    $is_subdomain = $this->is_subdomain($sub_path_items);
                    if (is_array($is_subdomain)) {
                        $this->subdomain_index = $path_item_index;
                        $sub_path_item_index = 0;
                        foreach ($is_subdomain as $sub_path_item_properties) {
                            //$sub_path_item_properties ["url"] = "subdomain url needs to set";
                            $sub_path_item_properties ["url"] = $pathitem_url . $sub_path_item_properties ["url"] . "/";
                            $full_path_extended_properties [$sub_path_item_index] = $sub_path_item_properties;
                            $sub_path_item_index++;
                        }

                        break;

                    } else {
                        if ($path_item_name === "/") {
                            $path_item_properties = array("name" => $path_item_name,
                                "type" => "base",
                                "url" => $this->get_domain() . "/" . $this->get_base(),
                                "filepath" => $this->filesystem->get_root(),
                                "properties" => array("mode" => "folder", "owner" => "master"));
                        } else {
                            $path_item_properties = array("name" => $path_item_name,
                                "type" => "faulty",
                                "url" => false,
                                "filepath" => false,
                                "properties" => false);
                        }
                        $full_path_extended_properties [$path_item_index] = $path_item_properties;
                    }

                }
            }
        } else {
            $pathitem_properties ["name"] = "/";
            $pathitem_properties ["type"] = "base";
            $pathitem_properties ["url"] = $pathitem_url;
            $pathitem_properties ["properties"] = array("mode" => "folder", "owner" => "master");
            $pathitem_properties ["filepath"] = $this->filesystem->get_root();
            $full_path_extended_properties [] = $pathitem_properties;
        }

        ksort($full_path_extended_properties);
        $this->sense ["pathitem"] = $full_path_extended_properties;

    }

    private function pathalyzer($parent, $target_path, $offset_index)
    {

        $parent_path = $parent ["filepath"];
        $results = array();
        if (is_string($target_path))
            $working_path_items = explode("/", $target_path);
        else
            $working_path_items = $target_path;
        $working_path_string = $parent_path;
        $controller_exists = false;

        foreach ($working_path_items as $path_item_index => $path_item_name) {
            $path_item_properties ["name"] = $path_item_name;
            $working_path_string .= "/" . $path_item_name;
            if (is_dir($working_path_string)) {
                $path_item_properties ["type"] = "folder";
                $path_item_properties ["url"] = "lehetoseg 1";
                $path_item_properties ["filepath"] = $working_path_string;
                $path_item_properties ["properties"]["owner"] = $parent ["properties"]["owner"];
            } else {
                if (is_file($working_path_string . ".php")) {
                    $path_item_properties ["type"] = "controller";
                    $path_item_properties ["url"] = "lehetoseg 2";
                    $path_item_properties ["filepath"] = $working_path_string . ".php";
                    $path_item_properties ["properties"]["mode"] = "file";
                    $path_item_properties ["properties"]["owner"] = $parent ["properties"]["owner"];
                    $controller_exists = true;
                    $this->controller_index = $offset_index + $path_item_index;
                } else {
                    if ($controller_exists) {
                        $path_item_properties ["type"] = "parameter";
                        $path_item_properties ["url"] = "lehetoseg 3";
                        $path_item_properties ["filepath"] = false;
                        $path_item_properties ["properties"] = false;
                    } else {
                        $path_item_properties ["type"] = "faulty";
                        $path_item_properties ["url"] = "lehetoseg 4";
                        $path_item_properties ["filepath"] = false;
                        $path_item_properties ["properties"] = false;
                    }
                }
            }

            $results [] = $path_item_properties;

        }

        return $results;

    }

    private function is_master_application($path_item_name)
    {
        $filesystem_target = $this->filesystem->get_root() . "/" . $this->configuration->filesystem ["relative_folders"] ["master_applications"] . "/" . $path_item_name;
        $filesystem_target = preg_replace('~/+~', '/', $filesystem_target);
        if (is_dir($filesystem_target) && $path_item_name != "/") {
            $result = array
            (
                "name" => $path_item_name,
                "type" => "application",
                "filepath" => $filesystem_target,
                "properties" => array("mode" => "folder", "owner" => "master")
            );
            return $result;
        } else {
            return false;
        }
    }

    private function is_private_application($path_items)
    {
        $last_item = array_pop($path_items);
        $filesystem_target = $this->filesystem->get_root() . "/" . $this->configuration->filesystem ["relative_folders"] ["subdomains"] . "/" . implode("/", $path_items) . "/" . $this->configuration->filesystem ["relative_folders"] ["private_applications"] . "/" . $last_item;
        $filesystem_target = preg_replace('~/+~', '/', $filesystem_target);

        if (is_dir($filesystem_target) && $last_item != "/") {
            $result = array
            (
                "name" => $last_item,
                "type" => "application",
                "properties" => array("mode" => "folder", "owner" => "private"),
                "filepath" => $filesystem_target
            );
            return $result;
        } else {
            return false;
        }
    }

    private function is_subdomain($path_items)
    {
        $result = false;
        $temp = array();
        $filesystem_target = $this->filesystem->get_root() . "/" . $this->configuration->filesystem ["relative_folders"] ["subdomains"] . implode("/", $path_items);
        $filesystem_target = preg_replace('~/+~', '/', $filesystem_target);

        if (is_dir($filesystem_target) && $filesystem_target !== $this->filesystem->get_root() . "/" . $this->configuration->filesystem ["relative_folders"] ["subdomains"] . "/") {
            $temp = array();
            foreach ($path_items as $path_item_index => $path_item_name) {

                $temp [] = $path_item_name;

                $filesystem_target = $this->filesystem->get_root() . "/" . $this->configuration->filesystem ["relative_folders"] ["subdomains"] . "/" . implode("/", $temp);
                $filesystem_target = preg_replace('~/+~', '/', $filesystem_target);

                $url = implode("/", $temp);
                $url = preg_replace('~/+~', '/', $url);

                if ($path_item_name != "/") {
                    $result [] = array(
                        "name" => $path_item_name,
                        "type" => "subdomain",
                        "url" => $url,
                        "filepath" => $filesystem_target,
                        "properties" => array("mode" => "folder", "owner" => "private"));
                } else {
                    $result [] = array(
                        "name" => "/",
                        "type" => "base",
                        "url" => "",
                        "filepath" => $this->filesystem->get_root(),
                        "properties" => array("mode" => "folder", "owner" => "master"));
                }

                //array_pop ( $temp );
            }

            return $result;
        } else //TODO: Logical subdomain without folder.
        {
            if ($filesystem_target === $this->filesystem->get_root() . "/" . $this->configuration->filesystem ["relative_folders"] ["subdomains"] . "/") {
                //TODO: is subdomain? /.../.../.../.../mvcd/subdomains/ no
            }
            return false;
        }

    }

    public function debug()
    {
        print ("<br><h1>route debug</h1>");
        print ("url: " . $this->url . "<br>");
        var_dump(parse_url($this->url));
        print ("<br>");
        var_dump(parse_url($this->url));
        print ("<br>");
        var_dump(parse_url($this->url, PHP_URL_SCHEME));
        print ("<br>");
        var_dump(parse_url($this->url, PHP_URL_USER));
        print ("<br>");
        var_dump(parse_url($this->url, PHP_URL_PASS));
        print ("<br>");
        var_dump(parse_url($this->url, PHP_URL_HOST));
        print ("<br>");
        var_dump(parse_url($this->url, PHP_URL_PORT));
        print ("<br>");
        var_dump(parse_url($this->url, PHP_URL_PATH));
        print ("<br>");
        var_dump(parse_url($this->url, PHP_URL_QUERY));
        print ("<br>");
        var_dump(parse_url($this->url, PHP_URL_FRAGMENT));
        print ("<br>");
        print ('<br>$route -> sense');
        ob_start();
        var_dump($this->sense);
        $dump = ob_get_contents();
        ob_end_clean();
        $dump = str_replace('["', '<span style="color:red; font-weight: bold;">["', $dump);
        $dump = str_replace('"]', '"]</span>', $dump);
        echo "<pre> $dump </pre>";


        echo "<br><br>get_controller()<br>";
        ob_start();
        var_dump($this->get_controller());
        $dump = ob_get_contents();
        ob_end_clean();
        $dump = str_replace('["', '<span style="color:red; font-weight: bold;">["', $dump);
        $dump = str_replace('"]', '"]</span>', $dump);
        echo "<pre> $dump </pre>";


        echo "<br><br>get_application()<br>";
        ob_start();
        var_dump($this->get_application());
        $dump = ob_get_contents();
        ob_end_clean();
        $dump = str_replace('["', '<span style="color:red; font-weight: bold;">["', $dump);
        $dump = str_replace('"]', '"]</span>', $dump);
        echo "<pre> $dump </pre>";

    }

    function __destruct()
    {
        // TODO: Implement __destruct() method.
    }

    function process()
    {
        $posibilities = array();

        $item = array(
            "id" => "",
            "name" => "",
            "type" => "",
            "owner" => "",
            "path" => "",
            "child" => array()
        );

        foreach ($this->point_list as $index => $point_name) {
            $item ["id"] = $index;
            $item ["name"] = $point_name;
            $posibilities [] = $item;
        }

        $this->recursive($posibilities);

        define("JSON_PRETTY_PRINT", 128);
        echo json_encode($posibilities, JSON_PRETTY_PRINT);


        die ();

        echo "<br><strong>process</strong><br>";
        echo "<br>point_list<br>";
        ob_start();
        var_dump($posibilities);
        $dump = ob_get_contents();
        ob_end_clean();
        $dump = str_replace('["', '<span style="color:red; font-weight: bold;">["', $dump);
        $dump = str_replace('"]', '"]</span>', $dump);
        echo "<pre> $dump </pre>";

        //$posibilities = $this -> recursive ();

        //foreach ( $this -> point_list as $index => $point_name )

        //$master_application_directory = $this -> filesystem -> get_root () . "/" . $this -> configuration -> filesystem [ "relative_folders" ] [ "master_applications" ] . "/";

        /*        foreach ( $this -> point_list as $index => $point_name )
                {
                    if ( is_dir ( $master_application_directory . $point_name ) )
                    {
                        $posibilities [ $index ] [] = $master_application_directory . $point_name;
                    }
                }*/


        echo "<br>posibilities<br>";
        ob_start();
        var_dump($posibilities);
        $dump = ob_get_contents();
        ob_end_clean();
        $dump = str_replace('["', '<span style="color:red; font-weight: bold;">["', $dump);
        $dump = str_replace('"]', '"]</span>', $dump);
        echo "<pre> $dump </pre>";
    }

    function recursive(&$target, $parent_path = "")
    {
        foreach ($target as $index => $item) {
            echo("root? " . $item ["name"]);
            if ($item ["name"] === "/") {
                echo(" - yes<br>");
            } else {
                echo(" - no<br>");
                //$master_application_directory = $this -> filesystem -> get_root () . "/" . $this -> configuration -> filesystem [ "relative_folders" ] [ "master_applications" ] . "/";
                if ($parent_path !== "")
                    $subdomain_directory = $parent_path;
                else
                    $subdomain_directory = $this->filesystem->get_root() . "/" . $this->configuration->filesystem ["relative_folders"] ["subdomains"] . "/";

                echo("subdomain? " . $subdomain_directory . $item ["name"]);
                if (is_dir($subdomain_directory . $item ["name"])) {
                    echo(" - yes<br>");
                    $target [$index] ["path"] = $subdomain_directory . $item ["name"];
                    $target [$index] ["child"] = array_slice($target, $index + 1, count($target) - 1);
                    $this->recursive($target [$index] ["child"], $subdomain_directory);
                } else
                    echo(" - no<br>");

                /*
                                echo ( "master app? " . $master_application_directory . $item [ "name" ] );
                                if ( is_dir ( $master_application_directory . $item [ "name" ] ) )
                                {
                                    echo ( " - yes<br>" );
                                }
                                else
                                    echo ( " - no<br>" );
                */
            }
            print ("<br>next</br>");
        }
        print ("<br>exiting...</br>");
    }


    private function application_route_builder ( $applications_root_directory )
    {
        //$applications_root_directory = $this -> filesystem -> get_root () . "/" . $this -> configuration -> filesystem [ "relative_folders" ][ "master_applications" ];
        $application_routes = array ();

        $iterator = new \RecursiveIteratorIterator ( new \RecursiveDirectoryIterator ( $applications_root_directory ), \RecursiveIteratorIterator::SELF_FIRST );
        $iterator -> setFlags ( \RecursiveDirectoryIterator::SKIP_DOTS );

        foreach ( $iterator as $path )
        {
            if ( $path -> isDir () )
            {
                if
                (
                    $path -> __toString () != $applications_root_directory &&
                    $this -> is_routable_path ( $applications_root_directory, str_replace ( $applications_root_directory, "", $path -> __toString () ) )
                )
                {
                    $target = $path -> __toString () . "/" . $this -> default_controller_filename ();
                    $route = $this -> clean_route_name ( str_replace ( $applications_root_directory, "", $path -> __toString () ) );
                    $application_routes [ $route ] = $target;
                }
            }
            else
            {
                if
                (
                $this -> is_routable_path ( $applications_root_directory, str_replace ( $applications_root_directory, "", $path -> __toString () ) )
                )
                {
                    $target = $path -> __toString ();
                    $route = $this -> clean_route_name ( str_replace ( $applications_root_directory, "", $path -> __toString () ) );
                    $application_routes [ $route ] = $target;
                }
            }
        }

        return $application_routes;

    }

    private function domain_route_builder ( $subdomains_root_directory, $private_applications_directory_name, $excluded_directories = false )
    {
        //$subdomains_root_directory = $this -> filesystem -> get_root () . "/" . $this -> configuration -> filesystem [ "relative_folders" ][ "subdomains" ];
        //$private_applications_directory_name = $this->configuration->filesystem ["relative_folders"] ["private_applications"];

        $subdomains_routes = array ();
        $subdomains_private_application_routes = false;

        if ( !is_array ( $excluded_directories ) )
        {
            $excluded_directories = $this->configuration->filesystem ["relative_folders"];
            $excluded_directories = array_values($excluded_directories);
        }

        $iterator = new \RecursiveIteratorIterator ( new \RecursiveDirectoryIterator ( $subdomains_root_directory ), \RecursiveIteratorIterator::SELF_FIRST );
        $iterator -> setFlags ( \RecursiveDirectoryIterator::SKIP_DOTS );

        while ( $iterator -> valid () )
        {
            if ( !preg_match ( "/(\.|\..)$/", $iterator->key(), $output_array ) )
            {
                $path_string = str_replace($subdomains_root_directory, $this->configuration->core ["root_domain"], $iterator->key());
                $path_array = explode("/", $path_string);
                $excluded_path = array_intersect($excluded_directories, $path_array);
                $excluded_path = !empty ($excluded_path);
                $route = str_replace($subdomains_root_directory, "", $iterator->key());
                if (!$excluded_path) {
                    $target = $iterator->key();
                    $subdomains_routes [$route] = $target;
                }

                if (preg_match("/(" . $private_applications_directory_name . "|\/" . $private_applications_directory_name . "\/|" . $private_applications_directory_name . "\/)$/", $iterator->key(), $output_array)) {
                    $subdomain = preg_replace("/(" . $private_applications_directory_name . "|\/" . $private_applications_directory_name . "\/|" . $private_applications_directory_name . "\/)$/", "", $route);
                    $subdomains_private_application_routes [$subdomain] = $this->application_route_builder($iterator->key());
                }
            }

            $iterator -> next ();

        }

        $array_keys = array_map ( 'strlen', array_keys ( $subdomains_routes ) );
        array_multisort ( $array_keys, SORT_DESC, $subdomains_routes );

        if ( is_array ( $subdomains_private_application_routes ) ) {
            foreach ($subdomains_private_application_routes as $subdomain_index => $subdomain_private_application_routes) {
                $array_keys = array_map('strlen', array_keys($subdomains_private_application_routes [$subdomain_index]));
                array_multisort($array_keys, SORT_DESC, $subdomains_private_application_routes [$subdomain_index]);
            }
        }

        $result [ "subdomain_routes" ] = $subdomains_routes;
        $result [ "application_routes" ] = $subdomains_private_application_routes;

        unset($iterator);

        return $result;

    }


    function builder ()
    {

        $master_applications_root_directory = $this -> filesystem -> get_root () . "/" . $this -> configuration -> filesystem [ "relative_folders" ][ "master_applications" ];
        $master_application_file_routes = $this -> application_route_builder ( $master_applications_root_directory );
        $master_application_file_routes [ "/" ] = $master_applications_root_directory . "/" . $this -> default_application_folder_name () . "/" . $this -> default_controller_filename ();
        $master_application_file_routes [ "" ] = $master_applications_root_directory . "/" . $this -> default_application_folder_name () . "/" . $this -> default_controller_filename ();
        $array_keys = array_map ( 'strlen', array_keys ( $master_application_file_routes ) );
        array_multisort ( $array_keys, SORT_DESC, $master_application_file_routes );

                                                echo "<br>master_application_file_routes<br>";
                                                ob_start();
                                                var_dump($master_application_file_routes);
                                                $dump = ob_get_contents();
                                                ob_end_clean();
                                                $dump = str_replace('["', '<span style="color:red; font-weight: bold;">["', $dump);
                                                $dump = str_replace('"]', '"]</span>', $dump);
                                                echo "<pre> $dump </pre>";
                                                print ("<hr>");

        $subdomains_root_directory = $this -> filesystem -> get_root () . "/" . $this -> configuration -> filesystem [ "relative_folders" ][ "subdomains" ];
        $private_applications_directory_name = $this->configuration->filesystem ["relative_folders"] ["private_applications"];
        $domain_route_builder_results = $this -> domain_route_builder ( $subdomains_root_directory, $private_applications_directory_name );
        $subdomains_routes = $domain_route_builder_results [ "subdomain_routes" ];
        $subdomains_private_application_routes = $domain_route_builder_results [ "application_routes" ];

        $domains_root_directory = $this -> filesystem -> get_root () . "/" . $this -> configuration -> filesystem [ "relative_folders" ][ "domains" ];
        $private_applications_directory_name = $this->configuration->filesystem ["relative_folders"] ["private_applications"];
        $domain_route_builder_results = $this -> domain_route_builder ( $domains_root_directory, $private_applications_directory_name );
        $domains_routes = $domain_route_builder_results [ "subdomain_routes" ];
        $domains_private_application_routes = $domain_route_builder_results [ "application_routes" ];

        /*
        $subdomains_root_directory = $this -> filesystem -> get_root () . "/" . $this -> configuration -> filesystem [ "relative_folders" ][ "subdomains" ];
        $subdomains_routes = array ();
        $excluded_directories = $this -> configuration -> filesystem [ "relative_folders" ];
        $excluded_directories = array_values ( $excluded_directories );

        $iterator = new \RecursiveIteratorIterator ( new \RecursiveDirectoryIterator ( $subdomains_root_directory ), \RecursiveIteratorIterator::SELF_FIRST );
        $iterator -> setFlags ( \RecursiveDirectoryIterator::SKIP_DOTS );

        while ( $iterator -> valid () )
        {
            $path_string = str_replace ( $subdomains_root_directory, $this -> configuration -> core [ "root_domain" ], $iterator -> key () );
            $path_array = explode ( "/", $path_string );
            $excluded_path = array_intersect ( $excluded_directories, $path_array );
            $excluded_path = !empty ( $excluded_path );
            $route = str_replace ( $subdomains_root_directory, "", $iterator -> key () );
            if ( !$excluded_path )
            {
                $target = $iterator -> key ();
                $subdomains_routes [ $route ] = $target;
            }

            if ( preg_match("/(" . $this->configuration->filesystem ["relative_folders"] ["private_applications"] . "|\/" . $this->configuration->filesystem ["relative_folders"] ["private_applications"] . "\/|" . $this->configuration->filesystem ["relative_folders"] ["private_applications"] . "\/)$/", $iterator -> key (), $output_array ) )
            {
                $subdomain = preg_replace ("/(" . $this->configuration->filesystem ["relative_folders"] ["private_applications"] . "|\/" . $this->configuration->filesystem ["relative_folders"] ["private_applications"] . "\/|" . $this->configuration->filesystem ["relative_folders"] ["private_applications"] . "\/)$/", "", $route );
                //print ("subdomain: ".$subdomain." has private application: ".$route." / ".$target."<br>");
                $subdomains_private_application_routes [ $subdomain ] = $this -> application_route_builder ( $iterator -> key () );
            }

            $iterator -> next ();

        }

        $array_keys = array_map ( 'strlen', array_keys ( $subdomains_routes ) );
        array_multisort ( $array_keys, SORT_DESC, $subdomains_routes );

        foreach ( $subdomains_private_application_routes as $subdomain_index => $subdomain_private_application_routes )
        {
            $array_keys = array_map ( 'strlen', array_keys ( $subdomains_private_application_routes [ $subdomain_index ] ) );
            array_multisort ( $array_keys, SORT_DESC, $subdomains_private_application_routes [ $subdomain_index ] );
        }
        */

                                                    echo "<br>domains_routes<br>";
                                                    ob_start();
                                                    var_dump($domains_routes);
                                                    $dump = ob_get_contents();
                                                    ob_end_clean();
                                                    $dump = str_replace('["', '<span style="color:red; font-weight: bold;">["', $dump);
                                                    $dump = str_replace('"]', '"]</span>', $dump);
                                                    echo "<pre> $dump </pre>";
                                                    print ("<hr>");

                                                    echo "<br>domains_private_application_routes<br>";
                                                    ob_start();
                                                    var_dump($domains_private_application_routes);
                                                    $dump = ob_get_contents();
                                                    ob_end_clean();
                                                    $dump = str_replace('["', '<span style="color:red; font-weight: bold;">["', $dump);
                                                    $dump = str_replace('"]', '"]</span>', $dump);
                                                    echo "<pre> $dump </pre>";
                                                    print ("<hr>");

                                                    echo "<br>subdomains_routes<br>";
                                                    ob_start();
                                                    var_dump($subdomains_routes);
                                                    $dump = ob_get_contents();
                                                    ob_end_clean();
                                                    $dump = str_replace('["', '<span style="color:red; font-weight: bold;">["', $dump);
                                                    $dump = str_replace('"]', '"]</span>', $dump);
                                                    echo "<pre> $dump </pre>";
                                                    print ("<hr>");

                                                    echo "<br>subdomains_private_application_routes<br>";
                                                    ob_start();
                                                    var_dump($subdomains_private_application_routes);
                                                    $dump = ob_get_contents();
                                                    ob_end_clean();
                                                    $dump = str_replace('["', '<span style="color:red; font-weight: bold;">["', $dump);
                                                    $dump = str_replace('"]', '"]</span>', $dump);
                                                    echo "<pre> $dump </pre>";
                                                    print ("<hr>");



        foreach ( $subdomains_routes as $subdomain_route_key => $subdomain_route_value )
        {
            foreach ( $master_application_file_routes as $master_application_route_key => $master_application_route_value )
            {
                $all_routes [] = $this -> get_domain() . $subdomain_route_key . $master_application_route_key;
            }
        }

                                                    echo "<br>all_routes<br>";
                                                    ob_start();
                                                    var_dump($all_routes);
                                                    $dump = ob_get_contents();
                                                    ob_end_clean();
                                                    $dump = str_replace('["', '<span style="color:red; font-weight: bold;">["', $dump);
                                                    $dump = str_replace('"]', '"]</span>', $dump);
                                                   // echo "<pre> $dump </pre>";

                                                    print ("<hr>");

        die ( "aborting here. testing..." );

    }

    function is_routable_path ( $base, $relative_path )
    {
        $path = $base;
        $elements = explode ( "/", $relative_path );
        foreach ( $elements as $index => $element )
        {
            if ( $element !== "" )
            {
                $path .= "/" . $element;
                if ( !$this -> is_routeable_file ( $path ) ) return false;
            }
        }
        return ( true );
    }

    function is_routeable_file ( $filepath )
    {
        $info = pathinfo ( $filepath );
        $prefix = false;
        $suffix = false;

        if ( is_dir ( $filepath ) )
        {
            if ( $info [ "extension" ] )
                $filename = $info [ "filename" ] . "." . $info [ "extension" ];
            else
                $filename = $info [ "filename" ];
            $prefix = $this -> configuration -> core [ "routable_script_prefix" ];
            $suffix = $this -> configuration -> core [ "routable_script_suffix" ];
        }

        elseif ( is_file ( $filepath ) )
        {
            $filename = $info [ "filename" ];
            if ( $info [ "extension" ] !== $this -> configuration -> core [ 'script_extension' ] ) return false;
            $prefix = $this -> configuration -> core [ "routable_folder_prefix" ];
            $suffix = $this -> configuration -> core [ "routable_folder_suffix" ];
        }

        if ( !$prefix && !$suffix ) return false;

        $prefix_escaped = str_replace ( "." , "\." , $prefix );
        $suffix_escaped = str_replace ( "." , "\." , $suffix );

        if ( preg_match ("/^" . $prefix_escaped . ".*" . $suffix_escaped . "$/" , $filename, $match ) )
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    private function default_controller_filename ()
    {
        return $this -> configuration -> core [ "routable_script_prefix" ] . $this -> configuration -> core [ "default_controller" ] . $this -> configuration -> core [ "routable_script_suffix" ] . "." . $this -> configuration -> core [ "script_extension" ];
    }

    private function default_application_folder_name ()
    {
        return $this -> configuration -> core [ "routable_folder_prefix" ] . $this -> configuration -> core [ "default_application" ] . $this -> configuration -> core [ "routable_folder_suffix" ];
    }

    private function clean_route_name ( $target )
    {

        $prefix = $this -> configuration -> core [ "routable_script_prefix" ];
        $suffix = $this -> configuration -> core [ "routable_script_suffix" ];
        $extension = "." . $this -> configuration -> core [ "script_extension" ];

        $result = str_replace ( $prefix, "", $target );
        $result = str_replace ( $suffix, "", $result );
        $result = str_replace ( $extension, "", $result );

        return $result;

    }


/*
    function recursive ( $index = 0, $as = "" ) //as = application, controller, folder, paramterer
    {
        $master_application_directory = $this -> filesystem -> get_root () . "/" . $this -> configuration -> filesystem [ "relative_folders" ] [ "master_applications" ] . "/";
        $subdomain_directory = $this -> filesystem -> get_root () . "/" . $this -> configuration -> filesystem [ "relative_folders" ] [ "subdomains" ] . "/";
        $source = array_slice ( $this -> point_list, $index, count ( $this -> point_list ) - 1 );
        foreach ( $source as $index => $point_name )
        {
            if ( is_dir ( $master_application_directory . $point_name ) )
            {
                $this -> recursive ( $index = 0, $as = "" )
            }
        }
        $result
    }
*/
}




//Removed

/*    function processRoute ()
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

    }*/

/*    private function isApplication ( $pathitem_relative_path )
    {
        $pathitem_name = end ( explode ( "/", $pathitem_relative_path ) );
        //$pathitem_name = substr ( $pathitem_relative_path, strrpos ( $pathitem_relative_path, '/' ) + 1 );
        if ( !$pathitem_name )
        {
            $pathitem_name = end ( explode ( "/", rtrim ( $pathitem_relative_path, '/' ) ) );
            //$pathitem_name = substr ( $pathitem_relative_path, strrpos ( rtrim ( $pathitem_relative_path, '/' ), '/' ) + 1 );
        }

        $filesystem_target = $this -> filesystem -> get_root () . "/" . $this -> configuration -> filesystem [ "relative_folders" ] [ "applications" ] . "/" . $pathitem_name;
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
        $filesystem_target = $this -> filesystem -> get_root () . "/" . $this -> configuration -> filesystem [ "relative_folders" ] [ "subdomains" ] . $pathitem_relative_path;
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

        $filesystem_target = $this -> filesystem -> get_root () . "/controllers/" . $pathitem_name . ".php";
        if ( is_file ( $filesystem_target ) )
        {
            $result = array ( "type" => "controller", "properties" => array ( "mode" => "file", "owner" => "master" ), "filepath" => $filesystem_target );
            return $result;
        }
        else //TODO: Logical controller without file.
        {
            $filesystem_target = $this -> filesystem -> get_root () . "/subdomains" . rtrim ( $pathitem_relative_path, $pathitem_name . "/" ) . "/" . $pathitem_name . ".php";
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
    }*/

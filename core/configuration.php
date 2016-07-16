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
    //Global
    public $core = array
    (
        "root_domain"            => "ertelmetlen.hu",
        "default_application"    => "default",
        "default_controller"     => "main",
        "routable_script_prefix" => "",
        "routable_script_suffix" => ".route",
        "routable_folder_prefix" => "",
        "routable_folder_suffix" => ".route",
        "script_extension"       => "php",
        "namespace"              => 'improwerk\implement\mvcd\\'
    );

    //Filesystem
    public $filesystem = array
    (
        "relative_folders" => array
        (
            "private_applications" => "applications",
            "common_applications"  => "applications",
            "subdomains"           => "subdomains",
            "domains"              => "domains",
            "user_files"           => "files",
            "cache"                => "cache",
            "temporary"            => "tmp",
            "includes"             => "inc",
            "libraries"            => "lib",
            "styles"               => "css"
        )
    );

    //Security
    public $security = array
    (
        "private_controllers" => disabled
    );

    //Applications
    public $applications = array
    (
        "model_prefix" => "model_",
        "view_prefix" => "view_",
        "control_prefix" => "control_"
    );

    public function get ()
    {
        return array
        (
            "core" => $this -> core,
            "filesystem" => $this -> filesystem,
            "security" => $this -> security
        );
    }

    public function __construct()
    {
    }

    public function __destruct()
    {
        // TODO: Implement __destruct() method.
    }
}
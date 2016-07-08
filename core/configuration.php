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
        "default_application" => "default",
        "default_controller"  => "main",
        "routable_prefix"     => "!",
        "routable_suffix"     => "!",
        "namespace"           => 'improwerk\implement\mvcd\\',
        "root_domain"         => "ertelmetlen.hu"
    );

    //Filesystem
    public $filesystem = array
    (
        "relative_folders" => array
        (
            "private_applications" => "applications",
            "common_applications"  => "applications",
            "subdomains"           => "subdomains",
            "user_files"           => "files",
            "cache"                => "cache",
            "temporary"            => "tmp"
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
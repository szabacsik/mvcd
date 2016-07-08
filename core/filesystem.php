<?php
/**
 * Created by PhpStorm.
 * User: andra
 * Date: 2016. 06. 21.
 * Time: 17:39
 */

namespace improwerk\implement\mvcd;


class filesystem implements ibasic
{

    public $working_directory = "";
    public function __construct ()
    {
        $executed_file_path = $_SERVER [ "SCRIPT_NAME" ];
        $break = explode ( '/', $executed_file_path );
        $executed_file_name = $break [ count ( $break ) - 1 ];
        $this -> working_directory = rtrim ( str_replace ( $executed_file_name, "", $_SERVER [ 'SCRIPT_FILENAME' ] ), "/" );
    }

    public function get_root ()
    {
        return $this -> working_directory;
    }

    public function get_home ()
    {

    }

    public function debug ()
    {
        print ( "<br>working directory: " . $this -> working_directory."<br>");
    }

    public function __destruct()
    {
        // TODO: Implement __destruct() method.
    }
}
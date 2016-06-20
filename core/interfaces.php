<?php
/**
 * Created by PhpStorm.
 * User: szabacsik
 * Date: 2016. 06. 05.
 * Time: 15:31
 */

namespace improwerk\implement\mvcd;

interface ibasic
{
    public function __construct ();
    public function __destruct ();
}


interface iadvanced
{
    public function __construct ( $object );
    public function __destruct ();
}

<?php
/**
 * Created by PhpStorm.
 * User: andra
 * Date: 2016. 06. 23.
 * Time: 13:52
 */

namespace improwerk\implement\mvcd;

class welcome implements ibasic
{
    public function __construct ()
    {
        Print ( "Welcome! You have reached the landing page." );
    }

    public function __destruct ()
    {
    }
}

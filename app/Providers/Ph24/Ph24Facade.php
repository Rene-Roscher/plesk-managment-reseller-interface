<?php
/**
 * Created by PhpStorm.
 * User: mrlog
 * Date: 09.09.2018
 * Time: 18:54
 */

namespace App\Providers\Ph24;


class Ph24Facade
{

    /**
     * @return Ph24 Client
     */
    public static function client()
    {
        return new Ph24('XXXX-XXXX-XXXX-XXXX-XXXX-XXXX-XXXX-XXXX-XXXX-XXXX-XXXX-XXXX');
    }

}
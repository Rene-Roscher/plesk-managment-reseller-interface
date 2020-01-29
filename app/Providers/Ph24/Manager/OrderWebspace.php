<?php
/**
 * Created by PhpStorm.
 * User: mrlog
 * Date: 09.09.2018
 * Time: 20:32
 */

namespace App\Providers\Ph24\Manager;


use App\Providers\Ph24\Ph24;

class OrderWebspace
{

    private $ph24;

    public function __construct(Ph24 $ph24)
    {
        $this->ph24 = $ph24;
    }

    /**
     * @param $disk : mb
     * @param $site : amount
     * @param $subdom : amount
     * @param $mail : amount
     * @param $db : amount
     * @param $ftp : amount
     * @param $domain : string
     * @param $runtime : integer (days)
     * @return bool|\Psr\Http\Message\ResponseInterface
     */
    public function create(int $disk, int $site, int $subdom, int $mail, int $db, int $ftp, string $domain, int $runtime)
    {
        return $this->ph24->get([
            'disk' => $disk,
            'site' => $site,
            'subdom' => $subdom,
            'mail' => $mail,
            'db' => $db,
            'ftp' => $ftp,
            'domain' => $domain,
            'runtime' => $runtime
        ], 'OrderWebspace');
    }

    /**
     * @param $id : Webspace id
     * @return bool|\Psr\Http\Message\ResponseInterface
     */
    public function get($id)
    {
        return $this->ph24->get([
            'id' => $id,
        ], 'OrderWebspace');
    }

}
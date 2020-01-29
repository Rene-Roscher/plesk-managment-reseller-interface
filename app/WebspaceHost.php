<?php
/**
 * Created by PhpStorm.
 * User: mrlog
 * Date: 03.09.2018
 * Time: 19:48
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use PleskX\Api\Client;

class WebspaceHost extends Model
{

    protected $table = "webspace_hosts";

    protected $fillable = [
        'title', 'ip_address', 'username', 'password', 'max_webspaces', 'webspaces',
    ];

    public static function getFreeHost(): WebspaceHost
    {
        $webhost = WebspaceHost::all()->first();

        if (intval($webhost->webspaces) >= intval($webhost->max_webspaces)) {
            throw new \ErrorException('no free webhost found.');
        }

        return $webhost;
    }

    public static function get($key, $value): WebspaceHost
    {
        return WebspaceHost::all()->where($key, $value)->first();
    }

    public function webspaces()
    {
        return $this->hasMany(Webspace::class, 'webhost_id');
    }

    public function autoLogin(Webspace $webspace)
    {
        return $this->api()->server()->createSession($webspace->plesk_username, request()->ip());
    }

    /**
     * @param $customerId
     * @param int $state | 1 = suspend, 0 unsuspend
     * @return \PleskX\Api\XmlResponse
     */
    public function suspend($customerId, $state = 1)
    {
        $res = '<packet version="1.6.3.0"><customer><set><filter><id>'.$customerId.'</id></filter><values><gen_info><status>'.$state.'</status></gen_info></values></set></customer></packet>';
        return $this->api()->request($res);
    }

    public function api(): Client
    {
        $client = new Client($this->ip_address);
        $client->setCredentials($this->username, $this->password);
        return $client;
    }

    public function deletePlan($plan_name)
    {
        return $this->api()->request('<packet>
<service-plan>
   <del>
   <filter>
   <name>'.$plan_name.'</name>
</filter>
   </del>
</service-plan>
</packet>');
    }

    public function createPlan($plan_name, $max_disk, $max_site, $max_subdom, $max_box, $max_db, $max_subftp_users)
    {
        return $this->api()->request('<packet>
<service-plan>
<add>
<name>' . $plan_name . '</name>
<limits>
<overuse>block</overuse>
<limit>
<name>disk_space</name>
<value>' . $this->convertToBytes($max_disk) . '</value>
</limit>
<limit>
<name>max_traffic</name>
<value>' . $this->convertToBytes(102400) . '</value>
</limit>
<limit>
<name>max_site</name>
<value>' . $max_site . '</value>
</limit>
<limit>
<name>max_subdom</name>
<value>' . $max_subdom . '</value>
</limit>
<limit>
<name>max_box</name>
<value>' . $max_box . '</value>
</limit>
<limit>
<name>max_db</name>
<value>' . $max_db . '</value>
</limit>
<limit>
<name>max_subftp_users</name>
<value>' . $max_subftp_users . '</value>
</limit>
</limits>
<performance>
<bandwidth>1000</bandwidth>
<max_connections>20</max_connections>
</performance>
</add>
</service-plan>
</packet>');

    }

    public function reconfigurePlan($plan_name, $max_disk, $max_site, $max_subdom, $max_box, $max_db, $max_subftp_users)
    {
        return $this->api()->request('<packet>
<service-plan>
   <set>
   <filter>
       <name>'.$plan_name.'</name>
   </filter>
   <limits>
<overuse>block</overuse>
<limit>
<name>disk_space</name>
<value>' . $this->convertToBytes($max_disk) . '</value>
</limit>
<limit>
<name>max_traffic</name>
<value>' . $this->convertToBytes(102400) . '</value>
</limit>
<limit>
<name>max_site</name>
<value>' . $max_site . '</value>
</limit>
<limit>
<name>max_subdom</name>
<value>' . $max_subdom . '</value>
</limit>
<limit>
<name>max_box</name>
<value>' . $max_box . '</value>
</limit>
<limit>
<name>max_db</name>
<value>' . $max_db . '</value>
</limit>
<limit>
<name>max_subftp_users</name>
<value>' . $max_subftp_users . '</value>
</limit>
</limits>
   </set>
</service-plan>
</packet>');
    }

    public function convertToBytes($input)
    {
        return 1073741824 / 1024 * $input;
    }

    public function createWebspace(Webspace $webspace, array $data)
    {

        $id = $data['id'];
        $username = 'web-' . $data['id'];
        $password = str_random(12);
        $plan = $data['plan-name'];
        if (isset($data['domain'])) {
            $domain = $username . '.' . $data['domain'];
        } else {
            $domain = $username . '.prohosting24.de';
        }

        $customerId = $this->api()->customer()->create([
            'cname' => $id,
            'pname' => $username,
            'login' => $username,
            'passwd' => $password,
            'email' => $username . '@prohosting24.de',
        ])->id;

        $webspace->save();

        sleep(0.3);

        $webspaceId = $this->api()->webspace()->create([
            'name' => $domain,
            'ip_address' => $this->ip_address,
            'owner-id' => $customerId,
        ], [
            'ftp_login' => $username,
            'ftp_password' => str_random(8),
        ], $plan)->id;

        $webspace->installed = true;
        $webspace->plesk_url = $domain;
        $webspace->plesk_customer_id = $customerId;
        $webspace->plesk_id = $webspaceId;
        $webspace->plesk_username = $username;
        $webspace->plesk_password = encrypt($password);
        $webspace->plan = $plan;
        $webspace->save();
    }

}
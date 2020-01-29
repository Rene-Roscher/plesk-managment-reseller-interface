<?php
/**
 * Created by PhpStorm.
 * User: mrlog
 * Date: 09.09.2018
 * Time: 18:22
 */

namespace App\Providers\Ph24;


use GuzzleHttp\Client;

class Ph24
{

    /**
     * @var Client
     */
    private $httpClient;
    private $url;
    private $token;

    /**
     * Ph24 constructor.
     * @param $token
     */
    public function __construct($token)
    {
        $this->token = $token;
        $this->url = 'https://rest.nexmo.com/sms/json/';
        $this->httpClient = new Client([
            'allow_redirects' => false,
            'timeout' => 120
        ]);
    }

    /**
     * @param array $params
     * @param $action
     * @return bool|\Psr\Http\Message\ResponseInterface
     */
    public function get(array $params, $action)
    {
        return $this->client($params, 'GET', $action);
    }

    public function post(array $params, $action)
    {
        return $this->client($params, 'POST', $action);
    }

    public function delete(array $params, $action)
    {
        return $this->client($params, 'DELETE', $action);
    }

    public function put(array $params, $action)
    {
        return $this->client($params, 'PUT', $action);
    }

    /**
     * @param array $params
     * @param $method
     * @param $action
     * @return bool|\Psr\Http\Message\ResponseInterface
     */
    private function client(array $params, $method, $action)
    {

        $params['config'] = [];
        $params['config']['timezone'] = 'UTC';
        $params = $this->formatValues($params);

        switch ($method) {
            case 'GET':
                return $this->request($this->httpClient->get($this->url.$action, [
                    'verify' => false,
                    'query'  => $params,
                ]));
                break;
            case 'POST':
                return $this->request($this->httpClient->post($this->url.$action, [
                    'verify' => false,
                    'form_params' => $params,
                ]));
                break;
            case 'PUT':
                return $this->request($this->httpClient->put($this->url.$action, [
                    'verify' => false,
                    'form_params' => $params,
                ]));
            case 'DELETE':
                return $this->request($this->httpClient->delete($this->url.$action, [
                    'verify' => false,
                    'form_params' => $params,
                ]));
            default:
                return false;
        }
    }

    /**
     * @param $response
     * @return mixed
     */
    private function request($response)
    {
        $response = $response->getBody()->__toString();
        $result = json_decode($response);
        if (json_last_error() == JSON_ERROR_NONE) {
            return $result;
        } else {
            return $response;
        }
    }

    /**
     * @param array $array
     * @return array
     */
    private function formatValues(array $array)
    {
        foreach ($array as $key => $item) {
            if (is_array($item)) {
                $array[$key] = self::formatValues($item);
            } else {
                if ($item instanceof \DateTime)
                    $array[$key] = $item->format("Y-m-d H:i:s");
            }
        }

        return $array;
    }

}
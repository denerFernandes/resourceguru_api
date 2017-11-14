<?php

/*
 * (c) Dener Fernandes <dener.php@gmail.com>
 * https://github.com/denerFernandes
 *
 * This source file is subject to the GNU license that is bundled
 * with this source code in the file LICENSE.
 */
namespace ResourceGuru;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;

/**
*
*/
class ResourceGuru{
    const API_URL = "https://api.resourceguruapp.com/v1";

    protected $client;
    protected $username;
    protected $password;
    protected $client_id;
    protected $client_secret;
    protected $access_token;

    public function __construct()
    {
       $this->client = new \GuzzleHttp\Client();
    }

    public function setToken($username,$password,$client_id,$client_secret)
    {
        $this->username         = $username;
        $this->password         = $password;
        $this->client_id        = $client_id;
        $this->client_secret    = $client_secret;
    }
    protected function prepareAccessToken()
    {
        try{
            $url = "https://api.resourceguruapp.com/oauth/token";
            $value = [
                'grant_type' => "password",
                'username'   => $this->username,
                'password'   => $this->password
            ];
            $header = array('Authorization'=>'Basic ' .base64_encode($this->client_id.":".$this->client_secret),
            "Content-Type"=>"application/json;charset=UTF-8");
            $response = $this->client->post($url, ['query' => $value,'headers' => $header]);
            $result = json_decode($response->getBody()->getContents());

            $this->accesstoken = $result->access_token;
        }
        catch (RequestException $e) {
            $response = $this->statusCodeHandling($e);
            return $response;
        }
    }
    protected function call($method,$request,$post = [])
    {
        try{
            $this->prepareAccessToken();
            $url = self::API_URL . $request;
            $header = array('Authorization'=>'Bearer ' . $this->access_token);
            $response = $this->client->request($method,$url, array('query' => $post,'headers' => $header));
            return json_decode($response->getBody()->getContents());
        } catch (RequestException $e) {
            $response = $this->StatusCodeHandling($e);
            return $response;
        }
    }
    protected function statusCodeHandling($e)
    {
        $response = array("statuscode" => $e->getResponse()->getStatusCode(),
        "error" => json_decode($e->getResponse()->getBody(true)->getContents()));
        return $response;
    }

}

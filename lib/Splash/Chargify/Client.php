<?php
/**
 *
 * @package Splash
 * @subpackage Chargify
 *
 * @copyright 2012 Splash Media, LP
 * @author Daniel Cousineau <dcousineau@splashmedia.com>
 */

namespace Splash\Chargify;

use Splash\Chargify\Client\Response;
use Splash\Chargify\Exception as ChargifyException;

class Client {
    protected $api_key;
    protected $domain;
    protected $site_shared_key;
    protected $curl;

    /**
     * @var ResponseHydrator
     */
    protected $hydrator;

    /**
     * @param string $api_key
     * @param string $domain
     * @param string|null $site_shared_key
     * @param resource|null $curl
     */
    public function __construct($api_key, $domain, $site_shared_key = null, $curl = null) {
        $this->api_key = $api_key;
        $this->domain = $domain;
        $this->site_shared_key = $site_shared_key;
        $this->curl = $curl;

        $this->setHydrator(new ResponseHydrator());
    }

    public function api($uri, $data = array(), $method = 'GET') {
        return $this->_execute_request($uri, $data, $method);
    }

    /**
     * @param $uri
     * @param array $data
     * @param string $method
     * @return Client\Response
     */
    protected function _execute_request($uri, $data = array(), $method = 'GET') {
        $ch = $this->getCurl();
        $url = sprintf('https://%s.chargify.com/%s', $this->getDomain(), ltrim($uri, '/'));

        curl_setopt_array($ch, array(
            CURLOPT_URL        => $url,
            CURLOPT_USERPWD    => sprintf("%s:x", $this->getApiKey()),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Accept: application/json',
            ),
        ));

        switch (strtoupper($method)) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                break;
            case 'PUT':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                break;
            case 'GET':
                break;
            default:
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        }

        $body = curl_exec($ch);

        $errno = curl_errno($ch);
        if ($errno !== 0) {
            throw new ChargifyException(sprintf("Error connecting to Chargify: [%s] %s", $errno, curl_error($ch)), $errno);
        }

        $body = json_decode($body, true);

        return $this->getHydrator()->hydrate($body);
    }


    /**
     * @param string $api_key
     * @return Client
     */
    public function setApiKey($api_key) {
        $this->api_key = $api_key;
        return $this;
    }

    /**
     * @return string
     */
    public function getApiKey() {
        return $this->api_key;
    }

    /**
     * @param resource $curl
     * @return Client
     */
    public function setCurl($curl) {
        $this->curl = $curl;
        return $this;
    }

    /**
     * @return resource|null
     */
    public function getCurl() {
        if ($this->curl === null) {
            $this->curl = curl_init();
            
            curl_setopt_array($this->curl, array(
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => 2,
                CURLOPT_FOLLOWLOCATION => false,
                CURLOPT_MAXREDIRS      => 1,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_TIMEOUT        => 30,
            ));
        }

        return $this->curl;
    }

    /**
     * @param string $domain
     * @return Client
     */
    public function setDomain($domain) {
        $this->domain = $domain;
        return $this;
    }

    /**
     * @return string
     */
    public function getDomain() {
        return $this->domain;
    }

    /**
     * @param string $site_shared_key
     * @return Client
     */
    public function setSiteSharedKey($site_shared_key) {
        $this->site_shared_key = $site_shared_key;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSiteSharedKey() {
        return $this->site_shared_key;
    }

    /**
     * @param ResponseHydrator $hydrator
     * @return Client
     */
    public function setHydrator(ResponseHydrator $hydrator) {
        $this->hydrator = $hydrator;
        return $this;
    }

    /**
     * @return ResponseHydrator
     */
    public function getHydrator() {
        return $this->hydrator;
    }


}
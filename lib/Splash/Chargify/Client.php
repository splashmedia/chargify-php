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

use Splash\Chargify\Resource\ResourceAbstract;
use Splash\Chargify\Exception as ChargifyException;

class Client {

    /** @var string */
    protected $api_key;

    /** @var string */
    protected $domain;

    /** @var string */
    protected $site_shared_key;

    /** @var resource */
    protected $curl;

    /** @var \Memcached */
    protected $cache;

    /**
     * @var ResponseHydrator
     */
    protected $hydrator;

    /**
     * @param string $api_key
     * @param string $domain
     * @param string|null $site_shared_key
     */
    public function __construct($api_key, $domain, $site_shared_key = null) {
        $this->api_key = $api_key;
        $this->domain = $domain;
        $this->site_shared_key = $site_shared_key;
        $this->setHydrator(new ResponseHydrator());
    }

    public function api($uri, $data = array(), $method = 'GET', $hydrate=true, $bust=false) {
        $r = $this->getCached($uri, $data, $method, $hydrate, $bust);
        if (false !== $r) return $r;

        $r = $this->_execute_request($uri, $data, $method, $hydrate);

        $this->cache($uri, $data, $method, $hydrate, $r);

        return $r;
    }

    /**
     * @param string $uri
     * @param array $data
     * @param string $method
     * @param boolean $hydrate
     * @return ResourceAbstract|array
     * @throws Exception
     */
    protected function _execute_request($uri, $data = array(), $method = 'GET', $hydrate=true) {
        $ch = $this->getCurl();
        $url = sprintf('https://%s.chargify.com/%s', $this->getDomain(), ltrim($uri, '/'));

        if (!empty($data) && $method == 'GET') {
            $url .= "?" . http_build_query($data);
        }

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
                curl_setopt($ch, CURLOPT_POST, false);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                break;
            case 'GET':
                curl_setopt($ch, CURLOPT_POST, false);
                break;
            default:
                curl_setopt($ch, CURLOPT_POST, false);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        }

        $body = curl_exec($ch);

        $errno = curl_errno($ch);
        if ($errno !== 0) {
            throw new ChargifyException(sprintf("Error connecting to Chargify: [%s] %s", $errno, curl_error($ch)), $errno);
        }

        $body = json_decode($body, true);

        if ( $hydrate ) {
            return $this->getHydrator()->hydrate($body);
        } else {
            return $body;
        }
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
     * @return $this
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
                //Using integer value instead constant because in CentOS
                //Curl uses NSS and there CURL_SSLVERSION_TLSv1_2 is undefined
                CURLOPT_SSLVERSION     => 6,
            ));
        }

        return $this->curl;
    }

    /**
     * @param \Memcached $memcached
     * @return $this
     */
    public function setMemcached(\Memcached $memcached) {
        $this->cache = $memcached;
        return $this;
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

    protected function getCached($uri, $data, $method, $hydrate, $bust)
    {
        if ($bust || !$this->cache) return false;

        $key = $this->getCacheKey($uri, $data, $method, $hydrate);
        if (!$key) return false;

        return $this->cache->get($key);
    }

    protected function cache($uri, $data, $method, $hydrate, $val)
    {
        if (!$this->cache) return false;

        $key = $this->getCacheKey($uri, $data, $method, $hydrate);
        if (!$key) return false;

        $this->cache->set($key, $val);
        if ('chargify.products' == $key) {
            // Also cache individual products
            foreach($val as $product) {
                $this->cache->set('chargify.product.' . $product->id, $product);
            }
        }

        return $val;
    }

    protected function getCacheKey($uri, $data, $method, $hydrate) {
        if (empty($data) && 'GET' === $method && true === $hydrate) {
            if ('products' === $uri) {
                return 'chargify.products';
            }

            if (0 === stripos($uri, 'products/')) {
                $id = substr($uri, 9);
                return 'chargify.product.' . $id;
            }
        }

        return null;
    }
}

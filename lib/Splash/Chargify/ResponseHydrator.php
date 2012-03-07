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

use Splash\Chargify\Exception as ChargifyException;

class ResponseHydrator {

    public function __construct() {

    }

    public function hydrate($data, $depth = -1) {

        $resp = array();
        foreach ((array)$data as $key => $value) {
            $cls = "Splash\\Chargify\\Resource\\" . $this->normalizeClassName($key);

            if (class_exists($cls))
                $resp[$key] = $cls::hydrate($value, $this);
            elseif ($depth !== 0)
                $resp[$key] = $this->hydrate($value, $depth - 1);
            else
                $resp[$key] = $value;
        }

        if (count($resp) == 1)
            return reset($resp);
        else
            return $resp;
    }


    protected function normalizeClassName($key) {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
    }
}
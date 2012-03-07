<?php
/**
 *
 * @package Splash
 * @subpackage Chargify
 *
 * @copyright 2012 Splash Media, LP
 * @author Daniel Cousineau <dcousineau@splashmedia.com>
 */

namespace Splash\Chargify\Resource;

use Splash\Chargify\ResponseHydrator;

abstract class ResourceAbstract implements \ArrayAccess {
    /**
     * @abstract
     * @return string
     */
    abstract public function getName();

    public static function hydrateFilter() { return array(); }
    public static function hydrate($data, ResponseHydrator $hydator) {
        $filter = static::hydrateFilter();

        $obj = new static();
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $obj->{$key} = $hydator->hydrate(array($key => $value), 0);
            } else {
                if (isset($filter[$key]))
                    $obj->{$key} = $filter[$key]($value);
                else
                    $obj->{$key} = $value;
            }
        }
        return $obj;
    }

    public function getJSON() {
        return json_encode(array($this->getName() => (array)$this));
    }

    public function offsetExists($offset) {
        return isset($this->{$offset});
    }

    public function offsetGet($offset) {
        return $this->{$offset};
    }

    public function offsetSet($offset, $value) {
        $this->{$offset} = $value;
    }

    public function offsetUnset($offset) {
        unset($this->{$offset});
    }
}
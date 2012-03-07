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

class Component extends ResourceAbstract {
    public $name;
    public $unit_name_;
    public $unit_price;
    public $pricing_scheme;
    public $prices;
    public $product_family_id;
    public $kind;
    public $price_per_unit_in_cents;
    public $archived;

    public function getName() { return 'component'; }
}
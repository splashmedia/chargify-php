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

class Product extends ResourceAbstract {
    public $price_in_cents;
    public $name;
    public $handle;
    public $description;
    public $product_family;
    public $accounting_code;
    public $interval_unit;
    public $interval;
    public $initial_charge_in_cents;
    public $trial_price_in_cents;
    public $trial_interval;
    public $trial_interval_unit;
    public $expiration_interval;
    public $expiration_interval_unit;
    public $return_url;
    public $return_params;
    public $require_credit_card;
    public $request_credit_card;
    public $created_at;
    public $updated_at;
    public $archived_at;

    public function getName() { return 'product'; }
    public static function hydrateFilter() { return array(
        'created_at' => function($value) { return new \DateTime($value); },
        'updated_at' => function($value) { return new \DateTime($value); },
        'archived_at' => function($value) { return new \DateTime($value); },
    ); }
}
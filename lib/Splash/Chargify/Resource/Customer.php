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

class Customer extends ResourceAbstract {
    public $first_name;
    public $last_name;
    public $address;
    public $organization;
    public $zip;
    public $state;
    public $id;
    public $country;
    public $city;
    public $reference;
    public $address_2;
    public $email;
    public $phone;
    public $created_at;
    public $updated_at;
    public $vat_number;

    public function getName() { return 'customer'; }
    public static function hydrateFilter() { return array(
        'created_at' => function($value) { return new \DateTime($value); },
        'updated_at' => function($value) { return new \DateTime($value); },
    ); }
}
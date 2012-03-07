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

class ProductFamily extends ResourceAbstract {
    public $name;
    public $handle;
    public $id;
    public $accounting_code;
    public $description;

    public function getName() { return 'product_family'; }
}
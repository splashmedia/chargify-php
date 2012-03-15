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

class Errors extends ResourceAbstract {
    public $errors = array();
    
    public function getName() { return 'errors'; }
    public static function hydrate($data) {
        $obj = new self();
        
        foreach ($data as $error) {
            $obj->errors[] = $error;
        }
    }
}
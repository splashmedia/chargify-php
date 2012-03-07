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

class Subscription extends ResourceAbstract {
    public $state;
    public $credit_card;
    public $current_period_started_at;
    public $next_assessment_at;
    public $balance_in_cents;
    public $signup_revenue;
    public $cancel_at_end_of_period;
    public $trial_ended_at;
    public $canceled_at;
    public $delayed_cancel_at;
    public $signup_payment_id;
    public $id;
    public $cancellation_message;
    public $coupon_code;
    public $trial_started_at;
    public $expires_at;
    public $current_period_ends_at;
    public $customer;
    public $product;
    public $previous_state;
    public $updated_at;
    public $created_at;
    public $activated_at;

    public function getName() { return 'subscription'; }
    public static function hydrateFilter() { return array(
        'created_at' => function($value) { return new \DateTime($value); },
        'updated_at' => function($value) { return new \DateTime($value); },
        'archived_at' => function($value) { return new \DateTime($value); },
        'expires_at' => function($value) { return new \DateTime($value); },
        'activated_at' => function($value) { return new \DateTime($value); },
        'current_period_started_at' => function($value) { return new \DateTime($value); },
        'current_period_ends_at' => function($value) { return new \DateTime($value); },
        'trial_ended_at' => function($value) { return new \DateTime($value); },
        'trial_started_at' => function($value) { return new \DateTime($value); },
    ); }
}
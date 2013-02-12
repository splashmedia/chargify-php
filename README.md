Chargify PHP Wrapper
===

This library is a simple wrapper for the [Chargify](http://chargify.com/) payment platform. Documentation for the Chargify API can be found at http://docs.chargify.com/

Installation
---

Installation is easiest with [Composer](http://getcomposer.org/). Just add the following to your requirements section:

```json
{
    "require": {
        "splashmedia/chargify-php": "dev-master"
    }
}
```

Alternatively you can autoload the library yourself as it complies with PSR-0 namespacing.

Usage
---

The client is relatively straightforward to use. First you must initialize the connection:

```php
<?php
$client = new \Splash\Chargify\Client(APIKEY, DOMAIN, SITESHAREDKEY);
```

Afterwards you may make calls to API endpoints as per the official chargify documentation:

```php
<?php
$data = array(
    'subscription' => array(
        'customer_attributes' => array(
            //...
        ),
        'payment_profile_attributes' => array(
            //...
        ),
    ),
);

/** @var $subscription \Splash\Chargify\Resource\Subscription **/
$subscription = $client->api('subscriptions', $data, 'POST');
```

The API will automatically hydrate Chargify API responses into the domain objects located in `lib/Splash/Chargify/Resource/`. You can optionally pass a 4th parameter into the `api()` method to disable hydration if you would prefer to work with the raw response array.
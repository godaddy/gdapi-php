gdapi-php
=========

A PHP client for Go Daddy&reg; REST APIs.

Requirements
---------
* PHP 5.3 or greater
* [libcurl](http://us3.php.net/curl) extension
* An account in a compatible service, such as [Cloud Servers&trade;](http://www.godaddy.com/hosting/cloud-computing.aspx)
* Your API Access and Secret key pair

Getting Started
--------
If you haven't already tried it, open up the base URL of the API you want to use in a browser.
Enter your Access Key as the username and your Secret Key as the password.
This interface will allow you to get familiar with what Resource types are available in the API
and what operations and actions you can perform on them.

Connecting
--------
```php
<?php
require_once('gdapi-php/init.php');
$url        = 'https://api.cloud.secureserver.net/v1/schemas';
$access_key = 'your-access-key';
$secret_key = 'your-secret-key';
    
$client = new \GDAPI\Client($url, $access_key, $secret_key);
?>
```

Listing Collections of Resources
--------
Each resource type in the API is available as a member of the Client.

### Listing all resources in a collection
```php
<?php
$machines = $client->virtualmachine->query();
foreach ( $machines as $machine )
{
  echo $machine->getName();
}
?>
```
    
### Filtering
Filters allow you to search a collection for resources matching a set of conditions.
```php
<?php
$http_balancers = $client->loadbalancers->query(array(
  'publicStartPort' => 80,
));

$privileged_portforwards = $client->portforwards->query(array(
  array('modifier' => 'lt', 'value' => 1024)
));
?>
```

Getting a single Resource
--------
```php
<?php
$machine = $client->virtualmachine->getById('your-vm-id');
?>
```

Creating Resources
--------
```php
<?php
$network = $client->network->create(array(
  'name'     => 'My Network',
  'domain'   => 'mynetwork.local',
  'ipv4Cidr' => '192.168.0.0/24'
));
?>
```

Editing Resources
--------

Deleting Resources
--------

Executing Actions
--------

Following Links
--------

Mapping response objects to your own class
--------

Handling Errors
--------


```php
<?php

?>
```
gdapi-php
=========

A PHP client for Go Daddy&reg; REST APIs.

Requirements
---------
* PHP 5.3 or greater
* [libcurl](http://us3.php.net/curl) PHP extension with SSL support
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

### Problems connecting
Consult the [SSL Problems](#ssl-problems) section if you get an error when creating the client that says something like this:
> SSL certificate problem, verify that the CA cert is OK.
> Details: error:14090086:SSL routines:SSL3_GET_SERVER_CERTIFICATE:certificate verify failed

Finding Resources
--------
Each resource type in the API is available as a member of the Client.

### Listing all resources in a collection
```php
<?php
$machines = $client->virtualmachine->query();
echo "There are " . count($machines) . " machines:\n";
foreach ( $machines as $machine )
{
  echo $machine->getName() . "\n";
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
  'publicStartPort' => array('modifier' => 'lt', 'value' => 1024)
));

$active_machines = $client->virtualmachine->query(array(
  'removed' => array('modifier' => 'null')
));
?>
```

### Getting a single Resource by ID
If you know the ID of the resource you are looking for already, you can also get it directly.
```php
<?php
$machine = $client->virtualmachine->getById('your-machine-id');
?>
```

Working with Resources
--------

### Accessing attributes
Resources have a getter method for each attribute, as "get"+{attribute name}.  Attributes that can be changed also have a "set"+{attribute name} method.  The first character of the attribute name may be capitalized for readability, but the rest of the name must match the API.

```php
<?php
$machine = $client->virtualmachine->getById('your-machine-id');

$privateIp = $machine->getPrivateIpv4Address(); // e.g. '10.1.1.3'
$size = $machine->getRamSizeMb(); // e.g. 1024
?>
```

### Making changes
```php
<?php
$machine = $client->virtualmachine->getById('your-machine-id');
$machine->setName('bigger machine');
$machine->setOffering('2gb-4cpu');

// Save the changes
$result = $machine->save();
?>
```

### Creating new resources
```php
<?php
$network = $client->network->create(array(
  'name'     => 'My Network',
  'domain'   => 'mynetwork.local',
  'ipv4Cidr' => '192.168.0.0/24'
));
?>
```

### Removing resources

With an instance of the resource:
```php
<?php
$machine = $client->virtualmachine->getById('your-machine-id');
$result = $machine->remove();
?>
```

Or statically:
```php
<?php
$result = $client->virtualmachine->remove('your-machine-id');
?>
```

### Executing Actions
Actions are used to perform operations that go beyond simple create/read/update/delete.  Resources have a "do"+{action name} method for each action.  The first character of the action name may be capitalized for readability, but the rest of the name must match the API.

```php
<?php
$machine = $client->virtualmachine->getById('your-machine-id');
$result = $machine->doRestart();
?>
```

Following Links
--------
Response collections and resources generally have a "links" attribute containing URLs to related resources and collections.  For example a virtual machine belongs to a network and has one or more volumes.  Resources have a "fetch"+{link name} method for each link.  Invoking this will return the linked resource
```php
<?php
$machine = $client->virtualmachine->getById('your-machine-id');
$network = $machine->fetchNetwork(); // Network resource
$volumes = $machine->fetchVolumes(); // Collection of Volume resources
?>
```

Handling Errors
--------
By default, any error response will be thrown as an exception.  The most general type of exception is \GDAPI\APIException, but several more specific types are defined in class/APIException.php.
```php
<?php
try
{
  $machine = $client->virtualmachine->getById('your-machine-id');
  echo "I found it";
}
catch ( \GDAPI\NotFoundException $e )
{
  echo "I couldn't find that machine";
}
catch ( \GDAPI\APIException $e )
{
  echo "Something else went wrong";
}
?>
```

If you prefer to not use exceptions, the client has an option to disable them.  When an error occurs, the response will be an instance of \GDAPI\Error.
```php
<?php
$machine = $client->virtualmachine->getById('your-machine-id');
if ( $machine instanceof \GDAPI\Error )
{
  if ( $machine->getStatus() == 404 )
  {
    echo "I couldn't find that machine";
  }
  else
  {
    echo "Something else went wrong: " . print_r($machine,true);
  }
}
else
{
  echo "I found it";
}
?>
```

Advanced Options
--------
### Mapping response objects to your own classes
By default all response objects are an instance of \GDAPI\Resource, \GDAPI\Collection, or \GDAPI\Error.  In many cases it is useful to map responses to your own classes and add your own behavior to them.
```php
<?php

class MyVM extends \GDAPI\Resource
{
  function getFQDN()
  {
    $network = $this->fetchNetwork();
    return $this->getName() . "." . $network->getDomain();
  }
}

class MyLoadBalancer extends \GDAPI\Resource
{
  function getFQDN()
  {
    $network = $this->fetchNetwork();
    return $this->getName() . "." . $network->getDomain();
  }
}

$classmap = array(
  'virtualmachine' => 'MyVM',
  'loadbalancer'   => 'MyLoadBalancer'
);

$client = new \GDAPI\Client($url, $access_key, $secret_key);

$machines = $client->virtualmachine->query();
echo "There are " . count($machines) . " machines:\n";
foreach ( $machines as $machine )
{
  echo $machine->getFQDN() ."\n";
}
?>
```

SSL Problems
--------
Some installations of libcurl do not come with certificates for any Certificate Authorities (CA). This client always verifies the certificate by default, but having no CA certificates means it won't be able to verify any SSL certificate.  To fix this problem, you need a list of CA certificates to trust.   Curl provides a copy that contains the same CA certs as Mozilla browsers: [cacert.pem](http://curl.haxx.se/ca/cacert.pem).

If you have permission to edit your php.ini, you can fix this globally for anything that uses the libcurl extension:

Add a line like this to your php.ini, then restart your web server, if applicable:
> curl.cainfo = /path/to/cacert.pem

If you don't have permission, or don't want to make a global change, you can configure just the GDAPI client to use the file:
```php
<?php
$options = array(
  'ca_cert' => '/path/to/cacert.pem'
);

$client = new \GDAPI\Client($url, $access_key, $secret_key, $options);
?>
```

### More options
For info on other options that are available, see the $defaults array in class/Client.php.

Orion Context Explorer PHP Framework
=============================
#### PHP 5 framework for [Orion Context Broker](https://github.com/telefonicaid/fiware-orion).

This is the code repository for the Orion Context Explorer PHP Framework.
It's possible see the running implementation here:

http://orionexplorer.com/

## Features
- Make some operations of [Orion Context Broker](https://github.com/telefonicaid/fiware-orion) [API Operations](https://docs.google.com/spreadsheet/ccc?key=0Aj_S9VF3rt5DdEhqZHlBaGVURmhZRDY3aDRBdlpHS3c#gid=0)  such as create, delete and update Entities.
- Register subscriptions.
- Build query and context using flexible functions
- Get information about your Instance, such as uptime and availability
- List all created Entities and filter them by type
- And more.



## Requirements

PHP 5.6+ with the cURL extension installed



## Basic Example ##
See the examples/ directory for examples of the key client features.
```PHP
<?php
  require_once 'examples/autoloader.php'; // or other way to load classes
  $OrionConn = new Orion\NGSIAPIv1("127.0.0.1");

    //Build your query Context
    $queryContext = new Orion\Operations\queryContext();
    $queryResponse = $queryContext->addElement(".*", "Room",true)
            ->send($OrionConn);

    $responseData = $queryResponse->get();

    //Simple
    echo "<h2>Basic</h2>";
    echo "<h3>Request : </h3>", PHP_EOL;
    echo "<pre>";
    $queryContext->getRequest()->prettyPrint();
    echo "</pre>";
    echo "<h3>Response: </h3>", PHP_EOL;
    echo "<pre>";
    $queryResponse->prettyPrint();
    echo "</pre>";
```

#Running Examples:
- You can use native php server 
```
cd example
php -S  localhost:8000
```
- And access http://localhost:8000


You will find all the information on Orion Context Broker in its page in the FI-WARE Catalogue:

http://catalogue.fi-ware.eu/enablers/publishsubscribe-context-broker-orion-context-broker




## How to help us?
- If you're involved with Orion Context Broker and have PHP, Javascript, Python Skills.
- If you believe that can help develop this tool somehow.

Please send a contact to j.leonancarvalho:cyclone:gmail.com and let's start it.


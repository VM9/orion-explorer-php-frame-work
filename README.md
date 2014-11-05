Orion Context Explorer PHP Frame Work
=============================
#### PHP 5 framework for Orion Context Broker.

This is the code repository for the Orion Context Explorer PHP Frame Work.
It's possible see the running implementation here:

http://orionexplorer.com/

## Features
- Make some operations of Orion Context Broker API.
- Create, Delete and update Entities.
- Register subscriptions.
- Build query and context using flexible functions
-  Get information about your Instance, such as uptime and availability
- List all created Entities and filter them by type
- And more.



## Requirements

PHP 5.3+ with the cURL extension installed



## Basic Example ##
See the examples/ directory for examples of the key client features.
```PHP
<?php
  require_once 'examples/autoloader.php'; // or other way to load classes
  $OrionContextBroker = new Orion\ContextBroker("127.0.0.1");

  $updateContext = new \Orion\Operations\updateContext();

  $updateContext->addElement("Room", "Room1", false)
                    ->addAttrinbute("temperature", "centigrade", "26.5")
                    ->addAttrinbute("pressure", "mmHg", "763")
                    ->addGeolocation(40.1234, 3.4321)
                ->addElement("Room", "Room2", false)
                    ->addAttrinbute("temperature", "centigrade", "26.5")
                    ->addAttrinbute("pressure", "mmHg", "763")
                    ->addGeolocation(40.1234, 3.4321)
                ->setAction("APPEND"); 

  $reqBody = $updateContext->getRequest();
  $raw_return = $OrionConnection->updateContext($reqBody);

  $Context = new Orion\Context\Context($raw_return);

  $ResponseObject = $Context->__toObject();

    echo "<pre>";
    foreach ($contextResponses as $contextElement) {
        echo "Entity ID: ", $contextElement->contextElement->id, PHP_EOL;
        echo "Entity Type: ", $contextElement->contextElement->type, PHP_EOL;
        echo "isPattern: ", $contextElement->contextElement->isPattern, PHP_EOL;
        $attributes = $contextElement->contextElement->attributes;
    
        echo "Attributes:", PHP_EOL;
    
        foreach ($attributes as $attr) {
            echo "Name: ", $attr->name, PHP_EOL;
            echo "Type: ", $attr->type, PHP_EOL;
            echo "Value: ", $attr->value, PHP_EOL;
        }
        
    }
    echo "</pre>";
```


You will find all the information on Orion Context Broker in its page in the FI-WARE Catalogue:

http://catalogue.fi-ware.eu/enablers/publishsubscribe-context-broker-orion-context-broker






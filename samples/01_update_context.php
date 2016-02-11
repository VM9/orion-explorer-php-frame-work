<?php

include './autoloader.php';

$ip = "0.0.0.0";

$OrionConnection = new Orion\ContextBroker($ip);


/**
 * Sample 01: 
 * 
 * UPDATE CONTEXT
 * https://forge.fi-ware.org/plugins/mediawiki/wiki/fiware/index.php/Publish/Subscribe_Broker_-_Orion_Context_Broker_-_User_and_Programmers_Guide#Update_context_elements
 * 
 */


//UpdateContext

//A simple update
/**
{
    "contextElements": [
        {
            "type": "Room",
            "isPattern": "false",
            "id": "Room1",
            "attributes": [
            {
                "name": "temperature",
                "type": "centigrade",
                "value": "26.5"
            },
            {
                "name": "pressure",
                "type": "mmHg",
                "value": "763"
            }
            ]
        }
    ],
    "updateAction": "UPDATE"
} 
 */

$UPDATE = new \Orion\Operations\updateContext();

$UPDATE->addElement("Room1", "Room", false)
        ->addAttrinbute("temperature", "centigrade", "26.5")
        ->addAttrinbute("pressure", "mmHg", "763")
        ->setAction("UPDATE"); 


//Its Necessary get the request body from build elements 
$reqBody = $UPDATE->getRequest();

//From Orion Connection you need use updateContext passing your request body.
//Its will return a raw data from server, depends of your chose about type will return XML or JSON string, by default JSON is used
$raw_return = $OrionConnection->updateContext($reqBody);

//If request return a successful response like  that you can do process this information using some tools

//A successfull response should looks like that:
/**
 * {
    "contextResponses": [
        {
            "contextElement": {
                "attributes": [
                    {
                        "name": "temperature",
                        "type": "centigrade",
                        "value": ""
                    },
                    {
                        "name": "pressure",
                        "type": "mmHg",
                        "value": ""
                    }
                ],
                "id": "Room1",
                "isPattern": "false",
                "type": "Room"
            },
            "statusCode": {
                "code": "200",
                "reasonPhrase": "OK"
            }
        }
    ]
}
 * 
 */


//Is possible to create a Context Object using Context Factory Classs
$Context = new Orion\Context\Context($raw_return);

//Is possible get data in Array Format
$array = $Context->__toArray();
//Or in Object format
$object = $Context->__toObject();

$contextResponses = $object->contextResponses; //Based on json response above using that a array will be returned

//For exemple to debug returned elements on contextResponses

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



//You can't mix APPEND and UPDATE in same request
// So the example below is possible see a append method with geolocation attribute
// In this same example 3 entities will be created
// more info: https://forge.fi-ware.org/plugins/mediawiki/wiki/fiware/index.php/Publish/Subscribe_Broker_-_Orion_Context_Broker_-_User_and_Programmers_Guide#Geolocation_capabilities

$APPEND = new \Orion\Operations\updateContext();

$APPEND->addElement("Room1", "Room", false)
            ->addAttrinbute("temperature", "centigrade", "26.5")
            ->addAttrinbute("pressure", "mmHg", "763")
            ->addGeolocation(getRandLat(), getRandLng())
        ->addElement("Room2", "Room", false)
            ->addAttrinbute("temperature", "centigrade", "26.5")
            ->addAttrinbute("pressure", "mmHg", "763")
            ->addGeolocation(getRandLat(), getRandLng());

//There is another way to define objects:
$lat = getRandLat();
$lng = getRandLng();
$APPEND->addElement("Room3", "Room", false);
$APPEND->addAttrinbute("temperature", "centigrade", "26.5");
$APPEND->addAttrinbute("pressure", "mmHg", "763");
//addGeolocation method Auto-generates "position" attribute with metadata using location WSG84(Like Orion Docs, see link above)
$APPEND->addGeolocation($lat, $lng);

        
        
$APPEND->setAction("APPEND"); 


//Its Necessary get the request body from build elements 
$reqBodyAPPEND = $APPEND->getRequest();

$OrionConnection->updateContext($reqBodyAPPEND); //I can use the same instance of orion connection



//Delete Entities
$DELETE = new \Orion\Operations\updateContext();

$DELETE->addElement("Room1", "Room", false)
        ->addAttrinbute("temperature", "centigrade", "26.5")
        ->addAttrinbute("pressure", "mmHg", "763")
        ->setAction("DELETE"); 


//Its Necessary get the request body from build elements 
$reqBody = $DELETE->getRequest();

//From Orion Connection you need use updateContext passing your request body.
//Its will return a raw data from server, depends of your chose about type will return XML or JSON string, by default JSON is used
$raw_return = $OrionConnection->updateContext($reqBody);
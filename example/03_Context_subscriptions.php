<?php

include './autoloader.php';

$ip = "0.0.0.0";

$OrionConnection = new Orion\ContextBroker($ip);


/**
 * Sample 03: 
 * 
 * CONTEXT SUBSCRIPTIONS
 * https://forge.fi-ware.org/plugins/mediawiki/wiki/fiware/index.php/Publish/Subscribe_Broker_-_Orion_Context_Broker_-_User_and_Programmers_Guide#Context_subscriptions
 * 
 */

//Create a Subscription



$raw_ontime = <<<EOF
{
    "entities": [
        {
            "type": "Room",
            "isPattern": "false",
            "id": "Room1"
        }
    ],
    "attributes": [
        "temperature",
        "pressure",
    ],
    "reference": "http://localhost:1028/accumulate",
    "duration": "P1M",
    "notifyConditions": [
        {
            "type": "ONTIMEINTERVAL",
            "condValues": [
                "PT10S"
            ]
        }
    ]
}
EOF;


$SUBSCRIPTIONS = new Orion\Operations\subscribeContext("http://localhost:1028/accumulate", "P1M"); //Set url and duration


//On Time Interval
$SUBSCRIPTIONS->addElement("Room1", "Room")
        ->addAttr("temperature")
        ->addAttr("pressure")
        ->notifyConditions("ONTIMEINTERVAL","PT10S");



$raw_onchange = <<<EOF
{
    "entities": [
        {
            "type": "Room",
            "isPattern": "false",
            "id": "Room1"
        }
    ],
    "attributes": [
        "temperature"
    ],
    "reference": "http://localhost:1028/accumulate",
    "duration": "P1M",
    "notifyConditions": [
        {
            "type": "ONCHANGE",
            "condValues": [
                "pressure",
                "temperature"
            ]
        }
    ],
    "throttling": "PT5S"
}
EOF;

//ONChange
$SUBSCRIPTIONS->addElement("Room1", "Room")
        ->addAttr("temperature")
        ->notifyConditions("ONCHANGE", array("pressure","temperature"),"PT5S");
            //Multiples attributes can be sent as array on 2nd param
            //is possible set a throttling value as 3rd param


//To send Subscription to server is the same as queryContext

//You must get request body
$reqBody = $SUBSCRIPTIONS->getRequest();

//And send to server using Orion Conection instance
$raw_return = $OrionConnection->subscribeContext($reqBody);

//A successfull response should looks like that:
$return_expected = <<<EOF
{
    "subscribeResponse": {
        "duration": "P1M",
        "subscriptionId": "51c04a21d714fb3b37d7d5a7"
    }
}
EOF;


//Is possible to create a Context Object using Context Factory Classs
$Context = new Orion\Context\Context($raw_return);

//Is possible get data in Array Format
$array = $Context->__toArray();
//Or in Object format
$object = $Context->__toObject();

$contextResponses = $object->contextResponses; //Based on json response above using that a array will be returned

$info = $contextResponses->statusCode; //Some info about this request


var_dump($info);
var_dump($contextResponses);


//Update a Subscription

$update_subs_raw = <<<EOF
{
    "subscriptionId": "51c04a21d714fb3b37d7d5a7",
    "notifyConditions": [
        {
            "type": "ONTIMEINTERVAL",
            "condValues": [
                "PT5S"
            ]
        }
    ]
}
EOF;

$UPDATESUBSCRIPTION = new Orion\Operations\updateSubscription("538ce3419890cd828b9127b5");
$UPDATESUBSCRIPTION->notifyConditions("ONTIMEINTERVAL", "PT5S");

//UnsubscribeContext
// To unsubscribe a context you need send subscription ID using unsubscribeContext method on Orion Conection instance

$return = $OrionConnection->unsubscribeContext("538ce3419890cd828b9127b5");

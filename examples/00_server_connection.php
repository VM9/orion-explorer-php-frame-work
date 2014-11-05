<?php

include './autoloader.php';

/**
 * Sample 00
 * 
 * ORION CONNECTION 
 */
/**
 * Setup Orion Conection
 * @author      Leonan Carvalho <j.leonancarvalho@gmail.com>
 */
$ip = "0.0.0.0";

//Simple Connection
http://0.0.0.0:1026/NGSI10

$OrionSimpleIP = new Orion\ContextBroker($ip);


$OrionSimpleHostname = new Orion\ContextBroker("orion.example.com");

//Changing port and API Alias
// http://0.0.0.0:1024/v1
$OrionAPI = new Orion\ContextBroker($ip, 1024, "v1");


//Changing type of will change Content-Type and Accept headers
//This 1st version doesn't support xml returns, 
//you'll be able to send an receive requests but not to use context factory to build you orion objects.

$OrionXML = new Orion\ContextBroker($ip, 1024, "v1", "application/xml");


//Experimental Use: ORION CONTEXT BROKER Authentication
//NOTE: Orion don't have your own authentication mode, but you can implements Oauth authentication
//Take a look on https://github.com/fgalan/oauth2-example-orion-client and see a example of this implementation
$OrionToken = new Orion\ContextBroker($ip);
$OrionToken->setToken("X-Auth-Token", "HASHTOKEN-auth_token");


/**
 *  Connection Functions
 */

/* *
 * Chek if is possible to connect to server
 * 
 * This method checks IP connectivity using a socket connection.
 * Is used a timeout very low to not delay responses that use it 
 * Any authentication will be ignored.
 * If a Firewall is applied may this test will fail.

 */

$OrionSimpleIP->checkStatus();


/**
 * Return some info about your connection
 * This method uses /version from API 
 */

$OrionSimpleIP->serverInfo();

/**
 * Check server version
 * 
 * This method checks server version with a determined logical operation
 */
$OrionSimpleIP->checkVersion("0.15.0", "="); //IF version is equals to 0.15.0 ( You can omit op string for equal operations)
$OrionSimpleIP->checkVersion("0.15.0", "!="); //IF version is NOT equals to 0.15.0
$OrionSimpleIP->checkVersion("0.15.0", ">"); //IF version is greater than 0.15.0
$OrionSimpleIP->checkVersion("0.15.0", ">="); //IF version is greater or equals to 0.15.0
$OrionSimpleIP->checkVersion("0.15.0", "<"); //IF version is less than 0.15.0
$OrionSimpleIP->checkVersion("0.15.0", "<="); //IF version is greater or equals to 0.15.0



/**
 * Get Entities from your Server Connection
 */
//Get All Entities
$OrionSimpleIP->getEntities();

//Get entities from "Entitytype" with offset 10 and limit of 100, with details OFF
$OrionSimpleIP->getEntities('EntityType', 10, 100, "off");


/**
 * This method build a "gridview" like database view, where attributes and
 *  ID are colums with their respective values as rows for each entity
 * With this way is possible shows entity context type as database tables
 */
$OrionSimpleIP->getEntityAttributeView();

$OrionSimpleIP->getEntities('EntityType', 10, 100, "off");

//Get a list of All Entity Types Only Suported by Orion Context Broker version 0.15.0 or greater

$OrionSimpleIP->getEntityTypes();


/**
 * NGSI10 Standard Operations will be explained in their own examples
 */
//
//$OrionSimpleIP->updateContext($reqBody);
//$OrionSimpleIP->queryContext($reqBody, $limit, $offset, $details);
//$OrionSimpleIP->subscribeContext($reqBody);
//$OrionSimpleIP->unsubscribeContext($subscriptionId);
//$OrionSimpleIP->updateContextSubscription($reqBody);



/**
 * Convenience Operations 
 * Details : https://docs.google.com/spreadsheet/ccc?key=0Aj_S9VF3rt5DdEhqZHlBaGVURmhZRDY3aDRBdlpHS3c#gid=0
 */
$reqBody = ""; //Should be a json or XML sting
/**
 * 
$OrionSimpleIP->convenienceDELETE("contextSubscriptions/{subscriptionID}"); //Execute a DELETE Request
$OrionSimpleIP->convenienceGet("contextEntities"); // Execute a DELETE request
$OrionSimpleIP->conveniencePOST("contextEntities", $reqBody); //Execute a POST request
$OrionSimpleIP->conveniencePUT("contextEntities/{EntityID*}", $reqBody); //Execute a PUT request
 * 
*/


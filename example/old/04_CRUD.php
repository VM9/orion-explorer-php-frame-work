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
$ip = "192.168.1.20";
echo "<pre>";
try {
    $orion = new Orion\ContextBroker($ip);
    $OrionStatus = ($orion->checkStatus() ? "Up" : "Down");

    echo "Service Status {$OrionStatus}", PHP_EOL;

    $ServerInfo = $orion->serverInfo();
    echo "Version: {$ServerInfo['version']}", PHP_EOL;
    echo "Uptime: {$ServerInfo['uptime']}", PHP_EOL,PHP_EOL;


    echo "UpdateContext: APPEND",PHP_EOL;
    $UPDATE = new \Orion\Operations\updateContext();
   
   $contextResponses =  $UPDATE->addElement("Room".rand(1,400), "Room", false)
            ->addAttrinbute("temperature", "centigrade", rand(10, 999))
            ->addAttrinbute("pressure", "mmHg", rand(10, 999))
            ->setAction("UPDATE")
            ->send($orion);
   echo PHP_EOL,"UpdatecontextResponses: ",PHP_EOL;
   var_dump($contextResponses->get());
    
    echo "queryContext .* Room : ",PHP_EOL;
    $queryContext = new Orion\Operations\queryContext();
    $queryResponse = $queryContext->addElement(".*", "Room", true)
                                  ->send($orion); 
    $responseData = $queryResponse->get();
//    var_dump($responseData);
//    exit;
    foreach ($responseData as $contextElement) {
//        var_dump($contextElement->contextElement);continue;
        echo "\tEntity ID: ", $contextElement->contextElement->id, PHP_EOL;
        echo "\tEntity Type: ", $contextElement->contextElement->type, PHP_EOL;
        echo "\tisPattern: ", $contextElement->contextElement->isPattern, PHP_EOL;
        $attributes = $contextElement->contextElement->attributes;

        echo "\tAttributes:", PHP_EOL;

        foreach ($attributes as $attr) {
            echo "\t Name: ", $attr->name, PHP_EOL;
            echo "\t Type: ", $attr->type, PHP_EOL;
            echo "\t Value: ", $attr->value, PHP_EOL;
        }
        echo PHP_EOL;
    }
//Its Necessary get the request body from build elements 
//$reqBody = $UPDATE->getRequest();
//var_dump($reqBody);exit;
//From Orion Connection you need use updateContext passing your request body.
//Its will return a raw data from server, depends of your chose about type will return XML or JSON string, by default JSON is used
//$raw_return = $orionection->updateContext($reqBody);
    echo "List ALL Entities",PHP_EOL;
    $ServerEntities = $orion->getEntities();
//    var_dump($ServerEntities);
    $lastType = "";
    foreach ($ServerEntities as  $type => $entities) {
        
        if($lastType != $type){
            echo "\tType:",$type,PHP_EOL;
            $lastType = $type;
        }
        
        if(count($entities) > 0 ){
            foreach ($entities as $entityId => $attributes) {
                  echo "\t  ID:",$entityId,PHP_EOL;
                  if(count($attributes)>0){
                      echo "\t   Name | Type | Value",PHP_EOL;
                      foreach ($attributes as $attr) {
                          echo "\t   {$attr->name} | {$attr->type} | {$attr->value}",PHP_EOL;
                      }
                  }else{
                      echo "\t\t No attributes",PHP_EOL;
                  }
            }
        }else{
            echo "\tNo Entities";
        }
//        
//        
//        echo "Entity ID: ", $contextElement->contextElement->id, PHP_EOL;
//        echo "Entity Type: ", $contextElement->contextElement->type, PHP_EOL;
//        echo "isPattern: ", $contextElement->contextElement->isPattern, PHP_EOL;
//        $attributes = $contextElement->contextElement->attributes;
//
//        echo "Attributes:", PHP_EOL;
//
//        foreach ($attributes as $attr) {
//            echo "Name: ", $attr->name, PHP_EOL;
//            echo "Type: ", $attr->type, PHP_EOL;
//            echo "Value: ", $attr->value, PHP_EOL;
//        }
    }

    echo "</pre>";
} catch (\Exception $e) {
    echo "<h1>", get_class($e), "</h1><h2>", $e->getMessage(), "</h2>";
    echo $e->getFile(), " [", $e->getLine(), "]<br>";
    echo "<pre>", $e->getTraceAsString(), "</pre>";
}
exit;
//
//$OrionSimpleHostname = new Orion\ContextBroker("orion.example.com");
//
////Changing port and API Alias
//// http://0.0.0.0:1024/v1
//$OrionAPI = new Orion\ContextBroker($ip, 1024, "v1");
//
//
////Changing type of will change Content-Type and Accept headers
////This 1st version doesn't support xml returns, 
////you'll be able to send an receive requests but not to use context factory to build you orion objects.
//
//$OrionXML = new Orion\ContextBroker($ip, 1024, "v1", "application/xml");
//
///** Deprecated **/
////Experimental Use: ORION CONTEXT BROKER Authentication
////NOTE: Orion don't have your own authentication mode, but you can implements Oauth authentication
////Take a look on https://github.com/fgalan/oauth2-example-orion-client and see a example of this implementation
////$OrionToken = new Orion\ContextBroker($ip);
////$OrionToken->setToken("X-Auth-Token", "HASHTOKEN-auth_token");
//
//
//// Multi tenancy, Autentication and Service Path
//
//$CustonHeaders = array(
//    "Fiware-Service"=>"t_02",
//    "Fiware-ServicePath" => '/Madrid/Gardens/ParqueNorte/Parterre1', #Tip: Use ' instead " for values with /
//    "X-Auth-Token" => "HASHTOKEN-auth_token",
//    "Whatever" => "foo"
//    ); 
//
//$OrionWithHeaders = new Orion\ContextBroker($ip, 1024, "v1", "application/json", $CustonHeaders);
//
//$OrionWithHeaders->setHeader("Fiware-Service", "t_02"); //To Change service after some operation...



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

var_dump($orion->checkStatus());


/**
 * Return some info about your connection
 * This method uses /version from API 
 */
var_dump($orion->serverInfo());
//exit;

/**
 * Check server version
 * 
 * This method checks server version with a determined logical operation
 */
$orion->checkVersion("0.15.0", "="); //IF version is equals to 0.15.0 ( You can omit op string for equal operations)
$orion->checkVersion("0.15.0", "!="); //IF version is NOT equals to 0.15.0
$orion->checkVersion("0.15.0", ">"); //IF version is greater than 0.15.0
$orion->checkVersion("0.15.0", ">="); //IF version is greater or equals to 0.15.0
$orion->checkVersion("0.15.0", "<"); //IF version is less than 0.15.0
$orion->checkVersion("0.15.0", "<="); //IF version is greater or equals to 0.15.0



/**
 * Get Entities from your Server Connection
 */
//Get All Entities
var_dump($orion->getEntities());
exit;
//Get entities from "Entitytype" with offset 10 and limit of 100, with details OFF
$orion->getEntities('EntityType', 10, 100, "off");


/**
 * This method build a "gridview" like database view, where attributes and
 *  ID are colums with their respective values as rows for each entity
 * With this way is possible shows entity context type as database tables
 */
$orion->getEntityAttributeView();

$orion->getEntities('EntityType', 10, 100, "off");

//Get a list of All Entity Types Only Suported by Orion Context Broker version 0.15.0 or greater

$orion->getEntityTypes();


/**
 * NGSI10 Standard Operations will be explained in their own examples
 */
//
//$orion->updateContext($reqBody);
//$orion->queryContext($reqBody, $limit, $offset, $details);
//$orion->subscribeContext($reqBody);
//$orion->unsubscribeContext($subscriptionId);
//$orion->updateContextSubscription($reqBody);



/**
 * Convenience Operations 
 * Details : https://docs.google.com/spreadsheet/ccc?key=0Aj_S9VF3rt5DdEhqZHlBaGVURmhZRDY3aDRBdlpHS3c#gid=0
 */
$reqBody = ""; //Should be a json or XML sting
/**
 * 
$orion->convenienceDELETE("contextSubscriptions/{subscriptionID}"); //Execute a DELETE Request
$orion->convenienceGet("contextEntities"); // Execute a DELETE request
$orion->conveniencePOST("contextEntities", $reqBody); //Execute a POST request
$orion->conveniencePUT("contextEntities/{EntityID*}", $reqBody); //Execute a PUT request
 * 
*/


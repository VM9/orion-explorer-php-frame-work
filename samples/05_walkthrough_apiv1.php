<style>
    pre {
        display:block;
        font:normal 12px/22px Monaco,Monospace !important;
        color:#CFDBEC;
        background-color:#2f2f2f;
        background-image:-webkit-repeating-linear-gradient(top, #444 0px, #444 22px, #2f2f2f 22px, #2f2f2f 44px);
        background-image:-moz-repeating-linear-gradient(top, #444 0px, #444 22px, #2f2f2f 22px, #2f2f2f 44px);
        background-image:-ms-repeating-linear-gradient(top, #444 0px, #444 22px, #2f2f2f 22px, #2f2f2f 44px);
        background-image:-o-repeating-linear-gradient(top, #444 0px, #444 22px, #2f2f2f 22px, #2f2f2f 44px);
        background-image:repeating-linear-gradient(top, #444 0px, #444 22px, #2f2f2f 22px, #2f2f2f 44px);
        padding:0em 1em;
        overflow:auto;
    }
</style>
<?php
include './autoloader.php';

/**
 * Sample 05
 * 
 * Operations based on API V1 Walkthrough
 * https://fiware-orion.readthedocs.io/en/develop/user/walkthrough_apiv1/index.html
 */
/**
 * Setup Orion Conection
 * @author      Leonan Carvalho <j.leonancarvalho@gmail.com>
 */
$ip = "192.168.1.20";

try {
    //First of all we need create a instance of "Orion ContextBroker Connection"
    $OrionConn = new Orion\ContextBroker($ip);
    $OrionStatus = ($OrionConn->checkStatus() ? "Up" : "Down");

    echo "<h1>Service Status {$OrionStatus}</h1>", PHP_EOL;
    $ServerInfo = $OrionConn->serverInfo();
    echo "<p>";
    echo "Version: {$ServerInfo['version']}<br>", PHP_EOL;
    echo "Uptime: {$ServerInfo['uptime']}", PHP_EOL, PHP_EOL;
    echo "<p>";
    /**
     * Entity Creation
     * Ref: https://fiware-orion.readthedocs.io/en/develop/user/walkthrough_apiv1/index.html#entity-creation
     */
    echo "<h1>Entity Creation:</h1>", PHP_EOL;
    $Create = new \Orion\Operations\updateContext();

    $contextResponses = $Create->addElement("Room1", "Room", false)
            ->addAttrinbute("temperature", "float", "23")
            ->addAttrinbute("pressure", "integer", "720")
            ->setAction("APPEND")
            ->send($OrionConn); //To send it you must give the orion connection as parameter
//            $UPDATE->send($OrionConn2);//You also can work with 2 connections using this way, sending same entity to 2 different instances
    echo "<h3>Request: </h3>", PHP_EOL;
    echo "<pre>";
    $Create->getRequest()->prettyPrint();
    echo "</pre>";
    echo "<h3>Response: </h3>", PHP_EOL;
    echo "<pre>";
    $contextResponses->prettyPrint();
    echo "</pre>";

    /**
     * Query Context operation
     * Ref: https://fiware-orion.readthedocs.io/en/develop/user/walkthrough_apiv1/index.html#query-context-operation
     */
    echo "<h1>Query Context operation</h1>", PHP_EOL;
    $queryContext = new Orion\Operations\queryContext();
    $queryResponse = $queryContext->addElement("Room1", "Room")
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

    //With attribute params
    echo "<h2>With specified attribute and  regular expression for the entity ID</h2>";
    $queryContextAttr = new Orion\Operations\queryContext();
    $queryResponseAttr = $queryContextAttr->addElement("Room1", "Room")
            ->addAttr("temperature")
            ->send($OrionConn);
    $responseDataAttr = $queryResponseAttr->get();
    echo "<h3>Request(with attribute): </h3>", PHP_EOL;
    echo "<pre>";
    $queryContextAttr->getRequest()->prettyPrint();
    echo "</pre>";
    echo "<h3>Response (with attribute): </h3>", PHP_EOL;
    echo "<pre>";
    $queryResponseAttr->prettyPrint();
    echo "</pre>";

    //Non-existing Entity and patern
    echo "<h2>Non-existing Entity and patern</h2>";
    $queryContextAttr = new Orion\Operations\queryContext();
    $queryResponseAttr = $queryContextAttr->addElement(".*", "Room")
            ->addAttr("temperature")
            ->send($OrionConn);
    $responseDataAttr = $queryResponseAttr->get();
    echo "<h3>Request: </h3>", PHP_EOL;
    echo "<pre>";
    $queryContextAttr->getRequest()->prettyPrint();
    echo "</pre>";
    echo "<h3>Response: </h3>", PHP_EOL;
    echo "<pre>";
    $queryResponseAttr->prettyPrint();
    echo "</pre>";

    /**
     * Update context elements
     * Ref: https://fiware-orion.readthedocs.io/en/develop/user/walkthrough_apiv1/index.html#update-context-elements
     */
    echo "<h1>Update context elements:</h1>", PHP_EOL;
    $UPDATE = new \Orion\Operations\updateContext();

    $contextResponses = $UPDATE->addElement("Room1", "Room", false)
            ->addAttrinbute("temperature", "float", "26.5")
            ->addAttrinbute("pressure", "integer", "763")
            ->setAction("UPDATE")
            ->send($OrionConn); //To send it you must give the orion connection as parameter
//            $UPDATE->send($OrionConn2);//You also can work with 2 connections using this way, sending same entity to 2 different instances
    echo "<h3>Request: </h3>", PHP_EOL;
    echo "<pre>";
    $UPDATE->getRequest()->prettyPrint();
    echo "</pre>";
    echo "<h3>Response: </h3>", PHP_EOL;
    echo "<pre>";
    $contextResponses->prettyPrint();
    echo "</pre>";

    /**
     * Context subscriptions
     * Ref: https://fiware-orion.readthedocs.io/en/develop/user/walkthrough_apiv1/index.html#context-subscriptions
     */
    echo "<h1>Context subscriptions</h1>", PHP_EOL;
    $subscribeContext = new \Orion\Operations\subscribeContext("http://localhost:1028/accumulate", "P1M");
    $subscribeResponse = $subscribeContext->addElement("Room1", "Room", false)
            ->addAttr("temperature")
            ->notifyConditions("ONCHANGE", ["pressure"])
            ->setThrottling("PT5S")
            ->send($OrionConn);
    echo "<h3>Request: </h3>", PHP_EOL;
    echo "<pre>";
    $subscribeContext->getRequest()->prettyPrint();
    echo "</pre>";
    echo "<h3>Response: </h3>", PHP_EOL;
    echo "<pre>";
    $subscribeResponse->prettyPrint();
    echo "</pre>";

//    echo "<h1>Update subscription </h1>", PHP_EOL;
//    
//    $UPDATESUBSCRIPTION = new Orion\Operations\updateSubscription($subscribeResponse->get()->subscribeResponse->subscriptionId);
//    $UPDATESUBSCRIPTION->setDuration();
//    
//    echo "<h3>Response: </h3>", PHP_EOL;
//    echo "<pre>";
//    
//    echo $OrionConn->unsubscribeContext($subscribeResponse->get()->subscribeResponse->subscriptionId);
//    echo "</pre>";

    echo "<h1>Context Unsubscribe subscription </h1>", PHP_EOL;
    echo "<h3>Response: </h3>", PHP_EOL;
    echo "<pre>";
    
    echo $OrionConn->unsubscribeContext($subscribeResponse->get()->subscribeResponse->subscriptionId);
    echo "</pre>";


    //TODO: -> Update subscription(duration, adequar ao novo modelo etc)
    //TODO: -> NGSI10 convenience operations

//    var_dump($responseData);
    exit;
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
//$raw_return = $OrionConnection->updateContext($reqBody);
    echo "List ALL Entities", PHP_EOL;
    $ServerEntities = $OrionConn->getEntities();
//    var_dump($ServerEntities);
    $lastType = "";
    foreach ($ServerEntities as $type => $entities) {

        if ($lastType != $type) {
            echo "\tType:", $type, PHP_EOL;
            $lastType = $type;
        }

        if (count($entities) > 0) {
            foreach ($entities as $entityId => $attributes) {
                echo "\t  ID:", $entityId, PHP_EOL;
                if (count($attributes) > 0) {
                    echo "\t   Name | Type | Value", PHP_EOL;
                    foreach ($attributes as $attr) {
                        echo "\t   {$attr->name} | {$attr->type} | {$attr->value}", PHP_EOL;
                    }
                } else {
                    echo "\t\t No attributes", PHP_EOL;
                }
            }
        } else {
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
    echo "<h1>", get_class($e), "</h1><h3>", $e->getMessage(), "</h3>";
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
////Take a look on https://github.com/fgalan/oauth3-example-orion-client and see a example of this implementation
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

var_dump($OrionConn->checkStatus());


/**
 * Return some info about your connection
 * This method uses /version from API 
 */
var_dump($OrionConn->serverInfo());
//exit;

/**
 * Check server version
 * 
 * This method checks server version with a determined logical operation
 */
$OrionConn->checkVersion("0.15.0", "="); //IF version is equals to 0.15.0 ( You can omit op string for equal operations)
$OrionConn->checkVersion("0.15.0", "!="); //IF version is NOT equals to 0.15.0
$OrionConn->checkVersion("0.15.0", ">"); //IF version is greater than 0.15.0
$OrionConn->checkVersion("0.15.0", ">="); //IF version is greater or equals to 0.15.0
$OrionConn->checkVersion("0.15.0", "<"); //IF version is less than 0.15.0
$OrionConn->checkVersion("0.15.0", "<="); //IF version is greater or equals to 0.15.0



/**
 * Get Entities from your Server Connection
 */
//Get All Entities
var_dump($OrionConn->getEntities());
exit;
//Get entities from "Entitytype" with offset 10 and limit of 100, with details OFF
$OrionConn->getEntities('EntityType', 10, 100, "off");


/**
 * This method build a "gridview" like database view, where attributes and
 *  ID are colums with their respective values as rows for each entity
 * With this way is possible shows entity context type as database tables
 */
$OrionConn->getEntityAttributeView();

$OrionConn->getEntities('EntityType', 10, 100, "off");

//Get a list of All Entity Types Only Suported by Orion Context Broker version 0.15.0 or greater

$OrionConn->getEntityTypes();


/**
 * NGSI10 Standard Operations will be explained in their own examples
 */
//
//$OrionConn->updateContext($reqBody);
//$OrionConn->queryContext($reqBody, $limit, $offset, $details);
//$OrionConn->subscribeContext($reqBody);
//$OrionConn->unsubscribeContext($subscriptionId);
//$OrionConn->updateContextSubscription($reqBody);



/**
 * Convenience Operations 
 * Details : https://docs.google.com/spreadsheet/ccc?key=0Aj_S9VF3rt5DdEhqZHlBaGVURmhZRDY3aDRBdlpHS3c#gid=0
 */
$reqBody = ""; //Should be a json or XML sting
/**
 * 
$OrionConn->convenienceDELETE("contextSubscriptions/{subscriptionID}"); //Execute a DELETE Request
$OrionConn->convenienceGet("contextEntities"); // Execute a DELETE request
$OrionConn->conveniencePOST("contextEntities", $reqBody); //Execute a POST request
$OrionConn->conveniencePUT("contextEntities/{EntityID*}", $reqBody); //Execute a PUT request
 * 
*/


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

    echo "<h1>Update subscription </h1>", PHP_EOL;
//    
    $UPDATEsubscribeContext = new Orion\Operations\updateSubscription($subscribeResponse->get()->subscribeResponse->subscriptionId);
    $UPDATEsubscribeResponse = $UPDATEsubscribeContext->setDuration("P1M")->send($OrionConn);

    echo "<h3>Request: </h3>", PHP_EOL;
    echo "<pre>";
    $UPDATEsubscribeContext->getRequest()->prettyPrint();
    echo "</pre>";
    echo "<h3>Response: </h3>", PHP_EOL;
    echo "<pre>";
    $UPDATEsubscribeResponse->prettyPrint();
    echo "</pre>";

    echo "<h1>Context Unsubscribe subscription </h1>", PHP_EOL;
    echo "<h3>Response: </h3>", PHP_EOL;
    echo "<pre>";
    
    echo $OrionConn->unsubscribeContext($subscribeResponse->get()->subscribeResponse->subscriptionId);
    echo "</pre>";


    //TODO: -> NGSI10 convenience operations
} catch (\Exception $e) {
    echo "<h1>", get_class($e), "</h1><h3>", $e->getMessage(), "</h3>";
    echo $e->getFile(), " [", $e->getLine(), "]<br>";
    echo "<pre>", $e->getTraceAsString(), "</pre>";
}
?>
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
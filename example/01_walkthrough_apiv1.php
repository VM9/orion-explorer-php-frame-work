<h1><strong>FIWARE NGSI APIv1 Walkthrough</strong></h1>
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

try {
    //First of all we need create a instance of "Orion ContextBroker Connection"
    $orion = new Orion\NGSIAPIv1($ip);
    $OrionStatus = ($orion->checkStatus() ? "Up" : "Down");

    echo "<h1>Service Status {$OrionStatus}</h1>", PHP_EOL;
    $ServerInfo = $orion->serverInfo();
    echo "<p>";
    echo "Version: {$ServerInfo['version']}<br>", PHP_EOL;
    echo "Uptime: {$ServerInfo['uptime']}", PHP_EOL, PHP_EOL;
    echo "<p>";
    /**
     * Entity Creation
     * Ref: https://fiware-orion.readthedocs.io/en/develop/user/walkthrough_apiv1/index.html#entity-creation
     */
    echo "<h1><a href='https://fiware-orion.readthedocs.io/en/develop/user/walkthrough_apiv1/index.html#entity-creation' target='_blank'>Entity Creation:</a></h1>", PHP_EOL;
    
    echo "<h3>Request: </h3>", PHP_EOL;
    echo "<pre>";
    $Create = new \Orion\Operations\updateContext();
    $contextResponses = $Create->addElement("Room1", "Room", false)
            ->addAttrinbute("temperature", "float", "23")
            ->addAttrinbute("pressure", "integer", "720")
            ->setAction("APPEND")
            ->send($orion); //To send it you must give the orion connection as parameter
//            $UPDATE->send($orion2);//You also can work with 2 connections using this way, sending same entity to 2 different instances
    
    $Create->getRequest()->prettyPrint();//The contextElements sent to orion context broker in json format
    echo "</pre>";
    echo "<h3>Response: </h3>", PHP_EOL;
    echo "<pre>";
    $contextResponses->prettyPrint();
    echo "</pre>";

    /**
     * Query Context operation
     * Ref: https://fiware-orion.readthedocs.io/en/develop/user/walkthrough_apiv1/index.html#query-context-operation
     */
    echo "<h1><a href='https://fiware-orion.readthedocs.io/en/develop/user/walkthrough_apiv1/index.html#query-context-operation' target='_blank'>Query Context operation</a></h1>", PHP_EOL;
        
    echo "<h2>Basic</h2>";
    echo "<h3>Request : </h3>", PHP_EOL;
    echo "<pre>";
    $queryContext = new Orion\Operations\queryContext();
    $queryResponse = $queryContext->addElement("Room1", "Room")
            ->send($orion);
    $responseData = $queryResponse->get();


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
            ->send($orion);
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
            ->send($orion);
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
    echo "<h1><a href='https://fiware-orion.readthedocs.io/en/develop/user/walkthrough_apiv1/index.html#update-context-elements' target='_blank'>Update context elements:</a></h1>", PHP_EOL;
    $UPDATE = new \Orion\Operations\updateContext();

    $contextResponses = $UPDATE->addElement("Room1", "Room", false)
            ->addAttrinbute("temperature", "float", "26.5")
            ->addAttrinbute("pressure", "integer", "763")
            ->setAction("UPDATE")
            ->send($orion); //To send it you must give the orion connection as parameter
//            $UPDATE->send($orion2);//You also can work with 2 connections using this way, sending same entity to 2 different instances
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
    echo "<h1><a href='https://fiware-orion.readthedocs.io/en/develop/user/walkthrough_apiv1/index.html#context-subscriptions' target='_blank'>Context subscriptions</a></h1>", PHP_EOL;
    $subscribeContext = new \Orion\Operations\subscribeContext("http://localhost:1028/accumulate", "P1M");
    $subscribeResponse = $subscribeContext->addElement("Room1", "Room", false)
            ->addAttr("temperature")
            ->notifyConditions("ONCHANGE", ["pressure"])
            ->setThrottling("PT5S")
            ->send($orion);
    echo "<h3>Request: </h3>", PHP_EOL;
    echo "<pre>";
    $subscribeContext->getRequest()->prettyPrint();
    echo "</pre>";
    echo "<h3>Response: </h3>", PHP_EOL;
    echo "<pre>";
    $subscribeResponse->prettyPrint();
    echo "</pre>";

    echo "<h1>Update subscription </h1>", PHP_EOL;
    $subscriptionId = $subscribeResponse->get()->subscribeResponse->subscriptionId;
    echo "<h2>Subscription ID: {$subscriptionId}</h2>",PHP_EOL;
    $UPDATEsubscribeContext = new Orion\Operations\updateSubscription($subscribeResponse->get()->subscribeResponse->subscriptionId);
    $UPDATEsubscribeResponse = $UPDATEsubscribeContext->setDuration("P1M")->send($orion);

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
    
    $orion->unsubscribeContext($subscriptionId)->prettyPrint();
    echo "</pre>";


    //TODO: -> NGSI10 convenience operations
} catch (\Exception $e) {
    echo "<h1>", get_class($e), "</h1><h3>", $e->getMessage(), "</h3>";
    echo $e->getFile(), " [", $e->getLine(), "]<br>";
    echo "<pre>", $e->getTraceAsString(), "</pre>";
    if(method_exists($e, "getResponse")){
       echo "<pre>Orion Response:",PHP_EOL , $e->getResponse(),"</pre>";
    }
}
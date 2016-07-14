<h1><strong>FIWARE NGSI APIv2 Walkthrough</strong></h1>
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
    $OrionConn = new Orion\NGSIAPIv2($ip);
    $OrionStatus = ($OrionConn->checkStatus() ? "Up" : "Down");

    echo "<h1>Service Status {$OrionStatus}</h1>", PHP_EOL;
    $ServerInfo = $OrionConn->serverInfo();
    echo "<p>";
    echo "Version: {$ServerInfo['version']}<br>", PHP_EOL;
    echo "Uptime: {$ServerInfo['uptime']}", PHP_EOL, PHP_EOL;
    echo "<p>";


    /**
     * Entity Creation
     * Ref: https://fiware-orion.readthedocs.io/en/develop/user/walkthrough_apiv2/index.html#entity-creation
     */
    echo "<h1><a href='https://fiware-orion.readthedocs.io/en/develop/user/walkthrough_apiv2/index.html#entity-creation' target='_blank'>Entity Creation:</a></h1>", PHP_EOL;
    include './codeblock/v2_entitycreation_.php'; //Execute

    echo "<h3>Code: </h3>", PHP_EOL;
    echo "<code>";
        highlight_file('./codeblock/v2_entitycreation_.php'); //Displays
    echo "</code>";
    /**
     * Query Context operation
     * Ref: https://fiware-orion.readthedocs.io/en/develop/user/walkthrough_apiv2/index.html#query-entity
     */
    echo "<h1><a href='https://fiware-orion.readthedocs.io/en/develop/user/walkthrough_apiv2/index.html#query-entity' target='_blank'>Query Context operation</a></h1>", PHP_EOL;

    

    //Simple
    echo "<h2>Basic</h2>";
    echo "<h3>Request : </h3>", PHP_EOL;
    echo "<pre>";
    echo "GET ", $OrionConn->getUrl("entities/$RandomEntityID?type=Room");//Just return the url to be executed
    echo "</pre><code>";
    include './codeblock/v2_basic_query_.php'; //execute
    highlight_file('./codeblock/v2_basic_query_.php'); //Displays
    echo "</code>";
    echo "</pre>";
    echo "<h3>Response: </h3>", PHP_EOL;
    echo "<pre>";
        $queryResponse->prettyPrint();
    echo "</pre>";



    //With attribute params
    echo "<h2>With specified attribute and  options</h2>";

    $queryresponseDataAttr = $OrionConn->get("entities/$RandomEntityID?options=values&attrs=temperature,pressure");
    echo "<h3>Request(with attribute): </h3>", PHP_EOL;
    echo "<pre>";
    echo $OrionConn->getUrl("entities/$RandomEntityID?options=values&attrs=temperature,pressure");
    echo "</pre>";
    echo "<h3>Response (with attribute): </h3>", PHP_EOL;
    echo "<pre>";
    $queryresponseDataAttr->prettyPrint();
    echo "</pre>";

    //Non-existing Entity and patern
    echo "<h2>Non-existing Entity and patern</h2>";
    include './codeblock/v2_entity_attr.php'; //Execute
    echo "<h3>Request: </h3>", PHP_EOL;
    echo "<code>";
    highlight_file('./codeblock/v2_entity_attr.php'); //Displays
    echo "</code>";
    echo "<h3>Response: </h3>", PHP_EOL;
    echo "<pre>";
    $queryResponseAttr->prettyPrint();
    echo "</pre>";



    /**
     * Update context elements
     * Ref: https://fiware-orion.readthedocs.io/en/develop/user/walkthrough_apiv2/index.html#update-entity
     */
    echo "<h1><a href='https://fiware-orion.readthedocs.io/en/develop/user/walkthrough_apiv2/index.html#update-entity' target='_blank'>Update Entity:</a></h1>", PHP_EOL;
    include './codeblock/v2_update_entity.php'; //Execute
    echo "<h3>Code: </h3>", PHP_EOL;
    echo "<code>";
    highlight_file('./codeblock/v2_update_entity.php'); //Displays
    echo "</code>";
    
    echo "<h3>Request : </h3>", PHP_EOL;
    echo "<pre>";
    echo "PATCH ", $OrionConn->getUrl("entities/$RandomEntityID/attrs"),PHP_EOL;//Just return the url to be executed
    $updateEntity->getContext()->prettyPrint();
    echo "</pre><code>";
    include './codeblock/v2_basic_query_.php'; //execute
    echo "</code>";
    echo "<h3>Response Header: </h3>", PHP_EOL;
    echo "<pre>";
    foreach ($request->getResponseHeader() as $key => $value) {
        echo "$key:$value",PHP_EOL;
    }
    echo "</pre>";
    echo "<h3>Updated Entity: </h3>", PHP_EOL;
    echo "<pre>";
        $queryResponse->prettyPrint();
    echo "</pre>";
    exit;
    /**
     * Context subscriptions
     * Ref: https://fiware-orion.readthedocs.io/en/develop/user/walkthrough_apiv2/index.html#subscriptions
     */
    echo "<h1><a href='https://fiware-orion.readthedocs.io/en/develop/user/walkthrough_apiv2/index.html#subscriptions' target='_blank'>Context subscriptions</a></h1>", PHP_EOL;
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
    
    EXIT;
    
    echo "<h1>Update subscription </h1>", PHP_EOL;
    $subscriptionId = $subscribeResponse->get()->subscribeResponse->subscriptionId;
    echo "<h2>Subscription ID: {$subscriptionId}</h2>", PHP_EOL;
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

    $OrionConn->unsubscribeContext($subscribeResponse->get()->subscribeResponse->subscriptionId)->prettyPrint();
    echo "</pre>";


    //TODO: -> NGSI10 convenience operations
} catch (\Exception $e) {
    echo "<h1>", get_class($e), "</h1><h3>", $e->getMessage(), "</h3>";
    echo $e->getFile(), " [", $e->getLine(), "]<br>";
    echo "<pre>", $e->getTraceAsString(), "</pre>";
    if (method_exists($e, "getResponse")) {
        echo "<pre>", $e->getResponse(), "</pre>";
    }
}
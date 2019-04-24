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


try {
    //First of all we need create a instance of "Orion ContextBroker Connection"
    $orion = new Orion\NGSIAPIv2($ip);
    $OrionStatus = ($orion->checkStatus() ? "Up" : "Down");

    echo "<h1>Service Status {$OrionStatus}</h1>", PHP_EOL;
    $ServerInfo = $orion->serverInfo();
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
    echo "GET ", $orion->getUrl("entities/$RandomEntityID?type=Room"); //Just return the url to be executed
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

    $queryresponseDataAttr = $orion->get("entities/$RandomEntityID?options=values&attrs=temperature,pressure");
    echo "<h3>Request(with attribute): </h3>", PHP_EOL;
    echo "<pre>";
    echo $orion->getUrl("entities/$RandomEntityID?options=values&attrs=temperature,pressure");
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
    include './codeblock/v2_entity_update.php'; //Execute
    echo "<h3>Code: </h3>", PHP_EOL;
    echo "<code>";
    highlight_file('./codeblock/v2_entity_update.php'); //Displays
    echo "</code>";

    echo "<h3>Request : </h3>", PHP_EOL;
    echo "<pre>";
    echo "PATCH ", $orion->getUrl("entities/$RandomEntityID/attrs"), PHP_EOL; //Just return the url to be executed
    $updateEntity->getContext()->prettyPrint();
    echo "</pre><code>";
    include './codeblock/v2_basic_query_.php'; //execute
    echo "</code>";
    echo "<h3>Response Header: </h3>", PHP_EOL;
    echo "<pre>";
    foreach ($request->getResponseHeader() as $key => $value) {
        echo "$key:$value", PHP_EOL;
    }
    echo "</pre>";
    echo "<h3>Updated Entity: </h3>", PHP_EOL;
    echo "<pre>";
    $queryResponse->prettyPrint();
    echo "</pre>";

    /**
     * Context subscriptions
     * Ref: https://fiware-orion.readthedocs.io/en/develop/user/walkthrough_apiv2/index.html#subscriptions
     */
    echo "<h1><a href='https://fiware-orion.readthedocs.io/en/develop/user/walkthrough_apiv2/index.html#subscriptions' target='_blank'>Context subscriptions</a></h1>", PHP_EOL;


      include './codeblock/v2_subscription_creation.php'; //Execute
      echo "<h3>Code: </h3>", PHP_EOL;
      echo "<code>";
      highlight_file('./codeblock/v2_subscription_creation.php'); //Displays
      echo "</code>";

      echo "<h3>Request: </h3>", PHP_EOL;
      echo "<pre>";
      $subscription->prettyPrint();
      echo "</pre>";
      echo "<h3>Response: </h3>", PHP_EOL;
      echo "<pre>";
      echo "Subscription Location:", $subscriptionRequest->getResponseHeader("Location"), PHP_EOL;
      echo "Subscription Entity:", PHP_EOL;
      $subscriptionEntity->getContext()->prettyPrint();
      echo "</pre>";


      echo "<h1>Update subscription </h1>", PHP_EOL;
      include './codeblock/v2_subscription_update.php'; //Execute
      echo "<h2>Subscription ID: {$subscriptionId}</h2>", PHP_EOL;
      echo "<h3>Code: </h3>", PHP_EOL;
      echo "<code>";
      highlight_file('./codeblock/v2_subscription_update.php'); //Displays
      echo "</code>";

      echo "<h3>Updated Subscription: </h3>", PHP_EOL;
      echo "<pre>";
      $subscriptionEntity->getContext()->prettyPrint();
      echo "</pre>";


      echo "<h1>Subscription delete </h1>", PHP_EOL;
      //include './codeblock/v2_subscription_delete.php'; //Execute
      echo "<h3>Code: </h3>", PHP_EOL;
      echo "<code>";
      highlight_file('./codeblock/v2_subscription_delete.php'); //Displays
      echo "</code>";
      echo "<h3>Response: </h3>", PHP_EOL;
      echo "<pre>";
      // var_dump($httpRequest->getResponseInfo());
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
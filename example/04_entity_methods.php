<h1><strong>FIWARE NGSI APIv2 Entity Operations</strong></h1>
<?php
include './autoloader.php';


try {
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
    echo "<h3>Response</h3>",PHP_EOL;
    echo "<pre>";
    $request->debug("Entity Creation request");
    echo "</pre>";
    
    echo "<h1>Entity Instance Methods</h1>";
    echo "<h3>Code: </h3>", PHP_EOL;
    echo "<code>";
    highlight_file('./codeblock/v2_entity_instance.php'); //Displays
    echo "</code>";
    
    echo "<h3>Result </h3>",PHP_EOL;
    echo "<pre>";
    include './codeblock/v2_entity_instance.php'; //Execute
    echo "</pre>";
       
    
} catch (\Exception $e) {
    echo "<h1>", get_class($e), "</h1><h3>", $e->getMessage(), "</h3>";
    echo $e->getFile(), " [", $e->getLine(), "]<br>";
    echo "<pre>", $e->getTraceAsString(), "</pre>";
    if (method_exists($e, "getResponse")) {
        echo "<pre>", $e->getResponse(), "</pre>";
    }
}
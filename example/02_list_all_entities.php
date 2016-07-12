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
    $OrionConn = new Orion\NGSIAPIv1($ip);
    $OrionStatus = ($OrionConn->checkStatus() ? "Up" : "Down");

    echo "<h1>Service Status {$OrionStatus}</h1>", PHP_EOL;
    $ServerInfo = $OrionConn->serverInfo();
    echo "<p>";
    echo "Version: {$ServerInfo['version']}<br>", PHP_EOL;
    echo "Uptime: {$ServerInfo['uptime']}", PHP_EOL, PHP_EOL;
    echo "<p>";

    echo "<h1>List ALL Entities</h1>",PHP_EOL;
    $ServerEntities = $OrionConn->getEntities();
//    var_dump($ServerEntities);
    $lastType = "";
    echo "<pre>";
    echo "Raw:",PHP_EOL;
    $ServerEntities->prettyPrint();
    echo PHP_EOL,"Tab:",PHP_EOL;
    foreach ($ServerEntities->get() as  $type => $entities) {
        
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
    
    
    echo "</pre>";
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
    //TODO: -> NGSI10 convenience operations
} catch (\Exception $e) {
    echo "<h1>", get_class($e), "</h1><h3>", $e->getMessage(), "</h3>";
    echo $e->getFile(), " [", $e->getLine(), "]<br>";
    echo "<pre>", $e->getTraceAsString(), "</pre>";
}
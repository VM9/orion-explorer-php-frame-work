<h1><strong>Fi-Guardian Context Case</strong></h1>
<?php
include './autoloader.php';
$ip = "192.168.1.20";


try {
    $orion = new Orion\NGSIAPIv2($ip);
    $OrionStatus = ($orion->checkStatus() ? "Up" : "Down");
    echo "<h1>Service Status {$OrionStatus}</h1>", PHP_EOL;
    $ServerInfo = $orion->serverInfo();
    echo "<p>";
    echo "Version: {$ServerInfo['version']}<br>", PHP_EOL;
    echo "Uptime: {$ServerInfo['uptime']}", PHP_EOL, PHP_EOL;
    echo "<p>";


    $RandomEntityID = hash("crc32b", "O" . 1) . hash("crc32b", "D" . rand(1, 3000)); //AutoIncrementID
    $institutionId = 1;
    $clientId = "xpto123465";
    $EntityContext = new \Orion\Context\Entity($orion); //

    echo "<pre>";
    //Create Entity Context
    $EntityContext->create($RandomEntityID, "Devices", [
        "vm9:instituition" => [
            "value" => $institutionId,
            "type" => "Integer",
            "metadata" => [
                "client_id" => [
                    "type" => "Integer",
                    "value" => $clientId
                ]
            ]
        ]
    ])->debug("Create Entity");


    //Append Geo Location attribute
    $lat = -22.3006726;
    $lng = -42.5124478;
    $EntityContext->appendAttributes([
        "vm9:location" => [
            "value" => [
                "type" => "Point",
                "coordinates" => [$lng, $lat],
            ],
            "type" => "geo:json"
        ]
    ])->debug("Append Geo-Loc");
    
//    $EntityContext->appendAttributes([
//        "vm9:location_Point" => [
//            "value" => implode(", ", [$lat, $lng]),
//            "type" => "geo:point"
//        ]
//    ])->debug("Append Geo-Loc(point)");

    //Append network attributes (which networks this context is included)
    $EntityContext->appendAttributes([
        "device:network" => [
            "value" => [111,rand(3,7), rand(12,20)],
            "type" => "branch_id"]
    ])->debug("Append Branch");

    
    $EntityContext->appendAttributes([
        "rand".rand(1,22) => [
            "value" => rand(1,3e10),
            "type" => "randon_type".rand(1,22)
        ]
    ])->debug("Append Random Attribute");
    
    
    //Append new transducers:    
    $EntityContext->appendAttributes([
        "temperature" => [
            "value" => "27.1",
            "type" => "vm9:datapoint",
            "metadata" => [
                "transducer_type" => [
                    "type" => "Integer",
                    "value" => 1
                ],
                "uom" => [
                    "type" => "String",
                    "value" => "ºC"
                ]
            ]
        ]
    ])->debug("Append transducer");

//    $EntityContext
    
    $EntityContext->getContext()->prettyPrint();
    echo "Delete Entity:", PHP_EOL;
//    $EntityContext->delete()->debug("Delete Entity");
    echo "</pre>";
} catch (\Exception $e) {
    echo "<h1>", get_class($e), "</h1><h3>", $e->getMessage(), "</h3>";
    echo $e->getFile(), " [", $e->getLine(), "]<br>";
    echo "<pre>", $e->getTraceAsString(), "</pre>";
    if (method_exists($e, "getResponse")) {
        echo "<pre>", $e->getResponse(), "</pre>";
    }
}

$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$finish = $time;
$total_time = round(($finish - $start), 4);
echo 'Execution time: '.$total_time.' seconds.';
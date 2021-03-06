<h1><strong>Fi-Guardian Context Quering Case</strong></h1>
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


    $RandomEntityID = hash("crc32b", "O" . 1) . hash("crc32b", "D" . rand(1, 3000)); //AutoIncrementID
    $institutionId = 1;
    
    $orion->setService("i_$institutionId");
    
    $clientId = "xpto123465";
    $EntityContext = new \Orion\Context\Entity($orion);
//    $EntityContext->_setType('Devices'); //Reduce scope only for a specific Enity type
   
    echo "<h1> Simple Queries</h1>";
    
        $offset = 0;
        $limit = 10;


    $ContextQuery = $EntityContext->getContext(["offset" => $offset, "limit" => $limit, "options" => "count", 'q' => "network==1"], $request);
     echo "<h2> Request: </h2>";
     echo "<pre>";
        $request->debug("Simple Query", false);
     echo "</pre>";
    echo "<h2> Response: </h2>";
    echo "<pre>";
        echo "Total Entities: ",$request->getResponseHeader("Fiware-Total-Count"),PHP_EOL;
        echo "Returned Entities: ",count($ContextQuery->get()),PHP_EOL;
        $ContextQuery->prettyPrint();
    echo "</pre>";
    
    echo "<h1> Geo Queries</h1>";
    echo "<pre>";
    //Geo Spatial Query
    $request = null; //Pointer to store query request
//        georel=coveredBy&geometry=polygon&coords=25.774,-80.190;18.466,-66.118;32.321,-64.757;25.774,-80.190
    $line = '{"type":"LineString","coordinates":[[-42.510518431663506,-22.29866590991994],[-42.50978618860245,-22.29899348463585],[-42.51053184270859,-22.29883466062708],[-42.50992298126221,-22.29913493711663],[-42.510628402233124,-22.29888181152353],[-42.51014828681946,-22.299259018122225],[-42.51078397035599,-22.298916554279145],[-42.51023143529892,-22.299430249728946],[-42.51100927591324,-22.299058006837825],[-42.51031726598739,-22.299611407577157],[-42.51111924648285,-22.299286315928658],[-42.51037359237671,-22.29971811688465]]}';
    
    $point = json_decode('{
                "type": "Point",
                "coordinates": [
                    -42.5124,
                    -22.3007
                ]
            }');

    $polygon = (object) [
                "type" => "Polygon",
                "coordinates" => [
                    [
                        [
                            -42.48756408691406,
                            -22.25414831582032
                        ],
                        [
                            -42.57923126220703,
                            -22.276071328283138
                        ],
                        [
                            -42.5665283203125,
                            -22.333563186595907
                        ],
                        [
                            -42.51262664794922,
                            -22.347217992714814
                        ],
                        [
                            -42.48310089111328,
                            -22.28719038372
                        ],
                        [
                            -42.481727600097656,
                            -22.25986769362984
                        ],
                        [
                            -42.48756408691406,
                            -22.25414831582032
                        ]
                    ]
                ]
    ];

//    $Context = $EntityContext->getNearOfPoint(-22.3007,-42.5124, 1000, null, [], $request);//get entities located from a specified distance(max,min) from center (point)
//    $Context = $EntityContext->getIntersections($polygon, [], [], $request);//get entities those intersect the reference geometry
    $Context = $EntityContext->getCoveredBy($polygon, [], ["offset" => 0, "limit" => 1, "options" => "count", 'q' => "network==1"], $request);//with some options and filters
//    $Context = $EntityContext->getIntersections($point, [], [], $request);
//    $Context = $EntityContext->getIntersections($polygon, [], [], $request);
//    $Context = $EntityContext->getDisjoints($polygon, [], [], $request); //get entities those not intersect with the reference geometry
    
    echo json_encode($Context->toGeoJson(), JSON_PRETTY_PRINT),PHP_EOL;
    $request->debug("Geo Query", false);
    echo PHP_EOL;
    $Context->prettyPrint();
    echo "</pre>";
} catch (\Exception $e) {
    echo "<h1>", get_class($e), "</h1><h3>", $e->getMessage(), "</h3>";
    echo $e->getFile(), " [", $e->getLine(), "]<br>";
    echo "<pre>", $e->getTraceAsString(), "</pre>";
    if (method_exists($e, "getResponse")) {
        echo "<pre>";
        $e->getResponse()->debug('Exception URL');
        echo  "</pre>";
    }
}


$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$finish = $time;
$total_time = round(($finish - $start), 4);
echo 'Execution time: ' . $total_time . ' seconds.';

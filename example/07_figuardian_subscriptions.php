<h1><strong>Fi-Guardian Context Quering Case</strong></h1>
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
    echo "<pre>";
    
    
$subscription = new Orion\Context\SubscriptionFactory($orion, "OrionPHPSubscription");
$subscription->setExpiration((time() + 72000)); //You can use unix format also, to use function time you must configure your timezone on php.ini
//Like walkthrough api v2 example:
$subscriptionRequest = null; //A countainer for the HttpClient

$subscriptionEntity = $subscription->addEntitySubject(".*", "1s32132s132132")
        ->addAttrCondition("pressure")
        ->setNotificationURL("http://localhost:1028/accumulate")
        ->addNotificationAttr("temperature")
        ->setExpiration("2040-01-01T14:00:00.00Z")
        ->setThrottling(5)
        ->create($subscriptionRequest);



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

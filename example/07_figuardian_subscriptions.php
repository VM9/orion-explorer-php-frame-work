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

    //Multitenancy
    $institutionId = 1;
    //$orion->setService("i_$institutionId");

    echo "<pre>";

    $subscription = new \Orion\Context\SubscriptionFactory($orion, "OrionPHPSubscription");
    $subscription->addEntitySubject(".*", ".*", "idPattern")
            ->setExpiration("2099-01-01T00:00:00.00Z")
        ->setNotificationURL("http://direct.vm9it.com:81")
            ->setThrottling(1);

    $request = null;
    $SubscriptionEntity = $subscription->create($request);

    $request->debug("Create Subscription");
    echo $SubscriptionEntity->_getId();
//    $SubscriptionEntity->delete();

    return;



    $subscription = new Orion\Context\SubscriptionFactory($orion, "OrionPHPSubscription");
    $subscription->setExpiration((time() + 72000)); //You can use unix format also, to use function time you must configure your timezone on php.ini
//Like walkthrough api v2 example:
    $subscriptionRequest = null; //A countainer for the HttpClient

    $subscription->addEntitySubject(".*", "67e6410fcb530e03", "idPattern")
            ->addAttrCondition("pressure")
            ->addAttrCondition("temperature")
            ->setNotificationURL("http://localhost:5050/notify")
//        ->addNotificationAttr("temperature")
            ->setExpiration("2016-09-09T14:00:00.00Z")
            ->setThrottling(1);


//    echo json_encode($subscription->_subscription, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
//     $subscription->create($subscriptionRequest);
//     
//     $subscriptionRequest->debug();
//     
    //Update attr just to trigger subscription notification
    $EntityContext = new \Orion\Context\Entity($orion, "67e6410fce3af5ca", "67e6410fcb530e03");
//     $EntityContext->updateAttribute("temperature",["value"=> rand(15, 45) + (rand(0, 10) / 10) , "type"=>"datapoint"])->debug("Update Temperature Value");
    $EntityContext->updateAttributeValue("temperature", rand(15, 45) + (rand(0, 10) / 10))->debug("Update Temperature Value");
//     $EntityContext->appendAttribute("pressure", rand(1, 3) + (rand(0, 10) / 10), "datapoint")->debug("Update Pressure Value");
    $EntityContext->getContext()->prettyPrint();




    $subscriptionEntity = new \Orion\Context\SubscriptionEntity($orion, "57aaeb8537fbb3029be88977");

    $updateSubscription = new \Orion\Context\SubscriptionFactory();
    $updateSubscription->setThrottling(1);

    $subscriptionEntity->update($updateSubscription);



    echo "</pre>";
} catch (\Exception $e) {
    echo "<h1>", get_class($e), "</h1><h3>", $e->getMessage(), "</h3>";
    echo $e->getFile(), " [", $e->getLine(), "]<br>";
    echo "<pre>", $e->getTraceAsString(), "</pre>";
    if (method_exists($e, "getResponse")) {
        echo "<pre>";
        $e->getResponse()->debug('Exception URL');
        echo "</pre>";
    }
}


$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$finish = $time;
$total_time = round(($finish - $start), 4);
echo 'Execution time: ' . $total_time . ' seconds.';

<?php

//The create object return a instnace of SubscriptionEntity, this class provide operations for an entity
$subscriptionId = $subscriptionEntity->_getId(); //Store lastcreated subscription
//But its possible to construct a new instance using an orion connection and subscription id:
$subscriptionEntity = new \Orion\Context\SubscriptionEntity($orion, $subscriptionId);

//Some covenience methods:
$subscriptionEntity->inactive();
$subscriptionEntity->ative();
$subscriptionEntity->setExpiration(time() + (24 * 60 * 60) * 2);

//Its possible update it using a simple array
$subscriptionEntity->update([
    "description" => "Updated Description"
]);

$updateSubscription = new \Orion\Context\SubscriptionFactory($orion);
$updateSubscription
        ->setNotificationURL("http://localhost:1322/anotherone")
        ->addNotificationAttr("pressure")
        ->setThrottling(10);

$subscriptionEntity->update($updateSubscription);

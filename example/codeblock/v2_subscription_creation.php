<?php

$subscription = new Orion\Context\SubscriptionFactory($OrionConn, "OrionPHPSubscription");
$subscription->setExpiration((time() + 72000)); //You can use unix format also, to use function time you must configure your timezone on php.ini
//Like walkthrough api v2 example:
$subscriptionRequest = null; //A countainer for the HttpClient

$subscriptionEntity = $subscription->addEntitySubject("Room1", "Room")
        ->addAttrCondition("pressure")
        ->setNotificationURL("http://localhost:1028/accumulate")
        ->addNotificationAttr("temperature")
        ->setExpiration("2040-01-01T14:00:00.00Z")
        ->setThrottling(5)
        ->create($subscriptionRequest);

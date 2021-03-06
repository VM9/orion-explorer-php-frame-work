<?php

$subscription = new Orion\Context\SubscriptionFactory($orion, "OrionPHPSubscription");
$subscription->setExpiration((time() + 72000)); //You can use unix format also, to use function time you must configure your timezone on php.ini
//Like walkthrough api v2 example:
$subscriptionRequest = null; //A countainer for the HttpClient

$subscriptionEntity = $subscription->addEntitySubject(".*", ".*")
        ->addAttrCondition("pressure")
    ->setNotificationURL("http://direct.vm9it.com:81")
        ->addNotificationAttr("temperature")
        ->setExpiration("2040-01-01T14:00:00.00Z")
        ->setThrottling(5)
        ->create($subscriptionRequest);

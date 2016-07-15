<?php

$subscriptionEntity = new \Orion\Context\SubscriptionEntity($orion, $subscriptionId);
$httpRequest = $subscriptionEntity->delete();


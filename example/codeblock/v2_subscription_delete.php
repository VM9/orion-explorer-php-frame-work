<?php

$subscriptionEntity = new \Orion\Context\SubscriptionEntity($OrionConn, $subscriptionId);
$httpRequest = $subscriptionEntity->delete();


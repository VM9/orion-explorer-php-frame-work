<?php
$entityContext = new Orion\Context\Entity($orion);
$queryResponseAttr = $entityContext->getContext([
        "idPattern"=>"^Room[0-1][5-7][8-9]" 
]);
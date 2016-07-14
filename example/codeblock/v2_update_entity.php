<?php

$updateEntity = new Orion\Context\ContextFactory();
//For convenience it's possible append new attributes
$updateEntity->addAttribute("temperature", 26.5, "Float");
$updateEntity->addAttribute("pressure", 763, "Float");

$request = $OrionConn->patch("entities/$RandomEntityID/attrs", $updateEntity);
/**
* "Upon receipt of this request, the broker updates the values for the
* entity attributes in its internal database and responds with 204 No Content."
* If HTTP code isn't 204 it will throws a exception 
*/
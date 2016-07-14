<?php
$RandomEntityID = "Room1" . rand(1, 3000); //On V2 entities must be uniq for creation, on v1 if an entity excists it will be updated
$ContextCreate = new Orion\Context\ContextFactory([
    "type" => "Room",
    "temperature" => (object) [//When this array be converted to json it will becom object anyway because it's not possible to have arrays without senquential numeric keys
        "value" => 23,
        "type" => "Float"
    ]
        ]);
//For convenience it's possible append new attributes
$ContextCreate->addAttribute("pressure", 720, "Integer");
//Or Keys to context object
$ContextCreate->put("id", $RandomEntityID);

$OrionConn->create("entities", $ContextCreate); 

/**
 * "Upon receipt of this request, the broker will create the entity in its internal database,
 *  it will set the values for its attributes and it will respond with a 201 Created HTTP code."
 * If HTTP code isn't 201 it will throws a exception 
 */
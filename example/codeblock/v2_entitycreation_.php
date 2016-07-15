<?php
$RandomEntityID = "Room1" . rand(1, 3000); //On V2 entities must be uniq for creation, on v1 if an entity excists it will be updated
$EntityContext = new \Orion\Context\Entity($orion);
$request = $EntityContext->create($RandomEntityID, "Room", [
    "pressure" => [
        "value" => 720,
        "type" => "Float",
        "metadata" => [
            "name" => "bar",
            "type" => "UOM"
        ]
    ],
    "temperature" => [
        "value" => 23,
        "type" => "Float"
    ]
]);

/**
 * "Upon receipt of this request, the broker will create the entity in its internal database,
 *  it will set the values for its attributes and it will respond with a 201 Created HTTP code."
 * If HTTP code isn't 201 it will throws a exception 
 */
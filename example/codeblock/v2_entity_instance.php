<?php
echo "Running Operations:",PHP_EOL;
/**  
    It's possible to instanciate another entity but, for this example we will use the fresh created entity 
    $EntityContext = new \Orion\Context\Entity($orion,$RandomEntityID,"Room");
    $EntityContext->_setId($RandomEntityID);
    $EntityContext->_setType("Room");
**/

echo "Update Attribute data(pressure, change UOM to atm):",PHP_EOL;
$EntityContext->updateAttribute("pressure",["value"=>1.0321,"metadata" => [
            "uom"=>[
                    "type" => "String",
                    "value" => "bar"
                ]
        ]])->debug("Preassure Value & Metadata Update");

echo "Get attribute data:",PHP_EOL;
$EntityContext->getAttribute("pressure")->prettyPrint();


echo "Update attribute temperature to 27.5 :",PHP_EOL;
$EntityContext->updateAttributeValue("temperature", "27.5")->debug("Update Temperature Value");

echo "Get attribute value:",PHP_EOL;
$EntityContext->getAttributeValue("temperature")->prettyPrint();


echo "Append new attribute:",PHP_EOL;
$EntityContext->appendAttributes(["ref"=>["value"=>"abc","type"=>"string"]])->debug("Append attribute ref");

echo "Get Fresh created attribute:",PHP_EOL;
$EntityContext->getAttribute("ref")->prettyPrint();


echo "Get Entity: ",PHP_EOL;
$EntityContext->getContext()->prettyPrint();

echo "Delete Fresh created attribute:",PHP_EOL;
$EntityContext->deleteAttribute("ref")->debug("Delete ref attribute");



echo "Delete Entity:",PHP_EOL;
$EntityContext->delete()->debug("Delete Entity");
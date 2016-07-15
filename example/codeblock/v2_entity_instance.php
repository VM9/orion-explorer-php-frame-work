<?php
function print_request($request){
  echo $request->getMethod()," ",$request->getResponseInfo()['url'], " Status ",$request->getResponseInfo()['http_code'],PHP_EOL,$request->getResponseBody();  
}
echo "Running Operations:",PHP_EOL;
/**  
    It's possible to instanciate another entity but, for this example we will use the fresh created entity 
    $EntityContext = new \Orion\Context\Entity($orion,$RandomEntityID,"Room");
    $EntityContext->_setId($RandomEntityID);
    $EntityContext->_setType("Room");
**/

echo "Update Attribute data(pressure, change UOM to atm):",PHP_EOL;
$requestPres = $EntityContext->updateAttribute("pressure",["value"=>1.0321,"metadata" => [
            "name" => "atm",
            "type" => "UOM"
        ]]);
echo "Get attribute data:",PHP_EOL;
$EntityContext->getAttribute("pressure")->prettyPrint();
print_request($requestPres);

echo "Update attribute temperature to 27.5 :",PHP_EOL;
$requestTemp = $EntityContext->updateAttributeValue("temperature", "27.5");
print_request($requestTemp);

echo "Get attribute value:",PHP_EOL;
$EntityContext->getAttributeValue("temperature")->prettyPrint();


echo "Append new attribute:",PHP_EOL;
$requestAdd = $EntityContext->appendAttribute(["ref"=>["value"=>"abc","type"=>"string"]]);
print_request($requestAdd);

echo "Get Fresh created attribute:",PHP_EOL;
$EntityContext->getAttribute("ref")->prettyPrint();


echo "Get Entity: ",PHP_EOL;
$EntityContext->getContext()->prettyPrint();

echo "Delete Fresh created attribute:",PHP_EOL;
$requestDeleteAttr = $EntityContext->deleteAttribute("ref");
print_request($requestDeleteAttr);


echo "Delete Entity:",PHP_EOL;
$requestDelete = $EntityContext->delete();
print_request($requestDelete);
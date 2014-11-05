<?php

include './autoloader.php';

$ip = "0.0.0.0";

$OrionConnection = new Orion\ContextBroker($ip);


/**
 * Sample 02: 
 * 
 * QUERY CONTEXT
 * https://forge.fi-ware.org/plugins/mediawiki/wiki/fiware/index.php/Publish/Subscribe_Broker_-_Orion_Context_Broker_-_User_and_Programmers_Guide#Query_Context_operation
 * 
 */
//A simple query
$raw_query = <<<EOF
{
    "entities": [
    {
        "type": "Room",
        "isPattern": "true",
        "id": "Room.*"
    }
    ],
    "attributes" : [
    "temperature"
    ]
}
EOF;


$QUERY = new Orion\Operations\queryContext();
$reqBody = $QUERY->addElement(".*", "Room", true)
                    ->addAttr("temperature")
                    ->getRequest();

$raw_return = $Orion->queryContext($reqBody);

//A successfull response should looks like that:
$return_expected = <<<EOF
{
    "contextResponses": [
        {
            "contextElement": {
                "attributes": [
                    {
                        "name": "temperature",
                        "type": "centigrade",
                        "value": "23"
                    }
                ],
                "id": "Room1",
                "isPattern": "false",
                "type": "Room"
            },
            "statusCode": {
                "code": "200",
                "reasonPhrase": "OK"
            }
        },
        {
            "contextElement": {
                "attributes": [
                    {
                        "name": "temperature",
                        "type": "centigrade",
                        "value": "21"
                    }
                ],
                "id": "Room2",
                "isPattern": "false",
                "type": "Room"
            },
            "statusCode": {
                "code": "200",
                "reasonPhrase": "OK"
            }
        }
    ]
}
EOF;


//Is possible to create a Context Object using Context Factory Classs
$Context = new Orion\Context\Context($raw_return);

//Is possible get data in Array Format
$array = $Context->__toArray();
//Or in Object format
$object = $Context->__toObject();

$contextResponses = $object->contextResponses; //Based on json response above using that a array will be returned

$info = $contextResponses->statusCode; //Some info about this request


var_dump($info);
var_dump($contextResponses);



/**
 * GEO-LOCATED QUERIES
 * https://forge.fi-ware.org/plugins/mediawiki/wiki/fiware/index.php/Publish/Subscribe_Broker_-_Orion_Context_Broker_-_User_and_Programmers_Guide#Geo-located_queries
 */

//With Polygons:


$poly_query = <<<EOF
        {
  "entities": [
  {
    "type": "Point",
    "isPattern": "true",
    "id": ".*"
  }
  ],
  "restriction": {
    "scopes": [
    {
      "type" : "FIWARE::Location",
      "value" : {
        "polygon": {
          "vertices": [
          {
            "latitude": "3",
            "longitude": "3"
          },
          {
            "latitude": "3",
            "longitude": "8"
          },
          {
            "latitude": "11",
            "longitude": "8"
          },
          {
            "latitude": "11",
            "longitude": "3"
          }
          ],
          "inverted": "true"
        }
      }
    }
    ]
  }
}
EOF;

$polygon_restriction = new \Orion\Context\queryRestriction();

$polygon_restriction
        ->createScope("FIWARE::Location") //Create Scope will define scope type
        ->addPolygon(true);//By default inverted is true

//Just for less code ;)
$lat = array(3,3,11,11);
$lng = array(3,8,8,3);
$vertices = array_combine($lng, $lat);

//And add vertices to polygon
foreach ($vertices as $lng => $lat) {
    $polygon_restriction->addVertice($lat, (float)$lng);
}


//After just create a query object using Operation Factory
$queryPolygon = new Orion\Operations\queryContext();
$queryPolygon->addElement(".*", "City", true)
        ->addGeoRestriction($polygon_restriction); //and Append Geo Restriction to query


$reqBodyPolygonQuery = $queryPolygon->getRequest();

$dataPolygon = $OrionConnection->queryContext($reqBodyCircleQuery); //Do stuffs with returned data


//With Circles:

//GEO-Query with circle, sample:

$circle_query = <<<EOF
{
  "entities": [
  {
    "type": "City",
    "isPattern": "true",
    "id": ".*"
  }
  ],
  "restriction": {
    "scopes": [
    {
      "type" : "FIWARE::Location",
      "value" : {
        "circle": {
          "centerLatitude": "40.418889",
          "centerLongitude": "-3.691944",
          "radius": "13500",
          "inverted": "true"
        }
      }
    }
    ]
  }
}
EOF;

//Code Implementation:

//1st I need create a restriction

$circle_restriction = new \Orion\Context\queryRestriction();
$circle_restriction->createScope("FIWARE::Location")
                   ->addCircle(40.418889, -3.691944, 13500, true);


//After just create a query object using Operation Factory
$query = new Orion\Operations\queryContext();
$query->addElement(".*", "City", true)
        ->addGeoRestriction($circle_restriction); //and Append Geo Restriction to query


$reqBodyCircleQuery = $query->getRequest();

$dataCircle = $OrionConnection->queryContext($reqBodyCircleQuery); //Do stuffs with returned data
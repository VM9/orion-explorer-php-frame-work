<?php

//header("Content-Type: application/json");
set_time_limit(0);

set_include_path(dirname(__FILE__) . PATH_SEPARATOR .
        get_include_path());

spl_autoload_register(function ($class) {
    require_once(str_replace('Orion\\', '/', $class . '.php'));
});

//require './Orion/Orion.php';
//use Orion;
//$contextstring = <<<EOF
//{
//    "contextElements": [
//        {
//            "type": "Room",
//            "isPattern": "false",
//            "id": "Room1",
//            "attributes": [
//            {
//                "name": "temperature",
//                "type": "centigrade",
//                "value": "23"
//            },
//            {
//                "name": "pressure",
//                "type": "mmHg",
//                "value": "720"
//            }
//            ]
//        }
//    ],
//    "updateAction": "APPEND"
//}
//EOF;
//$Contexteste = new Orion\Context\Context($contextstring, "updateContextRequest");
//echo $Contexteste->__toString();
//var_dump($Contexteste->__toArray());
//var_dump($Contexteste->__toObject());
//
//
//

$sensores = <<<EOF
[
	{
		"id" : "0c3eff7bdfc3825d22b6f4fdc1e9e181",
		"nome" : "Suspiro - Rio Bengala",
		"fonte" : "INEA",
		"latitude" : "-22.279664",
		"longitude" : "-42.534922",
		"tipo" : "Hidro",
		"valor" : 0.0000,
		"dataleitura" : "2014-05-20 10:30:02"
	},
	{
		"id" : "18a334392d167ebf0d0ab4f96395b6a0",
		"nome" : "Olaria - Rio Cônego",
		"fonte" : "INEA",
		"latitude" : "-22.328842",
		"longitude" : "-42.542211",
		"tipo" : "Hidro",
		"valor" : 0.5900,
		"dataleitura" : "2014-05-20 10:30:03"
	},
	{
		"id" : "3f852b2d43dcefa1a05ed7808cce9d06",
		"nome" : "Ponte Estrada Dona Mariana - Rio Grande",
		"fonte" : "INEA",
		"latitude" : "-22.215989",
		"longitude" : "-42.57095",
		"tipo" : "Hidro",
		"valor" : 1.0600,
		"dataleitura" : "2014-05-20 10:30:03"
	},
	{
		"id" : "54f3e01a3b5e9215025cb5d6c420c8b2",
		"nome" : "Ypu - Rio Santo Antônio",
		"fonte" : "INEA",
		"latitude" : "-22.295958",
		"longitude" : "-42.526603",
		"tipo" : "Hidro",
		"valor" : 0.5100,
		"dataleitura" : "2014-05-20 10:30:03"
	},
	{
		"id" : "6ae5cbf337f670e571844c64d8a29b53",
		"nome" : "Venda das Pedras - Rio Córrego D`antas",
		"fonte" : "INEA",
		"latitude" : "-22.279664",
		"longitude" : "-42.581631",
		"tipo" : "Hidro",
		"valor" : 0.0000,
		"dataleitura" : "2014-05-20 10:30:03"
	},
	{
		"id" : "799b35711ba58a5b8de8a593d071969a",
		"nome" : "Conselheiro Paulino - Rio Bengala",
		"fonte" : "INEA",
		"latitude" : "-22.228564",
		"longitude" : "-42.520436",
		"tipo" : "Hidro",
		"valor" : 1.0700,
		"dataleitura" : "2014-05-20 10:30:03"
	},
	{
		"id" : "7aeac1a9cfcaa66523b7e622a26add19",
		"nome" : "Suspiro - Rio Bengala",
		"fonte" : "INEA",
		"latitude" : "-22.279564",
		"longitude" : "-42.534822",
		"tipo" : "Hidro",
		"valor" : 0.5800,
		"dataleitura" : "2014-05-20 10:30:03"
	},
	{
		"id" : "7b66c9e371a56368eafeedccacbcf0cd",
		"nome" : "Conselheiro Paulino - Rio Bengala",
		"fonte" : "INEA",
		"latitude" : "-22.228464",
		"longitude" : "-42.520136",
		"tipo" : "Hidro",
		"valor" : 0.0000,
		"dataleitura" : "2014-05-20 10:30:02"
	},
	{
		"id" : "8b1ae982b5feaa25ca6bc65f6ccc2c15",
		"nome" : "Pico Caledônia",
		"fonte" : "INEA",
		"latitude" : "-22.359507",
		"longitude" : "-42.567477",
		"tipo" : "Pluviométrica",
		"valor" : 0.0000,
		"dataleitura" : "2014-05-20 10:30:02"
	},
	{
		"id" : "8e40e84ce68f2a021a3e86a21d32c4ee",
		"nome" : "Ponte Estrada Dona Mariana - Rio Grande",
		"fonte" : "INEA",
		"latitude" : "-22.215889",
		"longitude" : "-42.57075",
		"tipo" : "Hidro",
		"valor" : 0.0000,
		"dataleitura" : "2014-05-20 10:30:02"
	},
	{
		"id" : "a206e8e0a8c658532d93812ad8a3a7a7",
		"nome" : "Venda das Pedras - Rio Córrego D`antas",
		"fonte" : "INEA",
		"latitude" : "-22.279564",
		"longitude" : "-42.581531",
		"tipo" : "Hidro",
		"valor" : 0.5000,
		"dataleitura" : "2014-05-20 10:30:03"
	},
	{
		"id" : "bbcd371c5ffcd3011248c7a91390a7cc",
		"nome" : "Olaria - Rio Cônego",
		"fonte" : "INEA",
		"latitude" : "-22.308902",
		"longitude" : "-42.542311",
		"tipo" : "Hidro",
		"valor" : 0.0000,
		"dataleitura" : "2014-05-20 10:30:02"
	},
	{
		"id" : "c7ff2d820f0349a06207301152c10447",
		"nome" : "Pico Caledônia",
		"fonte" : "INEA",
		"latitude" : "-22.359197",
		"longitude" : "-42.567367",
		"tipo" : "Pluviométrica",
		"valor" : 0.0000,
		"dataleitura" : "2014-05-20 10:30:03"
	},
	{
		"id" : "d55f7d1e989e51641f89774c72370d23",
		"nome" : "Ypu - Rio Santo Antônio",
		"fonte" : "INEA",
		"latitude" : "-22.295858",
		"longitude" : "-42.526503",
		"tipo" : "Hidro",
		"valor" : 0.0000,
		"dataleitura" : "2014-05-20 10:30:03"
	},
	{
		"id" : "3273",
		"nome" : "Vale dos Pinheiros",
		"fonte" : "CEMADEN",
		"latitude" : "-22.29",
		"longitude" : "-42.548",
		"tipo" : "Pluviométrica",
		"valor" : 0.0000,
		"dataleitura" : "2014-05-20 10:30:10"
	},
	{
		"id" : "3275",
		"nome" : "São Geraldo",
		"fonte" : "CEMADEN",
		"latitude" : "-22.239",
		"longitude" : "-42.547",
		"tipo" : "Pluviométrica",
		"valor" : 0.0000,
		"dataleitura" : "2014-05-20 10:30:10"
	},
	{
		"id" : "3276",
		"nome" : "Olaria",
		"fonte" : "CEMADEN",
		"latitude" : "-22.308",
		"longitude" : "-42.542",
		"tipo" : "Pluviométrica",
		"valor" : 0.0000,
		"dataleitura" : "2014-05-20 10:30:10"
	},
	{
		"id" : "3278",
		"nome" : "Amparo",
		"fonte" : "CEMADEN",
		"latitude" : "-22.258",
		"longitude" : "-42.459",
		"tipo" : "Pluviométrica",
		"valor" : 0.0000,
		"dataleitura" : "2014-05-20 10:30:10"
	},
	{
		"id" : "3279",
		"nome" : "Lumiar",
		"fonte" : "CEMADEN",
		"latitude" : "-22.348",
		"longitude" : "-42.327",
		"tipo" : "Pluviométrica",
		"valor" : 0.0000,
		"dataleitura" : "2014-05-20 10:30:11"
	},
	{
		"id" : "3280",
		"nome" : "Ponte da Saudade",
		"fonte" : "CEMADEN",
		"latitude" : "-22.31",
		"longitude" : "-42.524",
		"tipo" : "Pluviométrica",
		"valor" : 0.0000,
		"dataleitura" : "2014-05-20 10:30:11"
	},
	{
		"id" : "3285",
		"nome" : "Rio Grandina",
		"fonte" : "CEMADEN",
		"latitude" : "-22.197",
		"longitude" : "-42.511",
		"tipo" : "Pluviométrica",
		"valor" : 0.0000,
		"dataleitura" : "2014-05-20 10:30:11"
	},
	{
		"id" : "3286",
		"nome" : "Campo do Coelho",
		"fonte" : "CEMADEN",
		"latitude" : "-22.269",
		"longitude" : "-42.614",
		"tipo" : "Pluviométrica",
		"valor" : 0.0000,
		"dataleitura" : "2014-05-20 10:30:11"
	},
	{
		"id" : "3288",
		"nome" : "Varginha",
		"fonte" : "CEMADEN",
		"latitude" : "-22.294",
		"longitude" : "-42.505",
		"tipo" : "Pluviométrica",
		"valor" : 0.0000,
		"dataleitura" : "2014-05-20 10:30:11"
	},
	{
		"id" : "3289",
		"nome" : "Floresta",
		"fonte" : "CEMADEN",
		"latitude" : "-22.224",
		"longitude" : "-42.526",
		"tipo" : "Pluviométrica",
		"valor" : 0.0000,
		"dataleitura" : "2014-05-20 10:30:11"
	},
	{
		"id" : "3292",
		"nome" : "Granja Spinelli",
		"fonte" : "CEMADEN",
		"latitude" : "-22.279",
		"longitude" : "-42.56",
		"tipo" : "Pluviométrica",
		"valor" : 0.0000,
		"dataleitura" : "2014-05-20 10:30:11"
	},
	{
		"id" : "3294",
		"nome" : "Jardim Califórnia",
		"fonte" : "CEMADEN",
		"latitude" : "-22.239",
		"longitude" : "-42.536",
		"tipo" : "Pluviométrica",
		"valor" : 0.0000,
		"dataleitura" : "2014-05-20 10:30:11"
	},
	{
		"id" : "3295",
		"nome" : "Caledônia",
		"fonte" : "CEMADEN",
		"latitude" : "-22.335",
		"longitude" : "-42.564",
		"tipo" : "Pluviométrica",
		"valor" : 0.0000,
		"dataleitura" : "2014-05-20 10:30:11"
	},
	{
		"id" : "3296",
		"nome" : "Caledônia2",
		"fonte" : "CEMADEN",
		"latitude" : "-22.359",
		"longitude" : "-42.56",
		"tipo" : "Pluviométrica",
		"valor" : 0.0000,
		"dataleitura" : "2014-05-20 10:30:11"
	},
	{
		"id" : "3297",
		"nome" : "Perissê",
		"fonte" : "CEMADEN",
		"latitude" : "-22.293",
		"longitude" : "-42.533",
		"tipo" : "Pluviométrica",
		"valor" : 0.0000,
		"dataleitura" : "2014-05-20 10:30:11"
	},
	{
		"id" : "3298",
		"nome" : "Nova Suiça",
		"fonte" : "CEMADEN",
		"latitude" : "-22.279",
		"longitude" : "-42.498",
		"tipo" : "Pluviométrica",
		"valor" : 0.0000,
		"dataleitura" : "2014-05-20 10:30:11"
	},
	{
		"id" : "1",
		"nome" : "FI-Guardian - Rio Bengalas",
		"fonte" : "Fi-Guardian",
		"latitude" : "-22.287233",
		"longitude" : "-42.533833",
		"tipo" : "Pluviometro",
		"valor" : 0.0000,
		"dataleitura" : "2014-02-14 08:00:10"
	}
]

EOF;

function incrementalHash($len = 5) {
    $charset = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ-";
    $base = strlen($charset);
    $result = '';

    $now = explode(' ', microtime())[1];
    while ($now >= $base) {
        $i = $now % $base;
        $result = $charset[$i] . $result;
        $now /= $base;
    }
    return substr($result, $len * (-1));
}

$Orion = new Orion\ContextBroker();

/* * ************* UPDATE CONTEXT ************************ */
//$update = new Orion\Operations\updateContext("UPDATE");
//$update = new Orion\Operations\updateContext("DELETE");
$update = new Orion\Operations\updateContext("APPEND");

///$update->addElement("Room3", "Room"); 

function getRandLat() {
    return rand(-83, 83) + (mt_rand() / 10000000000);
}

function getRandLng() {
    return rand(-178, 179) + (mt_rand() / 10000000000);
}

ob_start();

//foreach (json_decode($sensores) as $key => $value) {
//    while ($key < 200){
//    switch ($value->tipo) {
//        case "Hidro":
//            //RiverLevel {id, provider, lastvalue, location, lastread }
////            var_dump($value->fonte);
//            $update = new Orion\Operations\updateContext();
//            $update->addElement($value->id, "RiverLevel")
//                    ->addAttrinbute("description", "string", $value->nome)
//                    ->addAttrinbute("lastvalue", "mm", $value->valor)
//                    ->addAttrinbute("lastread", "date", $value->dataleitura)
//                    ->addAttrinbute("provider", "string", $value->fonte)
//                    ->addGeolocation($value->latitude, $value->longitude);
//            $update->setAction("APPEND");
//
//            $return = $Orion->updateContext($update->getRequest());
//            $Contexteste = new Orion\Context\Context($return);
//            var_dump($Contexteste->__toObject()->contextResponses);
//
//            unset($update);
//            unset($return);
//            unset($Contexteste);
//
//            break;
//        case "Pluviométrica":
//        case "Pluviometro":
//            //Pluviometers {id, provider, lastvalue, location, lastread } 
//            $update = new Orion\Operations\updateContext();
//            $update->addElement($value->id, "Pluviometers")
//                    ->addAttrinbute("description", "string", $value->nome)
//                    ->addAttrinbute("lastvalue", "mm", $value->valor)
//                    ->addAttrinbute("lastread", "date", $value->dataleitura)
//                    ->addAttrinbute("provider", "string", $value->fonte)
//                    ->addGeolocation($value->latitude, $value->longitude);
//
//            $update->setAction("APPEND");
//
//            $return = $Orion->updateContext($update->getRequest());
//            $Contexteste = new Orion\Context\Context($return);
//            var_dump($Contexteste->__toObject()->contextResponses);
//
//            unset($update);
//            unset($return);
//            unset($Contexteste);
//
//            break;
//        default:
//            echo "ops";
//            var_dump($value->tipo);
//            break;
//    }

//$i = 0;
//while ($i < 300) {
//    
//    $Orion->convenienceDELETE("contextEntities/Car" . $i);
//    $Orion->convenienceDELETE("contextEntities/Room" . $i);
//    $Orion->convenienceDELETE("contextEntities/SampleType" . $i);
//    
//    echo "Deleted " . $i;
//     ob_flush();
//     flush();
//    $i++;
//}
//exit;
$key = 0;
while ($key < 250) {
//    Cars {id, model, speed, location }
//    $update = new Orion\Operations\updateContext();
//    $update->addElement("Car" . $key, "Cars")
//            ->addAttrinbute("model", "string", incrementalHash(6))
//            ->addAttrinbute("speed", "kmh", rand(10,200))
//            ->addGeolocation(rand(37, 42) + (mt_rand() / 10000000000), rand(-5, -3) + (mt_rand() / 10000000000)) //Spain
//            ->setAction("APPEND");
//
//    $return = $Orion->updateContext($update->getRequest());
//    $Contexteste = new Orion\Context\Context($return);
//    var_dump($Contexteste->__toObject()->contextResponses);
// ob_flush();
//     flush();
//    unset($update);
//    unset($return);
//    unset($Contexteste);

//    Rooms {id, temperature, pressure, umidity, light }
//    $update = new Orion\Operations\updateContext();
//    $update->addElement("Room" . $key, "Room")
//            ->addAttrinbute("temperature", "centigrade", $key/2)
//            ->addAttrinbute("pressure", "mmHg", $key / 20)
//            ->addAttrinbute("umidity", "kgm³", $key/12)
//            ->addAttrinbute("illuminance", "lux", rand(323, 400))
//            ->addGeolocation(rand(37, 42) + (mt_rand() / 10000000000), rand(-5, -3) + (mt_rand() / 10000000000)) //Spain
//            ->setAction("APPEND");
//
//    $return = $Orion->updateContext($update->getRequest());
//    $Contexteste = new Orion\Context\Context($return);
//    var_dump($Contexteste->__toObject()->contextResponses);
// ob_flush();
//     flush();
//    unset($update);
//    unset($return);
//    unset($Contexteste);
//        while ($key < 200){
    //    SampleType {id, attribute1, attribute2, attribute3, attributeN}
    
    $update = new Orion\Operations\updateContext();
    $update->addElement("SampleEntity" . $key, "SampleType")
            ->addAttrinbute("attribute1", "string", incrementalHash(10))
            ->addAttrinbute("attribute2", "string", incrementalHash(20))
            ->addAttrinbute("attribute3", "string", incrementalHash(60))
            ->addAttrinbute("attributeN", "int", mt_rand())
            ->addGeolocation(getRandLat(), getRandLng());

    $update->setAction("APPEND");
 
    $return = $Orion->updateContext($update->getRequest());
    
    $Contexteste = new Orion\Context\Context($return);
    
    var_dump($Contexteste->__toObject()->contextResponses);
    
    
//
    unset($update);
    unset($return);
    unset($Contexteste);
    
    $key++;
     ob_flush();
     flush();
    sleep(1);
}

echo "finished";

ob_flush();
flush();
ob_end_flush();
exit;
$update->setAction("APPEND");

//
//var_dump($update->getRequest());
//echo json_encode($update->getRequest(), JSON_UNESCAPED_SLASHES);
//exit;
$return = $Orion->updateContext($update->getRequest());
//
//
$Contexteste = new Orion\Context\Context($return);
echo $Contexteste->__toString();
var_dump($Contexteste->__toArray());

var_dump($return);

/* exit; */

/* * ************* Query CONTEXT ************************ */
//$query = new Orion\Operations\queryContext();
//$query->addElement(".*", "Room", true)
//        ->addElement("Room1", "Room");
//echo json_encode($query->getRequest());
//exit;
//$return = $Orion->queryContext($query->getRequest());
//
//

/* * ************* Subscribe CONTEXT ************************ */

$subs = new Orion\Operations\subscribeContext("http://localhost:1028/accumulate", "P1M");
$subs->addElement(".*", "Room", true)
        ->addAttr("temperature")
        ->addAttr("pressure")
        ->notifyConditions("ONCHANGE", "pressure");
//        ->notifyConditions("ONTIMEINTERVAL","PT10S");
//
//echo json_encode($subs->getRequest());exit;
$return = $Orion->subscribeContext($subs->getRequest());


/* * **************** Update Subscription
 */
//$updatesubs = new Orion\Operations\updateSubscription("538ce3419890cd828b9127b5");
//$updatesubs->notifyConditions("ONTIMEINTERVAL", "PT5S");
//
//echo json_encode($updatesubs->getRequest());exit;
//$return = $Orion->subscribeContext($updatesubs->getRequest());


/* * *************** unsubscribe context
  //$return = $Orion->unsubscribeContext("538ce3419890cd828b9127b5");



  /******************* Geolocation capabilities */
$update = new Orion\Operations\updateContext("UPDATE");
$update->addElement("SensorTeste", "PluviometricSensor")
        ->addAttrinbute("last", "mm", "32")
        ->addGeolocation(-22.2692008, -42.53786630);
$Orion->updateContext($update->getRequest());
//echo -31.321;
//echo PHP_EOL;
//echo -42.321;
//echo PHP_EOL;
//echo json_encode($update->getRequest());
//exit;



$restriction = new \Orion\Context\queryRestriction();

//$restriction->createScope()->addPolygon();
//$lat = array(-22.269200898612233, -22.268908004299764, -22.268880700561418,
//    -22.271633387006975, -22.27375805655054, -22.273733235830946,
//    -22.271941168238598, -22.271441643921562, -22.27057476332682,
//    -22.27070755712351, -22.270615718436442);
//
//$lng = array(-42.533966302871704, -42.53332793712616, -42.532547414302826,
//    -42.533161640167236, -42.53374099731445, -42.5370454788208,
//    -42.53682017326355, -42.537828013300896, -42.53768786787987,
//    -42.53637760877609, -42.53551661968231);
//
//$vertices = array_combine($lng, $lat);
//
//foreach ($vertices as $lng => $lat) {
//    $restriction->addVertice($lat, (float)$lng);
//}

$restriction->createScope()->addCircle(-22.2692008, -42.53786630, 20000);

// restriction no query context
//echo json_encode($restriction->getRequest());
//exit;

$query = new Orion\Operations\queryContext();
$query->addElement(".*", "PluviometricSensor", true)
        ->addGeoRestriction($restriction);

echo json_encode($query->getRequest());
//exit;
$return = $Orion->queryContext($query->getRequest());


$Contexteste = new Orion\Context\Context($return);

echo $Contexteste->__toString();
//var_dump($Contexteste->__toArray());
//var_dump($Contexteste->__toObject());




















//$contextElements = new \Orion\Context\ContextFactory();
//
//    $contextElement = new \Orion\Context\ContextFactory();
//    $contextElement->put("type", "Room");
//    $contextElement->put("isPattern", "false");
//    $contextElement->put("id", "Room1");
//    
//    $attribute1 = new \Orion\Context\ContextFactory();
//    $attribute1->put("name", "temperature");
//    $attribute1->put("type", "centigrade");
//    $attribute1->put("value", "23");
//    
//    
//    $attribute2 = new \Orion\Context\ContextFactory();
//    $attribute2->put("name", "pressure");
//    $attribute2->put("type", "mmHg");
//    $attribute2->put("value", "720");
//    
//    
//    $contextElement->put("attributes", array(
//        $attribute1->getContext(),
//        $attribute2->getContext()
//    ));
//    
//    
//
//$contextElements->put("contextElements", array($contextElement->getContext()));
//$contextElements->put("updateAction", "APPEND");

//
//$cs = new \stdClass();
//$cs->contextElements = $contextElements->getContext();
//echo "<br>";
//echo json_encode($contextElements->getContext());


//$Contexteste = new Orion\Context\Context(json_encode($contextElements->getContext()), "updateContextRequest");
//echo $Contexteste->__toString();
//var_dump($Contexteste->__toArray());
//var_dump($Contexteste->__toObject());

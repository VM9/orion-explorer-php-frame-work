<?php

/**
 * Orion Context Explorer FrameWork - a PHP 5 framework for Orion Context Broker
 *
 * @copyright   2014 VM9 Tecnologia da Informação Ltda  
 * @author      Leonan Carvalho <j.leonancarvalho@gmail.com>
 * @link        http://orionexplorer.com
 * @license     http://opensource.org/licenses/MIT
 * @version     1.0.0
 * @package     Orion
 *
 * MIT LICENSE
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace Orion;

use Orion\Context;
use Orion\Utils;

/**
 * Orion ContextBroker Class
 *  
 * @package      Orion
 * @author      Leonan Carvalho <j.leonancarvalho@gmail.com>
 * @since      1.0.0
 * 
 * @property \Orion\Context  Context Controller
 * @property \Orion\Utils    Http Requiest Utils
 */
class ContextBroker {

    /**
     * @var string
     */
    protected $ip;

    /**
     * @var mixed
     */
    protected $port;

    /**
     * @var string
     */
    protected $url; //Full URL with NGSI reference
    /**
     * @var string
     */
    protected $serverUrl;

    /**
     * @var object[\Utils\HttpRequest]
     */
    protected $restReq;

    /**
     * @var Float
     */
    protected $_orionVersion;

    /**
     * Constructor
     * @param  string $ServerAddress String that contain IPv4 Address or Hostname
     * @param  mixed $port String or Integer that contain Port Number
     * @param  string $alias String API Alias NGSI10 or NGSI9
     * @param  string $type String ContentType only json is supported actually
     */
    public function __construct($ServerAddress, $port = '1026', $alias = 'NGSI10', $type = "application/json") {
        $this->ip = (string) $ServerAddress;
        $this->port = $port;
        $this->serverUrl = $ServerAddress . ":" . $port . "/";
        $this->url = $this->serverUrl . $alias . "/";

        //Setup Http Requests
        $this->restReq = new Utils\HttpRequest();
        $this->restReq->setAcceptType($type);
        $this->restReq->setContentType($type);
    }

    /**
     * Experimental Authentication for Orion Context Broker
     * 
     * eg: X-Auth-Token: HLcJPAliV55X5zI68DfDZgVI-by2MBR0s3QhJF7WwwOU0u5AO3f85ycMouzxr3UWGfbCjO3ODcaM6ybt4wUdbV
     * 
     * @param string $key Name of Header Key
     * @param string $token Token
     */
    public function setToken($key, $token) {
        $this->restReq->addCustonHeader($key, $token);
    }

    /**
     * 
     * This method will run rest requests to Orion API and return the response
     * If Orion API returns a Ok Status, such 200
     * Otherwise, the response body is ignored. 
     *
     * @param  strinq  $url should contains https:// or https://
     * @param  string  $method GET/POST/DELETE/PUT
     * @param  /Orion/Context  $reqBody Context Object
     * @return string  json string
     */
    private function restRequest($url, $method = "GET", $reqBody = "") {
        try {
            if (is_array($reqBody) || is_object($reqBody)) {
                $reqBody = json_encode($reqBody, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }

            $this->restReq->setUrl($url);
            $this->restReq->setmethod($method);
            $this->restReq->buildPostBody($reqBody);
            $this->restReq->execute(); // Run the request...

            return $this->restReq->getResponseBody();
        } catch (Exception $e) {
//            var_dump($e); //Ungly Debug
            return "";
        }
    }

    /**
     * 
     * This method checks IP connectivity using a socket connection.
     * Is used a timeout very low to not delay responses that use it 
     * Any authentication will be ignored.
     * If a Firewall is applied may this test will fail.
     *
     * @param  strinq  $ip IPv4 or Hostname
     * @param  string  $port GET/POST/DELETE/PUT
     * @return boolean  
     */
    public function checkStatus($ip = false, $port = false) {
        $ip = ($ip) ? $ip : $this->ip;
        $port = ($port) ? $port : $this->port;

        $fp = @\fsockopen($ip, $port, $errno, $errstr, 2);
        $status = !!$fp; //Force boolean Type

        if ($status) {
            fclose($fp);
        }
        return (bool) $status;
    }

    /**
     * 
     * This method retrieves server information using convenience /version response
     *
     * @return array  
     */
    public function serverInfo() {
        try {
            if ($this->checkStatus()) {
                $url = $this->ip . ":" . $this->port;
                $ret = $this->restRequest($url . "/version", 'GET');
                $info = array();
                $VersionContext = new Context\Context($ret);
                $Version = $VersionContext->__toObject();
                if (null != $Version) {
                    $info["version"] = $Version->orion->version;
                    $info["uptime"] = $Version->orion->uptime;
//
//                    $StatisticsContext = new Context\Context($fixjson2);
//                    $Statistics = $StatisticsContext->__toObject();
//                    $info["statistics"] = $Statistics;
                } else {
//                    //issue #428 : https://github.com/telefonicaid/fiware-orion/issues/428
//                    $ret2 = $this->restRequest($url . "/statistics", 'GET');
//                    $fixjson2 = str_replace('"orion" : ', "", trim($ret2)); //fix to json_decode

                    $fixjson = str_replace('"orion" : ', "", trim($ret)); //fix to json_decode
                    $VersionContext = new Context\Context($fixjson);
                    $Version = $VersionContext->__toObject();
                    $info["version"] = $Version->version;
                    $info["uptime"] = $Version->uptime;
                }
                $this->_orionVersion = floatval($info["version"]);

                return $info;
            } else {
                return array();
            }
        } catch (Exception $e) {
            return FALSE;
        }
    }

    /**
     * 
     * This method checks server version with a determined logical operation
     * 
     *
     * @param  mixed  $version String or Float Version eg. "0.15.0" or 0.15
     * @param  string  $op Logical Operations to compare versions
     * @return boolean  
     */
    public function checkVersion($version, $op = false) {
        try {
            if (is_string($version)) {
                $version = floatval($version);
            }
            if (isset($this->_orionVersion)) {
//                var_dump($this->_orionVersion >= $version);
                switch ($op) {
                    case "=":
                        return $this->_orionVersion == $version;
                    case "!=":
                        return $this->_orionVersion != $version;
                    case ">":
                        return $this->_orionVersion > $version;
                    case ">=":
                        return $this->_orionVersion >= $version;
                    case "<":
                        return $this->_orionVersion < $version;
                    case "<=":
                        return $this->_orionVersion <= $version;
                    default:
                        return $this->_orionVersion == $version;
                }
            } else {
                if ($this->serverInfo()) {
                    $this->checkVersion($version, $op);
                } else {
                    return false;
                }
            }
        } catch (Exception $e) {
            return FALSE;
        }
    }

    /**
     * 
     * This method get all entity types
     * Since version 0.15.0 Is possible to get entity types using v1/contextTypes
     *
     * @param  mixed  $version String or Float Version eg. "0.15.0" or 0.15
     * @param  string  $op Logical Operations to compare versions
     * @return boolean  
     */
    public function getEntityTypes($type = false) {
        //Check if server version is 0.15 or Greater 
        if ($this->checkVersion("0.15.0", ">=")) {
            if (isset($this->_entityTypes)) {
                return $this->_entityTypes;
            } else {
                $url = $this->serverUrl . "v1/contextTypes";
                $ret = $this->restRequest($url, 'GET');

                $Context = new Context\Context($ret);
                if (is_object($Context->__toObject()) && isset($Context->__toObject()->types)) {
                    $types = $Context->__toObject()->types;

                    foreach ($types as $t) {
                        $Columns[$t->name] = $t->attributes;
                    }
                    $this->_entityTypes = $Columns;
                    return $this->_entityTypes;
                } else {
                    throw new Exception('Invalid Request', 500, null);
                }
            }
        } else {
            throw new Exception('Not Supported in this currently Orion Version', 500, null);
        }
    }

    /**
     * This method build a "gridview" like database view, where attributes and
     *  ID are colums with their respective values as rows for each entity
     * With this way is possible shows entity context type as database tables
     *
     * @param  string  $type Selected Type
     * @return Context object  
     * 
     */
    public function getEntityAttributeView($type = false, $offset = 0, $limit = 1000, $details = "on") {
        $Entities = array();
        $Columns = array();

        if ($type) {
            $url = $this->url . "contextEntityTypes/" . $type;
        } else {
            $url = $this->url . "contextEntityTypes/";
        }

        //Need improvments sinse paging was implemented
        $ret = $this->restRequest($url . "?offset=$offset&limit=$limit&details=$details", 'GET');

        $Context = new Context\Context($ret);

        $keysindex = array();


        $ContextOBject = $Context->__toObject();

        if (is_object($ContextOBject) && isset($ContextOBject->contextResponses)) {
            foreach ($ContextOBject->contextResponses as $entity) {
                $t = $entity->contextElement->type;
                $id = $entity->contextElement->id;

                if (!array_key_exists($t, $keysindex)) {
                    $keysindex[$t] = 0;
                }

                if (!array_key_exists($t, $Columns)) {
                    $Columns[$t] = array("id", "__original");
                }
                $i = $keysindex[$t];

                $Entities[$t][$i]['id'] = $id;

                foreach ($entity->contextElement->attributes as $value) {
                    $Entities[$t][$i][$value->name] = $value->value;
                    array_push($Columns[$t], $value->name);
                }

                $Entities[$t][$i]['__original'] = $entity->contextElement;

                $keysindex[$t] ++;
            }


            if ($this->checkVersion("0.15.0", ">=")) {
                if (!isset($this->_entityTypes)) {
                    $this->getEntityTypes();
                }
                foreach ($this->_entityTypes as $t => $attr) {
                    $Columns[$t] = array("id", "__original");
                    foreach ($attr as $a) {
                        array_push($Columns[$t], $a->name);
                    }
                }
            } else {
                //Slow
                foreach ($Columns as $c => $column) {
                    $Columns[$c] = array_unique($column);
                }
            }

            //Magic: Create Empy columns for different entities attribute of the same type
            foreach ($Entities as $EntityType => $EntityGroup) {
                foreach ($EntityGroup as $key => $EntityRow) {
                    $diff = array_diff($Columns[$EntityType], array_keys($EntityRow));
                    if (count($diff) > 0) {
                        foreach ($diff as $column) {
                            $Entities[$EntityType][$key][$column] = null; //Empty value
                        }
                    }
                }
            }
        }
//        }

        return array("entities" => $Entities, "columns" => $Columns);
    }

    /**
     * This method returns Context Entities
     *
     * @param type $type Selected Type
     * @param type $offset
     * @param type $limit
     * @param type $details
     * @return  Entities object  
     */
    public function getEntities($type = false, $offset = 0, $limit = 1000, $details = "on") {
        if ($type) {
            $url = $this->url . "contextEntityTypes/" . $type;
        } else {
            $url = $this->url . "contextEntityTypes/";
        }

        $ret = $this->restRequest($url . "?offset=$offset&limit=$limit&details=$details", 'GET');
        $Context = new Context\Context($ret);
        $Entities = array();

        
        $ContextObj = $Context->__toObject();
        if (is_object($ContextObj)) {
            foreach ($ContextObj->contextResponses as $entity) {
                $t = $entity->contextElement->type;
                $id = $entity->contextElement->id;
                $Entities[$t][$id] = $entity->contextElement->attributes;
            }
        }




        return $Entities;
    }

    /*     * ******************************************************************************
     * Convenience Operations 
     * Details : https://docs.google.com/spreadsheet/ccc?key=0Aj_S9VF3rt5DdEhqZHlBaGVURmhZRDY3aDRBdlpHS3c#gid=0
     * ***************************************************************************** */

    /**
     * Convenience Operations Get
     * @param  string  $url http:// or https:// is required
     * @return string 
     * 
     */
    public function convenienceGet($url) {
        try {
            $url = $this->url . $url;
            return $this->restRequest($url, 'GET');
        } catch (Exception $e) {
            var_dump($e);
        }
    }

    /**
     * Convenience Operations POST:
     * @param  string  $url http:// or https:// is required
     * @param  /Orion/Context  $reqBody Context Object
     * @return string 
     * 
     */
    public function conveniencePOST($url, $reqBody) {
        try {
            $url = $this->url . $url;
            return $this->restRequest($url, 'POST', $reqBody);
        } catch (Exception $e) {
            var_dump($e);
        }
    }

    /**
     * Convenience Operations PUT:
     * @param  string  $url http:// or https:// is required
     * @param  /Orion/Context  $reqBody Context Object
     * @return string 
     * 
     */
    public function conveniencePUT($url, $reqBody) {
        try {
            $url = $this->url . $url;
            return $this->restRequest($url, 'PUT', $reqBody);
        } catch (Exception $e) {
            var_dump($e);
        }
    }

    /**
     * Convenience Operations DELETE:
     * @param  string  $url http:// or https:// is required
     * @return string 
     * 
     */
    public function convenienceDELETE($url) {
        try {
            $url = $this->url . $url;
            return $this->restRequest($url, 'DELETE');
        } catch (Exception $e) {
            var_dump($e);
        }
    }

    /*     * ******************************************************************************
     * Orion Context Broker API standard operations
     * ***************************************************************************** */

    /**
     * Register Context:
     * @param  /Orion/Context  $reqBody 
     * @return string 
     * 
     */
    public function registerContext($reqBody) {
        try {
            $url = $this->url . "registerContext";
            return $this->restRequest($url, 'POST', $reqBody);
        } catch (Exception $e) {
            var_dump($e);
        }
    }

    /**
     * Update Context:
     * @param  /Orion/Context  $reqBody 
     * @return string 
     * 
     */
    public function updateContext($reqBody) {
        try {
            $url = $this->url . "updateContext";
            return $this->restRequest($url, 'POST', $reqBody);
        } catch (\ErrorException $e) {
            echo $e->__toString();
            $erro = array("erro" => $e->getMessage());
            return json_encode($erro);
        }
    }

    /**
     * Subscribe Context:
     * @param  /Orion/Context  $reqBody 
     * @return string 
     * 
     */
    public function subscribeContext($reqBody) {
        try {
            $url = $this->url . "subscribeContext";
            return $this->restRequest($url, 'POST', $reqBody);
        } catch (Exception $e) {
            var_dump($e);
        }
    }

    /**
     * Unsubscribe Context:
     * @param  string  $subscriptionId 
     * @return string 
     * 
     */
    public function unsubscribeContext($subscriptionId) {
        try {
            $url = $this->url . "unsubscribeContext";
            $context = new \Orion\Context\ContextFactory();
            $context->put("subscriptionId", $subscriptionId);
            $reqBody = $context->getContext();

            return $this->restRequest($url, 'POST', $reqBody);
        } catch (Exception $e) {
            var_dump($e);
        }
    }

    /**
     * Query Context:
     * @param  /Orion/Context  $reqBody 
     * @param  int  $limit 
     * @param  int  $offset 
     * @param  boolean  $details 
     * @return string 
     * 
     */
    public function queryContext($reqBody, $limit = 100, $offset = 0, $details = true) {
        try {
            $urldetails = ($details) ? "on" : "off";
            $url = $this->url . "queryContext?offset=$offset&limit=$limit&details=$urldetails";
            return $this->restRequest($url, 'POST', $reqBody);
        } catch (Exception $e) {
            var_dump($e);
        }
    }

    /**
     * updateContextSubscription Context:
     * @param  /Orion/Context  $reqBody 
     * @return string 
     * 
     */
    public function updateContextSubscription($reqBody) {
        try {
            $url = $this->url . "updateContextSubscription";
            return $this->restRequest($url, 'POST', $reqBody);
        } catch (Exception $e) {
            var_dump($e);
        }
    }

}

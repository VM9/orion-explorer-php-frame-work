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

/**
 * Orion NGSIAPIv1 Class
 *  
 * @package      Orion
 * @author      Leonan Carvalho <j.leonancarvalho@gmail.com>
 * @since      1.0.0
 * 
 * @property \Orion\Context  Context Controller
 * @property \Orion\Utils    Http Requiest Utils
 */
class NGSIAPIv1 extends AbstractNGSI implements NGSIInterface {

    /**
     * Constructor
     * @param  string $ServerAddress String that contain IPv4 Address or Hostname
     * @param  mixed $port String or Integer that contain Port Number Default: 1026
     * @param  string $type String ContentType only json is supported actually Default: application/json
     * @param  array $headers Array With headers key:value 
     */
    public function __construct($ServerAddress, $port = '1026', $type = "application/json", $headers = array()) {
        $this->apiversion = "v1";
        parent::__construct($ServerAddress, $port, $type, $headers);        
    }
    
    /**
     * MUST BE FIXED TO RETURN ONLY TYPES
     * This method get all entity types
     * Since version 0.15.0 Is possible to get entity types using v1/contextTypes
     */
    public function getEntityTypes($type = false) {
        //Check if server version is 0.15 or Greater 
        if ($this->checkVersion("0.15.0", ">=")) {
            if (isset($this->_entityTypes)) {
                return $this->_entityTypes;
            } else {
                if($type){
                    $url = $this->serverUrl .  $this->apiversion . "/contextTypes/$type";
                }else{
                    $url = $this->serverUrl . $this->apiversion . "/contextTypes";
                }
                $ret = $this->restRequest($url, 'GET')->getResponseBody();

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
     * This method build a view like database view, where attributes and ID are colums
     * With this way is possible shows entity context type as database tables.
     *
     * @param  string  $type Selected Type
     * @return Context object  
     * 
     */
    public function getEntityAttributeView($type = false, $offset = 0, $limit = 1000, $details = true) {
        $Entities = array();
        $Columns = array();

        if ($type) {
            $url = $this->url . "contextEntityTypes/" . $type;
        } else {
            $url = $this->url . "contextEntities";
        }
        
        $_details = ($details)? "on" : "off";
        
        //Need improvments sinse paging was implemented
        $ret = $this->restRequest($url . "?offset=$offset&limit=$limit&details=$_details", 'GET')->getResponseBody();

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
//                        var_dump($a);
                        array_push($Columns[$t], $a);
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
     * @param mixed $type Selected Type
     * @param mixed $offset
     * @param mixed $limit
     * @param mixed $details
     * @return  Entities object  
     */
    public function getEntities($type = false, $offset = 0, $limit = 1000, $details = "on") {
        if ($type) {
            $url = $this->url . "contextTypes/" . $type;
        } else {
            $url = $this->url . "contextEntities/";
        }

        $ret = $this->restRequest($url . "?offset=$offset&limit=$limit&details=$details", 'GET')->getResponseBody();
        $Context = (new Context\Context($ret))->get();
        $Entities = [];
        
        if($Context instanceof \stdClass && isset($Context->errorCode)){
            switch ((int) $Context->errorCode->code) {
                case 404:
                case 500:
                    throw new Exception\GeneralException($Context->errorCode->reasonPhrase,(int)$Context->errorCode->code, null, $ret);
                default:
                case 200:
                    break;
            }
        }else{
            throw new Exception\GeneralException("Malformed Orion Response",500, null, $ret);
        }
        
        if(isset($Context->contextResponses) && count($Context->contextResponses) > 0){
            foreach ($Context->contextResponses as $entity) {
                $t = $entity->contextElement->type;
                $id = $entity->contextElement->id;
                
                if(!array_key_exists($t, $Entities)){
                    $Entities[$t] = [];
                }
                $Entities[$t][$id] = $entity->contextElement->attributes;
            }
        }




        return new Context\Context($Entities);
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
            return  new Context\Context($this->restRequest($url, 'GET')->getResponseBody());
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
            return  new Context\Context($this->restRequest($url, 'POST', $reqBody)->getResponseBody());
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
            return  new Context\Context($this->restRequest($url, 'PUT', $reqBody)->getResponseBody());
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
            return  new Context\Context($this->restRequest($url, 'DELETE')->getResponseBody());
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
            return $this->restRequest($url, 'POST', $reqBody)->getResponseBody();
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
    public function updateContext(Context\Context $reqBody) {
        try {
            $url = $this->url . "updateContext";
            return $this->restRequest($url, 'POST', $reqBody->get())->getResponseBody();
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
    public function subscribeContext(Context\Context $reqBody) {
        try {
            $url = $this->url . "subscribeContext";
            return $this->restRequest($url, 'POST', $reqBody->get())->getResponseBody();
        } catch (Exception $e) {
            var_dump($e);
        }
    }

    /**
     * Unsubscribe Context:
     * @param  string  $subscriptionId 
     * @return Context\Context 
     * 
     */
    public function unsubscribeContext($subscriptionId) {
        try {
            $url = $this->url . "unsubscribeContext";
            $context = new \Orion\Context\ContextFactory();
            $context->put("subscriptionId", $subscriptionId);
            $reqBody = $context->get();
            
            $ret = $this->restRequest($url, 'POST', $reqBody)->getResponseBody();
            return new Context\Context($ret);
        } catch (\Exception $e) {
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
    public function queryContext(Context\Context $reqBody, $limit = 100, $offset = 0, $details = true) {
        try {
            $urldetails = ($details) ? "on" : "off";
            $url = $this->url . "queryContext?offset=$offset&limit=$limit&details=$urldetails";
            return $this->restRequest($url, 'POST', $reqBody->get())->getResponseBody();
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
    public function updateContextSubscription(Context\Context $reqBody) {
        try {
            $url = $this->url . "updateContextSubscription";
            return $this->restRequest($url, 'POST', $reqBody->get())->getResponseBody();
        } catch (Exception $e) {
            var_dump($e);
        }
    }

}

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

use Orion\Utils\HttpRequest as HTTPClient;

/**
 * Orion NGSIAPIv2 Class
 *  
 * @package      Orion
 * @author      Leonan Carvalho <j.leonancarvalho@gmail.com>
 * @since      1.0.0
 * 
 * @property \Orion\Context  Context Controller
 * @property \Orion\Utils    Http Requiest Utils
 */
abstract class AbstractNGSI {
    
    const FORBIDDEN_CHARACTERS = "/(<|>|\"|'|=|;|,|\(|\(|^geo:distance$|\s|#|\?|\/|%|&|^orderby$|!|^id$|^type$)/i"; //This REGEX contains all forbiden characters for attribute names

    /**
     * @var string
     */
    protected $ip;

    /**
     * @var string
     */
    protected $apiversion;

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
     * @var Float
     */
    protected $_orionVersion;

    /**
     * HttpClient Headers
     * @var array 
     */
    protected $_headers;

    /**
     *
     * @var string 
     */
    protected $_contentType = "application/json";

    /**
     * Constructor
     * @param  string $ServerAddress String that contain IPv4 Address or Hostname
     * @param  mixed $port String or Integer that contain Port Number Default: 1026
     * @param  string $type String ContentType only json is supported actually Default: application/json
     * @param  array $headers Array With headers key:value 
     */
    public function __construct($ServerAddress, $port = '1026', $contentType = "application/json", $headers = array(), $protocol = "http://") {
        $this->ip = (string) $ServerAddress;
        $this->port = $port;
        $this->serverUrl = $protocol . $ServerAddress . ":" . $port . "/";
        $this->url = "{$this->serverUrl}{$this->apiversion}/";

        $this->_headers = (array) $headers;
        $this->_contentType = $contentType;
    }

//    protected function initHttpClient() {
//        //Setup Http Requests
//        $this->restReq = new HTTPClient();
//        $restReq->setAcceptType($this->_contentType);
//        $restReq->setContentType($this->_contentType);
//
//
//        if (count($this->_headers) > 0) {
//            foreach ($this->_headers as $header => $value) {
//                $restReq->addCustonHeader($header, $value);
//            }
//        }
//    }

    /**
     * 
     * eg: X-Auth-Token: HLcJPAliV55X5zI68DfDZgVI-by2MBR0s3QhJF7WwwOU0u5AO3f85ycMouzxr3UWGfbCjO3ODcaM6ybt4wUdbV
     * 
     * @param string $header Name of Header Key
     * @param string $value Token
     */
    public function setHeader($header, $value) {
        $this->_headers[$header] = $value;
    }
    
    /**
     * Set Fiware Service if multitenancy is active 
     * @param type $name
     */
    public function setService($name){
        $this->setHeader("Fiware-Service", $name);
    }
    
    /**
     * Get Orion URL
     * @param string $path
     */
    public function getUrl($path = ""){
        return $url = $this->url . $path;
    }

    /**
     * 
     * This method will run rest requests to Orion API and return the response
     * If Orion API returns a Ok Status, such 200
     * Otherwise, the response body is ignored. 
     *
     * @param  strinq  $url should contains https:// or https://
     * @param  string  $method GET/POST/DELETE/PUT/PATCH 
     * @return HTTPClient
     * @throws Exception\GeneralException
     */
    public function restRequest($url, $method = "GET", $reqBody = "", $mime = false, $accept = "application/json") {
        try {
            
            $restReq = new HTTPClient();
            //Orion accepts no payload for GET/DELETE requests. HTTP header Content-Type is thus forbidden
            if($method != "GET" && $method != "DELETE"){
                if($mime){
                    $restReq->setContentType($mime);
                }else{
                    $restReq->setContentType($this->_contentType);
                }
            }elseif($method == "GET" && $mime){
                 $restReq->setContentType($mime);
                 $restReq->setAcceptType($accept);
            }else{
                $restReq->setContentType(null);
            }

            if (count($this->_headers) > 0) {
                foreach ($this->_headers as $header => $value) {
                    $restReq->addCustonHeader($header, $value);
                }
            }

            $restReq->setUrl($url);
            $restReq->setMethod($method);
            
            
            if ($reqBody != "" && !empty($reqBody) && null != $reqBody) {
                if (is_array($reqBody) || is_object($reqBody)) {
                    $reqBody = json_encode($reqBody, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                }
                $restReq->buildPostBody($reqBody);
            }
            $restReq->execute(); // Run the request...
            return $restReq;
        } catch (\Exception $e) {
            throw new Exception\GeneralException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * 
     * This method checks IP connectivity using a socket connection.
     * Is used a timeout very low to not delay responses that use it 
     * Any authentication will be ignored.
     * If a Firewall is applied may this test will fail.
     *
     * On CentOS fsockopen needs permition to network connect, to allow use this cmd:
     *  setsebool -P httpd_can_network_connect 1
     * http://yml.com/fv-b-1-619/selinux--apache-httpd--php-establishing-socket-connections-using-fsockopen---et-al.html
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
                $ret = $this->restRequest($url . "/version", 'GET')->getResponseBody();
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
                    if (isset($Version) && is_object($Version)) {
                        $info["version"] = $Version->version;
                        $info["uptime"] = $Version->uptime;
                    } else {
                        $info["version"] = null;
                        $info["uptime"] = null;
//                        echo "<pre>"; var_dump($Version);exit;
                    }
                }
                $this->_orionVersion = floatval($info["version"]);

                return $info;
            } else {
                return array("version" => null, "uptime" => null);
            }
        } catch (\Exception $e) {
            return array("version" => null, "uptime" => null, "error" => $e);
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

}

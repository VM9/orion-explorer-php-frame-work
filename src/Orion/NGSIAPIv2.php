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
class NGSIAPIv2 extends AbstractNGSI implements NGSIInterface {

    /**
     * Constructor
     * @param  string $ServerAddress String that contain IPv4 Address or Hostname
     * @param  mixed $port String or Integer that contain Port Number Default: 1026
     * @param  string $type String ContentType only json is supported actually Default: application/json
     * @param  array $headers Array With headers key:value 
     */
    public function __construct($ServerAddress, $port = '1026', $type = "application/json", $headers = array(), $protocol = "http://") {
        $this->apiversion = "v2";
        parent::__construct($ServerAddress, $port, $type, $headers, $protocol);
    }

    /**
     * 
     * @param type $type
     * @param type $offset
     * @param type $limit
     * @param type $options attrs,orderBy,options[count*,keyValues*,values*]
     * @return \Orion\Context\Context
     */
    public function getEntities($type = false, $offset = 0, $limit = 1000, $ptions = []) {
        $Entities = new Context\Entity($this);

        if ($type) {
            $Entities->_setType($type);
        }
        $options["offset"] = $offset;
        $options["limit"] = $limit;

        return $Entities->getContext($options);
    }

    public function getEntityAttributeView($type = false, $offset = 0, $limit = 1000, $details = true) {
        
    }

    /**
     * 
     * @return \Orion\Context\Context
     */
    public function getEntityTypes() {
        return $this->get("types");
    }

    //API V2 Operations

    /**
     * Generic Post Request
     * @param type $url
     * @param type $requestBody
     * @return HTTPClient
     */
    public function post($url, $requestBody = null) {
        $posturl = $this->url . $url;
        return $this->restRequest($posturl, 'POST', $requestBody);
    }

    /**
     * 
     * @param type $url
     * @param \Orion\Context\ContextFactory $context
     * @return HTTPClient
     * @throws type
     * @throws Exception\GeneralException
     */
    public function create($url, Context\ContextFactory $context) {
        $restReq = $this->post($url, $context->get());
//        $context->getContext()->prettyPrint();exit;
        $ret = $restReq->getResponseBody();
        $retInfo = $restReq->getResponseInfo();
        if (is_array($retInfo) && array_key_exists("http_code", $retInfo) && $retInfo['http_code'] == 201 //Te httpd request has executed with success
        ) {
            /**
             * Entity Creation:
             * "Upon receipt of this request, the broker will create the entity in its internal database,
             *  it will set the values for its attributes and it will respond with a 201 Created HTTP code."
             * 
             * Subscription Creation:
             * "The response corresponding to that request uses 201 Created as 
             * HTTP response code. In addition, it contains a Location header 
             * which holds the subscription ID: a 24 hexadecimal number used for
             *  updating and cancelling the subscription. "
             * In this case, the 
             */
//            if ($url == "entities" //Is entity endpoint
//                    && null != $context->get('id')//Have a valid Id on context
//            ) {
//                $Entity = new Context\Entity($this, $context->get('id'), $context->get('type'));
//                $responseContext = $Entity->getContext();
//            }
        }

        $responseContext = new Context\Context($ret);
        if (isset($responseContext->get()->error)) {
            $errorResponse = $responseContext->get();
            $exception_name = "\\Orion\\Exception\\{$errorResponse->error}";
            if (class_exists($exception_name)) {
                throw new $exception_name($errorResponse->description, 500, null, $restReq);
            } else {
                throw new \Orion\Exception\GeneralException($errorResponse->error . " : " . $errorResponse->description, 500, null, $restReq);
            }
        }
        return $restReq;
    }

    /**
     * 
     * @param type $url
     * @param \Orion\Context\ContextFactory $context
     * @return HTTPClient
     * @throws \Orion\Exception\GeneralException
     */
    public function put($url, Context\ContextFactory $context = null, $raw = null) {
        $patchUrl = $this->getUrl($url);
        if (null != $raw) {
            $restReq = $this->restRequest($patchUrl, 'PUT', $raw, "text/plain");
        } else {
            $restReq = $this->restRequest($patchUrl, 'PUT', $context->get());
        }
        $ret = $restReq->getResponseBody();
        $retInfo = $restReq->getResponseInfo();


        if ($url == "entities" //Is entity endpoint
                && null != $context && null != $context->get('id')//Have a valid Id on context
                && is_array($retInfo) && array_key_exists("http_code", $retInfo) && $retInfo['http_code'] == 204 //Te httpd request has executed with success
        ) {

            /**
             * "Upon receipt of this request, the broker updates the values for the
             * entity attributes in its internal database and responds with 204 No Content."
             */
        }

        $responseContext = new Context\Context($ret);
        if (isset($responseContext->get()->error)) {
            $errorResponse = $responseContext->get();
            $exception_name = "\\Orion\\Exception\\{$errorResponse->error}";
            if (class_exists($exception_name)) {
                throw new $exception_name($errorResponse->description, 500, null, $restReq);
            } else {
                throw new \Orion\Exception\GeneralException($errorResponse->error . " : " . $errorResponse->description, 500, null, $restReq);
            }
        }
        return $restReq;
    }

    /**
     * 
     * @param type $url
     * @param \Orion\Context\ContextFactory $context
     * @return HTTPClient
     * @throws Exception\GeneralException
     */
    public function patch($url, Context\ContextFactory $context) {
        $patchUrl = $this->getUrl($url);
        $restReq = $this->restRequest($patchUrl, 'PATCH', $context->get());
        $ret = $restReq->getResponseBody();
        $retInfo = $restReq->getResponseInfo();


        if ($url == "entities" //Is entity endpoint
                && null != $context->get('id')//Have a valid Id on context
                && is_array($retInfo) && array_key_exists("http_code", $retInfo) && $retInfo['http_code'] == 201 //Te httpd request has executed with success
        ) {

            /**
             * "Upon receipt of this request, the broker updates the values for the
             * entity attributes in its internal database and responds with 204 No Content."
             */
        }

        $responseContext = new Context\Context($ret);
        if (isset($responseContext->get()->error)) {
            $errorResponse = $responseContext->get();
            $exception_name = "\\Orion\\Exception\\{$errorResponse->error}";
            if (class_exists($exception_name)) {
                throw new $exception_name($errorResponse->description, 500, null, $restReq);
            } else {
                throw new \Orion\Exception\GeneralException($errorResponse->error . " : " . $errorResponse->description, 500, null, $restReq);
            }
        }
        return $restReq;
    }

    /**
     * Generic get
     * @param type $url
     * @param \Orion\Utils\HttpRequest $request
     * @param type $mime
     * @return \Orion\Context\Context
     * @throws type
     * @throws \Orion\Exception\GeneralException
     */
    public function get($url, &$request = null, $mime = false, $accept = "application/json") {
        $geturl = $this->getUrl($url);
        $request = $this->restRequest($geturl, "GET", null, $mime, $accept);

        $responseContext = new Context\Context($request->getResponseBody());
        if (isset($responseContext->get()->error)) {
            $errorResponse = $responseContext->get();
            $exception_name = "\\Orion\\Exception\\{$errorResponse->error}";
            if (class_exists($exception_name)) {
                throw new $exception_name($errorResponse->description, 500, null, $request);
            } else {
                throw new \Orion\Exception\GeneralException($errorResponse->error . " : " . $errorResponse->description, 500, null, $request);
            }
        }

        return $responseContext;
    }

    /**
     * 
     * @param type $url
     * @param type $request
     * @return HTTPClient
     */
    public function delete($url) {
        $deleteurl = $this->getUrl($url);
        $request = $this->restRequest($deleteurl, "DELETE");
        $responseContext = new Context\Context($request->getResponseBody());
        if (isset($responseContext->get()->error)) {
            $errorResponse = $responseContext->get();
            $exception_name = "\\Orion\\Exception\\{$errorResponse->error}";
            if (class_exists($exception_name)) {
                throw new $exception_name($errorResponse->description, 500, null, $request);
            } else {
                throw new \Orion\Exception\GeneralException($errorResponse->error . " : " . $errorResponse->description, 500, null, $request);
            }
        }
        return $request;
    }

}

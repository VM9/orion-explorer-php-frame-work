<?php

/**
 * HttpRequest Util
 * 
 * Curl implementation for HTTP requests.
 * 
 * This file is part of Orion Context Explorer FrameWork
 * 
 *
 * @copyright   Leonan Carvalho 
 * @author      Leonan Carvalho <j.leonancarvalho@gmail.com>
 * @link        http://br.linkedin.com/in/leonancarvalho/
 * @license     http://opensource.org/licenses/MIT
 * @version     1.0.0
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

namespace Orion\Utils;

/**
 * HttpRequest Class
 * 
 * 
 * 
 * @package      Orion
 * @author      Leonan Carvalho <j.leonancarvalho@gmail.com>
 * @since      1.0.0
 * 
 */
class HttpRequest {

    /**
     * @var string 
     */
    protected $url;

    /**
     * 
     * @var string 
     */
    protected $method;

    /**
     *
     * @var string 
     */
    protected $request_body;

    /**
     *
     * @var int 
     */
    protected $request_length;

    /**
     *
     * @var string 
     */
    protected $username;

    /**
     *
     * @var string 
     */
    protected $password;

    /**
     *
     * @var string 
     */
    protected $accept_type;

    /**
     * @var string
     */
    protected $content_type;

    /**
     * @var string 
     */
    protected $response_body;

    /**
     * @var string 
     */
    protected $response_info;

    /**
     * @var array
     */
    protected $file_to_upload = array();

    /**
     * HttpRequest Provide HTTP Requests based on cURL lib methods.
     * 
     * @depends cUrl
     * @param string $url
     * @param string $method
     * @param string $request_body
     * @param array $custonHeader
     */
    public function __construct($url = null, $method = 'GET', $request_body = null, $custonHeader = array()) {
        $this->url = $url;
        $this->method = $method;
        $this->request_body = $request_body;
        $this->request_length = 0;
        $this->username = null;
        $this->password = null;
        $this->accept_type = 'application/json';
        $this->content_type = 'application/json';
        $this->response_body = null;
        $this->response_info = null;
        $this->file_to_upload = array();

        $this->custonHeader = $custonHeader;


        if ($this->request_body !== null) {
            $this->buildPostBody();
        }
    }

    /**
     *  Flush Class
     */
    public function flush() {
        $this->request_body = null;
        $this->request_length = 0;
        $this->method = 'GET';
        $this->response_body = null;
        $this->response_info = null;
        $this->content_type = 'application/json';
        $this->accept_type = 'application/json';
        $this->file_to_upload = null;
    }

    /**
     * Execute curl request for each type of HTTP Request
     * 
     * @throws \InvalidArgumentException
     */
    public function execute() {
        $ch = curl_init();
        $this->setAuth($ch);

        try {
            switch (strtoupper($this->method)) {
                case 'GET':
                    $this->executeGet($ch);
                    break;
                case 'POST':
                    $this->executePost($ch);
                    break;
                case 'PUT':
                    $this->executePut($ch);
                    break;
                case 'DELETE':
                    $this->executeDelete($ch);
                    break;
                // This custom case is used to execute a Multipart PUT request
                case 'PUT_MP':
                    $this->method = 'PUT';
                    $this->executePutMultipart($ch);
                    break;
                case 'POST_MP':
                    $this->method = 'POST';
                    $this->executePostMultipart($ch);
                    break;
                default:
                    throw new \InvalidArgumentException('Current method (' . $this->method . ') is an invalid REST method.');
            }
        } catch (\InvalidArgumentException $e) {
            curl_close($ch);
            throw $e;
        } catch (\Exception $e) {
            curl_close($ch);
            throw $e;
        }
    }

    /**
     * Build RAW post body
     * 
     * @param string $data
     */
    public function buildPostBody($data = null) {
        $reqBody = ($data !== null) ? $data : $this->request_body;


        $this->request_body = $reqBody;
    }

    /**
     * Runs Get Request
     * 
     * @param \CURL $ch
     */
    protected function executeGet($ch) {
        $this->doExecute($ch);
    }

    /**
     * Runs Post Request
     * @param \CURL $ch
     */
    protected function executePost($ch) {
        if (!is_string($this->request_body)) {
            $this->buildPostBody();
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: .' . $this->content_type
        ));

        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->request_body);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');

        $this->doExecute($ch);
    }

    /**
     * Runs PUT multi-part request
     * @param \CURL $ch
     */
    protected function executePutMultipart($ch) {
        $xml = $this->request_body;

        $uri_string = $this->file_to_upload[0];
        $file_name = $this->file_to_upload[1];

        $post = array(
            'ResourceDescriptor' => $xml,
            $uri_string => '@' . $file_name . ';filename=' . basename($file_name)
        );

        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $this->response_body = curl_exec($ch);
        $this->response_info = curl_getinfo($ch);

        curl_close($ch);
    }

    /**
     * Run POST multi-part-data request 
     * @param \CURL $ch
     */
    protected function executePostMultipart($ch) {
        $xml = $this->request_body;

        if (!empty($this->file_to_upload)) {
            $uri_string = $this->file_to_upload[0];
            $file_name = $this->file_to_upload[1];

            $post = array(
                'ResourceDescriptor' => $xml,
                $uri_string => '@' . $file_name . ';filename=' . basename($file_name)
            );
        } else {
            $post = array(
                'ResourceDescriptor' => $xml
            );
        }

        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $this->response_body = curl_exec($ch);
        $this->response_info = curl_getinfo($ch);

        curl_close($ch);
    }

    /**
     * Run PUT request 
     * @param \CURL $ch
     */
    protected function executePut($ch) {

        if (!is_string($this->request_body)) {
            $this->buildPostBody();
        }

        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->request_body);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');

        $this->doExecute($ch);
    }

    /**
     * Run DELETE request 
     * @param \CURL $ch
     */
    protected function executeDelete($ch) {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');

        $this->doExecute($ch);
    }

    /**
     * Perform Curl REquest 
     * 
     * @param object $curlHandle
     */
    protected function doExecute(&$curlHandle) {
        $this->setCurlOpts($curlHandle);
        $this->response_body = curl_exec($curlHandle);
        $this->response_info = curl_getinfo($curlHandle);

        curl_close($curlHandle);
    }

    /**
     * Set Curl  Default Options
     * 
     * @param object $curlHandle
     */
    protected function setCurlOpts(&$curlHandle) {
        curl_setopt($curlHandle, CURLOPT_TIMEOUT, 10);
        curl_setopt($curlHandle, CURLOPT_URL, $this->url);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandle, CURLOPT_COOKIEFILE, '/dev/null');
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $this->getDefaultHeader());
    }

    /**
     * 
     * @return type
     */
    protected function getDefaultHeader() {
        $default = array('Content-Type: ' . $this->content_type,
            'Accept: ' . $this->accept_type);
        
        return array_merge($default, $this->custonHeader);
    }

    /**
     * 
     * @param type $key
     * @param type $value
     */
    public function addCustonHeader($key, $value){        
        $header = $key . ": ".$value;

        array_push( $this->custonHeader, $header);
    }

    /**
     * Set Basic Authentication Headers to Curl Options
     * @param object $curlHandle
     */
    protected function setAuth(&$curlHandle) {
        if ($this->username !== null && $this->password !== null) {
            curl_setopt($curlHandle, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($curlHandle, CURLOPT_USERPWD, base64_encode($this->username . ':' . $this->password));
//                        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
//                        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
        }
    }

    /**
     * Get File to perform Upload
     * 
     * @return string
     */
    public function getFileToUpload() {
        return $this->file_to_upload;
    }

    /**
     * Set File to upload a full path file should be seted.
     * 
     * Eg. /tamp/filename
     * 
     * @param string $filepath
     */
    public function setFileToUpload($filepath) {
        $this->file_to_upload = $filepath;
    }

    /**
     * Get Accept Type
     * @return string
     */
    public function getAcceptType() {
        return $this->accept_type;
    }

    /**
     * Set Accept Type 
     * @param string $accept_type
     */
    public function setAcceptType($accept_type) {
        $this->accept_type = $accept_type;
    }

    /**
     * Get Content Type
     * @return string
     */
    public function getContentType() {
        return $this->content_type;
    }

    /**
     * Set Content Type
     * @param string $content_type
     */
    public function setContentType($content_type) {
        $this->content_type = $content_type;
    }

    /**
     * Get Password
     * @return string
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * Set Password
     * @param string $password
     */
    public function setPassword($password) {
        $this->password = $password;
    }

    /**
     * Get Response Body
     * Its will return response from remote server after request finish
     * @return string
     */
    public function getResponseBody() {
        return $this->response_body;
    }

    /**
     * Get Response Info
     * Its will return response info from remote server after request finish
     * @return string
     */
    public function getResponseInfo() {
        return $this->response_info;
    }

    /**
     * Get URL
     * @return string
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * Set URL
     * http:// or https:// is required
     * @param string $url
     */
    public function setUrl($url) {
        $this->url = $url;
    }

    /**
     * Get User Name to authentications
     * @return type
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     * Set Username to authentications
     * @param type $username
     */
    public function setUsername($username) {
        $this->username = $username;
    }

    /**
     * Get HTTP request method
     * @return string
     */
    public function getmethod() {
        return $this->method;
    }

    /**
     * Set HTTP request method
     * Only GET, POST, PUT, DELETE, POST_MP and PUT_MP is supported
     * @param type $method
     */
    public function setmethod($method) {
        $this->method = $method;
    }

}

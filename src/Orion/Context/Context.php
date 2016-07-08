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

namespace Orion\Context;

/**
 * Orion Context Json Class
 *  
 * @package      Orion
 * @author      Leonan Carvalho <j.leonancarvalho@gmail.com>
 * @since      1.0.0
 * 
 */
class Context {

    /**
     * @var string
     */
    private $_rawcontext;

    /**
     *
     * @var  \stdClass
     */
    private $_context;

    /**
     * Constructor
     * @param  string $context String that contain json response from Orion API
     */
    public function __construct($raw_context = null) {
        if($raw_context instanceof \stdClass){
            $this->_rawcontext = (string) json_encode($raw_context);
        }else{
            if ($raw_context) {
                $this->_rawcontext = (string) $raw_context;
            } else {
                $this->_rawcontext = "{}";
            }
        }
    }

    /**
     * 
     * This method will retirn a Standard Class from json object string
     * @return stdClass
     */
    public function __toObject() {
        $this->_context = json_decode($this->_rawcontext);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \InvalidArgumentException(
            'Unable to decode raw data: ' . json_last_error_msg());
        }
        return $this->_context;
    }

    /**
     * 
     * This method will retirn a Standard Array from json object string
     * @return array
     */
    public function __toArray() {
        $array = json_decode($this->_rawcontext, true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \InvalidArgumentException(
            'Unable to decode raw data: ' . json_last_error_msg());
        }
        return $array;
    }

    /**
     * 
     * This method will retirn a Standard Array from json object string
     * @return string
     */
    public function __toString() {
        return $this->_rawcontext;
    }

    public function get() {
        $Context = $this->__toObject();
       
        return $Context;
    }
    
    public function prettyPrint(){
        echo json_encode($this->__toObject(),JSON_PRETTY_PRINT);
    }

}

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
 * Orion ContextFactory Class
 *  
 * @package      Orion
 * @author      Leonan Carvalho <j.leonancarvalho@gmail.com>
 * @since      1.0.0
 * 
 */
class ContextFactory {

    /**
     * @var stdClass
     */
    private $_context;

    /**
     * Create a context object, 
     * if the base context was setted the context will be create from array structure:
     *  keys will become properties with their respective value
     * @param array $base
     */
    public function __construct(array $base = []) {
        $this->_context = (object) $base;
    }

    /**
     * 
     * Put values based on a key into context object 
     * Update, append values is allowed
     *
     * @param  mixed  $key Key indentifier
     * @param  mixed  $value Value, can by any type
     * @return self  
     */
    public function put($key, $value) {
        $this->_context->$key = $value;
        return $this;
    }

    /**
     * This convenience method create a Entity NGSIv2 attribute format.
     * @param type $name
     * @param type $value
     * @param type $type
     */
    public function addAttribute($name, $value, $type = "Integer", $metadata = null) {
        $attr = (object) [
                    "value" => $value,
                    "type" => $type
        ];
        
        if(null != $metadata){
            $attr->metadata = (object) $metadata;
        }
        
        $this->put($name, $attr);
    }

    /**
     * 
     * Get values based on known key
     *
     * @param  mixed  $key 
     * @return mixed  
     */
    public function get($key = null) {
        if (null != $key) {
            if (isset($this->_context->$key)) {
                return $this->_context->$key;
            } else {
                return null;
            }
        } else {
            return $this->_context;
        }
    }

    /**
     * Return full object
     * @return \Orion\Context\Context
     */
    public function getContext() {
        return new Context($this->_context);
    }

}

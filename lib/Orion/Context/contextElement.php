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
 * Orion contextElement Class
 *  
 * @package      Orion
 * @author      Leonan Carvalho <j.leonancarvalho@gmail.com>
 * @since      1.0.0
 * 
 */
class contextElement {

    /**
     * @var  Orion\Context\ContextFactory
     */
    private $_contextElement;

    /**
     * @var  Array
     */
    private $_attributes = array();

    /**
     * Constructor
     * @param  mixed $id String with entity id
     * @param  mixed $type String with entity type
     * @param  boolean $isPattern insert is Parttern attribute
     */
    public function __construct($id = false, $type = false, $isPattern = false) {
        $this->_contextElement = new \Orion\Context\ContextFactory();
        if ($id)
            $this->_contextElement->put("id", $id);
        if ($type)
            $this->_contextElement->put("type", $type);

        $this->_contextElement->put("isPattern", ($isPattern) ? "true" : "false");
    }

    /**
     * 
     * This method will append attributes to Attributes Array
     *
     * @param  strinq  $name Attribute Name
     * @param  string  $type Attribute Type
     * @param  string  $value Attribute value
     * @param  Orion\Context\ContextFactory  $metadata should be a context build by Orion\Context\ContextFactory
     * @return self
     */
    public function addAttrinbute($name, $type, $value, $metadata = false) {

        //Build Attribute Context
        $attribute = new \Orion\Context\ContextFactory();
        $attribute->put("name", $name);
        $attribute->put("type", $type);
        $attribute->put("value", $value);

        if ($metadata) {
            $attribute->put("metadatas", $metadata);
        }

        $this->_attributes[] = $attribute->getContext();
        return $this;
    }

    /**
     * 
     * Get context Element ready to be attached to main Entity Object
     *
     * @param  boolean  $appendattributes lue
     * @return Orion\Context\ContextFactory
     */
    public function getElement($appendattributes = true) {
        if ($appendattributes) {
            $this->_contextElement->put("attributes", $this->_attributes);
        }
        return $this->_contextElement->getContext();
    }

}

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

namespace Orion\Operations;

/**
 * Orion subscribeContext Class
 * https://forge.fi-ware.org/plugins/mediawiki/wiki/fiware/index.php/Publish/Subscribe_Broker_-_Orion_Context_Broker_-_User_and_Programmers_Guide#Context_subscriptions
 * @package      Orion
 * @author      Leonan Carvalho <j.leonancarvalho@gmail.com>
 * @since      1.0.0
 * 
 */
class updateContext {

     /**
     * @var  Orion\Context\ContextFactory
     */
    private $_context;
    
    /**
     * @var array
     */
    private $_elements;
    
    /**
     * @var Orion\Context\ContextFactory
     */
    private $_currentelement;
    
    /**
     * @var string
     */
    private $_action;

    /**
     * Constructor
     */
    public function __construct() {
        $this->_elements = array();
        $this->_context = new \Orion\Context\ContextFactory();
        $this->_currentelement = false;
    }

    /**
     * Set updateContext Action 
     * 
     * @param string $action  UPDATE|APPEND|DELETE 
     * Default: UPDATE
     */

    public function setAction($action = "UPDATE") {
        $this->_action = $action;
        return $this;
    }

    /**
     * 
     * Commit Element and clean current element
     * @return
     * 
     */
    private function commitElement() {
        if ($this->_currentelement) {
            $this->_elements[] = $this->_currentelement->getElement();
            $this->_currentelement = null;
        }
    }

    /**
     * 
     * Append element to queryContext Object
     * 
     * @param  mixed $id String with entity id
     * @param  mixed $type String with entity type
     * @param  boolean $isPattern insert is Parttern attribute
     * @return self
     */
    public function addElement($id, $type, $isPattern = false) {
        $this->commitElement();

        $this->_currentelement = new \Orion\Context\contextElement($id, $type, $isPattern);

        return $this;
    }

    /**
     * 
     * This method will append attributes to Attributes Array
     *
     * @param  strinq  $name Attribute Name
     * @param  string  $type Attribute Type
     * @param  string  $value Attribute value
     * @return self
     * 
     */
    public function addAttrinbute($name, $type, $value) {
        $this->_currentelement->addAttrinbute($name, $type, $value);
        return $this;
    }

    /**
     * 
     * This method will append Geolocation attributes to Attributes Array
     *
     * @param type $lat
     * @param type $lng
     * @return \Orion\Operations\updateContext
     */
    public function addGeolocation($lat, $lng) {
        $geometadata = new \Orion\Context\ContextFactory();
        $geometadata->put("name", "location");
        $geometadata->put("type", "string");
        $geometadata->put("value", "WSG84");

        $metadata = array($geometadata->getContext());

        $value = $lat . ", " . $lng; //EX: "40.418889, -23.691944"

        $this->_currentelement->addAttrinbute("position", "coords", $value, $metadata);
        return $this;
    }

    /*
     * 
     * @return $this->_elements
     * 
     */

    public function getElements() {
        return $this->_elements;
    }

    /**
     * 
     * Returns stdClass to be converted in a json or xml
     * Its build 'contextElements' exactly as archteture used in Json requets
     * 
     * @return stdClass
     */
    public function getRequest() {
        $this->commitElement();

        $this->_context->put("contextElements", $this->_elements);
        $this->_context->put("updateAction", $this->_action);

        return $this->_context->getContext();
    }

}

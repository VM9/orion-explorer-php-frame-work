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
class updateSubscription {

    /**
     * @var  Orion\Context\ContextFactory
     */
    private $_context;

    /**
     * @var  array
     */
    private $_notifyConditions;

    /**
     * Constructor
     * 
     * @param type $subscriptionId
     */
    public function __construct($subscriptionId) {
        $this->_notifyConditions = array();
        $this->_context = new \Orion\Context\ContextFactory();

        $this->_context->put("subscriptionId", (string) $subscriptionId);
    }

    /**
     * Build notifyCondition object
     * 
     * @param   string $type ONTIMEINTERVAL | ONCHANGE (!ONVALUE not supported yet)
     * When ONTIMEINTERVAL use time as conditional value(ISO 8601 format)
     * EX: PT10S - 10 of interval 
     * 
     * When ONCHANGE use a attribute name to track changes only at this attribute
     * an empty  means all attributes
     * 
     * @param  array $condValues insert is Parttern attribute
     * @param  mixed $throttling throttling element is used to specify a minimum inter-notification arrival time(ISO 8601 format)
     */
    public function notifyConditions($type, $condValues = array()) {
        if (!is_array($condValues)) {
            $condValues = array($condValues);
        }

        $context = new \Orion\Context\ContextFactory();
        $context->put("type", $type);
        $context->put("condValues", $condValues);

        $this->_notifyConditions[] = $context->getContext();
        return $this;
    }

    /**
     * 
     * Returns stdClass to be converted in a json or xml
     * Its build 'contextElements' exactly as archteture used in Json requets
     * 
     * @return stdClass
     */
    public function getRequest() {
        $this->_context->put("notifyConditions", $this->_notifyConditions);

        return $this->_context->getContext();
    }

}

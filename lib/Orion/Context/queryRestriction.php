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
 * Orion queryRestriction Class
 *  
 * @package      Orion
 * @author      Leonan Carvalho <j.leonancarvalho@gmail.com>
 * @since      1.0.0
 * 
 */
class queryRestriction {

    /**
     * @var  Orion\Context\ContextFactory
     */
    private $_context;

    /**
     * @var  Orion\Context\ContextFactory
     */
    private $_restriction;

    /**
     * @var array
     */
    private $_scopes;

    /**
     * @var  Orion\Context\ContextFactory
     */
    private $_currentscope;

    /**
     * @var  Orion\Context\ContextFactory
     */
    private $_currentpolygon;

    /**
     * @var  Orion\Context\ContextFactory
     */
    private $_vertices;

    /**
     * Constructor
     * 
     * This classe build attribute "restriction" to be used in queryContext
     * restriction are used to make geo-located queries
     * 
     * Documentation for Geo-located queries
     * https://forge.fi-ware.org/plugins/mediawiki/wiki/fiware/index.php/Publish/Subscribe_Broker_-_Orion_Context_Broker_-_User_and_Programmers_Guide#Geo-located_queries
     * 
     * 
     */
    public function __construct() {
        $this->_context = new \Orion\Context\ContextFactory();
        $this->_restriction = new \Orion\Context\ContextFactory();
        $this->_scopes = array();
    }

    /**
     * 
     * Start the scope element, its possible create multiplies scopes
     *
     * @param  string  $type String with scope typeect
     * @return self
     */
    public function createScope($type = "FIWARE_Location") {
        //FIWARE::Location as introduced in 0.16.0 version
        if (null != $this->_currentscope) {
            $this->commitScope();
        }

        $this->_currentscope = new \Orion\Context\ContextFactory();
        $this->_currentscope->put("type", $type);
        return $this;
    }

    /**
     * 
     * Start the scope element, its possible create multiplies scopes
     *
     * @param  boolean  $inverted 
     * @return self
     */
    public function addPolygon($inverted = false) {
        if (null == $this->_currentscope) {
            $this->createScope();
        }
        $this->_currentpolygon = new \Orion\Context\ContextFactory();

        // if we consider the query to the external area to that triangle use inverted = true
        if ($inverted) {
            $this->_currentpolygon->put("inverted", "true");
        }

        return $this;
    }

    /**
     * 
     * Append new vertices to a Polygon
     *
     * @param  mixed  $lat
     * @param  mixed  $lng
     * @return self
     */
    public function addVertice($lat, $lng) {
        $vertice = new \Orion\Context\ContextFactory();
        $vertice->put("latitude", $lat);
        $vertice->put("longitude", $lng);

        $this->_vertices[] = $vertice->getContext();
        return $this;
    }

    
    /**
     * 
     * Commit Polygon to scope object
     * @return self
     */
    private function commitPolygon() {
        $this->_currentpolygon->put("vertices", $this->_vertices);

        $context = new \Orion\Context\ContextFactory();
        $context->put("polygon", $this->_currentpolygon->getContext());

        $this->_currentscope->put("value", $context->getContext());
        $this->_currentpolygon = null;
    }

    /**
     * 
     * Create Circle object
     * @param  mixed  $lat
     * @param  mixed  $lng
     * @param  mixed  $radius
     * @param  boolean  $inverted
     * @return self
     */
    public function addCircle($lat, $lng, $radius, $inverted = false) {
        if (null == $this->_currentscope) {
            $this->createScope();
        }

        $context = new \Orion\Context\ContextFactory();

        $circle = new \Orion\Context\ContextFactory();
        $circle->put("centerLatitude", $lat);
        $circle->put("centerLongitude", $lng);
        $circle->put("radius", $radius);

        if ($inverted) {
            $circle->put("inverted", "true");
        }



        $context->put("circle", $circle->getContext());

        $this->_currentscope->put("value", $context->getContext());
    }

    /**
     * 
     * Commit current scope to scope Arrays
     */
    private function commitScope() {
        if (null != $this->_currentpolygon) {
            $this->commitPolygon();
        }

        $this->_scopes[] = $this->_currentscope->getContext();
        $this->_currentscope = null;
    }

    /**
     * 
     * Build and return scope context OBject
     * @return stdClass
     */
    public function getRestriction() {
        if (null != $this->_currentscope) {
            $this->commitScope();
        }
        $this->_restriction->put("scopes", $this->_scopes);
        return $this->_restriction->getContext();
    }

    /**
     * 
     * Build and return restriction context Object
     * @return stdClass
     */
    public function getRequest() {
        if (null != $this->_currentscope) {
            $this->commitScope();
        }

        $this->_restriction->put("scopes", $this->_scopes);

        $this->_context->put("restriction", $this->_restriction->getContext());

        return $this->_context->getContext();
    }

}

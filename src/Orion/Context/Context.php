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
        if (null == $raw_context) {
            $this->_context = (object) [];
            $this->_rawcontext = "{}";
        } elseif ($raw_context instanceof \stdClass || is_array($raw_context)) {
            $this->_context = (object) $raw_context;
            $this->_rawcontext = json_encode($raw_context);
        } elseif (is_string($raw_context)) {
            $this->_context = (object) json_decode($raw_context);
            if ($this->_context instanceof \stdClass) {
                $this->_rawcontext = $raw_context;
            } else {
                $this->_context = (object) [];
                $this->_rawcontext = "{}";
            }
        } else {
            debug_print_backtrace();
            print_r($raw_context);
        }



        if ($raw_context instanceof \stdClass || is_array($raw_context)) {
            $this->_context = (object) $raw_context;
            $this->_rawcontext = (string) json_encode($raw_context);
        } else {
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
     * @return \stdClass
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

    /**
     * Return Context as a GeoJson FeatureCollection, only compatible with NGSIv2API
     * @return \stdClass
     */
    public function toGeoJson() {
        $Context = $this->_context;


        //If is a invalid object
        if (null === $Context) {
            return $Context;
        }


        //If is not an array(query response) and  is a object
        if (!is_array($Context)) {
            //Check if is a valid entity object
            if (is_object($Context) && isset($Context->id) && isset($Context->type)) {
                $Context = [$Context];
            } else {
                return null;
            }
        }

        $geoJson = (object) ["type" => "FeatureCollection", "features" => []];

        //Build FeatureCollection
//        $countContext = count($Context);
        foreach ($Context as $Entity) { //0.0080490112304688 Seconds
//        for ($i = 0; $i < $countContext; $i++) { //0.025826215744019 Seconds
//            $Entity = $Context[$i];

            if (!(isset($Entity->id) && isset($Entity->type))) {
                continue;
            }
            
            $Feature = (object) ["type" => "Feature", "properties" => ['__metadata'=>[]], "geometry" => null];

            $entityType = null;
            $entityID = null;
            //Roll-out all properties of this feature
            foreach ($Entity as $key => $attr) {
                switch ($key) {
                    case "id":
                        $entityID = $attr;
                        $Feature->properties[$key] = $attr;
                        break;
                    case "type":
                        $entityType = $attr;
                        $Feature->properties[$key] = $attr;
                        break;
                    default:
                        switch ($attr->type) {
                            case "geo:json":
                                $Feature->geometry = $attr->value;
                                $Feature->geometry->type = ucfirst(strtolower($Feature->geometry->type)); //Normalization of geoJson geometry type name.
                                if (isset($attr->metadata) && !empty((array) $attr->metadata)) {
                                    $Feature->properties['geo_metadata'] = $attr->metadata;
                                }
                                break;
                            case "geo:point":
                                $coords = array_reverse(explode(",", $attr->value)); //Change WGS84 Lat Long to Long Lat as GeoJson specifications.

                                array_filter($coords, function($coord) {
                                    return floatval(trim($coord));
                                });

                                $Feature->geometry = (object) [
                                            "type" => "Point",
                                            "coordinates" => $coords
                                ];

                                if (isset($attr->metadata) && !empty((array) $attr->metadata)) {
                                    $Feature->properties['geo_metadata'] = $attr->metadata;
                                }
                                break;
                            case "geo:box":
                                $x = array_reverse(explode(",", $attr->value[0])); //Change WGS84 Lat Long to Long Lat as GeoJson specifications.
                                $y = array_reverse(explode(",", $attr->value[1])); //Change WGS84 Lat Long to Long Lat as GeoJson specifications.
                                //Convert boundary box to polygon using WKT  format POLYGON(x1 y1, x1 y2, x2 y2, x2 y1, x1 y1)
                                $coords = [
                                    [$x[0], $y[0]], //x1 y1
                                    [$x[0], $y[1]], //x1 y2
                                    [$x[1], $y[1]], //x2 y2
                                    [$x[0], $y[0]], //x2 y1                                      
                                    [$x[0], $y[0]] //x1 y1
                                ];

                                $Feature->geometry = (object) [
                                            "type" => "Polygon",
                                            "coordinates" => $coords
                                ];

                                if (isset($attr->metadata) && !empty((array) $attr->metadata)) {
                                    $Feature->properties['geo_metadata'] = $attr->metadata;
                                }
                                break;
                            case "geo:polygon":
                            case "geo:line":
                                //Transform Value Pairs to Polygon/Line Coords
                                $coords = array_filter($attr->value, function($rawvalue) {
                                    $coords = array_reverse(explode(",", $rawvalue)); //Change WGS84 Lat Long to Long Lat as GeoJson specifications.
                                    array_filter($coords, function($coord) {
                                        return floatval(trim($coord));
                                    });
                                    return $coords;
                                });

                                $Feature->geometry = (object) [
                                            "type" => ($attr->type == "geo:polygon") ? "Polygon" : "LineString",
                                            "coordinates" => $coords
                                ];

                                if (isset($attr->metadata) && !empty((array) $attr->metadata)) {
                                    $Feature->properties['geo_metadata'] = $attr->metadata;
                                }
                                break;
                            default:
                                $Feature->properties[$key] = $attr->value;
                                if (isset($attr->metadata) && !empty((array) $attr->metadata)) {
                                    $Feature->properties['__metadata'][$key] = $attr->metadata;
                                }

                                //Add type to metadata
                                if(isset($attr->type)){
                                    if(!isset($Feature->properties['__metadata'][$key])){
                                        $Feature->properties['__metadata'][$key] = [];
                                    }

                                    $Feature->properties['__metadata'][$key]['type'] = $attr->type;
                                }

                                break;
                        }
                        break;
                }
            }
            
            $Feature->id = "{$entityType}-{$entityID}";

            //Finally if this context have a valid geometry append to feature collection
            if ($Feature->geometry != null) {
                $Feature->properties = (object) $Feature->properties;
                array_push($geoJson->features, $Feature);
            }
        }

        return $geoJson;
    }

    public function prettyPrint() {
        echo json_encode($this->__toObject(), JSON_PRETTY_PRINT), PHP_EOL;
    }

}

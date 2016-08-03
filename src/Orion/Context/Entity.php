<?php

namespace Orion\Context;

/**
 * Class to abstract NGSIv2 Entities
 *
 * @author Leonan
 */
class Entity {

    /**
     * Orion NGSI Connection interface V2
     * @var \Orion\NGSIAPIv2 
     */
    private $_orion;

    /**
     * Entity ID
     * @var string 
     */
    private $_id;

    /**
     * Entity Type
     * @var string
     */
    private $_type;

    public function __construct(\Orion\NGSIAPIv2 $orion, $entityId = null, $entityType = null) {
        $this->_orion = $orion;
        $this->_id = $entityId;
        $this->_type = $entityType;
    }

    /**
     * Method to easy perform GET v2/entities[..] endpoint
     * @param array $options key map of aditional parameters (limit,offset,attrs,orderBy,options[count*,keyValues*,values*]) can be any URI param, it will be converted to querystring.
     * @param \Orion\Utils\HttpRequest $request
     * @return \Orion\Context\Context
     */
    public function getContext($options = [], &$request = null) {
        $url = "entities";

        if ($this->_id) {
            $url .= "/{$this->_id}";
        }

        if ($this->_type) {
            $url .= "?type={$this->_type}";
        }

        if (is_array($options) && count($options) > 0) {
            $prefix = ($this->_type) ? "&" : "?";
            $url .= $prefix . urldecode(http_build_query($options));
        }
        return $this->_orion->get($url, $request);
    }

    /**
     * Delete current Entity
     * @return \Orion\Utils\HttpRequest
     */
    public function delete() {
        $url = "entities";

        if ($this->_id) {
            $url .= "/{$this->_id}";
        }

        if ($this->_type) {
            $url .= "?type={$this->_type}";
        }

        return $this->_orion->delete($url);
    }

    /**
     * Get Attribute Data
     * @param type $attr
     * @return \Orion\Context\Context
     */
    public function getAttribute($attr) {
        $url = "entities/{$this->_id}/attrs/$attr";

        if ($this->_type) {
            $url .= "?type={$this->_type}";
        }

        return $this->_orion->get($url);
    }

    /**
     * Get Attribute Value
     * @param type $attr
     * @return \Orion\Context\Context
     */
    public function getAttributeValue($attr, &$request = null) {
        $url = "entities/{$this->_id}/attrs/$attr/value";

        if ($this->_type) {
            $url .= "?type={$this->_type}";
        }

        return $this->_orion->get($url, $request, false);
    }

    /**
     * 
     * @param string $attr Attribute or attribute list
     * @param type $options Aditional parameters: limit,offset,attrs,orderBy,options[count*,keyValues*,values*]
     * @return \Orion\Context\Context
     * @throws Orion\Exception\GeneralException
     */
    public function getAttributes($attr = null, $options = []) {
        $url = "entities/{$this->_id}";
        if (is_array($attr) && count($attr) > 1) {
            $options["attrs"] = implode(',', $attr);
        } elseif (is_string($attr)) {
            $url .= "/attrs/$attr";
        }

        if (null === $this->_id) {
            throw new Orion\Exception\GeneralException("An Entity ID can not be empty");
        }

        if ($this->_type) {
            $url .= "?type={$this->_type}";
        }
        if (count($options) > 0) {
            $prefix = ($this->_type) ? "&" : "?";
            $url .= $prefix . urldecode(http_build_query($options));
        }

        return $this->_orion->get($url);
    }

    /**
     * Update Attributes
     * @param array $attrs 
     * @return \Orion\Utils\HttpRequest
     */
    public function updateAttribute($attr, $body = []) {
        $url = "entities/{$this->_id}/attrs/$attr";

        if ($this->_type) {
            $url .= "?type={$this->_type}";
        }

        $updateEntity = new ContextFactory($body);
        return $this->_orion->put($url, $updateEntity);
    }

    /**
     * 
     * @param type $attr
     * @param type $value
     * @param type $metadata
     * @return \Orion\Utils\HttpRequest
     */
    public function updateAttributeValue($attr, $value, $metadata = null) {
        $url = "entities/{$this->_id}/attrs/$attr/value";

        if ($this->_type) {
            $url .= "?type={$this->_type}";
        }

        $attrUpdate = [
            "value" => $value
        ];

        if (null != $metadata) {
            $attrUpdate['metadata'] = (object) $metadata;
        }

        $updateEntityContext = new ContextFactory($attrUpdate);
        return $this->_orion->put($url, $updateEntityContext);
    }

    /**
     * Remove a single attribute
     * @param type $attr
     * @return \Orion\Utils\HttpRequest
     */
    public function deleteAttribute($attr) {
        $url = "entities/{$this->_id}/attrs/$attr";

        if ($this->_type) {
            $url .= "?type={$this->_type}";
        }

        return $this->_orion->delete($url);
    }

    /**
     * Update Attributes
     * @param array $attrs 
     * @return \Orion\Utils\HttpRequest
     */
    public function updateAttributes(array $attrs) {
        $url = "entities/{$this->_id}/attrs";

        if ($this->_type) {
            $url .= "?type={$this->_type}";
        }

        $updateEntity = new ContextFactory($attrs);
        return $this->_orion->patch($url, $updateEntity);
    }

    /**
     * Replace all entity Attributes
     * @param array $attrs 
     * @return \Orion\Utils\HttpRequest
     */
    public function replaceAttributes(array $attrs) {
        $url = "entities/{$this->_id}/attrs";

        if ($this->_type) {
            $url .= "?type={$this->_type}";
        }

        $updateEntity = new ContextFactory($attrs);
        return $this->_orion->put($url, $updateEntity);
    }

    /**
     * Update or Append new attributes
     * @param array $attrs
     * @return \Orion\Utils\HttpRequest
     */
    public function appendAttributes(array $attrs, $options = ["option" => "append"]) {
        $url = "entities/{$this->_id}/attrs";

        if ($this->_type) {
            $url .= "?type={$this->_type}";
        }

        if (count($options) > 0) {
            $prefix = ($this->_type) ? "&" : "?";
            $url .= $prefix . urldecode(http_build_query($options));
        }

        $updateEntity = new ContextFactory($attrs);
        return $this->_orion->post($url, $updateEntity->get());
    }

    private function coordsQueryString(array $coords) {
        $count = count($coords);
        //If is a simple lat long array
        if ($count == 2) {
            if (is_numeric($coords[0]) && is_numeric($coords[1])) {
                return implode(',', $coords);
            } elseif (is_array($coords[0]) && is_array($coords[1])) { //Maybe is a 2 points line
                foreach ($coords[0] as $key => $coord) {
                    $coords[0][$key] = implode(',', $coord);
                }
                return implode(";", $coords[0]);
            }
        }
        //If is a polygon  geometry, multiple polygons aren't supported
        if ($count == 1 && is_array($coords[0]) && count($coords[0]) >= 3) {
            foreach ($coords[0] as $key => $coord) {
                $coords[0][$key] = implode(',', $coord);
            }
            return implode(";", $coords[0]);
        }

        //Maybe is a 3 points + line:
        if ($count > 2) {
            $first = $coords[0];
            $last = end($coords);
            reset($coords);

            //but just maybe, be a good developer with me or sugest a new function to do that.
            if (is_array($first) && is_array($last)) {
                foreach ($coords as $key => $coord) {
                    $coords[$key] = implode(',', $coord);
                }
                return implode(";", $coords);
            }
        }
        
        throw new \LogicException("You got me! :( Please report it to https://github.com/VM9/orion-explorer-php-frame-work/issues ");
    }

    /**
     * Geo Spatial handler method
     * @param string $georel Spatial relationship (a predicate) between matching entities and a referenced shape ($geoJson)
     * @param string|array|stdClass $geoJson
     * @param array $modifiers
     * @param array $options
     * @param type $request
     * @return \Orion\Context\Context
     * @throws \Exception
     */
    public function geoQuery($georel, $geoJson, array $modifiers = [], array $options = [], &$request = null) {
        if (is_string($geoJson)) {
            $geoJson = json_decode($geoJson);
        } elseif (is_array($geoJson)) {
            $geoJson = (object) $geoJson;
        }

        if ($geoJson == null) {
            throw new \Exception('$geoJson Param should be a valid GeoJson object or string');
        }

        array_unshift($modifiers, $georel);

        $options["georel"] = implode(";", $modifiers);
        $options["geometry"] = strtolower($geoJson->type);
        $options["coords"] = $this->coordsQueryString($geoJson->coordinates);

        return $this->getContext($options, $request);
    }

    /**
     * Matching entities must be located from a specified distance(max,min) from center (point)
     * @param float $latitude
     * @param float $longitude
     * @param int $maxDistance  Expresses, in meters, the maximum distance at which matching entities must be located.
     * @param int $minDistance  Expresses, in meters, the minimum distance at which matching entities must be located.
     * @param array $options Aditional parameters: limit,offset,attrs,orderBy,options[count*,keyValues*,values*]
     * @param pointer $request
     * @return \Orion\Context\Context
     */
    public function getNearOfPoint($latitude, $longitude, $maxDistance = 1000, $minDistance = null, array $options = [], &$request = null) {
        $modifiers = [];
        if ($minDistance != null) {
            $modifiers[] = "minDistance:$minDistance";
        }
        if ($maxDistance != null) {
            $modifiers[] = "maxDistance:$maxDistance";
        }

        //Build GeoJson Syntax
        $geoJson = (object) [
                    "type" => "Point",
                    "coordinates" => [$longitude, $latitude]
        ];

        return $this->geoQuery("near", $geoJson, $modifiers, $options, $request);
    }

    /**
     * Denotes that matching entities are those that exist entirely within the reference geometry.
     * When resolving a query of this type, the border of the shape must be considered to be part of the shape
     * @param GeoJson $geoJson
     * @param array $modifiers
     * @param array $options
     * @param pointer $request
     * @return type
     */
    public function getCoveredBy($geoJson, array $modifiers = [], array $options = [], &$request = null) {        
        return $this->geoQuery("coveredBy", $geoJson, $modifiers, $options, $request);
    }
    
    /**
     * Denotes that matching entities are those intersecting with the reference geometry
     * @param GeoJson $geoJson
     * @param array $modifiers
     * @param array $options
     * @param pointer $request
     * @return type
     */
    public function getIntersections($geoJson, array $modifiers = [], array $options = [], &$request = null) {        
        return $this->geoQuery("intersects", $geoJson, $modifiers, $options, $request);
    }
    
    /**
     * Denotes that matching entities are those intersecting with the reference geometry
     * @param GeoJson $geoJson
     * @param array $modifiers
     * @param array $options
     * @param pointer $request
     * @return type
     */
    public function getDisjoints($geoJson, array $modifiers = [], array $options = [], &$request = null) {        
        return $this->geoQuery("disjoint", $geoJson, $modifiers, $options, $request);
    }
    
    /**
     * The geometry associated to the position of matching entities and the reference geometry must be exactly the same
     * @param GeoJson $geoJson
     * @param array $modifiers
     * @param array $options
     * @param type $request
     * @return type
     */
    public function getGeoEquals($geoJson, array $modifiers = [], array $options = [], &$request) {        
        return $this->geoQuery("equals", $geoJson, $modifiers, $options, $request);
    }
    
    

    public function _setId($entityId) {
        $this->_id = $entityId;
        return $this;
    }

    public function _setOrionInterface(\Orion\NGSIAPIv2 $orion) {
        $this->_orion = $orion;
        return $this;
    }

    public function _setType($entityType) {
        $this->_type = $entityType;
        return $this;
    }

    /**
     * 
     * @param type $id
     * @param type $entityType
     * @param type $attrs
     * @return \Orion\Utils\HttpRequest
     */
    public function create($id, $entityType = null, $attrs = []) {
        $context = new ContextFactory(['id' => $id]);
        if (null != $entityType) {
            $context->put('type', $entityType);
        }

        if (count($attrs) > 0) {
            foreach ($attrs as $name => $attr) {
                $attr = (object) $attr;
                $metadata = (isset($attr->metadata)) ? $attr->metadata : null;
                $context->addAttribute($name, $attr->value, $attr->type, $metadata);
            }
        }
//        var_dump($context->get());exit;
        $request = $this->_orion->create("entities", $context);
        $this->_setId($id);
        $this->_setType($entityType);
        return $request;
    }

}

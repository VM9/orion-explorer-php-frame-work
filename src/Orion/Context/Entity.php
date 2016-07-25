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
     * Executes OPERATIONS IN THE NGSIv2 RC 2016.05
     * 
     * @param array $options Array of options compatible to operation described in https://docs.google.com/spreadsheets/d/1f4m624nmO3jRjNalGE11lFLQixnfCENMV6dc54wUCCg/edit#gid=50130961
     * @return \Orion\Context\Context
     */
    public function getContext($options = []) {
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
        return $this->_orion->get($url);
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
     * @param type $attr
     * @param type $options
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

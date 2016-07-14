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

        if (count($options) > 0) {
            $prefix = ($this->_type) ? "&" : "?";
            $url .= $prefix . urldecode(http_build_query($options));
        }
        return $this->_orion->get($url);
    }
    
    /**
     * 
     * @param type $attr
     * @param type $options
     * @return type
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
            $url .= $prefix . http_build_query($options);
        }

        return $this->_orion->get($url);
    }

    
    
    public function updateAttributes(){
        
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

}

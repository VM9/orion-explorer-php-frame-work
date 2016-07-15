<?php

namespace Orion\Context;

/**
 * Class to abstract NGSIv2 Subscriptions
 *
 * @author Leonan
 */
class SubscriptionEntity {

    /**
     * Orion NGSI Connection interface V2
     * @var \Orion\NGSIAPIv2 
     */
    private $_orion;

    /**
     * Subscription ID
     * @var string 
     */
    private $_id;

    public function __construct(\Orion\NGSIAPIv2 $orion, $subscriptionId = null) {
        $this->_orion = $orion;
        $this->_id = $subscriptionId;
    }

    private function getBaseURI() {
        $url = SubscriptionFactory::endPoint;

        if ($this->_id) {
            $url .= "/{$this->_id}";
        }
        return $url;
    }

    /**
     * Executes OPERATIONS IN THE NGSIv2 RC 2016.05
     * 
     * @param array $options Array of options compatible to operation described in https://docs.google.com/spreadsheets/d/1f4m624nmO3jRjNalGE11lFLQixnfCENMV6dc54wUCCg/edit#gid=50130961
     * @return \Orion\Context\Context
     */
    public function getContext($options = []) {
        $url = $this->getBaseURI();

//        if (count($options) > 0) {
//            $prefix = ($this->_type) ? "&" : "?";
//            $url .= $prefix . urldecode(http_build_query($options));
//        }

        return $this->_orion->get($url);
    }

    /**
     * Change subscription status to Inactive
     * @return type
     */
    public function inactive() {
        return $this->update([
            "status" => "inactive"
        ]);
    }

     /**
     * Change subscription status to Active
     * @return type
     */
    public function ative() {
        return $this->update([
            "status" => "active"
        ]);
    }
    
    /**
     * 
     * @param string|int $expire_time
     */
    public function setExpiration($expire_time, $timezone = "UTC"){
        $subscription = new SubscriptionFactory($this->_orion);
        $subscription->setExpiration($expire_time,$timezone);
        
        return $this->update($subscription);
    }
    
    /**
     * Update a subscription, you can pass a simple array chain in subscription know format 
     * or you can send a instance of SubscriptionFactory
     * @param \Orion\Context\SubscriptionFactory|array $subscription
     * @return type
     * @throws \Exception
     */
    public function update($subscription){
        if(!isset($this->_id) || null == $this->_id){
            throw new \Exception("You must especify an Id to perform subscription updates");
        }
        if($subscription instanceof SubscriptionFactory){
            $context = new ContextFactory((array) $subscription->get());
        }elseif(is_array($subscription) || is_object($subscription)){
            $context = new ContextFactory((array)$subscription);
        }
        return $this->_orion->patch($this->getBaseURI(), $context);
    }
    
    public function delete(){
        if(!isset($this->_id) || null == $this->_id){
            throw new \Exception("You must especify an Id to perform subscription updates");
        }
        return $this->_orion->delete($this->getBaseURI());
    }
    
    /**
     * Return Subscription ID
     * @return string
     */
    public function _getId() {
        return $this->_id;
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

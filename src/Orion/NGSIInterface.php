<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Orion;

/**
 *
 * @author Leonan
 */
interface NGSIInterface {
    
    /**
     * 
     * This method get all availables types
     * @return boolean  
     */
    public function getEntityTypes();
    
     /**
     * This method build a view like database view, where attributes and ID are colums
     * With this way is possible shows entity context type as database tables.
     *
     * @param  string  $type Selected Type
     * @return Context object  
     * 
     */    
    public function getEntityAttributeView($type = false, $offset = 0, $limit = 1000, $details = null);
    
    /**
     * This method returns ALL Context Entities
     *
     * @param mixed $type Selected Type
     * @param mixed $offset
     * @param mixed $limit
     * @param mixed $details
     * @return  Entities object  
     */
    public function getEntities($type = false, $offset = 0, $limit = 1000, $details = null);
}

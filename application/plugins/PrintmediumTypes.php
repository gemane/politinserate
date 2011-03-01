<?php

/**
* Check if '/tarif/form/medium' was called to set of onLoad-Javascript call in body
*
* LICENSE
*
* This source file is subject to the BSD license that is bundled
* with this package in the file LICENSE.
*
* @author     Gerold Neuwirt (gerold.neuwirt@politinserate.at)
* @category   Austrian Coding for Democracy
* @package    Polit-Inserate.at
* @copyright  Copyright (c) 2010 Gerold Neuwirt
* @license    http://github.com/gemane/politinserate/LICENSE   BSD License
* @version    Release: 1.0.0
* @link       http://politinserate.at
* @source     http://github.com/gemane/politinserate
*/

class Application_Plugin_PrintmediumTypes extends Zend_Controller_Plugin_Abstract
{
    
    public function preDispatch(Zend_Controller_Request_Abstract $request) 
    {
        // Form for printmedium needs the parser module at the very beginning
        if ('tarife' == $request->getControllerName() 
                && 'form' == $request->getActionName() 
                && !$request->has('file')) {
            Zend_Registry::set('PrintmediumTypes', true);
        } else {
            Zend_Registry::set('PrintmediumTypes', false);
        }
        
        // Dojo should get loaded at the end when on start page otherwise as first step
        if ('index' == $request->getControllerName() 
                && 'index' == $request->getActionName() 
                && !$request->has('file')) {
            Zend_Registry::set('StartPage', true);
        } else {
            Zend_Registry::set('StartPage', false);
        }
        
    }
    
}
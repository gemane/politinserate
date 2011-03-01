<?php
/**
* User controller for administration and profile.
*
* User specific data.
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

class Mobile_Controller_Action extends Zend_Controller_Action
{

    public function init()
    {
        $this->mobile = false;
        // are we in the mobile context?
        if (Zend_Registry::get('context') == '\mobile' || $this->getRequest()->getParam('format') == 'mobile') {
            // Mobile format context
            $mobileConfig =
                array(
                'mobile' => array(
                        'suffix'  => 'mobile',
                        'headers' => array(
                            'Content-type' => 'text/html; charset=utf-8')),
                );
    
            // Init the action helper
            $contextSwitch = $this->_helper->contextSwitch();
        
            // Add new context
            $contextSwitch->setContexts($mobileConfig);
        
            // This is where you have to define
            // which actions are available in the mobile context
            // ADOPT THIS TO YOUR NEEDS!
            //$contextSwitch->addActionContext('index', 'mobile');
    
            // enable layout but set different path to layout file
            $contextSwitch->setAutoDisableLayout(false);

            // Initializes action helper
            $contextSwitch->initContext('mobile');
            
            $this->mobile = true;
        }
        
    }
}

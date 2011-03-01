<?php

/**
* Check if user is logged in and set 'action' to username
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

class Application_Plugin_SessionCheck extends Zend_Controller_Plugin_Abstract
{
    
    public function preDispatch(Zend_Controller_Request_Abstract $request) 
    {
        $controllerFile = $request->getControllerName();
        $actionFile = $request->getActionName();
        if( !is_file($controllerFile) ) {
            $this->auth = Zend_Registry::get('auth');
            if ($this->auth->hasIdentity()) {
                $this->user_table = new Application_Model_Users();
                if ($this->user_table->checkUsername($controllerFile)) {
                    if ($controllerFile == $this->auth->getIdentity()->username) {
                        $request->setControllerName('stream')
                                ->setActionName('user')
                                ->setDispatched(false);
                    }
                } elseif ($controllerFile == 'index' && $actionFile == 'index') {
                        $request->setControllerName('index')
                                ->setActionName('user')
                                ->setDispatched(false);
                }
            }
        }
    }
    
}
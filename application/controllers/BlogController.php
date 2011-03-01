<?php
/**
* Blog page
*
* Upload button for advertisement images in printmedia.
* Overview of inserate data as diagramms.
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

include_once('Intern/Mobile/Controller.php');

class BlogController extends Mobile_Controller_Action
{
    
    public function init()
    {   
        parent::init();
        
        if ($this->mobile) {
            $this->_helper->getHelper('layout')->setLayoutPath(APPLICATION_PATH . '/layouts/mobile');
        }
    }

    public function indexAction()
    {    
        
    }
    
}


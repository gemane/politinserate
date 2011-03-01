<?php

/**
* Twitter search 
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

class Application_Plugin_TwitterSearch extends Zend_Controller_Plugin_Abstract
{

    public function preDispatch() 
    {
        $twitterSearch = new Zend_Service_Twitter_Search('json');
        $searchResults = $twitterSearch->search('polit-inserate', array('lang' => 'de'));
        
        Zend_Registry::set('tweetsearch', $searchResults);
    }
    
}

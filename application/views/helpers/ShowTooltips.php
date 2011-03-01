<?php

/**
* Show Tooltips
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

class Zend_View_Helper_ShowTooltips extends Zend_View_Helper_Abstract
{
    
    public function showTooltips()
    {
        /***********************************************
        * Image w/ description tooltip v2.0- (c) Dynamic Drive DHTML code library (www.dynamicdrive.com)
        * This notice MUST stay intact for legal use
        * Visit Dynamic Drive at http://www.dynamicdrive.com/ for this script and 100s more
        ***********************************************/
        //$this->view->headScript()->prependFile('http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js', 'text/javascript');
        $this->view->headScript()->appendScript($this->view->tooltips)
                                 ->appendFile('/js/intern/ddimgtooltip.js', 'text/javascript');
    }
}
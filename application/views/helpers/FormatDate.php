<?php

/**
* Format Date for View.
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

class Zend_View_Helper_FormatDate extends Zend_View_Helper_Abstract
{
    
    public function formatDate($string)
    {
        $date = new Zend_Date($string, Zend_Date::DATES);
        $result = $date->get(Zend_Date::DATE_MEDIUM);
        
        return $result;
    }
}
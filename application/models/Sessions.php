<?php
/**
* Database Model for Session.
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

class Application_Model_Sessions extends Zend_Db_Table_Abstract
{
    protected $_name = 'acd_inserate_session';
    protected $_primary = 'id_session';
    protected $array = array('id_session', 'modified', 'lifetime', 'session_data');
    
    public function getArray()
    {
        return $this->array;
    }
    
}
<?php
/**
* Database Model for Twitter data.
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

class Application_Model_Twitter extends Zend_Db_Table_Abstract
{
    protected $_name = 'acd_inserate_twitter';
    protected $_primary = 'id_twitter';
    protected $array = array('text', 'user_id', 'name', 'username', 'id_inserat');
    
    public function getArray()
    {
        return $this->array;
    }
    
    public function insertTweet($id_inserat, $values)
    {
        $array = array(
            'id_twitter',
            'text',
            'user_id',
            'name',
            'username',
            );
        
        foreach ($array as $key)
            $values_tweet[$key] = $values[$key];
        $values_tweet['id_inserat'] = $id_inserat;
        
        return $this->insert($values_tweet);
    }
    
    public function checkTweet($id_twitter)
    {
        $select = $this->select()->from(array('acd_inserate_printmedium'),
                                            array('id_twitter'))
                                 ->where('id_twitter = ?', $id_twitter);
        $result = $this->fetchAll($select)->toArray();
        
        return empty($result) ? false : true;
    }
    
}
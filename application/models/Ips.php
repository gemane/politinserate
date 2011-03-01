<?php
/**
* Database Model for IP-Adresses.
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

class Application_Model_Ips extends Zend_Db_Table_Abstract
{
    protected $_name = 'acd_inserate_ip';
    protected $_primary = 'id_ip';
    protected $array = array('id_ip', 'ip', 'timestamp', 'id_user', 'task');
    
    public function insertIP($task = '', $id_user = 1)
    {
        $ip_remote = $_SERVER['REMOTE_ADDR'];
        $values['ip'] = $ip_remote;
        $values['id_user'] = $id_user;
        $values['task'] = $task;
        $this->insert($values);
    }
    
    public function deleteOldIps($max_hours = 24)
    {
        $time = date('Y-m-d H:i:s', time() - $max_hours * 60 * 60); // Delete after $max_hours
        
        $where = $this->getAdapter()->quoteInto('timestamp < ?', $time);
        
        $this->delete($where);
    }
    
    public function checkMaxUploads($task = '', $max_tasks = 10)
    {
        $max_tasks++;
        $ip_remote = $_SERVER['REMOTE_ADDR'];
        
        $select = $this->select()->from(array('acd_inserate_ip'),
                                            array('num_uploads' => 'COUNT(*)'))
                                 ->where('ip = ?', $ip_remote)
                                 ->where('task = ?', $task);
        $result = $this->fetchAll($select);
        
        if ($max_tasks > $result[0]['num_uploads']) {
            return true;
        } else {
            return false;
        }
    }
     
}
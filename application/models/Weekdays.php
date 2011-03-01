<?php
/**
* Database Model for Weekdays.
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

class Application_Model_Weekdays extends Zend_Db_Table_Abstract
{
    protected $_name = 'acd_inserate_size_weekdays';
    protected $_primary = 'id_weekdays';
    protected $array = array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun');
    
    public function getArray()
    {   
        return $this->array;
    }
    
    public function getWeekdays($id)
    {
        $select = $this->select()->where('id_weekdays = ?', (int) $id);
        $result = $this->fetchRow($select)->toArray();
        
        return $result;
    }
    
    public function insertWeekdays($values)
    {
        foreach ($this->array as $key)
            $values_weekdays[$key] = $values[$key];
        
        return $this->insert($values_weekdays);
    }
    
    public function updateWeekdays($id_weekdays, $values)
    {
        $where = $this->getAdapter()->quoteInto('id_weekdays = ?', $id_weekdays);
        foreach ($this->array as $key)
            $values_weekdays[$key] = $values[$key];
        
        $this->update($values_weekdays, $where);
    }
    
    public function deleteWeekdays($id)
    {
        $where = $this->getAdapter()->quoteInto('id_weekdays = ?', (int) $id);
        
        $this->delete($where);
    }
    
    protected $weekdays_name = array(
        'Mon' => 'Montag',
        'Tue' => 'Dienstag',
        'Wed' => 'Mitwoch',
        'Thu' => 'Donnerstag',
        'Fri' => 'Freitag',
        'Sat' => 'Samstag',
        'Sun' => 'Sonntag');
    
    public function getWeekdayName($day)
    {   
        foreach ($this->weekdays_name as $key => $value)
            if (0 == strcmp($key, $day))
                return $value;
    }
    
    protected $weeknames = array(
        'Mon' => 'Mo',
        'Tue' => 'Di',
        'Wed' => 'Mi',
        'Thu' => 'Do',
        'Fri' => 'Fr',
        'Sat' => 'Sa',
        'Sun' => 'So');
    
    public function formatWeekdays($id)
    {
        $week = $this->getWeekdays($id);
        $result = '';
        $w = 0;
        foreach ($this->getArray() as $key) {
            if (1 == $week[$key]) {
                $day = $this->weeknames[$key];
                $result .= $day . ', ';
                $w++;
            } else if (1 < $w){
                $result = substr($result, 0, -($w-1)*4-2);
                $result .= '-' . $day . ', ';
                $w = 0;
            }
        }
        if (7 == $w) {
            return 'Alle';
        }
        return substr($result, 0, -2);
    }
    
    public function updateDBexistingWeekdays()
    {
        $select = $this->select()->from(array('acd_inserate_size_weekdays'),
                                            array('id_weekdays'));
        $result = $this->getAdapter()->fetchCol($select);
        echo '<br />';
        $table_tariff = new Application_Model_Tariff();
        foreach ($result as $key => $values) {
            if (!$table_tariff->checkWeekday($values))
                $this->deleteWeekdays($values);
                echo $values . ' ';
        }
    }
    
}

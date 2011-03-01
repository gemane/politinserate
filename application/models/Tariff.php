<?php
/**
* Database Model for Tariffs from Printmedia.
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

class Application_Model_Tariff extends Zend_Db_Table_Abstract
{
    protected $_name = 'acd_inserate_size';
    protected $_primary = 'id_size';
    protected $array = array('size', 'cover', 'id_printmedium_type', 'id_height_image', 'size_height', 'price', 'id_datafile', 'id_weekdays', 'id_user');
    
    public function getArray()
    {
        return $this->array;
    }
    
    public function getHeights($id_inserat, $type, $cover)
    {
        $inserat = new Application_Model_Inserate();
        $print_date = strtotime($inserat->getPrintDate($id_inserat));
        $weekday = date('D', $print_date);
        
        include_once('Intern/Holidays/Christian.php');
        $holiday = new Holidays_Christian();
        $print_year = date('Y', $print_date); 
        
        $holiday_array = $holiday->Austria($print_year, 'U');
        foreach ($holiday_array as $value) {
            if ($value == $print_date)
                $weekday = 'Sun';
        }
        
        $select = $this->select()->from(array('d' => 'acd_inserate_size'),
                                            array('id_size', 'size', 'id_height_image', 'size_height', 'cover'))
                                    ->where('cover = ?', $cover)
                                    ->join(array('q' => 'acd_inserate_config'),
                                         'd.id_height_image = q.id_config',
                                         array('height_image'))
                                    ->join(array('p' => 'acd_inserate'),
                                         'p.id_inserat = ' . $id_inserat,
                                         array())
                                    ->join(array('s' => 'acd_inserate_datafile'),
                                            'd.id_datafile = s.id_datafile',
                                            array('id_region_printmedium_bit'))
                                    ->where('s.id_printmedium = p.id_printmedium')
                                    ->join(array('u' => 'acd_inserate_printmedium_type'),
                                            'd.id_printmedium_type = u.id_printmedium_type',
                                            array('printmedium_type_position', 'printmedium_width'))
                                    ->where('u.printmedium_type_position = ?', $type)
                                    ->where('(s.id_region_printmedium_bit & p.id_region_printmedium_bit) = p.id_region_printmedium_bit')
                                    ->join(array('t' => 'acd_inserate_size_weekdays'),
                                         'd.id_weekdays = t.id_weekdays',
                                         array())
                                    ->where('p.print_date > s.date_from AND p.print_date < s.date_to')
                                    ->where('t.' . $weekday . ' = 1')
                                    ->order('height_image', 's.id_region_printmedium_bit');
        $select->setIntegrityCheck(false);
        
        $result = $this->fetchAll($select)->toArray();
        
        if (empty($result))
            return array();
        
        // Find smallest value for region
        $region = 1e20;
        foreach ($result as $values)
            if ($region > $values['id_region_printmedium_bit'])
                $region = $values['id_region_printmedium_bit'];
        
        foreach ($result as $values) {
            if ($region == $values['id_region_printmedium_bit'])
                $result_single[] = $values;
        }
        
        return $result_single;
    }
    
    public function getSizeDatafile($id_datafile)
    {
        $this->setSelect();
        $select_size = $this->query->where('d.id_datafile = ?', $id_datafile);
        $result = $this->fetchAll($select_size)->toArray();
        
        $weekdays = new Application_Model_Weekdays();
        $config = new Application_Model_Config();
        for ($i=0; $i < count($result); $i++) {
            $result[$i]['region_printmedium_bit'] = $config->formatRegion($result[$i]['id_region_printmedium_bit']);
            $result[$i]['weekdays'] = $weekdays->formatWeekdays($result[$i]['id_weekdays']);
        }
        
        return $result;
    }
    
    public function getSizesByPrintmedium($id_printmedium)
    {
        $this->setSelect();
        $select_size = $this->query->where('p.id_printmedium = ?', $id_printmedium);
        $result = $this->fetchAll($select_size)->toArray();
        
        $weekdays = new Application_Model_Weekdays();
        $config = new Application_Model_Config();
        for ($i=0; $i < count($result); $i++) {
            $result[$i]['region_printmedium_bit'] = $config->formatRegion($result[$i]['id_region_printmedium_bit']);
            $result[$i]['weekdays'] = $weekdays->formatWeekdays($result[$i]['id_weekdays']);
        }
        
        return $result;
    }
    
    public function getSize($id_size)
    {
        $this->setSelect();
        $select_size = $this->query->where('d.id_size = ?', $id_size);
        $result = $this->fetchRow($select_size);
        
        return $result->toArray();
    }
    
    protected function setSelect()
    {
        $this->query = $this->select()->from(array('d' => 'acd_inserate_size'),
                                        array('id_size', 'size', 'cover', 'id_printmedium_type', 'id_size_image', 'id_height_image', 'size_height', 'price', 'id_weekdays', 'id_datafile'))
                                    ->join(array('q' => 'acd_inserate_config'),
                                         'd.id_height_image = q.id_config',
                                         array('height_image', 'height_name'))
                                    ->join(array('s' => 'acd_inserate_datafile'),
                                            'd.id_datafile = s.id_datafile',
                                            array('id_printmedium', 'id_region_printmedium_bit'))
                                    ->join(array('p' => 'acd_inserate_printmedium'),
                                            's.id_printmedium = p.id_printmedium',
                                            array('printmedium'))
                                    ->join(array('r' => 'acd_inserate_printmedium_type'),
                                            'd.id_printmedium_type = r.id_printmedium_type',
                                            array('printmedium_width', 'printmedium_type_name', 'printmedium_type_position'))
                                    ->order(array('printmedium_type_position', 'cover', 'id_height_image', 'price'));
        $this->query->setIntegrityCheck(false);
    }
    
    public function getId_weekdays($id_size)
    {
        $select = $this->select()->from(array('acd_inserate_size'),
                                            array('id_weekdays'))
                                 ->where('id_size = ?', $id_size);
        $result = $this->fetchAll($select);
        return $result[0]['id_weekdays'];
    }
    
    public function insertTariff($values)
    {
        foreach ($this->array as $key)
            $values_tariff[$key] = $values[$key];
        
        return $this->insert($values_tariff);
    }
    
    public function updateTariff($id_size, $values)
    {
        $where = $this->getAdapter()->quoteInto('id_size = ?', $id_size);
        foreach ($this->array as $key)
            $values_tariff[$key] = $values[$key];
        $this->update($values_tariff, $where);
    }
    
    public function deleteTariff($id_size)
    {
        $where = $this->getAdapter()->quoteInto('id_size = ?', (int)$id_size);
        $this->delete($where);
    }
    
    public function getNumTariff($id_datafile)
    {
        $select = $this->select()->from(array('acd_inserate_size'),
                                            array('num_tariff' => 'COUNT(*)'))
                                 ->where('id_datafile = ?', $id_datafile);
        $result = $this->fetchAll($select);
        return $result[0]['num_tariff'];
    }
    
    public function checkTariff($id_size)
    {
        $select = $this->select()->from(array('acd_inserate_size'),
                                            array('size'))
                                 ->where('id_size = ?', $id_size);
        $result = $this->fetchAll($select)->toArray();
        
        return empty($result) ? false : true;
    }
    
    public function checkWeekday($id_weekdays)
    {
        $select = $this->select()->from(array('acd_inserate_size'),
                                            array('id_weekdays'))
                                 ->where('id_weekdays = ?', $id_weekdays);
        $result = $this->fetchAll($select)->toArray();
        
        return empty($result) ? false : true;
    }
    
    public function updateDBTypeName()
    {
        $select = $this->select()->from(array('d' => 'acd_inserate_size'),
                                            array('id_size', 'id_datafile'))
                                    ->join(array('s' => 'acd_inserate_datafile'),
                                            'd.id_datafile = s.id_datafile',
                                            array('id_printmedium', 'id_region_printmedium_bit'))
                                    ->join(array('p' => 'acd_inserate_printmedium_type'),
                                            's.id_printmedium = p.id_printmedium',
                                            array('id_printmedium_type', 'printmedium_type_position'));
        $select->setIntegrityCheck(false);
        $result = $this->fetchAll($select)->toArray();
        
        foreach ($result as $key => $values) {
            if (0 == $values['printmedium_type_position']) {
                $where = $this->getAdapter()->quoteInto('id_size = ?', $values['id_size']);
                $values_tariff['id_printmedium_type'] = $values['id_printmedium_type'];
                $this->update($values_tariff, $where);
            }
        }
    }
    
    public function getPrintmediumTypeByInserat($id_inserat)
    {
        $inserat = new Application_Model_Inserate();
        $print_date = strtotime($inserat->getPrintDate($id_inserat));
        
        if (empty($print_date))
            return array();
        
        $weekday = date('D', $print_date);
        
        include_once('Intern/Holidays/Christian.php');
        $holiday = new Holidays_Christian();
        $print_year = date('Y', $print_date); 
        
        $holiday_array = $holiday->Austria($print_year, 'U');
        foreach ($holiday_array as $value) {
            if ($value == $print_date)
                $weekday = 'Sun';
        }
        
        $select = $this->select()->from(array('d' => 'acd_inserate_size'),
                                            array('id_size'))
                                    ->join(array('p' => 'acd_inserate'),
                                         'p.id_inserat = ' . $id_inserat,
                                         array())
                                    ->join(array('s' => 'acd_inserate_datafile'),
                                            'd.id_datafile = s.id_datafile',
                                            array())
                                    ->where('s.id_printmedium = p.id_printmedium')
                                    ->join(array('u' => 'acd_inserate_printmedium_type'),
                                            'd.id_printmedium_type = u.id_printmedium_type',
                                            array('id_printmedium_type', 'printmedium_type_name', 'printmedium_type_position', 'id_printmedium', 'printmedium_columns_width', 'printmedium_width'))
                                    ->where('(s.id_region_printmedium_bit & p.id_region_printmedium_bit) = p.id_region_printmedium_bit')
                                    ->join(array('t' => 'acd_inserate_size_weekdays'),
                                         'd.id_weekdays = t.id_weekdays',
                                         array())
                                    ->where('p.print_date > s.date_from AND p.print_date < s.date_to')
                                    ->where('t.' . $weekday . ' = 1')
                                    ->group('printmedium_type_position');
        $select->setIntegrityCheck(false);
        
        $result = $this->fetchAll($select)->toArray();
        
        if (empty($result))
            return array();
        else
            return $result;
        
    }
    
}
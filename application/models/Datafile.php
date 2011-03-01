<?php
/**
* Database Model for Datafiles.
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

class Application_Model_Datafile extends Zend_Db_Table_Abstract
{
    protected $_name = 'acd_inserate_datafile';
    protected $_primary = 'id_datafile';
    protected $array = array('id_printmedium', 'id_region_printmedium_bit', 'year', 'date_from', 'date_to');
    
    public function getArray()
    {
        return $this->array;
    }
    
    public function getId_printmedium($id_datafile)
    {
        $select = $this->select()->from(array('acd_inserate_datafile'),
                                            array('id_printmedium'))
                                 ->where('id_datafile = ?', $id_datafile);
        $result = $this->fetchAll($select);
        if (!$result) {
            throw new Exception('Could not find row in Datafile ' . $id_datafile);
        }
        return $result[0]['id_printmedium'];
    }
    
    public function getAllDatafiles()
    {
        $select = $this->select()->from(array('acd_inserate_datafile'),
                                            array('id_datafile'));
        $result = $this->getAdapter()->fetchCol($select);
        return $result;
    }
    
    public function getId_datafile($id_printmedium)
    {
        $select = $this->select()->from(array('acd_inserate_datafile'),
                                            array('id_datafile'))
                                 ->where('id_printmedium = ?', $id_printmedium);
        $result = $this->getAdapter()->fetchCol($select);
        
        return $result;
    }
    
    public function getDatafile($id_datafile)
    {
        $this->setSelect();
        $select_datafile = $this->query->where('d.id_datafile = ?', $id_datafile);
        $result = $this->fetchRow($select_datafile)->toArray();
        
        $config = new Application_Model_Config();
        foreach ($config->getAllRegion() as $region ) {
            if ($result['id_region_printmedium_bit'] & pow(2, $region['id_config']))
                $result[$region['region_abb']] = 1;
        }
        
        return $result;
    }
    
    public function getAllPath()
    {
        $select = $this->select()->from(array('d' => 'acd_inserate_datafile'),
                                                  array('path', 'id_datafile'));
        $result = $this->fetchAll($select)->toArray();
        
        return $result;
    }
    
    public function insertDatafile($values)
    {
        foreach ($this->array as $key)
            $values_datafile[$key] = $values[$key];
        
        $values_datafile['path'] = $values['path'];
        
        $bit = 0;
        $config = new Application_Model_Config();
        foreach ($config->getAllRegion() as $region ) {
            if ((1 == $values[$region['region_abb']]) || (1 == $values['aut']))
                $bit += pow(2, $region['id_config']);
        }
        $values_datafile['id_region_printmedium_bit'] = $bit;
        
        $this->insert($values_datafile);
    }
    
    public function updateDatafile($id_datafile, $values)
    {
        $where = $this->getAdapter()->quoteInto('id_datafile = ?', $id_datafile);
        foreach ($this->array as $key)
            $values_datafile[$key] = $values[$key];
        
        $bit = 0;
        $config = new Application_Model_Config();
        foreach ($config->getAllRegion() as $region ) {
            if ((1 == $values[$region['region_abb']]) || (1 == $values['aut']))
                $bit += pow(2, $region['id_config']);
        }
        $values_datafile['id_region_printmedium_bit'] = $bit;
        
        $this->update($values_datafile, $where);
    }
    
    public function deleteDatafile($id_datafile)
    {
        $table_tariff = new Application_Model_Tariff();
        if (0 == $table_tariff->getNumTariff($id_datafile)) {
            $where = $this->getAdapter()->quoteInto('id_datafile = ?', (int) $id_datafile);
            $this->delete($where);
        }
    }
    
    public function getAllByIDMedium($id_medium)
    {
        $this->setSelect();
        $select = $this->query->where('d.id_printmedium = ?', $id_medium);
        $result = $this->fetchAll($select)->toArray();
        
        $config = new Application_Model_Config();
        for ($i=0; $i < count($result); $i++)
            $result[$i]['region_printmedium_bit'] = $config->formatRegion($result[$i]['id_region_printmedium_bit']);
        
        if (empty($result))
            return array();
        
        return $result;
    }
    
    protected function setSelect()
    {
        $this->query = $this->select()->from(array('d' => 'acd_inserate_datafile'),
                                                  array('path','year','id_datafile', 'id_printmedium', 'id_region_printmedium_bit', 'date_from', 'date_to'))
                                           ->join(array('p' => 'acd_inserate_printmedium'),
                                                  'd.id_printmedium = p.id_printmedium',
                                                  array('printmedium'))
                                           ->order(array('year DESC', 'id_printmedium', 'id_region_printmedium_bit'));
        $this->query->setIntegrityCheck(false);
    }
    
    /**
    * Check for existing datafiles.
    *
    * @param integer $id_printmedium
    * @param integer $id_region_printmedium
    * @param integer $year
    * @return array
    */
    public function checkRow($values)
    {
        $id_printmedium = $values['id_printmedium'];
        $year = $values['year'];
        
        $bit = 0;
        $config = new Application_Model_Config();
        foreach ($config->getAllRegion() as $region ) {
            if ((1 == $values[$region['region_abb']]) || (1 == $values['aut']))
                $bit += pow(2, $region['id_config']);
        }
        
        $select = $this->select()->from(array('acd_inserate_datafile'),
                                            array('id_datafile'))
                                 ->where('id_printmedium = ?', $id_printmedium)
                                 ->where('id_region_printmedium_bit = ?', $bit)
                                 ->where('year = ?', $year);
        $result = $this->fetchAll($select);
        
        return empty($result['id_datafile']) ? false : true;
    }
    
    public function checkDatafile($id_datafile)
    {
        $select = $this->select()->from(array('acd_inserate_datafile'),
                                            array('id_datafile'))
                                 ->where('id_datafile = ?', $id_datafile);
        $result = $this->fetchAll($select)->toArray();
        
        return empty($result) ? false : true;
    }
    
    public function getNumDatafile($id_printmedium)
    {
        $select = $this->select()->from(array('acd_inserate_datafile'),
                                            array('num_datafile' => 'COUNT(*)'))
                                 ->where('id_printmedium = ?', $id_printmedium);
        $result = $this->fetchAll($select);
        return $result[0]['num_datafile'];
    }
    
}
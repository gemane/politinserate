<?php
/**
* Database Model for Printmedium
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

class Application_Model_Printmedium extends Zend_Db_Table_Abstract
{
	protected $table_types;
	
    protected $_name = 'acd_inserate_printmedium';
    protected $_primary = 'id_printmedium';
    protected $array = array('id_printmedium', 'printmedium', 'keywords_printmedium');
    
    public function init()
    {
    	$this->table_types = new Application_Model_PrintmediumTypes();
    }
    
    public function getArray()
    {
        return $this->array;
    }
    
    public function getAllMedium()
    {
        $result = $this->fetchAll()->toArray();
        
        return $result;
    }
    
    public function getPrintmedium($id_printmedium)
    {   
        $id_printmedium = (0 == $id_printmedium) ? 1 : $id_printmedium;
        
        $select = $this->select()->from(array('acd_inserate_printmedium'),
                                            array('printmedium'))
                                  ->where('id_printmedium = ?', $id_printmedium);
        $result = $this->fetchAll($select);
        
        return $result[0]['printmedium'];
    }
    
    public function getRowPrintmedium($id_printmedium)
    {
        $select = $this->select()->from(array('acd_inserate_printmedium'))
                                 ->where('id_printmedium = ?', $id_printmedium);
        $result = $this->fetchRow($select)->toArray();
        
        return $result;
    }
    
    public function insertPrintmedium($values)
    {	
        $values_printmedium['printmedium'] = $values['printmedium'];
        
        $id_printmedium = $this->insert($values_printmedium);
        
        $this->table_types->updatePrintmediumType($id_printmedium, $values);
        
        return $id_printmedium;
    }
    
    public function updatePrintmedium($id_printmedium, $values)
    {
        $values_printmedium['printmedium'] = $values['printmedium'];
        
        $where = $this->getAdapter()->quoteInto('id_printmedium = ?', $id_printmedium);
        
        $this->update($values_printmedium, $where);
        
    	$this->table_types->updatePrintmediumType($id_printmedium, $values);
    }
    
    protected function formatKeywords($keywords)
    {
        $keywords = strtr($keywords, ",", " ");
        $keywords = strtr($keywords, ";", " ");
        $count = 1;
        while($count)
            $keywords = str_replace('  ', ' ', $keywords, $count);
        
        return $keywords;
    }
    
    public function checkPrintmedium($id_printmedium)
    {
        $select = $this->select()->from(array('acd_inserate_printmedium'),
                                            array('printmedium'))
                                 ->where('id_printmedium = ?', $id_printmedium);
        $result = $this->fetchAll($select)->toArray();
        
        return empty($result) ? false : true;
    }
    
    public function deletePrintmedium($id_printmedium)
    {
        $table_datafile = new Application_Model_Datafile();
        if (0 == $table_datafile->getNumDatafile($id_printmedium)) {
            
            $table_types = new Application_Model_PrintmediumTypes();
            $table_types->deletePrintmediumType($id_printmedium);
            
            $where = $this->getAdapter()->quoteInto('id_printmedium = ?', (int) $id_printmedium);
            
            $this->delete($where);
        }
    }
    
}
<?php
/**
* Database Model for Printmedium Types
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

class Application_Model_PrintmediumTypes extends Zend_Db_Table_Abstract
{
    protected $_name = 'acd_inserate_printmedium_type';
    protected $_primary = 'id_printmedium_type';
    protected $array = array('id_printmedium_type', 'printmedium_type_position', 'printmedium_type_name', 'printmedium_columns_width', 'printmedium_width', 'printmedium_height', 'id_printmedium');
    
    public function getArray()
    {
        return $this->array;
    }
    
    public function getAllTypes()
    {
        $result = $this->fetchAll()->toArray();
        
        return $result;
    }
    
    public function getTypesByPrintmedium($id_printmedium, $printmedium_type_position = -1)
    {   
        $select = $this->select()->from(array('acd_inserate_printmedium_type'))
                                  ->where('id_printmedium = ?', $id_printmedium)
                                  ->order(array('printmedium_type_position'));
        if (-1 < $printmedium_type_position)
            $select = $select->where('printmedium_type_position = ?', $printmedium_type_position);
        
        $result = $this->fetchAll($select)->toArray();
        
        if (empty($result))
            return array();
        
        return $result;
    }
    
    public function getPrintmediumType($id_printmedium_type, $id = '')
    {   
        $select = $this->select()->from(array('acd_inserate_printmedium_type'))
                                  ->where('id_printmedium_type = ?', $id_printmedium_type);
        $result = $this->fetchRow($select)->toArray();
        
        if (!empty($id)) {
            foreach ($result as $key => $value) {
                $result_id[$key . $id] = $value;
            }
            $result = $result_id;
        }
        
        return $result;
    }
    
    public function updatePrintmediumType($id_printmedium, $values)
    {
    	for ($i = 0; $i < $values['id']; $i++) {
	    	$i_string = ($i == 0) ? '' : $i;
	    	
		    $values_printmedium_type['printmedium_type_position'] = $i;
	        $values_printmedium_type['printmedium_type_name'] = $values['printmedium_type_name' . $i_string];
	        $values_printmedium_type['printmedium_columns_width'] = $values['printmedium_columns_width' . $i_string];
	        $values_printmedium_type['printmedium_width'] = $values['printmedium_width' . $i_string];
	        $values_printmedium_type['printmedium_height'] = $values['printmedium_height' . $i_string];
	        $values_printmedium_type['id_printmedium'] = $id_printmedium;
	        
	        if (0 < $id_printmedium_type = $this->checkPrintmediumType($id_printmedium, $i)) {
		        $where = $this->getAdapter()->quoteInto('id_printmedium_type = ?', $id_printmedium_type);
	        	$this->update($values_printmedium_type, $where);
	        } else {
	        	$this->insert($values_printmedium_type);
	        }
	        
    	}
    	
    	while (0 < $id_printmedium_type = $this->checkPrintmediumType($id_printmedium, $i)) {
	        $where = $this->getAdapter()->quoteInto('id_printmedium_type = ?', $id_printmedium_type);
	        $this->delete($where);
            $i++;
    	}
    	
    }
    
    public function checkPrintmediumType($id_printmedium, $printmedium_type_position)
    {
        $select = $this->select()->from(array('acd_inserate_printmedium_type'),
                                        array('id_printmedium_type'))
                                 ->where('id_printmedium = ?', $id_printmedium)
                                 ->where('printmedium_type_position = ?', $printmedium_type_position);
        $result = $this->fetchAll($select)->toArray();
        
        if (empty($result)) {
            return 0;
        } else {
            return $result[0]['id_printmedium_type'];
        }
    }
    
    public function getColumns($id_printmedium, $type_position)
    {
        $types = $this->getTypesByPrintmedium($id_printmedium, $type_position);
        
        return floor($types[0]['printmedium_width'] / $types[0]['printmedium_columns_width']);
    }
    
    public function getColumnwidthByInserat($id_inserat, $type_position = -1)
    {
        $table_inserate = new Application_Model_Inserate();
        if (-1 == $type_position)
            $type_position = $table_inserate->getPrintmediumTypePositionByInserat($id_inserat);
        
        $table_tariff = new Application_Model_Tariff();
        $types = $table_tariff->getPrintmediumTypeByInserat($id_inserat);
        
        return $types[$type_position]['printmedium_columns_width'];
    }
    
    public function getColumnsByInserat($id_inserat, $type_position = -1)
    {
        $table_inserate = new Application_Model_Inserate();
        if (-1 == $type_position)
            $type_position = $table_inserate->getPrintmediumTypePositionByInserat($id_inserat);
        
        $table_tariff = new Application_Model_Tariff();
        $types = $table_tariff->getPrintmediumTypeByInserat($id_inserat);
        
        return floor($types[$type_position]['printmedium_width'] / $types[$type_position]['printmedium_columns_width']);
    }
    
    public function getGapByInserat($id_inserat, $type_position = -1)
    {
        $table_inserate = new Application_Model_Inserate();
        if (-1 == $type_position)
            $type_position = $table_inserate->getPrintmediumTypePositionByInserat($id_inserat);
        
        $table_tariff = new Application_Model_Tariff();
        $types = $table_tariff->getPrintmediumTypeByInserat($id_inserat);
        
        $columns = floor($types[$type_position]['printmedium_width'] / $types[$type_position]['printmedium_columns_width']);
        $gap = ($types[$type_position]['printmedium_width'] - $columns * $types[$type_position]['printmedium_columns_width']) / ($columns -1);
        
        return $gap;
    }
    
    public function deletePrintmediumType($id_printmedium)
    {
        $where = $this->getAdapter()->quoteInto('id_printmedium = ?', $id_printmedium);
        $this->delete($where);
    }
}


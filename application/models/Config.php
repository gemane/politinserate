<?php
/**
* Database Model for configuration parameters.
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

class Application_Model_Config extends Zend_Db_Table_Abstract
{
    protected $_name = 'acd_inserate_config';
    protected $_primary = 'id_config';
    protected $array = array('id_config', 'party', 'region', 'region_abb', 'government', 'source', 'color_party', 'color_region', 'size_image', 'height_image');
    
    public function getArray()
    {
        return $this->array;
    }
    
    public function getAll()
    {
        return $this->fetchAll();
    }
    
    public function getAllRegion()
    {
        $select = $this->select()->from(array('acd_inserate_config'),
                                            array('id_config', 'region', 'region_abb'))
                                 ->where('region != ?', '');
        $result = $this->fetchAll($select)->toArray();
        
        return $result;
    }
    
    public function getAllParty()
    {
        $select = $this->select()->from(array('acd_inserate_config'),
                                            array('id_config', 'party'))
                                 ->where('party != ?', '');
        $result = $this->fetchAll($select)->toArray();
        
        return $result;
    }
    
    public function getAllGovernment()
    {
        $select = $this->select()->from(array('acd_inserate_config'),
                                            array('id_config', 'government'))
                                 ->where('government != ?', '');
        $result = $this->fetchAll($select)->toArray();
        
        return $result;
    }
    
    public function getAllSizes()
    {
        $select = $this->select()->from(array('acd_inserate_config'),
                                            array('id_config', 'size_image'))
                                 ->where('size_image != ?', '')
                                 ->order('size_image');
        $result = $this->fetchAll($select)->toArray();
        
        return $result;
    }
    
    public function getAllHeights()
    {
        $select = $this->select()->from(array('acd_inserate_config'),
                                            array('id_config', 'height_image'))
                                 ->where('height_image != ?', '')
                                 ->order('height_image');
        $result = $this->fetchAll($select)->toArray();
        
        return $result;
    }
    
    public function getConfig($id_config)
    {
        $select = $this->select()->from(array('acd_inserate_config'))
                                 ->where('id_config = ?', $id_config);
        $result = $this->fetchRow($select)->toArray();
        
        return $result;
    }
    
    public function getRegion($id)
    {
        $select = $this->select()->from(array('acd_inserate_config'),
                                            array('region'))
                                 ->where('id_config = ?', $id);
        $result = $this->fetchAll($select);
        
        return $result[0]['region'];
    }
    
    public function getParty($id)
    {
        $select = $this->select()->from(array('acd_inserate_config'),
                                            array('party'))
                                 ->where('id_config = ?', $id);
        $result = $this->fetchAll($select);
        
        return $result[0]['party'];
    }
    
    public function getColorParty($id)
    {
        $select = $this->select()->from(array('acd_inserate_config'),
                                            array('color_party'))
                                 ->where('id_config = ?', $id);
        $result = $this->fetchAll($select);
        
        return $result[0]['color_party'];
    }
    
    public function getColorRegion($id)
    {
        $select = $this->select()->from(array('acd_inserate_config'),
                                            array('color_region'))
                                 ->where('id_config = ?', $id);
        $result = $this->fetchAll($select);
        
        return $result[0]['color_region'];
    }
    
    public function getSource($id)
    {
        $select = $this->select()->from(array('acd_inserate_config'),
                                            array('source'))
                                 ->where('id_config = ?', $id);
        $result = $this->fetchAll($select);
        
        return $result[0]['source'];
    }
    
    public function checkRegion($id_config)
    {
        $select = $this->select()->from(array('acd_inserate_config'),
                                            array('region'))
                                 ->where('id_config = ?', $id_config)
                                 ->where('region != ?', '');
        $result = $this->fetchAll($select)->toArray();
        
        return empty($result) ? false : true;
    }
    
    public function checkParty($id_config)
    {
        $select = $this->select()->from(array('acd_inserate_config'),
                                            array('party'))
                                 ->where('id_config = ?', $id_config)
                                 ->where('party != ?', '');
        $result = $this->fetchAll($select)->toArray();
        
        return empty($result) ? false : true;
    }
    
    public function checkGovernment($id_config)
    {
        $select = $this->select()->from(array('acd_inserate_config'),
                                            array('government'))
                                 ->where('id_config = ?', $id_config)
                                 ->where('government != ?', '');
        $result = $this->fetchAll($select)->toArray();
        
        return empty($result) ? false : true;
    }
    
    public function checkSource($id_config)
    {
        $select = $this->select()->from(array('acd_inserate_config'),
                                            array('source'))
                                 ->where('id_config = ?', $id_config)
                                 ->where('source != ?', '');
        $result = $this->fetchAll($select)->toArray();
        
        return empty($result) ? false : true;
    }

    
    public function checkSizeImage($id_config)
    {
        $select = $this->select()->from(array('acd_inserate_config'),
                                            array('size_image'))
                                 ->where('id_config = ?', $id_config)
                                 ->where('size_image != ?', '');
        $result = $this->fetchAll($select)->toArray();
        
        return empty($result) ? false : true;
    }
    
    public function formatRegion($id_region_printmedium_bit)
    {
        $country = 1; // Index of the country in config-file
        if ($id_region_printmedium_bit & pow(2, $country)) {
            return 'Österreich';
            return $this->getRegion($country);
        } else {
            $region_string = '';
            foreach ($this->getAllRegion() as $region ) {
                if ($id_region_printmedium_bit & pow(2, $region['id_config']))
                    $region_string .= $region['region'] . ', ';
            }
            
            return substr($region_string, 0, -2);
        }
    
    }
    
}
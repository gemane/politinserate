<?php
/**
* Database Model for Inserate.
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

class Application_Model_Inserate extends Zend_Db_Table_Abstract
{
    protected $_name = 'acd_inserate';
    protected $_primary = 'id_inserat';
    protected $array = array('tagged', 'id_printmedium', 'id_region_printmedium_bit', 'print_page', 'id_size', 'pages', 'inserat_columns', 'print_date', 'id_party', 'id_government', 'id_region_party', 'url_image', 'upload_time', 'id_uploader', 'ip_uploader', 'id_tagger', 'id_source', 'timestamp');
    
    public function getArray()
    {
        return $this->array;
    }
    
    public function getAllID_Inserate()
    {
        $select = $this->select()->from(array('acd_inserate'),
                                            array('id_inserat'));
        $result = $this->getAdapter()->fetchCol($select);
        
        return $result;
    }
    
    public function getAllTaggedID_Inserate($id_user = false)
    {
        $select = $this->select()->from(array('acd_inserate'),
                                            array('id_inserat', 'id_tagger'))
                                    ->where('tagged = ?', 1)
                                    ->order('print_date DESC');
        
        if (false != $id_user) 
            $select_inserat = $select->where('id_tagger = ?', $id_user);
        
        $result = $this->getAdapter()->fetchCol($select);
        
        return $result;
    }
    
    public function getLastTaggedID_Inserate($rows = 3)
    {
        $select = $this->select()->from(array('acd_inserate'),
                                            array('id_inserat'))
                                    ->where('tagged = ?', 1)
                                    ->order('id_inserat DESC')
                                    ->limit($rows, 0);
        $result = $this->getAdapter()->fetchCol($select);
        
        return $result;
    }
    
    public function getAllUntaggedID_Inserate()
    {
        $select = $this->select()->from(array('acd_inserate'),
                                            array('id_inserat'))
                                 ->where('tagged = ?', 0)
                                 ->order('id_inserat DESC');
        $result = $this->getAdapter()->fetchCol($select);
        
        return $result;
    }
    
    public function getAllTrashedID_Inserate()
    {
        $select = $this->select()->from(array('acd_inserate'),
                                            array('id_inserat'))
                                 ->where('tagged = ?', -1)
                                 ->order('id_inserat DESC');
        $result = $this->getAdapter()->fetchCol($select);
        
        return $result;
    }
    
    public function getInserat($id_inserat)
    {
        $select = $this->select()->from(array('acd_inserate'))
                                 ->where('id_inserat = ?', $id_inserat);
        $result = $this->fetchAll($select)->toArray();
        return $result;
    }
    
    public function getInseratAll($id_inserat)
    {
        $this->setSelectAll();
        
        if ($this->getID_Government($id_inserat) > 0) {
            $select_inserat = $this->query->join(array('t' => 'acd_inserate_config'),
                                            'd.id_government = t.id_config',
                                            array('government'));
        }
        $select_inserat = $this->query->where('d.id_inserat = ?', $id_inserat);
        $result = $this->fetchAll($select_inserat)->toArray();
        
        $result[0]['size_width'] = $this->calculateWidth($id_inserat);
        $result[0]['price_inserat'] = $this->calculatePrice($id_inserat);
        
        $config = new Application_Model_Config();
        $result[0]['region_printmedium_bit'] = $config->formatRegion($result[0]['id_region_printmedium_bit']);
        
        if (empty($result)) {
            return false;
        }
        
        if (empty($result[0]['government'])) {
            $result[0]['payer'] = $result[0]['party'];
        } else {
            $result[0]['payer'] = $result[0]['government'];
        }
        
        $select = $this->select()->from(array('acd_inserate'),
                                        array('id_tagger'))
                                 ->where('id_inserat = ?', $id_inserat);
        $id_tagger = $this->fetchAll($select);
        $user = new Application_Model_Users();
        if (1 < $id_tagger[0]['id_tagger']) {
            $result[0]['tagger'] = $user->getUsernameByID($id_tagger[0]['id_tagger']);
        } else {
            $result[0]['tagger'] = $user->getUsernameByID(1);  // Username: Anonymous
        }
        
        return $result;
    }
    
    protected function calculatePrice($id_inserat)
    {
        $select = $this->select()->from(array('d' => 'acd_inserate'),
                                        array('id_inserat', 'pages', 'inserat_columns'))
                                    ->join(array('r' => 'acd_inserate_size'),
                                            'd.id_size = r.id_size',
                                            array('price'));
        $select->setIntegrityCheck(false);
        $select = $select->where('d.id_inserat = ?', $id_inserat);
        $result = $this->fetchAll($select)->toArray();
        
        $table_types = new Application_Model_PrintmediumTypes();
        
        return $result[0]['pages'] * $result[0]['price'] * $result[0]['inserat_columns'] / $table_types->getColumnsByInserat($id_inserat);
    }
    
    protected function calculateWidth($id_inserat)
    {
        $select = $this->select()->from(array('acd_inserate'),
                                        array('inserat_columns'));
        $select = $select->where('id_inserat = ?', $id_inserat);
        $result = $this->fetchAll($select);
        
        $table_types = new Application_Model_PrintmediumTypes();
        
        $gap = $table_types->getGapByInserat($id_inserat);
        $columns_width = $table_types->getColumnwidthByInserat($id_inserat);
        
        if (1 < $result[0]['inserat_columns'])
            $width = $result[0]['inserat_columns'] * $columns_width + ($result[0]['inserat_columns'] - 1) * $gap;
        else
            $width = $result[0]['inserat_columns'] * $columns_width;
        
        return round($width);
    }
    
    public function getInseratTagged($id_inserat)
    {
        $this->setSelectTagged();
        
        // When Id_Government is set to 0 then advertisment is paid by the party itself.
        if ($this->getID_Government($id_inserat) > 0) {
            $select_inserat = $this->query->join(array('t' => 'acd_inserate_config'),
                                            'd.id_government = t.id_config',
                                            array('government'));
        }
        $select_inserat = $this->query->where('d.id_inserat = ?', $id_inserat);
        
        $result = $this->fetchAll($select_inserat)->toArray();
        
        if (empty($result))
            return false;
        
        if (empty($result[0]['government']))
            $result[0]['payer'] = $result[0]['party'];
        else
            $result[0]['payer'] = $result[0]['government'];
        
        return $result;
    }
    
    public function getInseratUntagged($id_inserat)
    {
        $this->setSelectUntagged();
        $select_inserat = $this->query->where('d.id_inserat = ?', $id_inserat);
        $result = $this->fetchAll($select_inserat)->toArray();
        
        if (empty($result))
            return false;
        
        $untagged_medium = array('id_printmedium', 'id_region_printmedium_bit', 'id_size');
        $untagged_party = array('id_party', 'id_government', 'id_region_party');
        $count_untagged_medium = 3; $count_untagged_party = 3;
        $untagged_medium_list = ''; $untagged_party_list ='';
        foreach ($untagged_medium as $key) {
            if (0 == $result[0][$key]) {
                $count_untagged_medium--;
                $untagged_medium_list .= '<dd>- ' . $this->setTitel($key) .  '</dd>';
            }
        }
        foreach ($untagged_party as $key) {
            if (0 == $result[0][$key]) {
                $count_untagged_party--;
                $untagged_party_list .= '<dd>- ' . $this->setTitel($key) .  '</dd>';
            }
        }
        $result[0]['count_untagged_medium'] = $count_untagged_medium;
        $result[0]['count_untagged_party'] = $count_untagged_party;
        $result[0]['untagged_medium_list'] = $untagged_medium_list;
        $result[0]['untagged_party_list'] = $untagged_party_list;
        
        return $result;
    }
    
    protected function setTitel($id)
    {
        switch ($id) {
            case 'id_printmedium':
                return 'Printmedium'; break;
            case 'id_region_printmedium_bit':
                return 'Region'; break;
            case 'id_size':
                return 'Format'; break;
            case 'id_party':
                return 'Partei'; break;
            case 'id_government':
                return 'Zahlende'; break;
            case 'id_region_party':
                return 'Region'; break;
            default :
                return false;
        }
    }
    
    public function getInseratTrashed($id_inserat)
    {
        $this->setSelectTrashed();
        $select_inserat = $this->query->where('d.id_inserat = ?', $id_inserat);
        $result = $this->fetchAll($select_inserat)->toArray();
        
        if (empty($result)) {
            return false;
        }
        
        $untagged_medium = array('id_printmedium', 'id_region_printmedium_bit', 'id_size');
        $untagged_party = array('id_party', 'id_government', 'id_region_party');
        $count_untagged_medium = 3; $count_untagged_party = 3;
        $untagged_medium_list = ''; $untagged_party_list ='';
        foreach ($untagged_medium as $key) {
            if (0 == $result[0][$key]) {
                $count_untagged_medium--;
                $untagged_medium_list .= '<li>' . $this->setTitel($key) .  '</li>';
            }
        }
        foreach ($untagged_party as $key) {
            if (0 == $result[0][$key]) {
                $count_untagged_party--;
                $untagged_party_list .= '<li>' . $this->setTitel($key) .  '</li>';
            }
        }
        $result[0]['count_untagged_medium'] = $count_untagged_medium;
        $result[0]['count_untagged_party'] = $count_untagged_party;
        $result[0]['untagged_medium_list'] = $untagged_medium_list;
        $result[0]['untagged_party_list'] = $untagged_party_list;
        
        return $result;
    }
    
    protected function setSelectAll()
    {
        $this->query = $this->select()->from(array('d' => 'acd_inserate'),
                                        array('id_inserat', 'tagged', 'id_region_printmedium_bit', 'print_page', 'pages', 'inserat_columns', 'print_date', 'politician', 'url_image', 'upload_time', 'timestamp'))
                                    ->join(array('p' => 'acd_inserate_printmedium'),
                                            'd.id_printmedium = p.id_printmedium',
                                            array('printmedium'))
                                    ->join(array('r' => 'acd_inserate_size'),
                                            'd.id_size = r.id_size',
                                            array('size','price', 'size_height', 'cover'))
                                    ->join(array('s' => 'acd_inserate_config'),
                                            'd.id_party = s.id_config',
                                            array('party'))
                                    ->join(array('u' => 'acd_inserate_config'),
                                            'd.id_region_party = u.id_config',
                                            array('region_party' => 'region'))
                                    ->join(array('v' => 'acd_inserate_user'),
                                            'd.id_uploader = v.id_user',
                                            array('uploader' => 'username'))
                                    ->join(array('x' => 'acd_inserate_config'),
                                            'd.id_source = x.id_config',
                                            array('source'))
                                    ->join(array('y' => 'acd_inserate_printmedium_type'),
                                            'r.id_printmedium_type = y.id_printmedium_type',
                                            array('printmedium_type_name'))
                                    ->order('print_date DESC');
        $this->query->setIntegrityCheck(false);
    }
    
    protected function setSelectTagged()
    {
        $this->setSelect();
        $this->query = $this->query->join(array('p' => 'acd_inserate_printmedium'),
                                            'd.id_printmedium = p.id_printmedium',
                                            array('printmedium', 'd.id_tagger'))
                                    ->join(array('r' => 'acd_inserate_size'),
                                            'd.id_size = r.id_size',
                                            array('size','price'))
                                    ->join(array('s' => 'acd_inserate_config'),
                                            'd.id_party = s.id_config',
                                            array('party'))
                                    ->join(array('q' => 'acd_inserate_config'),
                                            'd.id_region_party = q.id_config',
                                            array('region_party' => 'region', 'region_abb'))
                                    ->where('d.tagged = ?', 1);
        $this->query->setIntegrityCheck(false);
    }
    
    protected function setSelectUntagged()
    {
        $this->setSelect();
        $this->query = $this->query->where('d.tagged = ?', 0);
    }
    
    protected function setSelectTrashed()
    {
        $this->setSelect();
        $this->query = $this->query->where('d.tagged = ?', -1);
    }
    
    protected function setSelect()
    {
        $this->query = $this->select()->from(array('d' => 'acd_inserate'),
                                        array('id_inserat', 'id_printmedium', 'id_region_printmedium_bit', 'id_size', 'pages', 'inserat_columns', 'print_date', 'id_party', 'id_government', 'id_region_party', 'price_inserat', 'url_image', 'upload_time', 'id_source', 'timestamp'))
                                    ->join(array('v' => 'acd_inserate_user'),
                                            'd.id_uploader = v.id_user',
                                            array('uploader' => 'username'))
                                    ->join(array('w' => 'acd_inserate_config'),
                                            'd.id_source = w.id_config',
                                            array('source'))
                                    ->order('print_date DESC');
        $this->query->setIntegrityCheck(false);
    }
    
    public function getPayment($year = false, $month = false, $id_printmedium = 0, $id_party = 0, $payer = 'all_payed', $id_region = 0)
    {   
        $select = $this->select()->from(array('d' => 'acd_inserate'),
                                        array('payment' => new Zend_Db_Expr('SUM(d.price_inserat)')))
                                 ->where('d.tagged = ?', 1);
        $select->setIntegrityCheck(false);
        
        if (0 < $id_printmedium)
            $select->where('d.id_printmedium = ?', $id_printmedium);
        
        if (0 < $id_party)
            $select->where('d.id_party = ?', $id_party);
        
        if ($payer == 'government_payed')
            $select->where('d.id_government > ?', 0);
        elseif ($payer == 'party_payed')
            $select->where('d.id_government = ?', 0);
        
        if (0 < $id_region)
            $select->where('d.id_region_party = ?', $id_region);
        
        if (false != $year) 
            $select->where('YEAR(d.print_date) = ?', $year);
        
        if (false != $month) 
            $select->where('MONTH(d.print_date) = ?', $month);
        
        $result = $this->fetchAll($select);
        
        if (empty($result) || empty($result[0]['payment']))
            return 0;
        else
            return $result[0]['payment'];
    }
    
    public function getRangeDate()
    {
        $select = $this->select()->from(array('acd_inserate'),
                                            array('max_date' => 'MAX(print_date)', 'min_date' => 'MIN(print_date)'))
                                    ->where('tagged = ?', 1);
        $result = $this->fetchAll($select)->toArray();
        
        return array('min_date' => strtotime($result[0]['min_date']), 'max_date' => strtotime($result[0]['max_date']));
        
    }
    
    protected function getID_Government($id_inserat)
    {
        $select = $this->select()->from(array('acd_inserate'),
                                            array('id_government'))
                                    ->where('id_inserat = ?', $id_inserat);
        $result = $this->fetchAll($select);
        
        return $result[0]['id_government'];
    }
    
    public function getRegionPrintmedium($id_inserat)
    {
        $select = $this->select()->from(array('acd_inserate'),
                                            array('id_region_printmedium_bit'))
                                 ->where('id_inserat = ?', $id_inserat);
        $result = $this->fetchAll($select);
        
        return $result[0]['id_region_printmedium_bit'];
    }
    
    public function getID_Printmedium($id_inserat)
    {
        $select = $this->select()->from(array('acd_inserate'),
                                            array('id_printmedium'))
                                 ->where('id_inserat = ?', $id_inserat);
        $result = $this->fetchAll($select);
        
        return $result[0]['id_printmedium'];
    }
    
    public function getExistingRegions()
    {
        $regions = array(1, 2, 4);
        
        return $regions;
    }
    
    public function getPrintDate($id_inserat)
    {
        $select = $this->select()->from(array('acd_inserate'),
                                            array('print_date'))
                                 ->where('id_inserat = ?', $id_inserat);
        $result = $this->fetchAll($select);
        
        return $result[0]['print_date'];
    }
    
    public function getImageUrl($id_inserat)
    {
        $select = $this->select()->from(array('acd_inserate'),
                                            array('url_image'))
                                 ->where('id_inserat = ?', $id_inserat);
        $result = $this->fetchAll($select);
        
        return $result[0]['url_image'];
    }
    
    public function getIDSource($id_inserat)
    {
        $select = $this->select()->from(array('acd_inserate'),
                                            array('id_source'))
                                 ->where('id_inserat = ?', $id_inserat);
        $result = $this->fetchAll($select);
        
        return $result[0]['id_source'];
    }
    
    public function getTagger($id_inserat)
    {
        $select = $this->select()->from(array('d' => 'acd_inserate'))
                                 ->join(array('p' => 'acd_inserate_user'),
                                            'd.id_tagger = p.id_user',
                                            array('tagger' => 'username', 'tagger_full' => 'user_fullname', 'id_user'))
                                 ->where('id_inserat = ?', $id_inserat);
        $select->setIntegrityCheck(false);
        $result = $this->fetchAll($select)->toArray();
        
        if (1 != $result[0]['id_user']) {
            if ('' != $result[0]['tagger_full'])
                return $result[0]['tagger_full'];
            else
                return $result[0]['tagger'];
        }
    }
    
    public function insertPhoto($values)
    {
        $values_photo['id_uploader'] = $values['id_uploader'];
        $values_photo['ip_uploader'] = $_SERVER['REMOTE_ADDR'];
        $values_photo['id_source'] = $values['id_source'];
        $values_photo['url_image'] = $values['url_image'];
        if (empty($values['upload_time'])) {
            $values_photo['upload_time'] = date('Y-m-d H:i:s', time());
        } else {
            $values_photo['upload_time'] = $values['upload_time'];
        }
        
        return $this->insert($values_photo);
    }
    
    public function deleteInserat($id_inserat)
    {   
        $where = $this->getAdapter()->quoteInto('tagged = -1 AND id_inserat = ?', $id_inserat);
        
        return $this->delete($where);
    }
    
    public function updateMedium($id_inserat, $values)
    {
        $array = array('id_printmedium', 'print_date', 'print_page');
        $where = $this->getAdapter()->quoteInto('id_inserat = ?', $id_inserat);
        foreach ($array as $key)
            $values_inserat[$key] = $values[$key];
            
        $bit = 0;
        $config = new Application_Model_Config();
        foreach ($config->getAllRegion() as $region ) {
            if ((1 == $values[$region['region_abb']]) || (1 == $values['aut']))
                $bit += pow(2, $region['id_config']);
        }
        $values_inserat['id_region_printmedium_bit'] = $bit;
            
        $this->update($values_inserat, $where);
    }
    
    public function updateFormat($id_inserat, $values)
    {
        $where = $this->getAdapter()->quoteInto('id_inserat = ?', $id_inserat);
        $values_inserat['id_size'] = $values['id_size'];
        $values_inserat['pages'] = $values['pages'];
        $values_inserat['inserat_columns'] = $values['inserat_columns'];
        $this->update($values_inserat, $where);
    }
    
    public function updateParty($id_inserat, $values)
    {
        $array = array('id_party', 'id_government', 'id_region_party', 'politician');
        $where = $this->getAdapter()->quoteInto('id_inserat = ?', $id_inserat);
        foreach ($array as $key)
            $values_inserat[$key] = $values[$key];
        $this->update($values_inserat, $where);
    }
    
    public function updateTagger($id_inserat, $values)
    {
        $where = $this->getAdapter()->quoteInto('id_inserat = ?', $id_inserat);
        $values_inserat['id_tagger'] = $values['id_tagger'];
        $values_inserat['tagged'] = 1;
        $this->update($values_inserat, $where);
    }
    
    public function updatePrice($id_inserat)
    {
        $where = $this->getAdapter()->quoteInto('id_inserat = ?', $id_inserat);
        $values_inserat['price_inserat'] = $this->calculatePrice($id_inserat);
        
        $this->update($values_inserat, $where);
    }
    
    public function updateRemote($id_inserat, $keywords)
    {
        $where = $this->getAdapter()->quoteInto('id_inserat = ?', $id_inserat);
        foreach ($keywords as $key => $value)
            $values_inserat[$key] = $value;
        
        $values_inserat['id_region_printmedium_bit'] = ($this->getRegionPrintmedium($id_inserat) | pow(2, $values_inserat['id_region']));
        unset($values_inserat['id_region']);
        
        $this->update($values_inserat, $where);
    }
    
    public function moveTrash($id_inserat)
    {
        $where = $this->getAdapter()->quoteInto('id_inserat = ?', $id_inserat);
        $values_inserat['tagged'] = -1;
        $result = $this->update($values_inserat, $where);
    }
    
    public function moveUntagged($id_inserat)
    {
        $where = $this->getAdapter()->quoteInto('id_inserat = ?', $id_inserat);
        $values_inserat['tagged'] = 0;
        $this->update($values_inserat, $where);
    }
    
    public function moveOldUntagged($days)
    {
        if ($days > 0) {
            $time = date('Y-m-d H:i:s', time() - $days * 60 * 60 * 24); // Move after $days
            $values_inserat['tagged'] = -1;
            $where = $this->getAdapter()->quoteInto('tagged = 0 AND timestamp < ?', $time);
            $this->update($values_inserat, $where);
        }
    }
    
    public function deleteOldTrashed($delete_ids = false, $days = 30)
    {
        if (false == $delete_ids)
            $delete_ids = $this->getOldTrashed($days);
        
        echo '<br />';
        foreach ($delete_ids as $delete_id) {
            // Check again if inserat is set to trash
            if (-1 == $this->getTagged($delete_id)) {
                $file_t = APPLICATION_PATH .'/../data/public/images/uploads/default/inserat_' . sprintf('%06d', $delete_id) . '_t.jpg';
                if (file_exists($file_t))
                    unlink($file_t);
                $file_d = APPLICATION_PATH .'/../data/public/images/uploads/default/inserat_' . sprintf('%06d', $delete_id) . '_d.jpg';
                if (file_exists($file_d))
                    unlink($file_d);
                $file_o = APPLICATION_PATH .'/../data/uploads/images/original/inserat_' . sprintf('%06d', $delete_id) . '_o.jpg';
                if (file_exists($file_o))
                    unlink($file_o);
                
                $this->deleteInserat($delete_id);
                echo 'Inserat gelöscht: ' . $delete_id . '<br />';
            }
        }
        
    }
    
    public function getOldTrashed($days)
    {
        if ($days > 0) {
            $time = date('Y-m-d H:i:s', time() - $days * 60 * 60 * 24); // Delete after $days
            $select = $this->select()->from(array('acd_inserate'),
                                            array('id_inserat'))
                                     ->where('tagged = ?', -1) // !! Do not change !! Value must be -1 !
                                     ->where('timestamp < ?', $time);
            $result = $this->fetchAll($select)->toArray();
            
            if (!empty($result))
                return $result[0];
            else
                return array();
        } else {
            return array();
        }
    }
    
    public function getTagged($id_inserat)
    {
        $select = $this->select()->from(array('acd_inserate'),
                                            array('tagged'))
                                 ->where('id_inserat = ?', $id_inserat);
        $result = $this->fetchAll($select);
        return $result[0]['tagged'];
    }
    
    public function getFormatTagged($id_inserat)
    {
        $select = $this->select()->from(array('acd_inserate'),
                                            array('id_inserat', 'id_size', 'pages', 'inserat_columns'))
                                    ->where('id_inserat = ?', $id_inserat);
        $result = $this->fetchRow($select)->toArray();
        
        return $result;
    }
    
    public function getMediumTagged($id_inserat)
    {
        $select = $this->select()->from(array('acd_inserate'),
                                            array('id_inserat', 'id_printmedium', 'id_region_printmedium_bit', 'print_page', 'print_date'))
                                    ->where('id_inserat = ?', $id_inserat);
        $result = $this->fetchRow($select)->toArray();
        
        $config = new Application_Model_Config();
        foreach ($config->getAllRegion() as $region ) {
            if ($result['id_region_printmedium_bit'] & pow(2, $region['id_config']))
                $result[$region['region_abb']] = 1;
        }
        
        return $result;
    }
    
    public function getPartyTagged($id_inserat)
    {
        $select = $this->select()->from(array('acd_inserate'),
                                            array('id_inserat', 'id_party', 'id_government', 'id_region_party', 'politician'))
                                    ->where('id_inserat = ?', $id_inserat);
        $result = $this->fetchRow($select)->toArray();
        
        return $result;
    }
    
    public function checkAllTagged($id_inserat)
    {
        $array = array('id_printmedium', 'id_region_printmedium_bit', 'print_page', 'id_size', 'print_date', 'id_party', 'id_region_party', 'url_image', 'id_uploader', 'id_source');
        $values = $this->getInserat($id_inserat);
        
        foreach ($array as $key)
            if (empty($values[0][$key])) return false;
        
        return true;
    }
    
    public function checkInserat($id_inserat)
    {
        $select = $this->select()->from(array('acd_inserate'),
                                            array('id_inserat'))
                                 ->where('id_inserat = ?', $id_inserat);
        $result = $this->fetchAll($select)->toArray();
        
        return empty($result) ? false : true;
    }
    
    public function checkUrl($url)
    {
        $select = $this->select()->from(array('acd_inserate'),
                                            array('id_inserat'))
                                 ->where('url_image = ?', $url);
        $result = $this->fetchAll($select)->toArray();
        
        return empty($result) ? false : true;
    }
    
    public function checkInseratExists($id_inserat)
    {
        $select = $this->select()->from(array('acd_inserate'),
                                            array('id_size', 'pages', 'inserat_columns', 'print_date', 'id_party', 'id_government', 'id_region_party'))
                                 ->where('id_inserat = ?', $id_inserat);
        $result = $this->fetchAll($select)->toArray();
        
        $inserat_columns = floatval($result[0]['inserat_columns']);
        
        $select = $this->select()->from(array('acd_inserate'),
                                            array('id_inserat'))
                                 ->where('id_size = ?', $result[0]['id_size'])
                                 ->where('pages = ?', $result[0]['pages'])
                                 ->where('inserat_columns = ?', $inserat_columns)
                                 ->where('print_date = ?', $result[0]['print_date'])
                                 ->where('id_party = ?', $result[0]['id_party'])
                                 ->where('id_government = ?', $result[0]['id_government'])
                                 ->where('id_region_party = ?', $result[0]['id_region_party'])
                                 ->where('tagged = ?', 1);
        $select->setIntegrityCheck(false);
        $result = $this->fetchAll($select)->toArray();
        
        if (empty($result))
            return false;
        else 
            return $result[0]['id_inserat'];
    }
    
    public function getNumInserate($id_size)
    {
        $select = $this->select()->from(array('acd_inserate'),
                                            array('num_inserate' => 'COUNT(*)'))
                                 ->where('id_size = ?', $id_size);
        $result = $this->fetchAll($select);
        return $result[0]['num_inserate'];
    }
    
    public function getNumPhotoByUserid($username)
    {
        $select = $this->select()->from(array('d' => 'acd_inserate'),
                                            array('num_photo' => 'COUNT(*)'))
                                 ->join(array('p' => 'acd_inserate_user'),
                                            'd.id_uploader = p.id_user',
                                            array())
                                 ->where('p.username = ?', $username);
        $select->setIntegrityCheck(false);
        $result = $this->fetchAll($select)->toArray();
        if (empty($result)) {
            return 0;
        } else {
            return $result[0]['num_photo'];
        }
    }
    
    public function getNumInserateByUserid($username)
    {
        $select = $this->select()->from(array('d' => 'acd_inserate'),
                                            array('num_inserate' => 'COUNT(*)'))
                                 ->join(array('p' => 'acd_inserate_user'),
                                            'd.id_tagger = p.id_user',
                                            array('username'))
                                 ->group('username')
                                 ->where('username = ?', $username);
        $select->setIntegrityCheck(false);
        $result = $this->fetchAll($select)->toArray();
        if (empty($result)) {
            return 0;
        } else {
            return $result[0]['num_inserate'];
        }
    }
    
    public function getPrintmediumByInserat($id_inserat)
    {
        $select = $this->select()->from(array('d' => 'acd_inserate'),
                                        array())
                                    ->join(array('r' => 'acd_inserate_printmedium'),
                                            'd.id_printmedium = r.id_printmedium',
                                            array('printmedium'))
                                    ->where('id_inserat = ?', $id_inserat);
        $select->setIntegrityCheck(false);
        $result = $this->fetchAll($select)->toArray();
        
        return $result[0]['printmedium'];
    }
    
    public function getPrintmediumTypePositionByInserat($id_inserat)
    {
        $select = $this->select()->from(array('d' => 'acd_inserate'),
                                        array())
                                 ->where('d.id_inserat = ?', $id_inserat)
                                 ->join(array('r' => 'acd_inserate_size'),
                                            'd.id_size = r.id_size',
                                            array())
                                 ->join(array('s' => 'acd_inserate_printmedium_type'),
                                            'r.id_printmedium_type = s.id_printmedium_type',
                                            array('printmedium_type_position'));
        $select->setIntegrityCheck(false);
        $result = $this->fetchAll($select)->toArray();
        
        return $result[0]['printmedium_type_position'];
    }
    
    public function getCover($id_inserat)
    {
        $select = $this->select()->from(array('d' => 'acd_inserate'),
                                        array())
                                 ->where('d.id_inserat = ?', $id_inserat)
                                 ->join(array('r' => 'acd_inserate_size'),
                                            'd.id_size = r.id_size',
                                            array('cover'));
        $select->setIntegrityCheck(false);
        $result = $this->fetchAll($select)->toArray();
        
        if (empty($result))
            return false;
        else
            return $result[0]['cover'];
    }
    
    public function updateDBColumn()
    {
        $table_types = new Application_Model_PrintmediumTypes();
        $inserate = $this->getAllTaggedID_Inserate();
        echo '<br />';
        foreach ($inserate as $values) {
            $select = $this->select()->from(array('d' => 'acd_inserate'),
                                            array('inserat_columns', 'price_inserat'));
            $result = $this->fetchAll($select)->toArray();
            $where = $this->getAdapter()->quoteInto('id_inserat = ?', $values);
            if (0 == $result[0]['inserat_columns']) {
                $inserat_columns = $table_types->getColumnsByInserat($values);
                
                $values_inserat['inserat_columns'] = $inserat_columns;
                $values_inserat['pages'] = 1;
            }
            //if (0 == $result[0]['price_inserat']) {
                $values_inserat['price_inserat'] = $this->calculatePrice($values);
            //}
            if (!empty($values_inserat)) {
                $this->update($values_inserat, $where);
                print_r($values);echo ' ';
            }
        }
    }
    
    public function updateDBPhotos()
    {
        $i_start = 1;
        $i_end = 180;
        
        echo '<br />';
        for ($i = $i_start; $i <= $i_end; $i++) {
            $file_i = APPLICATION_PATH . '/../data/uploads/images/original/inserat_'. sprintf('%06d', $i) . '_o.jpg';
            if (file_exists($file_i)) {
                if (!$this->checkInserat($i)) {
                    $values_photo['id_uploader'] = 54;
                    $values_photo['ip_uploader'] = $_SERVER['REMOTE_ADDR'];
                    $values_photo['id_source'] = 1;
                    $values_photo['url_image'] = 'unknown';
                    $values_photo['upload_time'] = date('Y-m-d H:i:s', filemtime($file_i));
                    
                    $id_inserat = $this->insert($values_photo);
                    $where = $this->getAdapter()->quoteInto('id_inserat = ?', $id_inserat);
                    $values['id_inserat'] = $i;
                    $this->update($values, $where);
                    echo $file_i . '<br/>';
                }
            }
        }
    }
}
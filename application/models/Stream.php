<?php
/**
* Model for stream data.
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

include_once('Intern/Image/Editing.php');

class Application_Model_Stream
{
    
    public function __construct()
    {
        $this->table_inserat = new Application_Model_Inserate();
        $this->cache = Zend_Registry::get('cache');
    }
    
    /**
    * Retrieving data from database for view (partial).
    *
    * @return array Data of all tagged advertisments.
    */
    public function setTableTagged($id_user = false)
    {
        $ID_Inserate = $this->table_inserat->getAllTaggedID_Inserate($id_user); // TODO2: Darstellung über Paginator
        
        if (!empty($ID_Inserate)) {
            $this->configuration = Zend_Registry::get('configuration');
            foreach ($ID_Inserate as $id_inserat) {
                $table[$id_inserat] = $this->table_inserat->getInseratTagged($id_inserat);
                $table = $this->setImage($id_inserat, $table);
                $table['ids'][] =  $id_inserat;
                $table['tooltips'] =  $this->tooltipTagged($table);
            }
            
            if ($this->configuration->general->csv)
                $lockfile = APPLICATION_PATH . '/../temp/lock_csv_inserate';
                if (86400 < (time() - filemtime($lockfile))) {
                    $this->createCSV();
                    $locktime = mktime($this->configuration->general->csv_time, 0, 0);
                    touch($lockfile, $locktime);
                }
        } else {
            $table['ids'] =  array();
            $table['tooltips'] = array();
        }
        
        if (false == $id_user)
            $this->cache->save($table, 'table_stream_tagged');
        
        return $table;
     }
     
    /**
    * Retrieving data from database for view (partial).
    *
    * @return array Data of all tagged advertisments.
    */
    public function setTableUntagged()
    {
        $ID_Inserate = $this->table_inserat->getAllUntaggedID_Inserate();
        
        if (!empty($ID_Inserate)) {
            foreach ($ID_Inserate as $id_inserat) {
                $table[$id_inserat] = $this->table_inserat->getInseratUntagged($id_inserat);
                $table[$id_inserat][0]['link'] = $this->getLink($table[$id_inserat]);
                $table = $this->setImage($id_inserat, $table);
            }
            $table['ids'] =  $ID_Inserate;
            $table['tooltips'] =  $this->tooltip($table);
        } else {
            $table['ids'] =  array();
            $table['tooltips'] = array();
        }
        
        $this->cache->save($table, 'table_stream_untagged');
        
        return $table;
     }
     
    /**
    * Retrieving data from database for view (partial).
    *
    * @return array Data of all trashed advertisments.
    */
    public function setTableTrashed()
    {
        $ID_Inserate = $this->table_inserat->getAllTrashedID_Inserate();
        
        if (!empty($ID_Inserate)) {
            foreach ($ID_Inserate as $id_inserat) {
                $table[$id_inserat] = $this->table_inserat->getInseratTrashed($id_inserat);
                $table = $this->setImage($id_inserat, $table);
            }
            $table['ids'] =  $ID_Inserate;
            $table['tooltips'] =  $this->tooltip($table);
        } else {
            $table['ids'] =  array();
            $table['tooltips'] = array();
        }
        
        $this->cache->save($table, 'table_stream_trash');
        
        return $table;
     }
     
     protected function getLink($row)
     {
        switch ($row[0]['count_untagged_medium']) {
            case 0:
            case 1:
                return 'medium';
            case 2:
                return 'format';
            case 3:
                switch ($row[0]['count_untagged_party']) {
                    case 0:
                    case 1:
                    case 2:
                        return 'partei';
                    case 3:
                        return 'kontrolle';
                    default;
                        return '';
                }
            }
     }
     
     protected function setImage($id_inserat, $table)
     {
        $this->image = new Image_Editing();
        $image = $this->image->orientationImageThumbnail($id_inserat);
        $table[$id_inserat][0]['width'] = $image['width'];
        $table[$id_inserat][0]['height'] = $image['height'];
        $table[$id_inserat][0]['image'] = $image['image'];
        
        return $table;
     }
     
     protected function tooltip($table)
     {
        if (empty($table)) {
            $this->view->tooltips = '';
            return false;
        }
        
        $tooltips = 'var tooltips=[];';
        foreach ($table['ids'] as $key) {
            if (3 > $table[$key][0]['count_untagged_medium'])
                $comment_medium = '<dl><dt>Printmedium</dt>' . $table[$key][0]['untagged_medium_list'] . '</dl>';
            else
                $comment_medium = '';
            
            if (3 > $table[$key][0]['count_untagged_party'])
                $comment_party = '<dl><dt>Partei</dt>' . $table[$key][0]['untagged_party_list'] . '</dl>';
            else
                $comment_party = '';
            
            if ('' == $comment_medium . $comment_party)
                $comment_medium = '<dl><dt>Keine</dt><dl>';
            
            $tooltips .= 'tooltips[' . $key . ']=["/images/status/status_' . $table[$key][0]['count_untagged_medium'] . '_' . $table[$key][0]['count_untagged_party'] . '.jpg", "<strong>Fehlende Zuordnungen</strong>' . $comment_medium . $comment_party . '", {font:"normal 12px Arial"}];';
        }
        return $tooltips . "\n";
     }
     
     protected function tooltipTagged($table)
     {
        if (empty($table)) {
            $this->view->tooltips = '';
            return false;
        }
        
        $tooltips = 'var tooltips=[];';
        foreach ($table['ids'] as $key) {
            $image = '/images/';
            if (empty($table[$key][0]['government']))
                $image .= 'logo_party/logo_' . strtolower($this->preparePath($table[$key][0]['party'])) .  '.jpg';
            else
                $image .= 'logo_government/logo_' . $this->preparePath($table[$key][0]['region_abb']) .  '.png';
            
            $tooltips .= 'tooltips[' . $key . ']=["' . $image . '", "Bezahlt von<br /><strong>' . $table[$key][0]['payer'] . '</strong>", {font:"normal 12px Arial"}];';
        }
        return $tooltips . "\n";
     }
     
    /**
    * Create CSV datafile of inserat data
    *
    * @param $array table of inserate
    * 
    */
    protected function createCSV()
    {
        $this->table_config = new Application_Model_Config();
        $ID_Inserate = $this->table_inserat->getAllTaggedID_Inserate();
        
        $path = APPLICATION_PATH . '/../data/downloads/';
        $path_target = $path . 'zugeordnete_inserate.zip';
        
        $path_table = $path . 'inserate/' . 'tabelle_inserate.csv';
        $path_image = $this->configuration->general->url . '/images/uploads/';
        $fp = fopen($path_table, 'w');
        $line_csv = array(
            'Partei',
            'Region Partei',
            'Politiker',
            'Zahlende',
            'Printmedium',
            'Region Printmedium',
            'Seite',
            'Datum',
            'Format',
            'Seiten',
            'Spalten',
            'Breite',
            'Höhe',
            'Kosten [EUR]',
            'Uploader',
            'Upload Zeit',
            'Tagger',
            'Tag Zeit',
            'Quelle',
            'Kleines Bild',
            'Normales Bild',
            );
        fputcsv($fp, $line_csv, ';');
        if (!empty($ID_Inserate)) {
            foreach ($ID_Inserate as $id_inserat) {
                $inserat = $this->table_inserat->getInseratAll($id_inserat);
                if (!empty($inserat[0]['party'])) {
                    $file_thumbnail = 'inserat_' . sprintf('%06d', $inserat[0]['id_inserat']) . '_t.jpg';
                    $file_default = $path_image . 'default/' . 'inserat_' . sprintf('%06d', $inserat[0]['id_inserat']) . '_d.jpg';
                    $line_csv = array(
                        $inserat[0]['party'], 
                        $inserat[0]['region_party'],
                        (1 == $inserat[0]['politician']) ? 'ja' : 'nein',
                        $inserat[0]['payer'],
                        $inserat[0]['printmedium'],
                        $this->table_config->formatRegion($inserat[0]['id_region_printmedium_bit']),
                        $inserat[0]['print_page'],
                        $inserat[0]['print_date'],
                        $inserat[0]['size'],
                        $inserat[0]['pages'],
                        $inserat[0]['inserat_columns'],
                        $inserat[0]['size_width'],
                        $inserat[0]['size_height'],
                        $inserat[0]['price_inserat'],
                        $inserat[0]['uploader'],
                        $inserat[0]['upload_time'],
                        $inserat[0]['tagger'],
                        $inserat[0]['timestamp'],
                        $inserat[0]['source'],
                        $file_thumbnail,
                        $file_default,
                        );
                    fputcsv($fp, $line_csv, ';');
                    
                    if (!file_exists($path . 'inserate/' . $file_thumbnail))
                        copy($path_image . '/thumbnail/' . $file_thumbnail , $path . 'inserate/' . $file_thumbnail);
                }
            }
        }
        fclose($fp);
        
        $filter = new Zend_Filter_Compress(array('adapter' => 'zip', 'options' => array('archive' => $path_target, 'target' => 'inserate')));
        $newname = $filter->filter($path . '/inserate');
        
        return $newname;
    }
    
    /**
    * Replace characters of path string for using as filename
    *
    */
    protected function preparePath($path)
    {
        $replace = array(
                ' ' => '_',
                'Ä' => 'Ae',
                'Ö' => 'Oe',
                'Ü' => 'Ue',
                'ä' => 'ae',
                'ö' => 'oe',
                'ü' => 'ue',
                'ß' => 'ss',
        );
        
        $path_new = strtr($path, $replace);
        return $path_new;
    }
    
}
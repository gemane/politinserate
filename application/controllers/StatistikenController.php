<?php
/**
* Statistical presentation of data via diagramm and table.
*
* Presentation of data depending on
* - political parties
* - printmedia
* - region
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

class StatistikenController extends Zend_Controller_Action
{
    protected $year_from;
    protected $year_to;

    public function init()
    {
        $this->table_config = new Application_Model_Config();
        $this->table_medium = new Application_Model_Printmedium();
        $this->table_inserat = new Application_Model_Inserate();
        $this->cache = Zend_Registry::get('cache');
        
        $this->configuration = Zend_Registry::get('configuration');
        $this->year_from = $this->configuration->general->year;
        $this->year_to = date('Y', time());
        $this->view->year = $this->table_inserat->getYear();
        
        $this->statistics = new Application_Model_Statistics();
        
        $this->id_printmedium = $this->checkParam('medium');
        $this->id_region = $this->checkParam('region');
        $this->id_party = $this->checkParam('partei');
        
        $this->view->headScript()->appendScript('document.getElementById("nav_statistic").style.textDecoration = "underline";');
    }
    
    public function indexAction()
    {
        $this->_forward('parteien');
    }
    
    public function parteienAction()
    {
        $this->view->table = $this->statistics->getTableParty($this->view->year, $this->id_printmedium, 'all_payed', $this->id_region);
        
        $plot_data = $this->statistics->getPlotDataParty($this->view->year, $this->id_printmedium, $this->id_region);
        $this->setPlotDataParty($plot_data);
        
        $this->setForm('party');
    }
    
    public function medienAction()
    {
        $this->view->table = $this->statistics->getTableMedium($this->view->year, $this->id_party, 'all_payed', $this->id_region);
        
        $plot_data = $this->statistics->getPlotDataMedium($this->view->year, $this->id_party, 'all_payed', $this->id_region);
        $this->setPlotDataMedium($plot_data);
        
        $this->setForm('medium');
    }
    
    public function regionenAction()
    {
        $this->view->table = $this->statistics->getTableRegion($this->view->year, $this->id_printmedium, $this->id_party, 'all_payed');
        
        $plot_data = $this->statistics->getPlotDataRegion($this->view->year, $this->id_printmedium, $this->id_party, 'all_payed');
        $this->setPlotDataRegion($plot_data);
        
        $this->setForm('region');
    }
    
    protected function setForm($param)
    {
        $this->view->num_column = 5;
        
        $link = '';
        if (false != $this->view->printmedium)
            $link = '/medium/' . $this->id_printmedium;
        if (false != $this->view->party)
            $link = '/partei/' . $this->id_party;
        if (false != $this->view->region)
            $link = '/region/' . $this->id_region;
        
        $regions = $this->table_config->getAllRegion();
        foreach ($regions as $value) {
            $region[] = array(
                'link' => $link . '/region/' . $value['id_config'], 
                'image' => '/images/logo_government/logo_' . $value['region_abb'] . '.png', 
                'name' => $value['region']);
        }
        $this->view->form_region = $region;
        
        $parties = $this->table_config->getAllParty();
        foreach ($parties as $value) {
            $party[] = array(
                'link' => $link . '/partei/' . $value['id_config'], 
                'image' => '/images/logo_party/logo_' . strtolower($this->view->preparePath($value['party'])) . '.jpg', 
                'name' => $value['party']);
        }
        $this->view->form_party = $party;
        
        $printmedien = $this->table_medium->getAllMedium();
        foreach ($printmedien as $value) {
            $printmedium[] = array(
                'link' => $link . '/medium/' . $value['id_printmedium'], 
                'color' => '888888', // TODO1 $value['logo_printmedium'], 
                'name' => $value['printmedium']);
        }
        $this->view->form_printmedium = $printmedium;
        
        $this->view->form_return = $printmedium;
    }
    
    protected function setPlotDataParty($data)
    {
        $this->view->dataPaymentsParty  = $data['dataPaymentsParty'];
        $this->view->dataColorsParty    = $data['dataColorsParty'];
        $this->view->dataLegendParty    = $data['dataLegendParty'];
        $this->view->dataLabelParty     = $data['dataLabelParty'];
        
        $this->view->dataPaymentsGovernment = $data['dataPaymentsGovernment'];
        $this->view->dataColorsGovernment   = $data['dataColorsGovernment'];
        $this->view->dataLegendGovernment   = $data['dataLegendGovernment'];
        
        $this->view->yaxis = $data['yaxis'];
    }
    
    protected function setPlotDataMedium($data)
    {
        $this->view->dataPaymentsMedium = $data['dataPaymentsMedium'];
        $this->view->dataColorsMedium   = $data['dataColorsMedium'];
        $this->view->dataLegendMedium   = $data['dataLegendMedium'];
        $this->view->dataLabelMedium    = $data['dataLabelMedium'];
    }
    
    protected function setPlotDataRegion($data)
    {
        $this->view->dataPaymentsRegion = $data['dataPaymentsRegion'];
        $this->view->dataColorsRegion   = $data['dataColorsRegion'];
        $this->view->dataLegendRegion   = $data['dataLegendRegion'];
        $this->view->dataLabelRegion    = $data['dataLabelRegion'];
    }
    
    /**
    * Check parameter for existence in database
    *
    * @param $integer Name of parameter to check
    * @return $integer Result is either the value for the parameter or -1 for failure
    */
    protected function checkParam($param)
    {
        if ( $this->getRequest()->has($param) ) {
            $id = $this->getRequest()->getParam($param);
            
            switch ($param) {
                case 'medium': 
                    $result = $this->table_medium->checkPrintmedium($id);
                    $this->view->printmedium = ($result) ? $this->table_medium->getPrintmedium($id) : false;
                    break;
                case 'partei':
                    $result = $this->table_config->checkParty($id);
                    $this->view->party = ($result) ? $this->table_config->getParty($id) : false;
                    break;
                case 'region':
                    $result = $this->table_config->checkRegion($id);
                    $this->view->region = ($result) ? $this->table_config->getRegion($id) : false;
                    break;
                default :
                    $result = false;
                    break;
            }
            if ($result) {
                return $id;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }
    
}


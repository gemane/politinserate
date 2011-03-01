<?php
/**
* Controller for Ajax Calls
*
* Inserting new Field in Prinmedium Form
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

class AjaxController extends Zend_Controller_Action
{

    /**
    * Initialisation of class Ajax
    *
    * @see $table
    * @see $id_medium
    */
    public function init()
    {
    }
    
    /**
    * Ajax action that returns the dynamic form field
    */
    public function newfieldAction() 
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('newfield', 'html')
                    ->initContext();
        
        $id = $this->_getParam('id', null);
                
        $element = new Application_Form_Type($id);
        $element = $this->view->formFixer($element);
        
        $this->view->field = '<div id="newType' . $id . '">' . $element->__toString() . '</div>';
        
    }
    
    /**
    * Ajax action that returns the dynamic form field
    */
    public function updatefieldAction() 
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('updatefield', 'html')
                    ->initContext();
        
        $id = $this->_getParam('id', null);
        $id_printmedium_type = $this->_getParam('type', null);
        
        $element = new Application_Form_Type($id);
        
        $table_types = new Application_Model_PrintmediumTypes();
        $printmedium_types = $table_types->getPrintmediumType($id_printmedium_type, $id);
        $element->setDefaults($printmedium_types);
        
        $element = $this->view->formFixer($element);
        
        $this->view->field = '<div id="newType' . $id . '">' . $element->__toString() . '</div>';
        
    }
    
    /**
    * Ajax action that returns diagrams
    */
    public function plotmediumAction() 
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('plotmedium', 'html')
                    ->initContext();
        
        $id = $this->_getParam('id', null);
        $id_printmedium_type = $this->_getParam('type', null);
        
        $this->statistics = new Application_Model_Statistics();
        $plot_data_printmedium = $this->statistics->getPlotDataMedium(false, 0, 'all_payed', $id_region_printmedium);
        $this->setPlotDataMedium($plot_data_printmedium);
    }
    
}
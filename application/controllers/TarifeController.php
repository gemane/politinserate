<?php
/**
* Organising tariff data for printmedia.
*
* Uploading and downloading PDF-Files containing the tariff data for printmedia.
* Inserting, updating and deleting of tariff data for printmedia.
* Output table of all tariff data and datafiles.
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

include_once('Intern/Mobile/Controller.php');

class TarifeController extends Mobile_Controller_Action
{
    /**
     * Database class for Datafiles
     *
     * @var Zend_Db_Table
     */
    protected $table_datafile;
    
    /**
     * Database class for Tariff
     *
     * @var Zend_Db_Table
     */
    protected $table_tariff;
    
    /**
     * Database class for Weekdays belonging to Tariff
     *
     * @var Zend_Db_Table
     */
    protected $table_weekdays;
    
    /**
     * Array for replacing characters in file paths
     *
     * @var Array
     */
    protected $replace = array(
                ' ' => '_',
                'Ä' => 'Ae',
                'Ö' => 'Oe',
                'Ü' => 'Ue',
                'ä' => 'ae',
                'ö' => 'oe',
                'ü' => 'ue',
                'ß' => 'ss',
            );
    
    /**
    * Initialisation of class Tarife
    *
    * @see $table
    * @see $id_medium
    */
    public function init()
    {
        parent::init();
        
        $this->table_config = new Application_Model_Config();
        $this->table_medium = new Application_Model_Printmedium();
        if ( -1 == $this->view->id_medium = $this->checkParam('medium') ) 
            $this->view->id_medium = 1;
        
        $this->configuration = Zend_Registry::get('configuration');
        $this->auth = Zend_Registry::get('auth');
        
        $this->getCache();
        
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        
        if ($this->mobile)
            $this->_helper->getHelper('layout')->setLayoutPath(APPLICATION_PATH . '/layouts/mobile');
        
        $this->view->advertisments = $this->configuration->general->advertisments;
    }
    
    /**
    * Default action for Tariff.
    * Listing of all tariff and datafiles with 'download'-button.
    *
    * @see $table
    */
    public function indexAction()
    {   
        $this->view->auth = ( $this->auth->hasIdentity() ) ?  true : false;
        
        if ($this->configuration->general->csv)
            $this->view->csv = true;
        else
            $this->view->csv = false;
    }
    
    /**
    * Edit action for Tariff.
    * Listing of all tariff and datafiles with 'edit' and 'delete'-button.
    * Button for inserting of new tariff and datafile.
    *
    * @see $form
    * @see $table
    */
    public function editAction()
    {   
        // If not logged-in redirect to tarif page
        if (!$this->auth->hasIdentity())
            $this->_helper->redirector('index', 'tarife');
        
        if ($this->_flashMessenger->hasMessages())
            $this->view->message = end($this->_flashMessenger->getMessages());
    }
    
    /**
    * Form action for editing and inserting data for Tariff.
    * Listing of all tariff and datafiles.
    * Updating database for tariff (including weekdays) and datafile.
    *
    * @see $form
    * @see $table
    */
    public function formAction()
    {   
        // If not logged-in redirect to tarif page
        if (!$this->auth->hasIdentity())
            $this->_helper->redirector('index', 'tarife');
        
        $this->table_datafile = new Application_Model_Datafile();
        $this->table_tariff = new Application_Model_Tariff();
        $this->table_weekdays = new Application_Model_Weekdays();
        
        if ( $this->getRequest()->has('cancel') ) 
            $this->redirect('edit', false);
        
        // Check which parameters are set then decide for routine
        if ( -1 != $medium = $this->checkParam('medium') ) {
            if ( -1 != $datafile = $this->checkParam('file') ) {
                // TODO1 Überprüfen, ob id_datafile in dem Printmedium existiert
                if ( -1 != $tariff = $this->checkParam('tariff') ) {
                    $form = $this->editTariff($datafile, $tariff);
                } else {
                    $form = $this->editDatafile($medium, $datafile);
                }
            } else {
                $form = $this->editPrintmedium($medium);
            }
        }
        if ($this->_flashMessenger->hasMessages())
            $this->view->message = end($this->_flashMessenger->getMessages());
        $this->view->form = $form;
    }
    
    /**
    * Delete action for Tariff.
    * Deletion of tariff and datafile.
    * Rendering by 'edit'-view.
    *
    * @see $message
    * @see $tariff
    * @see $table
    */
    public function deleteAction()
    {
        // If not logged-in redirect to tarif page
        if (!$this->auth->hasIdentity())
            $this->_helper->redirector('index', 'tarife');
        
        $this->table_datafile = new Application_Model_Datafile();
        $this->table_tariff = new Application_Model_Tariff();
        $this->table_weekdays = new Application_Model_Weekdays();
    
        if ( -1 != $medium = $this->checkParam('medium') ) {
            if ( -1 != $datafile = $this->checkParam('file') ) {
                if ( -1 != $tariff = $this->checkParam('tariff') ) {
                    $form = $this->deleteTariff($tariff);
                    $this->redirect('edit', true, $id_printmedium = $this->view->id_medium);
                } else {
                    $form = $this->deleteDatafile($datafile);
                    $this->redirect('edit', true, $id_printmedium = $this->view->id_medium);
                }
            } else {
                $form = $this->deletePrintmedium($medium);
                $this->redirect('edit', true, $id_printmedium = 1);
            }
        } else {
            $this->redirect('index');
        }
    }
    
    /**
    * Download action for Tariff.
    * Download of datafiles to local computer.
    *
    * @see $message
    */
    public function downloadAction()
    {
        $url = $this->getRequest()->REQUEST_URI;
        $url = str_replace('/download/', '/', '../data' . $url);
        
        $this->download($url, null, 'application/pdf');
        $this->_helper->flashMessenger->addMessage("Download erfolgreich.");
    }
    
    /**
    * Export CSV action for Tariff.
    * Export tariff table as CSV.
    *
    * @see $message
    */
    public function exportcsvAction()
    {
        $path = APPLICATION_PATH . '/../data/downloads/' . 'tarife_printmedien.csv';
        $this->download($path, null, 'text/csv');
        $this->view->message = "Export erfolgreich.";
    }
    
    
    /**
    * Update of Database for new Version
    *
    */
    public function changefjhwcAction()
    {
        $this->auth = Zend_Registry::get('auth');
        if ($this->auth->hasIdentity())
            if ('admin' == $this->auth->getIdentity()->username) {
                //$this->table_tariff->updateDBTypeName();
                $this->table_weekdays = new Application_Model_Weekdays();
                $this->table_weekdays->updateDBexistingWeekdays();
            }
    }
    
    /**
    * Update cache and redirect action
    *
    * @param $string Value of action
    */
    protected function redirect($action, $cache = true, $id_printmedium)
    {
        if (!isset($id_printmedium))
            $id_printmedium = $this->view->id_medium;
            
        if ($cache)
            $cache = $this->setCache();
    
        $this->_helper->redirector($action, 'tarife', null,
                            array('medium' => $id_printmedium));
    }
    
    /**
    * Get cache for tariff table
    *
    * @see $table
    * @see tooltips
    */
    protected function getCache()
    {
        $this->cache = Zend_Registry::get('cache');
        if(!$this->configuration->general->cache || !$cache = $this->cache->load('table_tariff'))
            $cache = $this->setCache();
        
        $this->view->table = $cache['table'];
        $this->view->tooltips = $cache['tooltips'];
    }
    
    /**
    * Save cache of tariff table
    *
    * @return cache
    */
    protected function setCache()
    {
        $cache['table'] = $this->setTable();
        $cache['tooltips'] = $this->tooltip();
        $this->cache->save($cache, 'table_tariff');
        
        return $cache;
    }
    
    /**
    * Editing of tariff data.
    * Setting default values for tariff form with database values.
    * Updating tariff and weekdays databases.
    * Validating of form values.
    *
    * @see $message
    * @return Zend_Form This Zend_Form object for tariff
    */
    protected function editTariff($id_datafile, $id_tariff)
    {
        $form = new Application_Form_Sizes($this->view->id_medium);
        $form->setAction('/tarife/form/medium/' . $this->view->id_medium . '/file/' . $id_datafile . '/tariff/' . $id_tariff);
        if (0 < $id_tariff) {
            if ($this->getRequest()->isPost())
                $result = $this->updateTariff($form, $id_datafile, $id_tariff);
            $form = $this->setEditFormTariff($form, $id_datafile, $id_tariff);
            $form->getMessages();
            $this->view->titel = 'Tarif editieren';
        } else if (0 == $id_tariff) {
            if ($this->getRequest()->isPost())
                $result = $this->insertTariff($id_datafile, $form);
            $form = $this->setNewFormTariff($form, $id_datafile);
            $form->getMessages();
            $this->view->titel = 'Tarif hinzufügen';
        }
        
        return $form;
    }
    
    /**
    * Editing of datafile data.
    * Setting default values for datafile form with database values.
    * Updating datafile database.
    * Validating of form values.
    *
    * @see $message
    * @return Zend_Form This Zend_Form object for datafile
    */
    protected function editDatafile($id_printmedium, $id_datafile)
    {
        $form = new Application_Form_Datafiles($id_datafile);
        $form->setAction('/tarife/form/medium/' . $this->view->id_medium . '/file/' . $id_datafile);
        if (0 < $id_datafile) {
            if ($this->getRequest()->isPost())
                $result = $this->updateDatafile($form, $id_datafile);
            $form = $this->setEditFormDatafile($form, $id_datafile);
            $form->getMessages();
            $this->view->titel = 'Tarifdatei editieren';
        } else if (0 == $id_datafile) {
            if ($this->getRequest()->isPost())
                $result = $this->insertDatafile($form);
            $form = $this->setNewFormDatafile($form, $id_printmedium);
            $form->getMessages();
            $this->view->titel = 'Tarifdatei hinzufügen';
        }
        
        return $form;
    }
    
    /**
    * Editing of config data.
    * Setting default values for printmedium form with database values.
    * Updating config database.
    * Validating of form values.
    *
    * @see $message
    * @return Zend_Form This Zend_Form object for datafile
    */
    protected function editPrintmedium($id_printmedium)
    {
        $form = new Application_Form_Printmedium();
        $form->setAction('/tarife/form/medium/' . $this->view->id_medium);
        if (0 < $id_printmedium) {
            if ($this->getRequest()->isPost())
                $result = $this->updatePrintmedium($form, $id_printmedium);
            $form = $this->setEditFormPrintmedium($form, $id_printmedium);
            $form->getMessages();
            $this->view->titel = 'Printmedium editieren';
        } else if (0 == $id_printmedium) {
            if ($this->getRequest()->isPost())
                $result = $this->insertPrintmedium($form);
            $form = $this->setNewFormPrintmedium($form);
            $form->getMessages();
            $this->view->titel = 'Printmedium hinzufügen';
        }
        
        return $form;
    }
    
    /**
    * Setting values for tariff form (including weekdays) with database data.
    *
    * @param integer $id_size Index of each tariff from printmedium in database
    * @see $id_size
    * @return Zend_Form This Zend_Form object for tariff
    */
    protected function setEditFormTariff($form, $id_datafile, $id_size)
    {
        $size = $this->table_tariff->getSize($id_size);
        $form->setDefaults($size);
        
        $week = $this->table_weekdays->getWeekdays($size['id_weekdays']);
        $form->setDefaults($week);
        
        $form->config_tariff->setValue($id_size);
        
        $this->setDownloadPath($id_datafile);
        
        return $form;
    }
    
    /**
    * Setting default values for datafile form with database data.
    *
    * @param integer $id_datafile Index of each datafile in database
    * @return Zend_Form This Zend_Form object for datafile
    */
    protected function setEditFormDatafile($form, $id_datafile)
    {
        $datafile = $this->table_datafile->getDatafile($id_datafile);
        $form->setDefaults($datafile);
        
        $form->config_datafile->setValue($id_datafile);
        
        $this->setDownloadPath($id_datafile);
        
        return $form;
    }
    
    /**
    * Setting default values for printmedium form with database data.
    *
    * @param integer $datafile Index of each datafile in database
    * @return Zend_Form This Zend_Form object for datafile
    */
    protected function setEditFormPrintmedium($form, $id_printmedium)
    {
        $table_types = new Application_Model_PrintmediumTypes();
        
        $types_exist = false;
        $types = 'var printmedium_types = new Array(';
        foreach ($table_types->getTypesByPrintmedium($id_printmedium) as $value)
            if (0 != $value['printmedium_type_position']) {
                $types .= '"' . $value['id_printmedium_type'] . '", ';
                $types_exist = true;
            }
        
        if ($types_exist) 
            $types = ($types_exist) ? substr($types, 0, -2) : $types;
        $types .= ');' . "\n";
        $this->view->types = $types;
        
        $printmedium = $this->table_medium->getRowPrintmedium($id_printmedium);
        $form->setDefaults($printmedium);
        
        $table_types = new Application_Model_PrintmediumTypes();
        $printmedium_types = $table_types->getTypesByPrintmedium($id_printmedium, -1);
        $form->setDefaults($printmedium_types[0]);
        
        $form->config_printmedium->setValue($id_printmedium);
        
        return $form;
    }
    
    /**
    * Setting default values for tariff form.
    *
    * @param integer $datafile Index of datafile in database where new tariff belongs
    * @return Zend_Form This Zend_Form object for tariff
    */
    protected function setNewFormTariff($form, $id_datafile)
    {
        $year = date('Y', time());
        $data = array(
            'id_datafile'           => $id_datafile,
            'date_from'             => $year . '-01-01',
            'date_to'               => $year . '-12-31',
            'tariff'                => 0);

        $form->setDefaults($data);
        
        $this->setDownloadPath($id_datafile);
        
        return $form;
    }
    
    /**
    * Setting default values for datafile form.
    *
    * @return Zend_Form This Zend_Form object for datafile
    */
    protected function setNewFormDatafile($form, $id_printmedium)
    {
        $data = array(
            'id_printmedium'        => $id_printmedium,
            'year'                  => date('Y', time()),
            'file'                  => 0,
            'date_from'             => date('Y', time()) . '-01-01',
            'date_to'               => date('Y', time()) . '-12-31');
        $form->setDefaults($data);
        
        return $form;
    }
    
    /**
    * Setting default values for printmedium form.
    *
    * @return Zend_Form This Zend_Form object for datafile
    */
    protected function setNewFormPrintmedium($form)
    {
        $types = 'var printmedium_types = new Array();' . "\n";
        $this->view->types = $types;
    
        return $form;
    }
    
    /**
    * Inserting values into database from tariff form.
    *
    * @param Zend_Form This Zend_Form object for tariff
    */
    protected function insertTariff($id_datafile, $form)
    {   
        if ($this->getRequest()->has('config_tariff') && $form->isValid($_POST) ) {
            $values = $form->getValues();
            if ('' != $values['size'] && '' != $values['price'] && '' != $values['size_height']) {
                if (!empty($values['id_height_image'])) {
                    foreach ($this->table_weekdays->getArray() as $key) {
                        if (1 == $values[$key]) {
                            
                            $values['id_printmedium'] = $this->view->id_medium;
                            $values['id_datafile'] = $id_datafile;
                            $values['id_weekdays'] = $this->table_weekdays->insertWeekdays($values);
                            
                            $this->user_table = new Application_Model_Users();
                            $username = $this->auth->getIdentity()->username;
                            $values['id_user'] = $this->user_table->getUserId($username);
                            
                            $this->table_tariff->insertTariff($values);
                            
                            $this->_helper->flashMessenger->addMessage("Tarif wurde eingetragen.");
                            
                            $this->redirect('edit');
                        }
                    }
                    $this->view->message = "Bitte mindestens einen Wochentag auswählen.";
                } else {
                    $this->view->message = "Bitte ein Format auswählen.";
                }
            } else {
                $this->view->message = "Bitte Felder ausfüllen.";
            }
        } else {
            $this->checkToken($form);
        }
        
        return false;
    }
    
    /**
    * Inserting values into database from datafile form and uploading tariff-datafile.
    * Creating new folder for tariff-datafile if not existing.
    *
    * @param Zend_Form This Zend_Form object for datafile
    * @see message
    */
    protected function insertDatafile($form)
    {
        if ($this->getRequest()->has('config_datafile') && $form->isValid($_POST) ) {
            $values = $form->getValues();
            
            // Check if at least one region was selected
            $region_set = false;
            foreach ($this->table_config->getAllRegion() as $region ) {
                if (1 == $values[$region['region_abb']])
                    $region_set = true;
            }
            
            if (0 < $values['id_printmedium'] && '' != $values['path'] && $region_set) {
                if (!$this->table_datafile->checkRow($values) ) {
                    $id_printmedium = $values['id_printmedium'];
                    $year = $values['year'];
                    $path = $values['path'];
                    
                    $printmedium = $this->table_medium->getPrintmedium($id_printmedium);
                    
                    // Check if path exists, if not then create path
                    $path_to = APPLICATION_PATH .'/../data/tarife/' . $year . '/' . strtr($printmedium, $this->replace) . '/';
                    if (!is_dir($path_to)) {
                        $old = umask(0);
                        mkdir($path_to, 0777, true);
                        umask($old); 
                    }
                    $form->path->setDestination($path_to);
                    
                    // Upload datafile and insert into database
                    if ($form->path->receive()) {
                        $this->table_datafile->insertDatafile($values);
                        // TODO1 Überprüfen, ob Wochentag bereits ausgewählt wurde
                        $this->_helper->flashMessenger->addMessage("Datenfile wurde eingetragen.");
                        $this->redirect('edit');
                    } else {
                        $this->view->message = "Fehler beim Empfangen der Datei.";
                    }
                } else {
                    $this->view->message = "Datei existiert bereits.";
                }
            } else {
                $this->view->message = "Bitte Felder ausfüllen.";
            }
        } else {
            //$this->checkToken($form);
        }
        
        return false;
    }
    
    /**
    * Inserting values into database from tariff form.
    *
    * @param Zend_Form This Zend_Form object for tariff
    * @see $message
    */
    protected function insertPrintmedium($form)
    {   
        // Form has been submitted - run data through preValidation()
        $form->preValidation($_POST);
        
        if ($this->getRequest()->has('config_printmedium') && $form->isValid($_POST) ) {
            $values = $form->getValues();
            if ('' != $values['printmedium'] && 0 < $values['printmedium_columns_width'] && 0 < $values['printmedium_width'] && 0 < $values['printmedium_height']) {
                
                $id_medium = $this->table_medium->insertPrintmedium($values);
                
                $this->_helper->flashMessenger->addMessage("Printmedium wurde eingetragen.");
                $this->redirect('edit');
            } else {
                $this->view->message = "Bitte alle Felder ausfüllen.";
            }   
        } else {
            $this->checkToken($form);
        }
        
        return false;
    }
    
    /**
    * Updating values in database from tariff form (including weekdays).
    *
    * @param Zend_Form This Zend_Form object for tariff
    * @param integer Index from tariff-database
    * @see $message
    */
    protected function updateTariff($form, $id_datafile, $id_size)
    {   
        if ($this->getRequest()->has('config_tariff') && $form->isValid($_POST) ) {
            $values = $form->getValues();
            if ('' != $values['size'] && '' != $values['price'] && '' != $values['size_height']) {
                if (!empty($values['id_height_image'])) {
                    foreach ($this->table_weekdays->getArray() as $key) {
                        if (1 == $values[$key]) {
                            
                            $values['id_printmedium'] = $this->view->id_medium;
                            $values['id_datafile'] = $id_datafile;
                            $values['id_weekdays'] = $this->table_tariff->getId_weekdays($id_size);
                            
                            $this->user_table = new Application_Model_Users();
                            $username = $this->auth->getIdentity()->username;
                            $values['id_user'] = $this->user_table->getUserId($username);
                            
                            $this->table_tariff->updateTariff($id_size, $values);
                            
                            $this->table_weekdays->updateWeekdays($values['id_weekdays'], $values);
                            
                            $this->_helper->flashMessenger->addMessage("Tarif wurde überschrieben.");
                            $this->redirect('edit');
                        }
                    }
                    $this->view->message = "Bitte mindestens einen Wochentag auswählen.";
                } else {
                    $this->view->message = "Bitte ein Format auswählen.";
                }
            } else {
                $this->view->message = "Bitte alle Felder ausfüllen.";
            }   
        } else {
            $this->checkToken($form);
        }
        
        return false;
    }
    
    /**
    * Updating values in database from datafile.
    *
    * @param Zend_Form This Zend_Form object for datafile
    * @param integer Index from datafile-database
    * @see $message
    */
    protected function updateDatafile($form, $id_datafile)
    {
        if ($this->getRequest()->has('config_datafile') && $form->isValid($_POST) ) {
            $values = $form->getValues();
            
            $region_set = false;
            foreach ($this->table_config->getAllRegion() as $region ) {
                if (1 == $values[$region['region_abb']])
                    $region_set = true;
            }
            
            if (0 < $values['id_printmedium'] && $region_set) {
                $this->table_datafile->updateDatafile($id_datafile, $values);
                
                $this->_helper->flashMessenger->addMessage("Datenfile wurde überschrieben.");
                $this->redirect('edit');
            } else {
                $this->view->message = "Bitte alle Felder ausfüllen.";
            }   
        } else {
            $this->checkToken($form);
        }
        
        return false;
    }
    
    /**
    * Updating values in database from printmedium.
    *
    * @param Zend_Form This Zend_Form object for config
    * @param integer Index from printmedium database
    * @see $message
    */
    protected function updatePrintmedium($form, $id_medium)
    {
        // Form has been submitted - run data through preValidation()
        $form->preValidation($_POST);
        
        if ($this->getRequest()->has('config_printmedium') && $form->isValid($_POST) ) {
            $values = $form->getValues();
            if ('' != $values['printmedium'] && 0 < $values['printmedium_columns_width'] && 0 < $values['printmedium_width'] && 0 < $values['printmedium_height']) {
                
                $x = $this->table_medium->updatePrintmedium($id_medium, $values);
                
                $this->_helper->flashMessenger->addMessage("Printmedium wurde überschrieben.");
                $this->redirect('edit');
            } else {
                $this->view->message = "Bitte alle Felder ausfüllen.";
            }   
        } else {
            $this->checkToken($form);
        }
        
        return false;
    }
    
    /**
    * Deleting row in database from tariff(including weekdays).
    *
    * @param integer Index from tariff-database
    * @see $message
    */
    protected function deleteTariff($id_size)
    {
        $this->table_inserat = new Application_Model_Inserate();
        if (0 == $this->table_inserat->getNumInserate($id_size)) {
            $id_weekdays = $this->table_tariff->getId_weekdays($id_size);
            
            $this->table_tariff->deleteTariff($id_size);
            $this->table_weekdays->deleteWeekdays($id_weekdays);
            $this->_helper->flashMessenger->addMessage("Tarif wurde gelöscht.");
        }
    }
    
    /**
    * Deleting row in database from datafile.
    *
    * @param integer Index from datafile-database
    * @see $message
    */
    protected function deleteDatafile($id_datafile)
    {
        if (0 == $this->table_tariff->getNumTariff($id_datafile)) {
            $values = $this->table_datafile->getDatafile($id_datafile);
            $path_to = APPLICATION_PATH .'/../data/tarife/' . $values['year'] . '/' . strtr($values['printmedium'], $this->replace) . '/';
            unlink($path_to . $values['path']);
            $this->table_datafile->deleteDatafile($id_datafile);
            
            $this->_helper->flashMessenger->addMessage("Datenfile wurde gelöscht.");
        }
    }
    
    /**
    * Deleting printmedium
    *
    * @param integer Index from config-database
    */
    protected function deletePrintmedium($id_printmedium)
    {
        if (0 == $this->table_datafile->getNumDatafile($id_printmedium)) {
            $this->table_medium->deletePrintmedium($id_printmedium);
            $this->_helper->flashMessenger->addMessage("Printmedium wurde gelöscht.");
        }
    }
    
    /**
    * Retrieving data from database for view (partial).
    *
    * @return array Data of all datafiles and tariffs.
    */
    protected function setTable()
    {
        $this->table_datafile = new Application_Model_Datafile();
        $this->table_tariff = new Application_Model_Tariff();
        $table_types = new Application_Model_PrintmediumTypes();
    
        $AllMedium = $this->table_medium->getAllMedium();
        
        foreach ($AllMedium as $printmedium) {
            $id_printmedium = $printmedium['id_printmedium'];
            
            $AllTypes[$id_printmedium] = $table_types->getTypesByPrintmedium($id_printmedium);
            
            $AllDatafiles[$id_printmedium] = $this->table_datafile->getAllByIDMedium($id_printmedium);
            
            $TagDelete[$id_printmedium][0] = false;
            if (0 == $this->table_datafile->getNumDatafile($id_printmedium)) {
                $TagDelete[$id_printmedium][0] = true;
            }
            
            $sizes = $this->table_tariff->getSizesByPrintmedium($id_printmedium);
            
            foreach ($sizes as $values) {
                $table[$id_printmedium][$values['id_datafile']][] = $values;
                
                $TagDelete[$values['id_datafile']] = true;
            }
            
        };
        
        if (empty($table)) {
            return array();
        } else {
            $table['types'] = $AllTypes;
            $table['datafiles'] = $AllDatafiles;
            $table['printmedien'] = $AllMedium;
            $table['delete'] = $TagDelete;
            
            if ($this->configuration->general->csv)
                $lockfile = APPLICATION_PATH . '/../temp/lock_csv_tariff';
                if (86400 < (time() - filemtime($lockfile))) {
                    $this->exportcsv($table);
                    $locktime = mktime($this->configuration->general->csv_time, 0, 0);
                    touch($lockfile, $locktime);
                }
            
            return $table;
        }
     }
     
    /**
    * Get tooltip with image sizes
    *
    * @return $string Tooltip array with images
    */
     protected function tooltip()
     {
        $tooltips = 'var tooltips=[];' . "\n";
        foreach ($this->table_config->getAllHeights() as $key)
            $tooltips .= 'tooltips[' . $key['id_config'] . ']=["/images/heights/' . $key['height_image'] . '.jpg"];';
        return $tooltips . "\n";
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
            
            if (0 == $id)
                return 0;
            
            switch ($param) {
                case 'medium': 
                    $result = $this->table_medium->checkPrintmedium($id);
                    break;
                case 'file':
                    $result = $this->table_datafile->checkDatafile($id);
                    break;
                case 'tariff':
                    $result = $this->table_tariff->checkTariff($id);
                    break;
                default :
                    $result = false;
                    break;
            }
            if ($result) {
                return $id;
            } else {
                $this->_helper->flashMessenger->addMessage("Parameter existiert nicht.");
                $this->_helper->redirector('index', 'tarife', null, array('medium' => 1));
            }
        } else {
            return -1;
        }
    }
    
    /**
    * Checking token of form if valid
    *
    * @param Zend_Form $form 
    * @return forward
    */
    protected function checkToken($form)
    {
        if ($this->_request->isPost() && !$form->isValid($_POST))
            if (count($form->getErrors('token')) > 0)
                return $this->_forward('csrf', 'error');
        
        return true;
    }
    
    /**
    * Set download path for tariff datafile
    *
    * @param $integer datafile id
    * @see downloadDatafile 
    * @see year
    * @see printmedium
    * @see path
    */
    protected function setDownloadPath($id_datafile)
    {
        $datafile = $this->table_datafile->getDatafile($id_datafile);
        $this->view->downloadDatafile = true;
        $this->view->year = $datafile['year'];
        $this->view->printmedium = $this->table_medium->getPrintmedium($datafile['id_printmedium']);
        $this->view->path = $datafile['path'];
    }
    
    /**
    * Create CSV datafile of tarif data
    *
    * @param $array table of tariff
    */
    protected function exportcsv($table)
    {
        $this->table_weekdays = new Application_Model_Weekdays();
        
        $path = APPLICATION_PATH . '/../data/downloads/' . 'tarife_printmedien.csv';
        $fp = fopen($path, 'w');
        $line_csv = array(
            'Printmedium', 
            'Region',
            'Format',
            'Bezeichnung',
            'Inserat Höhe',
            'Printmedium Breite',
            'Titelblatt',
            'Gültig von',
            'Gültig bis',
            'Wochentage',
            'Tarif [EUR]'
            );
        fputcsv($fp, $line_csv, ';');
        
        foreach ($table['printmedien'] as $medium) {
            foreach ($table['datafiles'][$medium['id_printmedium']] as $datafile) {
                if (!empty($table[$datafile['id_printmedium']][$datafile['id_datafile']])) {
                    foreach ($table[$datafile['id_printmedium']][$datafile['id_datafile']] as $size) {
                        $line_csv = array(
                            $size['printmedium'],
                            $size['region_printmedium_bit'],
                            $size['printmedium_type_name'],
                            $size['size'],
                            $size['size_height'],
                            $size['printmedium_width'],
                            (1 == $size['cover']) ? 'Ja' : 'Nein',
                            $datafile['date_from'],
                            $datafile['date_to'],
                            $size['weekdays'],
                            $size['price']
                            );
                        fputcsv($fp, $line_csv, ';');
                    }
                }
            }
        }
        fclose($fp);
    }
    
    /**
    * Datafile download
    *
    * @throws Exception
    * @param string $path Pfad zu der Datei
    * @param string $fileName Alternativer Dateiname für den Download
    * @param string $contentType Download Content-Type
    */
    protected function download($path, $fileName = null, $contentType = 'application/octet-stream') 
    {
        if (!file_exists($path)) {
            throw new Exception('Datei nicht gefunden. Pfad: ' . $path);
        }
        
        if (is_null($fileName)) {
            $fileName = basename($path);
        }
        
        $fileSize = filesize($path);
        $disposition  = !strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 5.5') ? 'attachment; ' : ''
                    . 'filename=' . $fileName;
        
        $response = $this->_response;
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout()->disableLayout();
        
        $response->clearAllHeaders();
        
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true) 
                    ->setHeader('Pragma', 'no-cache', true)
                    ->setHeader('Content-Description', $fileName, true)
                    ->setHeader('Content-Type', $contentType, true)
                    ->setHeader('Content-Transfer-Encoding', 'binary', true)
                    ->setHeader('Content-Disposition', 'attachment; filename="'.$fileName.'"; size: '.$fileSize, true);
        $response->sendHeaders();
        
        $response->headersSentThrowsException = false;
        @ob_clean(); 
        @ob_flush();
        $bytes = @readfile($path);
        die(); 
    }
    
}

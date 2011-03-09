<?php
/**
* Input controller for new advertisments.
*
* Upload of new images.
* Input form for printmedia and political parties for categorisation.
* Check of data.
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
include_once('Intern/Mobile/Controller.php');

class EingabeController extends Mobile_Controller_Action
{
    public function init()
    {
        parent::init();
        
        $this->table_inserat = new Application_Model_Inserate();
        $this->ip_table = new Application_Model_Ips();
        $this->image = new Image_Editing();
        
        if ( $this->getRequest()->has('id') ) {
            $id_inserat = $this->getRequest()->getParam('id');
            
            if (false == $this->table_inserat->checkInserat($id_inserat))
                $this->_helper->redirector('index', 'index');
            
            if (-1 == $this->table_inserat->getTagged($id_inserat)) {
                $this->table_inserat->moveUntagged($id_inserat);
                $this->cache = Zend_Registry::get('cache');
                $this->cache->remove('table_stream_trash');
                $this->cache->remove('table_stream_untagged');
                $expenses_file = APPLICATION_PATH . '/../data/cache/expenses.txt';
                unlink($expenses_file);
            }
            
            if ( $this->getRequest()->has('trash') ) {
                // TODO2 Kommentar hinzufügen -> Warum in den Mülleimer?
                $this->table_inserat->moveTrash($id_inserat);
                $this->cache = Zend_Registry::get('cache');
                $this->cache->remove('table_stream_tagged');
                $this->cache->remove('table_stream_untagged');
                $this->cache->remove('table_stream_trash');
                $expenses_file = APPLICATION_PATH . '/../data/cache/expenses.txt';
                unlink($expenses_file);
                
                $this->_helper->redirector('trash', 'stream');
            }
        }
        
        $this->auth = Zend_Registry::get('auth');
        $this->configuration = Zend_Registry::get('configuration');
        $this->cache = Zend_Registry::get('cache');
        
        $this->view->headScript()->appendScript('document.getElementById("nav_input").style.textDecoration = "underline";');

        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        
        if ($this->mobile)
            $this->_helper->getHelper('layout')->setLayoutPath(APPLICATION_PATH . '/layouts/mobile');
    }
    
    public function indexAction()
    {
        $this->_forward('foto');
    }
    
    public function fotoAction()
    {
        $max_uploads = $this->maxUploads();
        
        if ( !$this->getRequest()->has('cancel') ) {
            if (true == $max_uploads) {
                if ($this->getRequest()->has('id_inserat_front'))
                    $form = new Application_Form_Upload();
                else
                    $form = new Application_Form_Photo();
                
                $form->setAction('/eingabe/foto');
                if ( $this->getRequest()->has('id_inserat') ||  $this->getRequest()->has('id_inserat_front')) {
                    if ($form->isValid($_POST)) {
                            $id_inserat = $this->uploadPhoto($form);
                            $this->redirect('medium', $id_inserat);
                    } else {
                        $this->checkToken($form);
                        $this->view->message = 'Fehler beim hochladen.';
                        $form->getMessages();
                        $this->view->form = $form;
                    }
                } else {
                    $this->view->form = $this->setFormPhoto($form);
                }
            }
        } else {
            $this->_helper->redirector('index', 'index');
        }
        $id_inserat = $this->getRequest()->getParam('id');
        
        $this->setNavigation('photo', $id_inserat);
    }
    
    public function restAction()
    {
      $this->_helper->layout->disableLayout();
      
      $server = new Zend_Json_Server();
      $server->setClass('AppService');
      
      $server->handle();
    }
    
    public function mediumAction()
    {
        $max_entries = $this->maxEntries();
        
        $id_inserat = $this->getRequest()->getParam('id');
        if (1 != $this->table_inserat->getTagged($id_inserat) || $this->checkAdmin()) {
            $this->view->tagged = false;
            if (true == $max_entries) {
                if ( $this->getRequest()->has('id_inserat') ) {
                    $form = new Application_Form_Medium();
                    $form->setAction('/eingabe/medium/id/' . $id_inserat);
                    if ($form->isValid($_POST)) {
                        $values = $form->getValues();
                        if (time() > strtotime($values['print_date'])) {
                            $this->table_inserat->updateMedium($id_inserat, $values);
                            $this->resetCache();
                            if (!$this->auth->hasIdentity())
                                $this->ip_table->insertIP('entry');
                            $this->redirect('format', $id_inserat);
                        } else {
                            $this->view->message = 'Datum liegt in der Zukunft.';
                            $this->view->form = $form;
                        }
                    } else {
                        $this->checkToken($form);
                        $this->view->message = 'Daten sind nicht korrekt.';
                        $form->getMessages();
                        $this->view->form = $form;
                    }
                } else {
                    $this->view->form = $this->setFormMedium();
                }
                
                $this->parameterImages($id_inserat);
            }
        } else {
            $this->view->tagged = true;
        }
        
        $this->view->auth = ( $this->auth->hasIdentity() ) ?  true : false;
        $this->setNavigation('medium', $id_inserat);
    }
    
    public function formatAction()
    {
        $max_entries = $this->maxEntries();
        $id_inserat = $this->getRequest()->getParam('id');
        
        if (!$cover = $this->table_inserat->getCover($id_inserat))
            $cover = 0;
        
        if (1 != $this->table_inserat->getTagged($id_inserat) || $this->checkAdmin()) {
            $this->view->tagged = false;
            if (true == $max_entries) {
                if ( $this->getRequest()->has('id_inserat') &&  !$this->getRequest()->has('yes_cover') && !$this->getRequest()->has('no_cover')) {
                    $cover = $this->getRequest()->getParam('cover');
                    $form = new Application_Form_Format($id_inserat, $cover);
                    $form->setAction('/eingabe/format/id/' . $id_inserat);
                    if ($form->isValid($_POST)) {
                        $values = $form->getValues();
                        $table_types =  new Application_Model_PrintmediumTypes();
                        $this->table_inserat->updateFormat($id_inserat, $values);
                        $this->resetCache();
                        if (!$this->auth->hasIdentity())
                            $this->ip_table->insertIP('entry');
                        $this->redirect('partei', $id_inserat);
                    } else {
                        $this->checkToken($form);
                        $this->view->message = 'Daten sind nicht korrekt.';
                        $form->getMessages();
                        $this->view->form = $form;
                    }
                } else {
                    if ($this->getRequest()->has('yes_cover'))
                        $cover = 1;
                    else if ($this->getRequest()->has('no_cover'))
                        $cover = 0;
                    $this->view->form = $this->setFormFormat($cover); 
                    $values = $this->view->form->getValues();
                    if ($values['format_empty'] == 'true') {
                        $this->view->nosizes = 'Keine Formate für die angegebenen Parameter vorhanden. ' . '<br /><br />'. 'Bitte die <a href="/eingabe/medium/id/' . $id_inserat . '">Daten</a> überprüfen oder' . '<br />'. ' fehlende <a target="_blank"  href="/tarife/edit">Tarife</a> neu eintragen.' . '<br /><br />';
                        if (!$this->auth->hasIdentity())
                            $this->view->nosizes .=  '(Beachte: Sie müssen <a href="/user/login">eingelogged</a> sein, <br />um Tarife zu editieren.)<br />';
                        $this->view->nosizes .= '<br />';
                    }
                }
                
                $this->view->tooltips = $this->tooltip($cover);
                $this->parameterImages($id_inserat);
            }
        } else {
            $this->view->tagged = true;
        }
        $this->view->id_printmedium = $this->table_inserat->getID_Printmedium($id_inserat);
        $this->view->auth = ( $this->auth->hasIdentity() ) ?  true : false;
        $this->setNavigation('format', $id_inserat);
    }
    
    public function parteiAction()
    {
        $max_entries = $this->maxEntries();
        
        $id_inserat = $this->getRequest()->getParam('id');
        if (1 != $this->table_inserat->getTagged($id_inserat) || $this->checkAdmin()) {
            $this->view->tagged = false;
            if (true == $max_entries) {
                if ( $this->getRequest()->has('id_inserat') ) {
                    $form = new Application_Form_Party();
                    $form->setAction('/eingabe/partei/id/' . $id_inserat);
                    if ($form->isValid($_POST)) {
                        $values = $form->getValues();
                        $this->table_inserat->updateParty($id_inserat, $values);
                        $this->resetCache();
                        if (!$this->auth->hasIdentity())
                            $this->ip_table->insertIP('entry');
                        $this->redirect('kontrolle', $id_inserat);
                    } else {
                        $this->checkToken($form);
                        $this->view->message = 'Daten sind nicht korrekt.';
                        $form->getMessages();
                        $this->view->form = $form;
                    }
                } else {
                    $this->view->form = $this->setFormParty();
                }
                
                $this->parameterImages($id_inserat);
            }
        } else {
            $this->view->tagged = true;
        }
        $this->setNavigation('party', $id_inserat);
    }
    
    protected function checkAdmin()
    {
        $this->auth = Zend_Registry::get('auth');
        if ($this->auth->hasIdentity())
            if ('admin' == $this->auth->getIdentity()->username)
                return true;
        
        return false;
    }
    
    protected function resetCache()
    {
        $this->cache->remove('tagged_table_front');
        $this->cache->remove('table_medium');
        $this->cache->remove('table_party');
        $this->cache->remove('table_stream_tagged');
        $this->cache->remove('table_stream_untagged');
    }

    public function kontrolleAction()
    {
        $id_inserat = $this->getRequest()->getParam('id');
        if (1 != $this->table_inserat->getTagged($id_inserat)) {
            $this->view->tagged = false;
            if (true == $max_entries = $this->maxEntries()) {
                if ( !$this->getRequest()->has('cancel')) {
                    if ( $this->getRequest()->has('id_inserat')) {
                        if ($this->getRequest()->has('nonidentical'))
                            $form = new Application_Form_Trash($id_inserat);
                        else
                            $form = new Application_Form_Check($id_inserat);
                        
                        $form->setAction('/eingabe/kontrolle/id/' . $id_inserat);
                        if ($form->isValid($_POST)) {
                            if (2 == $this->table_inserat->getIDSource($id_inserat) ) {
                                $image_destination_path = APPLICATION_PATH .'/../data/uploads/images/original/';
                                $image_destination = $image_destination_path. 'inserat_'. sprintf('%06d', $id_inserat) . '_o.jpg'; // o...Original
                                if (!file_exists($image_destination)) {
                                    $image_url = $this->table_inserat->getImageUrl($id_inserat);
                                    require_once('Intern/Service/Twitter.php');
                                    $twitter = new Service_Twitter();
                                    $image_source = $twitter->getTwitterImage($image_url);
                                    $file_copied = copy($image_source[0]['image_remote'], $image_destination);
                                } else {
                                    $file_copied = true;
                                }
                            } else {
                                $file_copied = true;
                            }
                            $values = $form->getValues();
                            if ($this->auth->hasIdentity()) {
                                $this->user_table = new Application_Model_Users();
                                $username = $this->auth->getIdentity()->username;
                                $id_tagger = $this->user_table->getUserId($username);
                            } else {
                                $id_tagger = 1;
                            }
                            $values['id_tagger'] = $id_tagger;
                            if ($file_copied) {
                                $this->table_inserat->updatePrice($values['id_inserat']);
                                $this->table_inserat->updateTagger($values['id_inserat'], $values);
                            }
                            if (!$this->auth->hasIdentity())
                                $this->ip_table->insertIP('entry');
                            
                            $this->resetCache();
                            $expenses_file = APPLICATION_PATH . '/../data/cache/expenses.txt';
                            unlink($expenses_file);
                            
                            $this->_helper->redirector('tagged', 'stream', null,
                                                        array('id' => $id_inserat));
                        } else {
                            $this->checkToken($form);
                            $this->view->message = 'Daten sind nicht korrekt.';
                            $form->getMessages();
                            $this->view->form = $form;
                        }
                    } else {
                        if ($this->table_inserat->checkAllTagged($id_inserat)) {
                            if (false != $id_inserat_tagged = $this->table_inserat->checkInseratExists($id_inserat)) {
                                $this->view->image_tagged = $this->image->orientationImageThumbnail($id_inserat_tagged);
                                $this->view->inserat_tagged = $this->table_inserat->getInseratAll($id_inserat_tagged);
                                $this->view->image = $this->image->orientationImageThumbnail($id_inserat);
                                $this->view->inserat = $this->table_inserat->getInseratAll($id_inserat);
                                $this->view->form = $this->setFormTrash();
                                $this->setNavigation('check', $id_inserat);
                                $this->renderScript('eingabe/kontrolle_exist.phtml');
                            } else {
                                $this->view->form = $this->setFormCheck();
                            }
                        } else {
                            $this->view->message = 'Nicht alle Daten wurden eingetragen.' . '<br />'.'Bitte die eingegebenen Daten noch einmal überprüfen.' . '<br /><br />'.'<a href="/eingabe/medium/id/' . $id_inserat . '">Zurück</a>';
                        }
                    }
                } else {
                    $this->_helper->redirector('index', 'index');
                }
            }
        } else {
            $this->view->tagged = true;
        }
        
        $this->view->image = $this->image->orientationImageDefault($id_inserat);
        if ($this->table_inserat->checkAllTagged($id_inserat)) {
            $inserat = $this->table_inserat->getInseratAll($id_inserat);
            if ($this->auth->hasIdentity()) {
                $this->user_table = new Application_Model_Users();
                $inserat[0]['tagger'] = $this->auth->getIdentity()->username;
            }
            $this->view->inserat = $inserat;
        } else {
            $this->view->inserat = false;
        }
        
        $this->setNavigation('check', $id_inserat);
    }
    
    public function imageAction()
    {
        // TODO3 Foto zuschneiden -> Nur Inserat als Thumbnail sichtbar
        
        if ($this->getRequest()->has('id')) {
            $id_inserat = $this->getRequest()->getParam('id');
            $this->view->id = $id_inserat;
            $this->formRotate('medium');
            $this->view->image = $this->image->orientationImageDefault($id_inserat);
            if (2 == $this->table_inserat->getIDSource($id_inserat) && 1 != $this->table_inserat->getTagged($id_inserat)) {
                $table_twitter = new Application_Model_Twitter();
                $this->view->imageOriginal = $this->table_inserat->getImageUrl($id_inserat);
            } else {
                $this->view->imageOriginal = '/eingabe' . $this->image->getImageOriginal($id_inserat);
            }
        } else {
            $this->view->image = false;
        }
        $this->_helper->layout->disableLayout();
    }
    
    /**
    * Download action for original image.
    * Download of image to local computer.
    *
    * @see $message
    */
    public function downloadAction()
    {
        $url = $this->getRequest()->REQUEST_URI;
        $url = str_replace('/eingabe/download/', '/', '../data/uploads/images' . $url);
        $this->download($url, null, 'application/jpg');
        $this->_helper->flashMessenger->addMessage("Download erfolgreich.");
    }
    
    public function changeadlcmbjbkdemflcjyAction()
    {
        $this->auth = Zend_Registry::get('auth');
        if ($this->auth->hasIdentity())
            if ('admin' == $this->auth->getIdentity()->username) {
                $this->table_inserat->updateDBColumn();
                $this->table_inserat->updateDBPhotos();
            }
    }
    
    /**
    * Redirect action
    *
    * @param $string Value of action
    */
    protected function redirect($action, $id_inserat)
    {
        $this->_helper->redirector($action, 'eingabe', null,
                            array('id' => $id_inserat));
    }
    
    /**
    * Setting default values for format form.
    *
    * @return Zend_Form This Zend_Form object for format
    */
    protected function setFormPhoto($form)
    {
        $data = array('id_inserat' => 0,);
        $form->setDefaults($data);
        
        $this->formRotate('foto');
        
        return $form;
    }
    
    /**
    * Setting default values for medium form.
    *
    * @return Zend_Form This Zend_Form object for medium
    */
    protected function setFormMedium()
    {
        $id_inserat = $this->getRequest()->getParam('id');
        
        $values = $this->table_inserat->getMediumTagged($id_inserat);
        $form = new Application_Form_Medium($id_inserat);
        
        if (0 < $values['id_printmedium'] || 0 < $values['print_page'] || 0 < $values['id_region_printmedium_bit']) {
                $data = $values;
        } else {
            if ($this->image->checkImagebyID($id_inserat))
                $date = $this->image->getImageDatebyID($id_inserat);
            else
                $date = false;
            
            if (false == $date) {
                $date = date('Y-m-d', time());
            }
            if ($this->auth->hasIdentity()) {
                $this->user_table = new Application_Model_Users();
                $username = $this->auth->getIdentity()->username;
                $id_printmedium = $this->user_table->getPreferedPrintmedium($username);
                $id_region_printmedium = $this->user_table->getPreferedRegion($username);
            } else {
                $id_printmedium = 0;
                $id_region_printmedium = 0;
            }
            $data = array(
                'id_printmedium'        => $id_printmedium,  // TODO1 Funktioniert nicht mit _bit
                'print_date'            => $date,
                'print_page'            => 1,
                'id_inserat'            => $id_inserat);
            
            $config = new Application_Model_Config();
            foreach ($config->getAllRegion() as $region ) {
                if ($id_region_printmedium & $region['id_config'])
                    $data['region_abb'] = 1;
            }
        }
        $form->setDefaults($data);
        
        return $form;
    }
    
    /**
    * Setting default values for format form.
    *
    * @return Zend_Form This Zend_Form object for format
    */
    protected function setFormFormat($cover)
    {
        $id_inserat = $this->getRequest()->getParam('id');
        $form = new Application_Form_Format($id_inserat, $cover);
        
        $values = $this->table_inserat->getFormatTagged($id_inserat);
        if (0 < $values['id_size']) {
            $data = $values;
        } else {
            $data = array(
                'id_inserat' => $id_inserat);
         }
        $form->setDefaults($data);
        
        return $form;
    }
    
    /**
    * Setting default values for party form.
    *
    * @return Zend_Form This Zend_Form object for party
    */
    protected function setFormParty()
    {
        $id_inserat = $this->getRequest()->getParam('id');
        $form = new Application_Form_Party($id_inserat);
        
        $data = $this->table_inserat->getPartyTagged($id_inserat);

        $form->setDefaults($data);
        
        return $form;
    }
    
    /**
    * Setting default values for check form.
    *
    * @return Zend_Form This Zend_Form object for check
    */
    protected function setFormCheck()
    {
        $id_inserat = $this->getRequest()->getParam('id');
        $form = new Application_Form_Check($id_inserat);
        
        $data = array('id_inserat' => $id_inserat);
        $form->setDefaults($data);
        
        $this->formRotate('kontrolle');
        
        return $form;
    }
    
    /**
    * Setting default values for trash form (if advertisements are identical)
    *
    * @return Zend_Form This Zend_Form object for trash
    */
    protected function setFormTrash()
    {
        $id_inserat = $this->getRequest()->getParam('id');
        $form = new Application_Form_Trash($id_inserat);
        
        $data = array('id_inserat' => $id_inserat);
        $form->setDefaults($data);
        
        return $form;
    }
    
    protected function setNavigation($page, $id_inserat)
    {
        $navigation = array(
            'first'  => array('Homepage', '/'),
            'medium' => array('Medium', '/eingabe/medium/'),
            'format' => array('Format', '/eingabe/format/'),
            'party'  => array('Partei', '/eingabe/partei/'),
            'check'  => array('Kontrolle', '/eingabe/kontrolle/'),
            'last'   => array('Stream', '/stream/untagged'),
            );
        
        $this->view->navigation = array(
        'list'      => $navigation, 
        'page'      => $page, 
        'id'        => $id_inserat);
    }
    
    protected function parameterImages($id_inserat)
    {
        $this->view->imageThumbnail = $this->image->orientationImageThumbnail($id_inserat);
        $this->view->imageDefault = $this->image->orientationImageDefault($id_inserat);
        $this->view->id = $id_inserat;
    }
    
    protected function formRotate($action_rotate)
    {
        if ($this->getRequest()->has('id_inserat_rotate')) {
            $action_rotate = $this->getRequest()->getParam('action_rotate');
            $id_inserat_rotate = $this->getRequest()->getParam('id_inserat_rotate');
            
            if ($this->getRequest()->has('rotate_left')) {
                $this->image->rotateImages($id_inserat_rotate, 90);
            }
            if ($this->getRequest()->has('rotate_right')) {
                $this->image->rotateImages($id_inserat_rotate, -90);
            }
        }
        
        $form_rotate = new Application_Form_Rotate();
        $id_inserat_rotate = $this->getRequest()->getParam('id');
        $data = array('action_rotate' => $action_rotate, 'id_inserat_rotate' => $id_inserat_rotate);
        $form_rotate->setDefaults($data);
        $form_rotate->setAction('/eingabe/image/id/' . $id_inserat_rotate);
        $this->view->rotate = $form_rotate;
    }
    
     protected function tooltip($cover)
     {
        $table_tariff = new Application_Model_Tariff();
        $id_inserat = $this->getRequest()->getParam('id');
        $tooltips = 'var tooltips=[];' . "\n";
        
        $printmedium_types = $table_tariff->getPrintmediumTypeByInserat($id_inserat);
        foreach ($printmedium_types as $type) {
            foreach ($table_tariff->getHeights($id_inserat, $type['printmedium_type_position'], $cover) as $key)
                $tooltips .= 'tooltips[' . $key['id_size'] . ']=["/images/heights/' . $key['height_image'] . '.jpg", "' . $key['size'] . '<br />' . $key['printmedium_width'] . ' x ' . $key['size_height'] . 'mm", {font:"bold 14px Arial"}];';
        }
        
        return $tooltips . "\n";
     }
    
    protected function uploadPhoto($form)
    {
        $values = $form->getValues();
        if ($this->auth->hasIdentity()) {
            $this->user_table = new Application_Model_Users();
            $username = $this->auth->getIdentity()->username;
            $values['id_uploader'] = $this->user_table->getUserId($username);
        } else {
            $values['id_uploader'] = 1;
            $username = 'anonym';
        }
        $values['id_source'] = 1; // Upload from Homepage
        
        if ($this->getRequest()->has('id_inserat_front'))
            $values['url_image'] = $values['url_image_front'];
        $id_inserat = $this->table_inserat->insertPhoto($values);
        $source_path = APPLICATION_PATH .'/../data/uploads/images/original/';
        $source_file = 'inserat_'. sprintf('%06d', $id_inserat) . '_o.jpg'; // o...Original
        
        if ($this->getRequest()->has('id_inserat_front'))
            $form->url_image_front->addFilter('Rename', array('target' => $source_path . $source_file));
        else
            $form->url_image->addFilter('Rename', array('target' => $source_path . $source_file));
        
        try {
            if ($this->getRequest()->has('id_inserat_front'))
                $form->url_image_front->receive(); // TODO3: Process bar (Hochladen und größe ändern)
            else
                $form->url_image->receive();
            
        } catch (Zend_File_Transfer_Exception $e) {
            $this->table_inserat->deleteInserat($id_inserat);
            $this->view->message = "Fehler beim Empfangen der Datei: " . $e->getMessage();
            
            return $id_inserat;
        }
        
        $destination_path = APPLICATION_PATH .'/../public/images/uploads/';
        $destination_file = 'default/inserat_'. sprintf('%06d', $id_inserat) . '_d.jpg'; // d...Default
        $this->image->createThumbnail($source_path . $source_file, $destination_path . $destination_file, 750, 750);
        
        $destination_file = 'thumbnail/inserat_'. sprintf('%06d', $id_inserat) . '_t.jpg'; // t...Thumbnail
        $this->image->createThumbnail($source_path . $source_file, $destination_path . $destination_file, 120, 120);
        
        $this->ip_table->insertIP('upload', $values['id_uploader']);
        
        require_once 'Intern/Authentication/Mailer.php';
        $mailer = new Authentication_Mailer();
        $mailer->sendNewInseratMail($id_inserat, $values['id_source'], $username); // TODO2 Nur gesammelt versenden (z.B. 1x am Tag)
        
        $this->cache->remove('table_stream_untagged');
        
        return $id_inserat;
    }
    
    protected function maxUploads()
    {
        $this->ip_table->deleteOldIps();
        
        $this->view->max_uploads = true;
        if ($this->auth->hasIdentity()) {
            $this->user_table = new Application_Model_Users();
            $username = $this->auth->getIdentity()->username;
            $max_uploads = $this->user_table->getMaxUploadsbyUsername($username);
            if (false == $this->ip_table->checkMaxUploads('upload', $max_uploads)) {
                $this->view->max_uploads = false;
                $this->view->message = 'Es dürfen aus Sicherheitsgründen pro User und Tag <br />maximal ' . $max_uploads . ' Inserate hochgeladen werden. <br /><br />Bitte versuchen sie es später noch einmal <br />oder kontaktieren Sie den Administrator für mehr Uploadrechte.<br /><br />';
            }
        } else {
            $max_uploads = $this->configuration->general->uploads;
            if (false == $this->ip_table->checkMaxUploads('upload', $max_uploads)) {
                $this->view->max_uploads = false;
                $this->view->message = 'Sie dürfen als anonymer User pro Tag <br />maximal ' . $max_uploads . ' Inserate hochladen. <br /><br />Um weitere Inserate hochzuladen, müssen Sie sich <a href="/user/login">hier</a> einloggend.<br />';
            }
        }
        
        return $this->view->max_uploads;
    }
    
    protected function maxEntries()
    {
        $this->ip_table->deleteOldIps();
        
        $this->view->max_entries = true;
        if (!$this->auth->hasIdentity()) {
            $max_entries = 4;
            if (false == $this->ip_table->checkMaxUploads('entry', $max_entries)) {
                $this->view->max_entries = false;
                $this->view->message = 'Sie dürfen als anonymer User pro Tag <br />nur 4 Einträge editieren. <br /><br />Um weitere Inserate zu editieren, müssen Sie sich <a href="/user/login">hier</a> einloggen.<br />';
            }
        }
        
        return $this->view->max_entries;
    }
    
    protected function checkToken($form)
    {
        if ($this->_request->isPost() && !$form->isValid($_POST))
            if (count($form->getErrors('token')) > 0)
                return $this->_forward('csrf', 'error');
                // TODO1 Ursache des Fehlers überprüfen -> Timeout
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

/**
/* 
/* curl -d '{"method": "login", "params": ["username", "password", "{4->Android, 5->iPhone, 6->Nokia}", "apikey"], "id":99}' http://politinserate.at/eingabe/rest
/*
/* curl -d '{"method": "submit", "params": ["username", "password", "{4->Android, 5->iPhone, 6->Nokia}", "apikey", "{base64}"], "id":99}' http://politinserate.at/eingabe/rest
/*
/* curl -d '{"method": "getMedium", "params": ["username", "password", "{4->Android, 5->iPhone, 6->Nokia}", "apikey", "id_inserat"], "id":99}' http://inserate.local/eingabe/rest
/*
/* curl -d '{"method": "setMedium", "params": ["username", "password", "{4->Android, 5->iPhone, 6->Nokia}", "apikey", "id_inserat", "id_printmedium"], "id":99}' http://inserate.local/eingabe/rest
*/
/**  
/* Zur Diskussion:
/* "submit" -> "uploadPhoto"
/* Error Codes verändert
/* $app und $hash zu login hinzugefügt
/* The question should be: how to hide data inside Android apps.
*/

/**
/*
/* URL: http://politinserate.at/eingabe/rest/v/1
/* Method(s): POST
/*
*/
class AppService
{
    
    public function __construct()
    {
        // TODO1 Check how many times an application connects in a time range
    }
    
    /**
    /* Authentication of user
    /*
    /* @param string $username
    /* @param string $password
    /* @param int $app
    /* @param string $apikey
    /* @return bool
    */
    public function login($username, $password, $app, $apikey)
    {   
        if (!$this->checkHash($app, $apikey))
            return false;
        
        $user = new AppLogin();
        
        $return = $user->submitLogin('', $username, $password);
        
        return $return;
    }
    
    /**
    /* Upload foto
    /*
    /* @param string $username
    /* @param string $password
    /* @param int $app
    /* @param string $apikey
    /* @param string $file64
    /* @return string
    */
    public function uploadPhoto($username, $password, $app, $credential, $file64)
    {
        if (!$this->checkCredentials($app, $credential))
            return Array('message' => 'Credentials are not correct', 'code' => 403);
        
        $user = new AppLogin();
        
        $this->user_table = new Application_Model_Users();
        
        if (!$return = $user->submitLogin('', $username, $password))
            return Array('message' => 'Login Error', 'code' => 401);
        
        if (!$file = base64_decode($file64))
            return Array('message' => 'File Error', 'code' => 415);
        
        $filename = 'img'; // TODO1 Soll entfernt werden
        $values_photo['id_uploader'] = $this->user_table->getUserId($username);
        $values_photo['ip_uploader'] = $_SERVER['REMOTE_ADDR'];
        $values_photo['id_source'] = $app;
        $values_photo['url_image'] = $filename;
        
        $this->table_inserat = new Application_Model_Inserate();
        $id_inserat = $this->table_inserat->insertPhoto($values_photo);
        
        $source_path = APPLICATION_PATH .'/../data/uploads/images/original/';
        $source_file = 'inserat_'. sprintf('%06d', $id_inserat) . '_o.jpg'; // o...Original
        
        $f = fopen($source_path . $source_file, 'w');
        fwrite($f, $file);
        fclose($f);
        
        $this->image = new Image_Editing();
        if (!$this->image->checkImage($source_path . $source_file)) {
            $this->table_inserat->deleteInserat($id_inserat);
            
            return Array('message' => 'Filetype is not supported', 'code' => 415);
        }
        
        $destination_path = APPLICATION_PATH .'/../public/images/uploads/';
        $destination_file = 'default/inserat_'. sprintf('%06d', $id_inserat) . '_d.jpg'; // d...Default
        $this->image->createThumbnail($source_path . $source_file, $destination_path . $destination_file, 750, 750);
        
        $destination_file = 'thumbnail/inserat_'. sprintf('%06d', $id_inserat) . '_t.jpg'; // t...Thumbnail
        $this->image->createThumbnail($source_path . $source_file, $destination_path . $destination_file, 120, 120);
        
        $this->ip_table = new Application_Model_Ips();
        $this->ip_table->insertIP('upload', $values_photo['id_uploader']);
        
        require_once 'Intern/Authentication/Mailer.php';
        $mailer = new Authentication_Mailer();
        $mailer->sendNewInseratMail($id_inserat, $app, $username); // TODO2 Nur gesammelt versenden (z.B. 1x am Tag)
        
        $this->cache = Zend_Registry::get('cache');
        $this->cache->remove('table_stream_untagged');
        $expenses_file = APPLICATION_PATH . '/../data/cache/expenses.txt';
        unlink($expenses_file);
        
        return Array('message' => 'Ok', 'code' => 200);
    }
    
    /**
    /* Get parameters for medium
    /*
    /* @param int $username
    /* @param string $password
    /* @param int $app
    /* @param string $apikey
    /* @param int $id_inserat
    /* @return array
    */
    public function getMedium($username, $password, $app, $credential, $id_inserat)
    {
        if (!$this->checkCredentials($app, $credential))
            return Array('message' => 'Credentials are not correct', 'code' => 403);
        
        $user = new AppLogin();
        if (!$return = $user->submitLogin('', $username, $password))
            return Array('message' => 'Login Error', 'code' => 401);
        
        // Ranges
        $table_medium = new Application_Model_Printmedium();
        $printmedium['0'] = '(Wähle Printmedium)';
        foreach ($table_medium->getAllMedium() as $key)
            $printmedium[$key['id_printmedium']] = $key['printmedium'];
        
        $table_config = new Application_Model_Config();
        foreach ($table_config->getAllRegion() as $key)
            $region_printmedium_bit[$key['region_abb']] = $key['region'];
        
        // Selected values
        $this->table_inserat = new Application_Model_Inserate();
        $values = $this->table_inserat->getMediumTagged($id_inserat);
        if (0 < $values['id_printmedium'] || 0 < $values['print_page'] || 0 < $values['id_region_printmedium_bit']) {
            $id_printmedium = $values['id_printmedium'];  // TODO1 Funktioniert nicht mit _bit
            $region = $values['id_region_printmedium_bit'];
            $date = $values['print_date'];
            $page = $values['print_page'];
        } else {
            $this->user_table = new Application_Model_Users();
            $id_printmedium = $this->user_table->getPreferedPrintmedium($username);
            $region = $this->user_table->getPreferedRegion($username);
            $date = date('Y-m-d', time());
            $page = 1;
        }
        
        return Array('printmedien' => $printmedium, 'regions' => $region_printmedium_bit, 'medium' => $id_printmedium, 'region' => $region, 'date' => $date, 'page' => $page);
    }
    
    /**
    /* Set parameters for medium
    /*
    /* @param string $username
    /* @param string $password
    /* @param int $app
    /* @param string $apikey
    /* @param string $id
    /* @param string $id_printmedium
    /* @param array $region
    /* @param string $date
    /* @param string $page
    /* @return string
    */
    public function setMedium($username, $password, $app, $apikey, $id_inserat, $id_printmedium = "", $region = array(), $date = "", $page = "")
    {
        $this->table_inserat = new Application_Model_Inserate();
        if (false == $this->table_inserat->checkInserat($id_inserat))
            return Array('message' => 'ID value does not exist.', 'code' => 400);
        
        $table_medium = new Application_Model_Printmedium();
        if (!empty($id_printmedium))
            if (false == $table_medium->checkPrintmedium($id_printmedium))
                return Array('message' => 'Printmedium value does not exist.', 'code' => 400);
            else
                $values['id_printmedium'] = $id_printmedium;
        
        if (!empty($page))
            if ($page > 0 || $page < 100)
                $values['print_page'] = $page;
            else
                return Array('message' => 'Page value is out of range.', 'code' => 400);
        
        if (!empty($date))
            if (time() > strtotime($date))
                $values['print_date'] = $date;
            else
                return Array('message' => 'Date is out of range.', 'code' => 400);
        
        if (!is_array($region))
            return Array('message' => 'Region is not an array.', 'code' => 400);
        
        if (empty($region['aut']))
            $values['aut'] = 0;
        
        $config = new Application_Model_Config();
        foreach ($config->getAllRegion() as $regions ) {
            if (!empty($region[$regions['region_abb']]) )
                $values[$regions['region_abb']] = 0;
            else
                $values[$regions['region_abb']] = 1;
        }

        $this->table_inserat->updateMedium($id_inserat, $values);
        $this->cache = Zend_Registry::get('cache');
        $this->cache->remove('table_stream_untagged');
        
        return Array('message' => 'Ok', 'code' => 200);
    }
    
    protected function checkCredentials($app, $credential)
    {
        $this->configuration = Zend_Registry::get('configuration');
        
        switch ($app) {
        case 4: 
            if ($credential == $this->configuration->app->android->hash)
                return true;
            else
                return false;
        case 5:
            if ($credential == $this->configuration->app->iphone->hash)
                return true;
            else
                return false;
        case 6:
            if ($credential == $this->configuration->app->symbian->hash)
                return true;
            else
                return false;
        default:
            return false;
        }
    }
}


class AppLogin
{
    public function __construct()
    {
        $this->user_table = new Application_Model_Users();
    }
    
    public function submitLogin($form = "", $username, $password)
    {
        $this->configuration = Zend_Registry::get('configuration');
        $this->auth = Zend_Registry::get('auth');
        $staticSalt = $this->configuration->password->salt;
        
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $authAdapter = new Zend_Auth_Adapter_DbTable(
            $db,
            'acd_inserate_user',
            'username',
            'password',
            'SHA1(CONCAT("' . $staticSalt . '", ? )) AND user_activated=1'
        );
        
        $authAdapter->setIdentity($username)
                    ->setCredential($password);
        
        $result = $this->auth->authenticate($authAdapter);
        
        Zend_Session::regenerateId();
        
        if ($result->isValid()) {
            $storage = $authAdapter->getResultRowObject();
            $storage->ip = $_SERVER['REMOTE_ADDR'];
            $storage->user_agent = $_SERVER['HTTP_USER_AGENT'];
            $this->auth->getStorage()->write($storage);
            ;
            $this->session->username = $result->getIdentity();
            $this->user_table->updateLastAccess($username);
            
            return true;
        } else {
        
            return false;
        }
    }
}


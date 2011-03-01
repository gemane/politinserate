<?php
/**
* Front page for Polit-Inserate.at.
*
* Upload button for advertisement images in printmedia.
* Overview of inserate data as diagramms.
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

class IndexController extends Mobile_Controller_Action
{
    protected $year;
    
    public function init()
    {   
        parent::init();
        
        $this->table_inserat = new Application_Model_Inserate();
        
        $this->configuration = Zend_Registry::get('configuration');
        $this->cache = Zend_Registry::get('cache');
        
        $this->view->num_column = $this->configuration->general->columns;
        $this->view->column = 1;
        
        if ($this->mobile) {
            $this->_helper->getHelper('layout')->setLayoutPath(APPLICATION_PATH . '/layouts/mobile');
            $this->view->mobile = $this->mobile;
            $this->view->num_column = floor($this->view->num_column/2);
        }
    }

    public function indexAction()
    {    
        $form = new Application_Form_Upload();
        $this->view->form = $form;
        
        if ($this->mobile)
            $this->view->num_column = 3;
        else
            $this->view->num_column = 5;
        
        $this->view->column = 0;
        
        if (!$this->configuration->general->cache || !$table = $this->cache->load('tagged_table_front')) {
            $table = $this->setTableTagged();
            $this->cache->save($table, 'tagged_table_front');
        }
        $this->view->table = $table;
        $this->view->year = $year = date('Y', time());
        
        /*if ($this->configuration->general->random) {
            $regions = $this->table_inserat->getExistingRegions();
            $length_regions = count($regions)-1;
            $id_region_party = $regions[rand(0, $length_regions)];
            $id_region_printmedium = $regions[rand(0, $length_regions)];
            
            $this->table_config = new Application_Model_Config();
            $this->view->region_printmedium = 
                (0 == $id_region_printmedium) ? ' gesamt' : ' in ' . $this->table_config->getRegion($id_region_printmedium);
            $this->view->region_party = 
                (0 == $id_region_party) ? ' gesamt' : ' in ' . $this->table_config->getRegion($id_region_party);
        } else {
            $id_region_party = 2;
            $this->view->region_party = ' in Wien';
            $id_region_printmedium = 2;
            $this->view->region_printmedium = ' in Wien';
        }
        
        $this->statistics = new Application_Model_Statistics();
        $plot_data_party = $this->statistics->getPlotDataParty(false, 0, $id_region_party);
        $this->setPlotDataParty($plot_data_party);
        
        $plot_data_printmedium = $this->statistics->getPlotDataMedium(false, 0, 'all_payed', $id_region_printmedium);
        $this->setPlotDataMedium($plot_data_printmedium);
        */
    }
    
    public function askmobileAction()
    {
        $this->view->url = $this->configuration->general->url;
        $this->view->mobile = $this->configuration->general->mobile;
    }
    
    public function userAction()
    {
        $form = new Application_Form_Upload();
        $this->view->form = $form;
        
        $this->table_inserat->moveOldUntagged(180);
        
        if ($this->configuration->twitter->active) {
            require_once('Intern/Service/Twitter.php');
            $twitter = new Service_Twitter();
            $image_list = $twitter->getTwitterLinks();
            $this->insertTweets($image_list);
        }
        
        if(!$this->configuration->general->cache || !$cache = $this->cache->load('table_stream_untagged')) {
            $stream = new Application_Model_Stream();
            $cache = $stream->setTableUntagged();
        }
        
        $this->view->table = $cache;
    }
    
    public function agbAction()
    {
        $this->view->author = $this->configuration->general->author;
        $this->view->sitename = '"' . $this->configuration->general->sitename . '"';
        $this->view->url = substr($this->configuration->general->url, 7);
        
        $this->view->modified = $this->filemtime_r(APPLICATION_PATH . '/views/scripts/index/agb.phtml');
    }
    
    public function datenschutzAction()
    {
        $this->view->author = $this->configuration->general->author;
        $this->view->sitename = '"' . $this->configuration->general->sitename . '"';
        $this->view->url = substr($this->configuration->general->url, 7);
        
        $this->view->modified = $this->filemtime_r(APPLICATION_PATH . '/views/scripts/index/datenschutz.phtml');
    }
    
    public function impressumAction()
    {
        $lastEdited_application = $this->filemtime_r(APPLICATION_PATH);
        $lastEdited_css = $this->filemtime_r(APPLICATION_PATH . '/../public/css');
        $lastEdited_js = $this->filemtime_r(APPLICATION_PATH . '/../public/js/intern');
        
        $lastEdited = max($lastEdited_application, $lastEdited_css, $lastEdited_js);
        $this->view->lastEdited = $lastEdited;
    }

    public function faqAction()
    {
        if ($this->configuration->general->csv) {
            $this->view->csv = true;
            $this->view->csv_inserate = $this->configuration->general->url . '/stream/tagged';
        } else
            $this->view->csv = false;
        
        if ($this->configuration->twitter->active)
            $this->view->twitter = true;
        else
            $this->view->twitter = false;
    }

    public function kontaktAction()
    {
        $form = new Application_Form_Contact();
        if ( $this->getRequest()->has('contact') ) {
            if ($form->isValid($_POST)) {
                $this->sendContactMail($form);
            } else {
                $this->checkToken($form);
                $this->view->message = 'Daten sind nicht korrekt.';
                $form->getMessages();
            }
        } else {
            $form = $this->setContact($form);
        }
        $this->view->form = $form;
    }
    
    public function linksAction()
    {
    
    }
    
    public function setPlotDataParty($data)
    {
        $this->view->dataPaymentsParty = $data['dataPaymentsParty'];
        $this->view->dataColorsParty = $data['dataColorsParty'];
        $this->view->dataLegendParty = $data['dataLegendParty'];
        $this->view->dataLabelParty = $data['dataLabelParty'];
        
        $this->view->dataPaymentsGovernment = $data['dataPaymentsGovernment'];
        $this->view->dataColorsGovernment = $data['dataColorsGovernment'];
        $this->view->dataLegendGovernment = $data['dataLegendGovernment'];
        
        $this->view->yaxis = $data['yaxis'];
    }
    
    public function setPlotDataMedium($data)
    {
        $this->view->dataPaymentsMedium = $data['dataPaymentsMedium'];
        $this->view->dataColorsMedium = $data['dataColorsMedium'];
        $this->view->dataLegendMedium = $data['dataLegendMedium'];
        $this->view->dataLabelMedium = $data['dataLabelMedium'];
    }
    
    /**
    * Retrieving data from database for view (partial).
    *
    * @return array Data of all tagged advertisments.
    */
    protected function setTableTagged()
    {
        $ID_Inserate = $this->table_inserat->getLastTaggedID_Inserate($this->view->num_column);
        if (!empty($ID_Inserate)) {
            include_once('Intern/Image/Editing.php');
            $this->image = new Image_Editing();
            $i = 0;
            foreach ($ID_Inserate as $id_inserat) {
                $table[$id_inserat] = $this->table_inserat->getInseratTagged($id_inserat);
                $image = $this->image->orientationImageThumbnail($id_inserat);
                $table[$id_inserat][0]['width'] = $image['width'];
                $table[$id_inserat][0]['height'] = $image['height'];
                $table[$id_inserat][0]['image'] = $image['image'];
                $table['ids'][$i++] =  $id_inserat;
                $table['tooltips'] =  $this->tooltipTagged($table);
            }
        } else {
            $table = array();
        }
        
        return $table;
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
                $image .= 'logo_party/logo_' . strtolower($this->view->preparePath($table[$key][0]['party'])) .  '.jpg';
            else
                $image .= 'logo_government/logo_' . $this->view->preparePath($table[$key][0]['region_abb']) .  '.png';
            
            $tooltips .= 'tooltips[' . $key . ']=["' . $image . '", "Bezahlt von<br /><strong>' . $table[$key][0]['payer'] . '</strong>", {font:"normal 12px Arial"}];';
        }
        return $tooltips . "\n";
     }
    
    protected function setContact($form)
    {
        $auth = Zend_Registry::get('auth');
        if ($auth->hasIdentity()) {
            $this->user_table = new Application_Model_Users();
            $username = $auth->getIdentity()->username;
            $email = $this->user_table->getEmail($username);
            $fullname = $this->user_table->getFullname($username);
            if ('' != $fullname)
                $username = $fullname;
            $data = array(
                'name'    => $username,
                'email'   => $email,
                'titel'   => '',
                'message' => '');
        } else {
            $data = array(
                'name'    => '',
                'email'   => '',
                'titel'   => '',
                'message' => '');
        }
        $form->setDefaults($data);
        
        return $form;
    }
    
    protected function sendContactMail($form)
    {
        $values = $form->getValues();
        if ('' != $values['name'] && '' != $values['email'] && '' != $values['titel'] && '' != $values['message']) {
            require_once 'Intern/Authentication/Mailer.php';
            $mailer = new Authentication_Mailer();
            $mailer->sendContactMail($values['email'], $values['name'], $values['titel'], $values['message']); 
            $this->view->message = 'Mitteilung wurde gesendet.';
            $form = $this->setContact($form);
        } else {
            $this->view->message = "Bitte alle Felder ausfüllen.";
        }
    }
    
    protected function checkToken($form)
    {
        if ($this->_request->isPost() && !$form->isValid($_POST))
            if (count($form->getErrors('token')) > 0)
                return $this->_forward('csrf', 'error');
    }
    
    protected function filemtime_r($path)
    {
        $allowedExtensions = array(
            'php',
            'phtml',
            'css',
            'js'
        );
        
        if (!file_exists($path))
            return 0;
        
        $extension = end(explode(".", $path));
        if (is_file($path) && in_array($extension, $allowedExtensions))
            return filemtime($path);
        $ret = 0;
        
        foreach (glob($path."/*") as $fn)
        {
            if ($this->filemtime_r($fn) > $ret)
                $ret = $this->filemtime_r($fn);
        }
        return $ret;
    }
    
}


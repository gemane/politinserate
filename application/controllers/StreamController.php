<?php
/**
* Stream of advertisment images with categorised data.
*
* Stream of ads that are
* - fully tagged
* - not at all or not enough tagged 
* - useless (trashed)
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

class StreamController extends Mobile_Controller_Action
{

    public function init()
    {
        parent::init();
        
        $this->table_inserat = new Application_Model_Inserate();
        $this->image = new Image_Editing();
        
        $this->cache = Zend_Registry::get('cache');
        $this->configuration = Zend_Registry::get('configuration');
        
        $this->view->num_column = $this->configuration->general->columns;
        $this->view->column = 1;
        
        if ($this->mobile) {
            $this->_helper->getHelper('layout')->setLayoutPath(APPLICATION_PATH . '/layouts/mobile');
            $this->view->num_column = floor($this->view->num_column/2);
        }
        
        $this->view->headScript()->appendScript('document.getElementById("nav_stream").style.textDecoration = "underline";');
    }
    
    public function indexAction()
    {
        if ( $this->getRequest()->has('correction') ) {
            $form = new Application_Form_Correction();
            if ($form->isValid($_POST)) {
                $values = $form->getValues();
                require_once 'Intern/Authentication/Mailer.php';
                $mailer = new Authentication_Mailer();
                $mailer->sendCorrectionMail($values['id_inserat'], $values['category'], $values['message']);
                $this->view->message = 'Korrekturvorschlag wurde abgesendet.';
            } else {
                $this->view->message = 'Formular nicht korrekt ausgefüllt.';
            }
        }
        
        $this->view->admin = false;
        $this->view->correction = false;
        $id_inserat = $this->getRequest()->getParam('inserat',0);
        if (0 < $id_inserat) {
            $this->view->tagged = $this->table_inserat->getTagged($id_inserat);
            $this->view->image = $this->image->orientationImageDefault($id_inserat);
            $this->view->imageOriginal = $this->image->getImageOriginal($id_inserat);
            if (1 == $this->view->tagged) {
                $this->view->inserat = $this->table_inserat->getInseratAll($id_inserat);
                
                $this->auth = Zend_Registry::get('auth');
                if ($this->auth->hasIdentity())
                    if ('admin' == $this->auth->getIdentity()->username) 
                        $this->view->admin = true;
                    else {
                        $this->view->correction = true;
                        $this->view->form = new Application_Form_Correction();
                        $data = array('id_inserat' => $id_inserat);
                        $this->view->form->setDefaults($data);
                    }
            } else if (-1 == $this->view->tagged) {
                $this->view->inserat = $this->table_inserat->getInseratTrashed($id_inserat);
            } else {
                $this->view->inserat = $this->table_inserat->getInseratUntagged($id_inserat);
            }
        } else {
            $this->_forward('tagged');
        }
    }
    
    public function taggedAction()
    {
        $year_from = $this->configuration->general->year;
        $year_to = date('Y', time());
        $year = $year = $this->getRequest()->getParam('year', date('Y', time() ));
        $month = $month = $this->getRequest()->getParam('month', date('n', time() ));
        
        $list = Zend_Registry::get('list_months'); 
        
        // Check if the list is in range
        if (empty($list[$year])) {
            $count = end($list);
            $year = key($list);
            $month = max($list[$year]);
        } else if (false == in_array($month, $list[$year])) 
            $month = max($list[$year]);
        
        // If user just tagged a new inserat, then say thank you.
        $this->view->thanks = false;
        if ($this->getRequest()->has('id')) {
            $this->view->thanks = true;
            $id_inserat = $this->getRequest()->getParam('id');
            $this->view->name = $this->table_inserat->getTagger($id_inserat);
        }
        
        if(!$this->configuration->general->cache || !$cache = $this->cache->load('table_stream_tagged')) {
            $stream = new Application_Model_Stream();
            $cache = $stream->setTableTagged();
        }
        
        $this->view->year = $year;
        $this->view->month = $month;
        $this->view->table = $cache;
        $this->view->navigation = array(
            'url' => '/stream/tagged', 
            'month' => $month, 
            'year' => $year, 
            'list' => $list, 
            'year_from' => $year_from, 
            'year_to' => $year_to);
        
        if ($this->configuration->general->csv && !empty($cache) )
            $this->view->csv = true;
        else
            $this->view->csv = false;
        
    }

    public function untaggedAction()
    {
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
    
    public function trashAction()
    {
        $this->table_inserat->deleteOldTrashed(false, 30);
        
        if(!$this->configuration->general->cache || !$cache = $this->cache->load('table_stream_trash')) {
            $stream = new Application_Model_Stream();
            $cache = $stream->setTableTrashed();
        }
        
        $this->auth = Zend_Registry::get('auth');
        if ($this->auth->hasIdentity())
            if ('admin' == $this->auth->getIdentity()->username) 
                Zend_Registry::set('admin', true);
            else
                Zend_Registry::set('admin', false);
        else
            Zend_Registry::set('admin', false);
        
        $this->view->table = $cache;
    }
    
    public function deleteAction()
    {
        $this->view->deleted = false;
        $this->auth = Zend_Registry::get('auth');
        if ($this->auth->hasIdentity()) {
            if ('admin' == $this->auth->getIdentity()->username) {
                if ($this->getRequest()->has('inserat')) {
                    $id_inserat = $this->getRequest()->getParam('inserat');
                    $delete_ids['id_inserat'] = $id_inserat;
                    
                    $this->table_inserat->deleteOldTrashed($delete_ids);
                    $this->view->deleted = true;
                    
                    $this->cache->remove('table_stream_trash');
                }
            }
        }
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
        $url = str_replace('/stream/download/', '/', '../data/uploads/images' . $url);
        $this->download($url, null, 'application/jpg');
        $this->view->message = "Download erfolgreich.";
    }
    
    public function userAction()
    {
        $parameters = $this->getRequest()->getParams();
        
        $this->streamUser($parameters['controller']);
        
    }
    
    /**
    * Export CSV action for Inserate.
    * Export inserate table as CSV.
    *
    * @see $message
    */
    public function exportcsvAction()
    {
        $path = APPLICATION_PATH . '/../data/downloads/' . 'zugeordnete_inserate.zip';
        $this->download($path, null, 'application/zip');
        
        $this->view->message = "Export erfolgreich.";
    }
    
    protected function streamUser($username)
    {
        $this->view->year = $year = $this->getRequest()->getParam('year', date('Y', time() ));
        $this->view->month = $month = $this->getRequest()->getParam('month', date('n', time() ));
        
        $this->user_table = new Application_Model_Users();
        $form = new Application_Form_Untagged();
        $form = $this->setFormConfig($form, $username);
        $this->view->form = $form;
        
        $id_user = $this->user_table->getUserId($username);
        $stream = new Application_Model_Stream();
        $table = $stream->setTableTagged(false, false, $id_user); // TODO1 Aus dem Cache laden und nach User filtern
        
        $this->view->name = $this->user_table->getName($username);
        $this->view->tooltips = $table['tooltips'];
        $this->view->table =  $table;
    }
    
    protected function setFormConfig($form, $username)
    {
        $config = $this->user_table->getConfiguration($username);
        $form->setDefaults($config[0]);
        
        $form->config->setValue($username);
        
        return $form;
    }
    
    protected function insertTweets($image_list)
    {
        $this->view->images = array();
        foreach ($image_list as $link_image) {
            if (!$this->table_inserat->checkUrl($link_image['source'])) {
                if (!$link_image['checked'])
                    $this->checkImage($link_image['image_remote']);
                
                $values['id_source'] = $link_image['id_source'];
                $values['id_uploader'] = 1;
                $values['upload_time'] = $link_image['upload_time'];
                $values['url_image'] = $link_image['source'];
                $id_inserat = $this->table_inserat->insertPhoto($values);
                // TODO3 Processanzeige
                $destination_path = APPLICATION_PATH .'/../public/images/uploads/';
                $destination_file = 'thumbnail/inserat_'. sprintf('%06d', $id_inserat) . '_t.jpg'; // t...Thumbnail
                $this->image->createThumbnail($link_image['image_remote'], $destination_path . $destination_file, 120, 120);
                
                $destination_file = 'default/inserat_'. sprintf('%06d', $id_inserat) . '_d.jpg'; // d...Default
                $this->image->createThumbnail($link_image['image_remote'], $destination_path . $destination_file, 750, 750);
                
                if (empty($link_image['keywords']['print_date'])) {
                    include_once('Intern/Image/Editing.php');
                    $this->image = new Image_Editing();
                    $link_image['keywords']['print_date'] = $this->image->getImageDate($link_image['image_remote']);
                    if (false == $link_image['keywords']['print_date']) {
                        $link_image['keywords']['print_date'] = date('Y-m-d', time());
                    }
                }
                $this->table_inserat->updateRemote($id_inserat, $link_image['keywords']);
                
                $table_twitter = new Application_Model_Twitter();
                $table_twitter->insertTweet($id_inserat, $link_image);
                
                $this->cache->remove('table_stream_untagged');
            }
        }
    }
    
    protected function checkImages($link_image)
    {
        if (!checkImageType($link_image))
            return false;
        
        $filesize = $this->checkImageSize($link_image);
        if (false == $filesize && $this->configuration->general->filesize < $filesize) 
            return false;
        
        $maxsize = $this->checkImageDimensions($link_image);
        if (700 > $maxsize) 
            return false;
        
        return true;
    }
    
    protected function checkImageType($link_image)
    {
        return true;
    }
    
    protected function checkImageDimensions($link_image)
    {
        // TODO2 Dimension sollte immer überprüft werden (eventuell über EXIF)
        list($width, $height, $type, $attr) = getimagesize($link_image);
        
        return min($width, $height);
    }
    
    protected function checkImageSize($link_image)
    {
        $ch = curl_init($link_image); // TODO2 Bildgröße ermitteln
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); //not necessary unless the file redirects (like the PHP example we're using here)
        $data = curl_exec($ch);
        curl_close($ch);
        if ($data === false) {
            echo 'cURL failed';
            exit;
        }
        
        $contentLength = false;
        if (preg_match('/Content-Length: (\d+)/', $data, $matches)) {
            $contentLength = (int)$matches[1];
        }
        
        echo 'Content-Length: ' . $contentLength;
        
        return $contentLength;
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


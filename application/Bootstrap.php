<?php
/**
* Bootstrap file.
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

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    
    public function init()
    {   
        Zend_Layout::startMvc();
    }
    
    public function _initBrowser()
    {
        $using_ie6 = (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6.') !== FALSE);
        $browser = !$using_ie6;
        
        Zend_Registry::set('browser', $browser);
    }
    
    public function _initConfig()
    {
        if (APPLICATION_ENV == 'development') {
            $this->configuration = new Zend_Config_Ini(APPLICATION_PATH . '/configs/config.ini', 'development');
            Zend_Registry::set('development', true);
        }
        else if (APPLICATION_ENV == 'testing') {
            $this->configuration = new Zend_Config_Ini(APPLICATION_PATH . '/configs/config.ini', 'testing');
            Zend_Registry::set('development', true);
        }
        else {
            $this->configuration = new Zend_Config_Ini(APPLICATION_PATH . '/configs/config.ini', 'main');
            Zend_Registry::set('development', false);
        }
        
        Zend_Registry::set('configuration', $this->configuration);
    }
    
    protected function _initLog()
    {
        $logger = new Zend_Log();
        
        $writer1 = new Zend_Log_Writer_Stream(realpath(APPLICATION_PATH .'/../temp/zf_log_info.txt'));
        $logger->addWriter($writer1);
        
        if ('production' != APPLICATION_ENV) {
            $writer2 = new Zend_Log_Writer_Firebug();
            $logger->addWriter($writer2);
        }
        
        Zend_Registry::set('logger',$logger);
    }
    
    public function _initCache()
    {
        $temp_path = realpath(APPLICATION_PATH . '/../data/cache');
        $frontendOptions    = array('lifetime' => 24*3600, 'automatic_serialization' => true);  // 3600 = 1 h
        $backendOptions     = array('cache_dir' => $temp_path);
        $this->cache = Zend_Cache::factory('Page', 'File', $frontendOptions, $backendOptions);
        Zend_Registry::set('cache', $this->cache);
        //Zend_Paginator::setCache($this->cache); // Funktioniert nicht mit Umlaute
    }
    
    public function _initLocale()
    {   
        date_default_timezone_set('Europe/Vienna');
        $locale = new Zend_Locale('de_AT');
        Zend_Registry::set('Zend_Locale', $locale);
        Zend_Locale::setCache($this->cache);
    }
    
    public function _initTranslation()
    {
        Zend_Translate::setCache($this->cache); 
        // TODO3: Übersetzungen
    }
    
    protected function _initView()
    {   
        $this->view = new Zend_View();
        
        $this->view->title = $this->configuration->general->sitename;
        $this->view->subtitle = $this->configuration->general->subtitle;
        
        $this->view->headMeta()->appendHttpEquiv('Content-Type', 'text/html; charset=UTF-8');
        $this->view->setEncoding('UTF-8');
        
        $this->view->doctype('XHTML1_STRICT');
        $this->view->headTitle($this->view->title)->setSeparator(' - ');
        $this->view->headLink()->appendStylesheet('/css/inserate.css');
        $this->view->headLink(Array('rel' => 'icon', 'href' => '/favicon.ico', 'type' => 'image/x-icon'));
    }
    
    protected function _initDatabase()
    {
        $resource = $this->getPluginResource('db');
        $db = $resource->getDbAdapter();
        $db->query('SET CHARACTER SET \'UTF8\'');
        
        $this->bootstrap('db')
             ->bootstrap('session');
        
        if ('production' != APPLICATION_ENV) {
            $profiler = new Zend_Db_Profiler_Firebug('All DB Queries');
            $profiler->setEnabled(true);
            
            // Attach the profiler to your db adapter
            $db->setProfiler($profiler);
        }
        
        Zend_Registry::set('db', $db);
    }
    
    protected function _initViewHelpers()
    {   
        $this->view->addHelperPath( APPLICATION_PATH .'/common/helpers');
        
        $this->view->addHelperPath('Zend/Dojo/View/Helper/', 'Zend_Dojo_View_Helper'); 
        $viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer(); 
        $viewRenderer->setView($this->view); 
        Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);
    }
    
    protected function _initDojo()
    {
        Zend_Dojo_View_Helper_Dojo::setUseDeclarative(); 
        $this->view->dojo()->setDjConfigOption('parseOnLoad', true) 
                           ->setDjConfigOption('isDebug', false)
                           ->setLocalPath('/js/dojo/dojo.js');  // Default
                           //->setCdnBase(Zend_Dojo::CDN_BASE_GOOGLE)
                           //->setCdnDojoPath(Zend_Dojo::CDN_DOJO_PATH_GOOGLE)
                           //->addStyleSheetModule('dijit.themes.tundra'); 
        //$this->view->headLink()->appendStylesheet('/css/tundra.css');
        $this->view->headLink()->appendStylesheet('http://ajax.googleapis.com/ajax/libs/dojo/1.4.3/dijit/themes/tundra/tundra.css');
    }
    
    protected function _initFront()
    {
        $this->frontController = Zend_Controller_Front::getInstance();
        Zend_Registry::set('frontController', $this->frontController);
        
        $this->frontController->registerPlugin(new Application_Plugin_SessionCheck());
        $this->frontController->registerPlugin(new Application_Plugin_GetPayments());
        $this->frontController->registerPlugin(new Application_Plugin_PrintmediumTypes());
        $this->frontController->registerPlugin(new Application_Plugin_CheckMobile());
        //$this->frontController->registerPlugin(new Application_Plugin_TwitterSearch()); // TODO1 Brauch ich das noch? Verlangsamt das System ungemein!!!
        //$this->frontController->addModuleDirectory(APPLICATION_PATH . '/modules');
    }
    
    protected function _initMobile()
    {
        $mobile_url = $this->configuration->general->mobile;
        $pos = strpos($mobile_url, '.');
        $mobile = substr($mobile_url, 7, $pos-7);
        // set correct context
        $domains = explode('.', $_SERVER['HTTP_HOST']);
        if ($domains[0] == $mobile || $this->frontController->getParam('format') == 'mobile') {
            if ($domains[0] == $mobile) {
                // if set, user will be redirected directly to requested page
                Zend_Registry::set('directmobile', '1');
            }
            Zend_Registry::set('context', '\mobile');
        } else {
            Zend_Registry::set('context', '');
        }
    }
    
    protected function _initJavascript()
    {
        if (0 == strcmp('\mobile', Zend_Registry::get('context')))
            $this->view->headScript()->appendFile('/js/intern/tabmousover_mobile.js', 'text/javascript');
        else
            $this->view->headScript()->appendFile('/js/intern/tabmousover.js', 'text/javascript');
    }
    
    protected function _initRouter()
    {
        $router = $this->frontController->getRouter();
        Zend_Registry::set('router', $router);
        
        $login = new Zend_Controller_Router_Route_Static('login', 
            array('controller' => 'user', 'action' => 'login'));
        $router->addRoute('login', $login);
        
        $logout = new Zend_Controller_Router_Route_Static('logout', 
            array('controller' => 'user', 'action' => 'logout'));
        $router->addRoute('logout', $logout);
        
        $register = new Zend_Controller_Router_Route_Static('register', 
            array('controller' => 'user', 'action' => 'register'));
        $router->addRoute('register', $register);
        
        $links = new Zend_Controller_Router_Route_Static('links', 
            array('controller' => 'index', 'action' => 'links'));
        $router->addRoute('links', $links);
        
        $faq = new Zend_Controller_Router_Route_Static('faq', 
            array('controller' => 'index', 'action' => 'faq'));
        $router->addRoute('faq', $faq);
        
        $contact = new Zend_Controller_Router_Route_Static('kontakt', 
            array('controller' => 'index', 'action' => 'kontakt'));
        $router->addRoute('contact', $contact);
        
        $impressum = new Zend_Controller_Router_Route_Static('impressum', 
            array('controller' => 'index', 'action' => 'impressum'));
        $router->addRoute('impressum', $impressum);
        
        $privacy = new Zend_Controller_Router_Route_Static('datenschutz', 
            array('controller' => 'index', 'action' => 'datenschutz'));
        $router->addRoute('datenschutz', $privacy);
        
        $agb = new Zend_Controller_Router_Route_Static('agb', 
            array('controller' => 'index', 'action' => 'agb'));
        $router->addRoute('agb', $agb);
        
        $partei = new Zend_Controller_Router_Route_Static('partei', 
            array('controller' => 'statistiken', 'action' => 'parteien'));
        $router->addRoute('partei', $partei);
        
        $parteien = new Zend_Controller_Router_Route_Static('parteien', 
            array('controller' => 'statistiken', 'action' => 'parteien'));
        $router->addRoute('parteien', $parteien);
        
        $medien = new Zend_Controller_Router_Route_Static('medien', 
            array('controller' => 'statistiken', 'action' => 'medien'));
        $router->addRoute('medien', $medien);
        
        $region = new Zend_Controller_Router_Route_Static('regionen', 
            array('controller' => 'statistiken', 'action' => 'regionen'));
        $router->addRoute('regionen', $region);
        
        $activateUserRoute = new Zend_Controller_Router_Route('user/activate/:userId/:activationCode', 
            array('controller' => 'user', 'action' => 'activate'));
        $router->addRoute('activateUser', $activateUserRoute);
        
        $profileUser = new Zend_Controller_Router_Route('user/profile/:username', 
            array('controller' => 'user', 'action' => 'profile'));
        $router->addRoute('profileUser', $profileUser);
        
    }
    
    protected function _initSession()
    {
        $config = array( 
            'name'           => 'acd_inserate_session',
            'primary'        => 'id_session',
            'modifiedColumn' => 'modified',
            'dataColumn'     => 'session_data',
            'lifetimeColumn' => 'lifetime'
        ); 
        
        $savehandler = new Zend_Session_SaveHandler_DbTable($config);
        Zend_Session::rememberMe($seconds = (60 * 60 * 24 * 30)); // 30 days
        $savehandler->setLifetime($seconds) 
                    ->setOverrideLifetime(true);
        Zend_Session::setSaveHandler($savehandler);
        
        Zend_Session::start();
        
        $auth =  Zend_Auth::getInstance();
        Zend_Registry::set('auth', $auth);
    }
    
}


<?php
/**
* User controller for administration and profile.
*
* User specific data.
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

class UserController extends Mobile_Controller_Action
{
    protected $_flashMessenger = null;

    public function init()
    {
        parent::init();
        
        $this->user_table = new Application_Model_Users();
        $this->namespace = new Zend_Session_Namespace('Default');
        $this->auth = Zend_Registry::get('auth');
        $this->configuration = Zend_Registry::get('configuration');
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
    
        if ($this->mobile)
            $this->_helper->getHelper('layout')->setLayoutPath(APPLICATION_PATH . '/layouts/mobile');
    }

    public function indexAction()
    {
        $this->_helper->redirector('login', 'user');
    }
    
    public function loginAction() 
    {
        // If logged-in redirect to front page
        if ($this->auth->hasIdentity())
            $this->_helper->redirector('index', 'index');
        
        if ( $this->getRequest()->has('cancel') )
            $this->_helper->redirector('index', 'index');
        
        $this->setSSL();
        
        if ($this->_flashMessenger->hasMessages()) {
            $this->view->message = end($this->_flashMessenger->getMessages());
        }
        
        $form = new Application_Form_Login();
        $form->setAction('/user/login');
        $this->checkToken($form); // TODO1
        if ( $this->getRequest()->has('login_hidden') ) {
            $values = $form->getValues();
            $username = $values['username'];
            $password = $values['password'];
            if ('' != $username && '' != $password) {
                if (!$this->user_table->checkUsername($username))
                    $this->_helper->redirector('noexist', 'user');
                
                if (!$this->user_table->userActive($username))
                    $this->_helper->redirector('inactive', 'user');
                
                if ($this->submitLogin($username, $password) ) {
                    $this->unsetSSL();
                    if (0 === strpos( $values['return_link'], $this->configuration->general->url) || strpos( $values['return_link'], "http://blog.politinserate.at"))
                        $this->_helper->redirector->gotoUrl($values['return_link']);
                    else
                        $this->_helper->redirector('index', $this->auth->getIdentity()->username);
                    
                } else {
                    $this->_helper->redirector('login', 'user');
                }
            } else {
                $this->view->message = "Bitte Felder ausfüllen.";
            }
        }
        
        if ($this->_flashMessenger->hasMessages())
            $this->view->message = end($this->_flashMessenger->getMessages());
        $this->view->form = $form;
    }
    
    public function logoutAction()
    {
        if ($this->auth->hasIdentity()) {
            $this->auth->clearIdentity();
            Zend_Session::forgetMe();
            Zend_Session::destroy();
        } else {
            $this->_helper->redirector('login', 'user');
        }
    }
    
    public function registerAction()
    {
        // If logged-in redirect to front page
        if ($this->auth->hasIdentity())
            $this->_helper->redirector('index', 'index');
    
        if ( $this->getRequest()->has('cancel') )
            $this->_helper->redirector('index', 'index');
        
        $this->setSSL();
        
        $form = new Application_Form_Register();
        
        if ( $this->getRequest()->has('id_user') ) {
            if(!$this->addUser($form))
                $form->getMessages();
            if ($this->_flashMessenger->hasMessages())
                $this->view->message = end($this->_flashMessenger->getMessages());
        }
        
        $this->view->form = $form;
    }
    
    public function activateAction()
    {
        $this->user_table->deleteOldUser();
        
        $parameters = $this->getRequest()->getParams();
        if (isset($parameters['userId']) && isset($parameters['activationCode'])) {
            // activate the user if the activation code is valid.
            $rows = $this->user_table->find($parameters['userId']);
            $user = $rows->current();
            if ($user) {
                if (!$user->user_activated && $user->user_activationcode == $parameters['activationCode']) {
                    // activate the user
                    $user->user_activated = 1;
                    $user->save();
                    $this->view->activatedOK = true;
                    
                    // Email zur Begrüßung versenden
                    require_once 'Intern/Authentication/Mailer.php';
                    $mailer = new Authentication_Mailer();
                    $values = $this->user_table->getUserByID($parameters['userId']);
                    $mailer->sendWelcomeMail($values[0]['user_email'], $values[0]['username']);
                    
                    $this->view->username = $values[0]['username'];
                    
                    $form = new Application_Form_Login();
                    $form->setAction('/user/login');
                    $this->view->form = $form;
                } else if ($user->user_activated) {
                    $this->view->userAlreadyActive = true;
                }
            }
        } else if (isset($this->namespace->userJustRegistered)) {
            $this->view->username = $this->namespace->userJustRegistered;
            $this->renderScript('user/justRegistered.phtml');
        }
    }
    
    public function profileAction()
    {
        if ($this->auth->hasIdentity()) {
            $parameters = $this->getRequest()->getParams();
            $username = $parameters['username'];
            if ($username == $this->auth->getIdentity()->username) {
                $this->showProfile($username);
            } else {
                $this->renderScript('user/noAccess.phtml');
            }
        } else {
            $this->renderScript('user/notLoggedIn.phtml');
        }
    }
    
    public function inactiveAction()
    {
    
    }
    
    public function noexistAction()
    {
    
    }
    
    public function lostpasswordAction()
    {
        // If logged-in redirect to front page
        if ($this->auth->hasIdentity())
            $this->_helper->redirector('index', 'index');
        
        if ($this->getRequest()->has('cancel') )
            $this->_helper->redirector('index', 'index');
        
        $this->setSSL();
        
        $form = new Application_Form_Lost();
        $form->setAction('/user/lostpassword');
        $this->checkToken($form); // TODO1
        if ( $this->getRequest()->has('lost_hidden') ) {
            if ($form->isValid($_POST)) {
                $values = $form->getValues();
                if ('' != $values['email']) {
                    if ($this->user_table->checkEmail($values['email'])) {
                        $username = $this->user_table->getUsernameByEmail($values['email']);
                        $id_user = $this->user_table->getUserId($username);
                        
                        require_once 'Intern/Authentication/Mailer.php';
                        $mailer = new Authentication_Mailer();
                        $newpassword = $mailer->sendLostPasswordMail($values['email'], $id_user);
                        
                        $staticSalt = $this->configuration->password->salt;
                        
                        $this->user_table->updatePassword($id_user, $newpassword, $staticSalt);
                        
                        $data = array('email' => '',);
                        $form->setDefaults($data);
                        
                        $this->view->message = 'Email wurde versendet.';
                    } else {
                        $this->view->message = 'Ungültige Email.';
                    }
                }
            } else {
                $this->view->message = 'Ungültige Email.';
            }
        }
        
        $this->view->form = $form;
    }
    
    public function changepasswordAction()
    {
        // If logged-out redirect to front page
        if (!$this->auth->hasIdentity())
            $this->_helper->redirector('index', 'index');
        
        $username = $this->auth->getIdentity()->username;
        if ($this->getRequest()->has('cancel') )
            $this->_helper->redirector($username, 'profile', 'user');
        
        $this->setSSL();
        
        $form = new Application_Form_Password();
        $form->setAction('/user/changepassword');
        $this->checkToken($form); // TODO1
        if ( $this->getRequest()->has('password_hidden') ) {
            if ($form->isValid($_POST)) {
                $values = $form->getValues();
                if ('' != $values['oldpassword'] && '' != $values['newpassword1'] && '' != $values['newpassword2']) {
                    if (0 == strcmp($values['newpassword1'], $values['newpassword2'])) {
                        if ($this->submitLogin($username, $values['oldpassword'])) {
                            $staticSalt = $this->configuration->password->salt;
                            $id_user = $this->user_table->getUserId($username);
                            
                            $this->user_table->updatePassword($id_user, $values['newpassword1'], $staticSalt);
                            $this->_helper->flashMessenger->addMessage("Passwort wurde geändert.");
                            $this->_helper->redirector($username, 'profile', 'user');
                        } else {
                            $this->view->message = 'Ungültige Authentifizierung.';
                        }
                    } else {
                        $this->view->message = 'Neue Passwörter sind nicht identisch.';
                    }
                } else {
                    $this->view->message = 'Bitte Felder ausfüllen.';
                }
            }
        }
        
        $this->view->form = $form;
    }
    
    public function deleteAction() // TODO2
    {
        
    }
    
    protected function setSSL()
    {
        if ($this->configuration->general->ssl)
            if (!isset($_SERVER['HTTPS']) ||
                (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) != 'on') ) {
                    header('Location: https://' . $_SERVER['SERVER_NAME']
                                                . $_SERVER['REQUEST_URI']);
                    exit;
                }
    }
    
    protected function unsetSSL()
    {
        if (isset($_SERVER['HTTPS']) &&
            strtolower($_SERVER['HTTPS']) == 'on') {
                header('Location: http://' . $_SERVER['SERVER_NAME']
                                           . $_SERVER['REQUEST_URI']);
                exit;
            }
    }
    
    protected function checkToken($form)
    {
        if ($this->_request->isPost() && !$form->isValid($_POST))
            if (count($form->getErrors('token')) > 0)
                return $this->_forward('csrf', 'error');
    }
    
    /**
    * Inserting values into database from register form.
    *
    * @param Zend_Form This Zend_Form object for tariff
    */
    protected function addUser($form)
    {   
        if ($form->isValid($_POST) ) {
            $values = $form->getValues();
            if ('' != $values['username'] && '' != $values['password1'] && '' != $values['password2'] && '' != $values['user_email']) {
                if (0 == strcmp($values['password1'], $values['password2'])) {
                    if (!$this->user_table->checkUsername($values['username'])) {
                        if (!$this->user_table->checkEmail($values['user_email'])) {
                            $staticSalt = $this->configuration->password->salt;
                            $id_user = $this->user_table->insertUser($values, $staticSalt);
                            
                            if (false != $id_user) {
                                // Email zur Verifikation versenden
                                require_once 'Intern/Authentication/Mailer.php';
                                $mailer = new Authentication_Mailer();
                                $activationCode = $mailer->sendRegistrationMail($values['user_email'], $values['username'], $id_user);
                                $this->user_table->updateUser($id_user, array('user_activationcode' => $activationCode));
                                
                                $this->namespace->userJustRegistered = $values['username'];
                                $this->_helper->redirector('activate', 'user');
                            } else {
                                $this->view->message = "Eintrag in Datenbank ist fehlgeschlagen.";
                            }
                        } else {
                            $this->view->message = "Email bereits vorhanden.";
                        }
                    } else {
                        $this->view->message = "Benutzername bereits vorhanden.";
                    }
                } else {
                    $this->view->message = "Passwörter sind nicht gleich.";
                }
            } else {
                $this->view->message = "Bitte alle Felder ausfüllen.";
            }
        } else {
            $this->checkToken($form);
        }
        
        return false;
    }
    
    public function submitLogin($username, $password)
    {
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
            
            $this->session->username = $result->getIdentity();
            $this->user_table->updateLastAccess($username);
            return true;
        } else {
            $this->_helper->flashMessenger->addMessage("Ungültige Authentifizierung.");
            sleep(3);
            return false;
        }
    }
    
    protected function showProfile($username)
    {
        
        $this->view->username = $username;
        $this->table_inserate = new Application_Model_Inserate();
        $this->view->numPhotos = $this->table_inserate->getNumPhotoByUserid($username);
        $this->view->numInserate = $this->table_inserate->getNumInserateByUserid($username);
        
        $form = new Application_Form_Configuration();
        $form->setAction('/user/profile/' . $username);
        
        if ($this->getRequest()->has('config') &&  !$this->getRequest()->has('cancel')) {
            $result = $this->updateConfig($form, $username);
            $form->getMessages();
        }
        $form = $this->setFormConfig($form, $username);
            
        if ($this->_flashMessenger->hasMessages())
            $this->view->message = end($this->_flashMessenger->getMessages());
        $this->view->form = $form;
    }
    
    protected function setFormConfig($form, $username)
    {
        $config = $this->user_table->getConfiguration($username);
        $form->setDefaults($config[0]);
        
        $form->config->setValue($username);
        
        return $form;
    }
    
    protected function updateConfig($form, $username)
    {
        if ($form->isValid($_POST) ) {
            $values = $form->getValues();
            //if ('' != $values['user_fullname']) {  // Ist nicht notwendig
                
                $this->user_table->updateConfiguration($username, $values);
                
                $this->_helper->flashMessenger->addMessage("Daten wurden überschrieben.");
            /*} else {
                $this->_helper->flashMessenger->addMessage("Bitte alle Felder ausfüllen.");
            }*/
        }
        
        return false;
    }
    
    public function mailAction()
    {
        require_once 'Intern/Authentication/Mailer.php';
        $mailer = new Authentication_Mailer();
        $mailer->sendWelcomeMail('gerold.neuwirt@gmail.com', 'Gerold Neuwirt');
    }
    
}

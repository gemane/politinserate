<?php
/**
* Error Controller
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

class ErrorController extends Zend_Controller_Action
{

    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');
        
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
        
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = 'Page not found';
                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->message = 'Application error';
                break;
        }
        
        // Log exception, if logger available
        if ($log = $this->getLog()) {
            $log->crit($this->view->message, $errors->exception);
        }
        
        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->exception = $errors->exception;
        }
        
        $this->view->request   = $errors->request;
        
        $message = $errors->exception->getMessage();
        $trace = $errors->exception->getTraceAsString();
        $this->configuration = Zend_Registry::get('configuration');
        if ( ('production' == APPLICATION_ENV || 'testing' == APPLICATION_ENV) && $this->configuration->general->senderror) {
            // Send email when error
            require_once 'Intern/Authentication/Mailer.php';
            $mailer = new Authentication_Mailer();
            $parameters = print_r($errors->request->getParams(), true);
            $mailer->sendErrorMail($this->view->message, $message, $trace, $parameters);
        }
        
        $this->writeError($message, $trace);
    }
    
    public function csrfAction()
    {
        $logger = Zend_Registry::get('logger');
        $logger->log(chr(10) . 'Invalid CSRF token' . chr(10) . $_SERVER['REMOTE_ADDR'] . chr(10), Zend_Log::CRIT);
        // 403 error -- Forbidden page
        $this->getResponse()->setHttpResponseCode(403);
    }

    public function getLog()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
        if (!$bootstrap->hasPluginResource('Log')) {
            return false;
        }
        $log = $bootstrap->getResource('Log');
        return $log;
    }
    
    public function writeError($message, $value)
    {
        $logger = Zend_Registry::get('logger');
        $logger->log(chr(10) . 'Log Message: ' . chr(10) . $_SERVER['REMOTE_ADDR'] . chr(10) . $message . chr(10) . $value . chr(10), Zend_Log::INFO);
        
        /*
        Firebug Logging Styles Style    Description
        LOG     Displays a plain log message
        INFO    Displays an info log message
        WARN    Displays a warning log message
        ERROR   Displays an error log message that increments Firebug's error count
        TRACE   Displays a log message with an expandable stack trace
        EXCEPTION   Displays an error long message with an expandable stack trace
        TABLE   Displays a log message with an expandable table
        */
        
        if ('production' != APPLICATION_ENV) {
            echo '<div style="text-align:center;">#';
            print_r($value);
            echo '#</div><br />';
        }
    }

}


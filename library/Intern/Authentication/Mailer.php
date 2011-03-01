<?php
/**
* Library for sending mails.
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

class Authentication_Mailer
{
    /**
     * Constructor for the class, provide directory path where mail templates are saved
     *
     * @param string $templatesDir Directory path, where mail templates are located
     */
    public function __construct()
    {
        
        $this->configuration = Zend_Registry::get('configuration');
        
        $mailConfig = $this->configuration->mail;
        if ($mailConfig->smtp) {
            $transport = new Zend_Mail_Transport_Smtp($mailConfig->host, $mailConfig->smtpconfig->toArray());
        } else {
            $transport = new Zend_Mail_Transport_Sendmail();
        }
        
        Zend_Mail::setDefaultTransport($transport);
        
        $this->subject = $this->configuration->general->subject;
 
    }
 
    public function sendRegistrationMail($emailAddress, $name, $id_user, $languageCode = 'de_at')
    {
        $sitename = $this->configuration->general->sitename;
        $url = $this->configuration->general->url;
 
        $templatePath = APPLICATION_PATH . '/../data/templates/registration/email_registration_' . $languageCode . '.txt';
        
        if (!is_file($templatePath)) {
            require_once 'Intern/Exception/Exception.php';
            throw new Intern_Exception('Missing template for registration mail - language code: '.$languageCode);
        }
        
        $templateTxt = file_get_contents($templatePath);
        
        $activationCode = sha1(uniqid('xyz', true)); 
        $activationLink = '/user/activate/' . $id_user . '/' . $activationCode;
 
        //replace tags: [name], [sitename], [activation link], [url]
        $templateTxt = str_replace('[name]', $name, $templateTxt);
        $templateTxt = str_replace('[sitename]', $sitename, $templateTxt);
        $templateTxt = str_replace('[activation_link]', $activationLink, $templateTxt);
        $templateTxt = str_replace('[url]', $url, $templateTxt);
        
        $mailer = new Zend_Mail('utf-8');
        $mailer->addTo($emailAddress, $name);
        $mailer->setSubject(sprintf('Bestätigen Sie die Registrierung der Seite "%s"', $sitename));
        $mailer->setBodyHtml($templateTxt, 'utf8');
        $mailer->setFrom($this->configuration->mail->from);
        $mailer->send();
        
        return $activationCode;
    }
    
    public function sendLostPasswordMail($emailAddress, $id_user, $languageCode = 'de_at')
    {
        $sitename = $this->configuration->general->sitename;
        $url = $this->configuration->general->url;
 
        $templatePath = APPLICATION_PATH . '/../data/templates/registration/email_password_lost_' . $languageCode . '.txt';
        
        if (!is_file($templatePath)) {
            require_once 'Intern/Exception/Exception.php';
            throw new Intern_Exception('Missing template for registration mail - language code: '.$languageCode);
        }
        
        $templateTxt = file_get_contents($templatePath);
        
        // http://www.laughing-buddha.net/php/lib/password
        $length = 8;
        $newpassword = "";
        $possible = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";
        $maxlength = strlen($possible);
    
        // check for length overflow and truncate if necessary
        if ($length > $maxlength) {
            $length = $maxlength;
        }
        
        $i = 0; 
        while ($i < $length) { 
            $char = substr($possible, mt_rand(0, $maxlength-1), 1);
                
            // have we already used this character in $password?
            if (!strstr($newpassword, $char)) { 
                // no, so it's OK to add it onto the end of whatever we've already got...
                $newpassword .= $char;
                $i++;
            }
        }
        
        //replace tags: [sitename], [new_password link], [url]
        $templateTxt = str_replace('[sitename]', $sitename, $templateTxt);
        $templateTxt = str_replace('[new_password]', $newpassword, $templateTxt);
        $templateTxt = str_replace('[url]', $url, $templateTxt);
        
        // Username will not be send to avoid having it with the password together
        
        $mailer = new Zend_Mail('utf-8');
        $mailer->addTo($emailAddress, '');
        $mailer->setSubject(sprintf('Neues Passwort für %s', $sitename));
        $mailer->setBodyHtml($templateTxt, 'utf8');
        $mailer->setFrom($this->configuration->mail->from);
        $mailer->send();
        
        return $newpassword;
    }
    
    public function sendWelcomeMail($emailAddress, $name, $languageCode = 'de_at')
    {
        $sitename = $this->configuration->general->sitename;
        $url = $this->configuration->general->url;
 
        $templatePath = APPLICATION_PATH . '/../data/templates/registration/email_welcome_' . $languageCode . '.txt';
        
        if (!is_file($templatePath)) {
            require_once 'Intern/Exception/Exception.php';
            throw new Intern_Exception('Missing template for welcome mail - language code: '.$languageCode);
        }
        
        $templateTxt = file_get_contents($templatePath);
        
        //replace tags: [name], [sitename], [url]
        $templateTxt = str_replace('[name]', $name, $templateTxt);
        $templateTxt = str_replace('[sitename]', $sitename, $templateTxt);
        $templateTxt = str_replace('[url]', $url, $templateTxt);
        
        $mailer = new Zend_Mail('utf-8');
        $mailer->addTo($emailAddress, $name);
        $mailer->setSubject(sprintf('Willkommen bei "%s"', $sitename));
        $mailer->setBodyHtml($templateTxt, 'utf8');
        $mailer->setFrom($this->configuration->mail->from);
        $mailer->send();
    }
    
    public function sendContactMail($email, $name, $titel, $message, $languageCode = 'de_at')
    {
        $templatePath = APPLICATION_PATH . '/../data/templates/admin/email_contact_' . $languageCode . '.txt';
        
        if (!is_file($templatePath)) {
            require_once 'Intern/Exception/Exception.php';
            throw new Intern_Exception('Missing template for contact mail - language code: '.$languageCode);
        }
        
        $templateTxt = file_get_contents($templatePath);
        
        //replace tags: [name], [email], [message]
        $templateTxt = str_replace('[name]', $name, $templateTxt);
        $templateTxt = str_replace('[titel]', $titel, $templateTxt);
        $templateTxt = str_replace('[message]', $message, $templateTxt);
        
        $emailAdmin = 'gerold.neuwirt@gmail.com';
        $nameAdmin = 'Gerold Neuwirt';
        $mailer = new Zend_Mail('utf-8');
        $mailer->addTo($emailAdmin, $nameAdmin);
        $mailer->setSubject(sprintf('[' . $this->subject . '] %s', $titel));
        $mailer->setBodyHtml($templateTxt, 'utf8');
        $mailer->setFrom($email);
        $mailer->send();
    }
    
    public function sendErrorMail($titel, $message, $trace, $parameter, $languageCode = 'de_at')
    {
        $sitename = $this->configuration->general->sitename;
        
        $templatePath = APPLICATION_PATH . '/../data/templates/admin/email_error_' . $languageCode . '.txt';
        
        if (!is_file($templatePath)) {
            require_once 'Intern/Exception/Exception.php';
            throw new Intern_Exception('Missing template for contact mail - language code: '.$languageCode);
        }
        
        $templateTxt = file_get_contents($templatePath);
        
        //replace tags: [name], [email], [message]
        $templateTxt = str_replace('[sitename]', $sitename, $templateTxt);
        $templateTxt = str_replace('[titel]', $titel, $templateTxt);
        $templateTxt = str_replace('[message]', $message, $templateTxt);
        $templateTxt = str_replace('[trace]', $trace, $templateTxt);
        $templateTxt = str_replace('[parameter]', $parameter, $templateTxt);
        
        $emailAdmin = 'gerold.neuwirt@gmail.com';
        $nameAdmin = 'Gerold Neuwirt';
        $mailer = new Zend_Mail('utf-8');
        $mailer->addTo($emailAdmin, $nameAdmin);
        $mailer->setSubject(sprintf('[' . $this->subject . '] Error: %s', $titel));
        $mailer->setBodyHtml($templateTxt, 'utf8');
        $mailer->setFrom($this->configuration->mail->from);
        $mailer->send();
    }
    
    public function sendNewInseratMail($id_inserat, $id_source, $uploader, $languageCode = 'de_at')
    {
        $templatePath = APPLICATION_PATH . '/../data/templates/news/email_photo_' . $languageCode . '.txt';
        
        if (!is_file($templatePath)) {
            require_once 'Intern/Exception/Exception.php';
            throw new Intern_Exception('Missing template for new inserat mail - language code: '.$languageCode);
        }
        
        $templateTxt = file_get_contents($templatePath);
        
        $sitename = $this->configuration->general->sitename;
        $siteurl = $this->configuration->general->url;
        $email = 'gerold.neuwirt@gmail.com'; // TODO2 Emailverteiler mit Berücksichtigung der Region
        $name = 'Gerold Neuwirt';
        
        $config_table = new Application_Model_Config();
        $source = $config_table->getSource($id_source);
        
        $url = $siteurl . '/stream/index/inserat/' . $id_inserat;
        if (!isset($time))
            $time = date('Y-m-d H:i:s', time());
        
        //replace tags: [name], [email], [message]
        $templateTxt = str_replace('[name]', $name, $templateTxt);
        $templateTxt = str_replace('[source]', $source, $templateTxt);
        $templateTxt = str_replace('[url]', $url, $templateTxt);
        $templateTxt = str_replace('[siteurl]', $siteurl, $templateTxt);
        $templateTxt = str_replace('[sitename]', $sitename, $templateTxt);
        $templateTxt = str_replace('[uploader]', $uploader, $templateTxt);
        
        $mailer = new Zend_Mail('utf-8');
        $mailer->addTo($email, $name);
        $mailer->setSubject(sprintf('[' . $this->subject . '] Neues Inserat Nr. %s', $id_inserat));
        $mailer->setBodyHtml($templateTxt, 'utf8');
        $mailer->setFrom($this->configuration->mail->from);
        $mailer->send();
    }
    
    public function sendCorrectionMail($id_inserat, $correction, $message, $languageCode = 'de_at')
    {
        $templatePath = APPLICATION_PATH . '/../data/templates/admin/email_correction_' . $languageCode . '.txt';
        
        if (!is_file($templatePath)) {
            require_once 'Intern/Exception/Exception.php';
            throw new Intern_Exception('Missing template for new inserat mail - language code: '.$languageCode);
        }
        
        $templateTxt = file_get_contents($templatePath);
        
        $siteurl = $this->configuration->general->url;
        $url = $siteurl . '/stream/index/inserat/' . $id_inserat;
        
        $categories = array(
                    0 => 'Nicht definiert',
                    1 => 'Datum',
                    2 => 'Printmedium',
                    3 => 'Format',
                    4 => 'Partei',
                    5 => 'Preis'
                );
        
        //replace tags: [email], [message], [url]
        $templateTxt = str_replace('[url]', $url, $templateTxt);
        $templateTxt = str_replace('[correction]', $categories[$correction], $templateTxt);
        $templateTxt = str_replace('[message]', $message, $templateTxt);
        
        $emailAdmin = 'gerold.neuwirt@gmail.com';
        $nameAdmin = 'Gerold Neuwirt';
        $mailer = new Zend_Mail('utf-8');
        $mailer->addTo($emailAdmin, $nameAdmin);
        $mailer->setSubject(sprintf('[' . $this->subject . '] Korrektur für Inserat Nr. %s', $id_inserat));
        $mailer->setBodyHtml($templateTxt, 'utf8');
        $mailer->setFrom($this->configuration->mail->from);
        $mailer->send();
    }
    
}
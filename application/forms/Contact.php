<?php 
/**
* Form for contacting admin via email.
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

class Application_Form_Contact extends Zend_Dojo_Form
{
    public function init()
    {
        $this->setAction('/kontakt')
             ->setMethod('post')
             ->setAttrib('name', 'contactForm');
        Zend_Dojo::enableForm($this);
        
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', 
                array('tag' => 'dl', 'class' => 'captcha_form')),
            array('Description', array('placement' => 'prepend')),
            'DijitForm'
        ));
    
        $hidden = new Zend_Form_Element_Hidden('contact');
        
        $configuration = Zend_Registry::get('configuration');
        if (!Zend_Registry::get('auth')->hasIdentity()) {
            if ($configuration->general->captcha) {
                $pubKey = $configuration->general->pubKey;
                $privKey = $configuration->general->privKey;
                $recaptcha = new Zend_Service_ReCaptcha($pubKey, $privKey);
                $captcha = new Zend_Form_Element_Captcha('captcha',
                    array(
                        'label'         => 'Verifikation',
                        'captcha'        => 'ReCaptcha',
                        'captchaOptions' => array('captcha' => 'ReCaptcha', 'service' => $recaptcha),
                        'ignore' => true
                        )
                    );
            }
        }
        
        $token = new Zend_Form_Element_Hash('token');
        $token->setSalt(md5(uniqid(rand(), TRUE)));
        $token->setTimeout($configuration->auth->timeout);
        
        $this->addElement($hidden, 'contact')
             ->addElement($token)
             ->addElement('ValidationTextBox','name',
                            array(
                           'label'        => 'Absender',
                           'filters' => array('StringTrim', 'StripTags'),
                           'invalidMessage' => 'Ungültige Buchstaben.')
                         )
             ->addElement('ValidationTextBox', 'email',
                             array (
                             'label'       => 'Emailadresse', 
                             'trim'        => true,
                             'regExp' => '^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$', 
                             'invalidMessage' => 'Bitte eine gültige Emailadresse angeben.')
                          )
             ->addElement('ValidationTextBox','titel',
                            array(
                           'label'        => 'Betreff',
                           'filters' => array('StringTrim', 'StripTags'),
                           'invalidMessage' => 'Ungültige Buchstaben.')
                         )
            ->addElement('SimpleTextarea', 'message',
                            array(
                            'label'    => 'Mitteilung',
                            'filters' => array('StringTrim', 'StripTags'),
                            'style'    => 'width: 15em; height: 5em;')
                          );
            if (!Zend_Registry::get('auth')->hasIdentity())
                if ($configuration->general->captcha)
        $this->addElement($captcha);
              
        $this->addElement('SubmitButton','send',
                    array(
                            'required'   => false,
                            'ignore'     => true,
                            'label'      => 'E-Mail absenden',
                        )
                    );
    }
}
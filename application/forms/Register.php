<?php 
/**
* Form for registering user.
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

class Application_Form_Register extends Zend_Dojo_Form
{
    public function init()
    {
        $this->setAction('/user/register')
             ->setMethod('post')
             ->setAttrib('name', 'registerForm');
        Zend_Dojo::enableForm($this);
        
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', 
                array('tag' => 'dl', 'class' => 'captcha_form')),
            array('Description', array('placement' => 'prepend')),
            'DijitForm'
        ));
        
        $configuration = Zend_Registry::get('configuration');
        if ($configuration->general->captcha) {
            $pubKey = $configuration->general->pubKey;
            $privKey = $configuration->general->privKey;
            $recaptcha = new Zend_Service_ReCaptcha($pubKey, $privKey);
            $captcha = new Zend_Form_Element_Captcha('captcha',
                array(
                    'label'         => 'Verifikation',
                    'captcha'       => 'ReCaptcha',
                    'captchaOptions'=> array('captcha' => 'ReCaptcha', 'service' => $recaptcha),
                    'ignore' => true
                    )
                );
        }
        
        $token = new Zend_Form_Element_Hash('token');
        $token->setSalt(md5(uniqid(rand(), TRUE)));
        $token->setTimeout($configuration->auth->timeout);
        
        $hidden = new Zend_Form_Element_Hidden('id_user');
        
        $this->addElement($hidden, 'id_user')
             ->addElement($token)
             ->addElement('ValidationTextBox','username',
                            array(
                           'label'        => 'Benutzername',
                           'stringtolower'=> true,
                           'regExp'       => '^[A-Za-z0-9]{2,}$',
                           'filters' => array('StringTrim', 'StripTags'),
                           'invalidMessage' => 'Ungültige Buchstaben.')
                         )
             ->addElement('PasswordTextBox','password1',
                            array(
                           'label'        => 'Passwort',
                           'trim'         => true,
                           'regExp'       => '^[A-Za-z0-9_\-\+\*\?\.]{6,20}$',
                           'intermediateChanges' => 'false',
                           'invalidMessage' => 'Ungültiges Passwort; ungültige Buchstaben oder Länge liegt nicht zwischen 6 und 20 Zeichen')
                         )
             ->addElement('PasswordTextBox','password2',
                            array(
                           'label'        => 'Passwort bestätigen',
                           'trim'         => true,
                           'regExp'       => '^[A-Za-z0-9_\-\+\*\?\.]{6,20}$',
                           'constraints'  => array('other' => 'password1'),
                           'validator'    => 'confirmPassword',
                           'intermediateChanges' => 'false',
                           'invalidMessage' => 'Passwort stimmt nicht mit dem ersten Passwort überein')
                         )
             ->addElement('ValidationTextBox', 'user_email',
                             array (
                             'label'       => 'Emailadresse', 
                             'trim'        => true,
                             'regExp' => '^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$', 
                             'invalidMessage' => 'Bitte eine gültige Emailadresse angeben.')
                          );
                if ($configuration->general->captcha)
             $this->addElement($captcha);
                
             $this->addElement('SubmitButton','send',
                array(
                        'required'   => false,
                        'ignore'     => true,
                        'label'      => 'Registrieren',
                    )
                )
             ->addElement('SubmitButton','cancel',
                array(
                        'required'   => false,
                        'ignore'     => true,
                        'label'      => 'Abbrechen',
                    )
                );
    }
}
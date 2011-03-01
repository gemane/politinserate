<?php 
/**
* Form for log in the user.
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

class Application_Form_Login extends Zend_Dojo_Form
{

    public function init()
    {
        $this->setMethod('post')
             ->setAttrib('name', 'loginForm');
        Zend_Dojo::enableForm($this);
        
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', 
                array('tag' => 'dl', 'class' => 'inserat_form')),
            array('Description', array('placement' => 'prepend')),
            'DijitForm'
        ));
        
        $configuration = Zend_Registry::get('configuration');
        $token = new Zend_Form_Element_Hash('token');
        $token->setSalt(md5(uniqid(rand(), TRUE)));
        $token->setTimeout($configuration->auth->timeout);
        
        $hidden = new Zend_Form_Element_Hidden('login_hidden');
        
        $this->addElement($hidden, 'login_hidden')
             ->addElement($token)
             ->addElement('ValidationTextBox','username',
                            array(
                           'label'        => 'Benutzername',
                           'stringtolower'=> true, 
                           'regExp'       => '^[A-Za-z0-9]{2,}$',
                           'filters'      => array('StringTrim', 'StripTags'),
                           'invalidMessage' => 'Ungültige Buchstaben.')
                         )
             ->addElement('PasswordTextBox','password',
                            array(
                           'label'        => 'Passwort',
                           'trim'         => true,
                           'regExp'       => '^[A-Za-z0-9_\-\+\*\?\.]{6,20}$',
                           'invalidMessage' => 'Ungültiges Passwort; ungültige Buchstaben oder Länge liegt nicht zwischen 6 und 20 Zeichen')
                         )
                ->addElement('SubmitButton','send',
                    array(
                            'required'   => false,
                            'ignore'     => true,
                            'label'      => 'Login',
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
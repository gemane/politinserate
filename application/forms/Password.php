<?php 
/**
* Form for changing password
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

class Application_Form_Password extends Zend_Dojo_Form
{

    public function init()
    {
        $this->setMethod('post')
             ->setAttrib('name', 'passwordForm');
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
        
        $hidden = new Zend_Form_Element_Hidden('password_hidden');
        
        $this->addElement($hidden, 'password_hidden')
             ->addElement($token)
             ->addElement('PasswordTextBox','oldpassword',
                            array(
                           'label'        => 'Altes Passwort',
                           'trim'         => true,
                           'regExp'       => '^[A-Za-z0-9_\-\+\*\?\.]{6,20}$',
                           'invalidMessage' => 'Ungültiges Passwort; ungültige Buchstaben oder Länge liegt nicht zwischen 6 und 20 Zeichen')
                         )
             ->addElement('PasswordTextBox','newpassword1',
                            array(
                           'label'        => 'Neues Passwort',
                           'trim'         => true,
                           'regExp'       => '^[A-Za-z0-9_\-\+\*\?\.]{6,20}$',
                           'intermediateChanges' => 'false',
                           'invalidMessage' => 'Ungültiges Passwort; ungültige Buchstaben oder Länge liegt nicht zwischen 6 und 20 Zeichen')
                         )
             ->addElement('PasswordTextBox','newpassword2',
                            array(
                           'label'        => 'Passwort bestätigen',
                           'trim'         => true,
                           'regExp'       => '^[A-Za-z0-9_\-\+\*\?\.]{6,20}$',
                           'constraints'  => array('other' => 'newpassword1'),
                           'validator'    => 'confirmPassword',
                           'intermediateChanges' => 'false',
                           'invalidMessage' => 'Passwort stimmt nicht mit dem ersten Passwort überein')
                         )
                ->addElement('SubmitButton','send',
                    array(
                            'required'   => false,
                            'ignore'     => true,
                            'label'      => 'Ändern',
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
<?php 
/**
* Form when password is lost
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

class Application_Form_Lost extends Zend_Dojo_Form
{
    public function init()
    {
        $this->setMethod('post')
             ->setAttrib('name', 'lostForm');
        Zend_Dojo::enableForm($this);
        
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', 
                array('tag' => 'dl', 'class' => 'inserat_form')),
            array('Description', array('placement' => 'prepend')),
            'DijitForm'
        ));
    
        $hidden = new Zend_Form_Element_Hidden('lost_hidden');
        
        $configuration = Zend_Registry::get('configuration');
        $token = new Zend_Form_Element_Hash('token');
        $token->setSalt(md5(uniqid(rand(), TRUE)));
        $token->setTimeout($configuration->auth->timeout);
        
        $this->addElement($hidden, 'lost_hidden')
             ->addElement($token)
             ->addElement('ValidationTextBox', 'email',
                             array (
                             'label'       => 'Emailadresse', 
                             'trim'        => true,
                             'regExp' => '^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$', 
                             'invalidMessage' => 'Bitte eine gültige Emailadresse angeben.')
                          )
              
             ->addElement('SubmitButton','send',
                    array(
                            'required'   => false,
                            'ignore'     => true,
                            'label'      => 'E-Mail absenden',
                        )
                    );
    }
}
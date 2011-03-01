<?php 
/**
* Form for correction suggestion of advertisement
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

class Application_Form_Correction extends Zend_Dojo_Form
{
    public function init()
    {
        $this->setMethod('post')
             ->setAttrib('name', 'correctForm');
        Zend_Dojo::enableForm($this);
        
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', 
                array('tag' => 'dl', 'class' => 'inserat_form')),
            array('Description', array('placement' => 'prepend')),
            'DijitForm'
        ));
        
        $categories = array(
                    0 => '(Wähle Kategorie)',
                    1 => 'Datum',
                    2 => 'Printmedium',
                    3 => 'Format',
                    4 => 'Partei',
                    5 => 'Preis'
                );
        
        $hidden = new Zend_Form_Element_Hidden('correction');
        
        $configuration = Zend_Registry::get('configuration');
        $token = new Zend_Form_Element_Hash('token');
        $token->setSalt(md5(uniqid(rand(), TRUE)));
        $token->setTimeout($configuration->auth->timeout);
        
        $id_inserat = new Zend_Form_Element_Hidden('id_inserat');
        
        $this->addElement($id_inserat, 'id_inserat')
             ->addElement($hidden, 'correction')
             ->addElement($token)
             ->addElement('FilteringSelect','category',
                            array(
                           'label'        => 'Kategorie',
                           'multiOptions' => $categories)
                          )
            ->addElement('SimpleTextarea', 'message',
                            array(
                            'label'    => 'Beschreibung',
                            'filters' => array('StringTrim', 'StripTags'),
                            'style'    => 'width: 15em; height: 3em;')
                          )
            ->addElement('SubmitButton','send',
                    array(
                            'required'   => false,
                            'ignore'     => true,
                            'label'      => 'Korrektur absenden',
                        )
                    );
    }
}
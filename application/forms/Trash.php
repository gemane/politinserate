<?php

/**
* Button for moving inserat to trash.
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

class Application_Form_Trash extends Zend_Dojo_Form
{
    
    public function init()
    {
        $this->setMethod('post')
             ->setAttrib('name', 'inseratForm');
        Zend_Dojo::enableForm($this);
        
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', 
                array('tag' => 'dl', 'class' => 'inserat_form')),
            array('Description', array('placement' => 'prepend')),
            'DijitForm'
        ));
        
        $hidden = new Zend_Form_Element_Hidden('id_inserat');
        
        $this   ->addElement($hidden, 'id_inserat')
                ->addElement('SubmitButton','trash',
                    array(
                            'required'   => false,
                            'ignore'     => true,
                            'label'      => 'Unbrauchbar',
                        )
                    )
                ->addElement('SubmitButton','cancel',
                    array(
                            'required'   => false,
                            'ignore'     => true,
                            'label'      => 'Abbrechen',
                        )
                    );
                    // TODO1 Button mit "Nicht identisch" unterhalb anordnen, mit Hinweis, dass die Seitennummer nicht berücksichtigt wird
        
    }
}
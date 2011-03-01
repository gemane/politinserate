<?php
/**
* Form for additional fields of types in printmedium.
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

class Application_Form_Type extends Zend_Dojo_Form_SubForm
{
    
    public function __construct($id)
    {
        parent::__construct($id);
        
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', 
                array('tag' => 'dl', 'class' => 'inserat_form')),
            array('Description', array('placement' => 'prepend'))
        ));
        
        $hidden = new Zend_Form_Element_Hidden('newName' . $id);
        
        $this->addDecorator(new Personal_Decorator_HrSeparator());
        $this->addElement($hidden, 'newName' . $id)
             ->addElement('ValidationTextBox', 'printmedium_type_name' . $id,
                            array(
                                'value'             => 'Magazin ' . $id,
                                'label'             => 'Typ',
                                'propercase'        => true,
                                'filters'           => array('StringTrim', 'StripTags'),
                                'promptMessage'     => 'Art des Printmediums (Tageszeitung, Magazin,.. )',
                                'invalidMessage'    => 'Ungültige Zeichen.')
                         )
             ->addElement(
                        'NumberSpinner', 'printmedium_columns_width' . $id,
                            array(
                                'value'             => '50',
                                'label'             => 'Spaltenbreite [mm]',
                                'smallDelta'        => 1,
                                'largeDelta'        => 10,
                                'defaultTimeout'    => 500,
                                'timeoutChangeRate' => 100,
                                'min'               => 1,
                                'max'               => 999,
                                'places'            => 0,
                                'maxlength'         => 3
                            )
                        )
             ->addElement('NumberSpinner', 'printmedium_width' . $id,
                            array(
                                'value'             => '100',
                                'label'             => 'Gesamtbreite [mm]',
                                'smallDelta'        => 1,
                                'largeDelta'        => 10,
                                'defaultTimeout'    => 500,
                                'timeoutChangeRate' => 100,
                                'min'               => 1,
                                'max'               => 999,
                                'places'            => 0,
                                'maxlength'         => 3
                            )
                        )
             ->addElement('NumberSpinner', 'printmedium_height' . $id,
                            array(
                                'value'             => '100',
                                'label'             => 'Gesamthöhe [mm]',
                                'smallDelta'        => 1,
                                'largeDelta'        => 10,
                                'defaultTimeout'    => 500,
                                'timeoutChangeRate' => 100,
                                'min'               => 1,
                                'max'               => 999,
                                'places'            => 0,
                                'maxlength'         => 3
                            )
                        );
        
    }
    
}

class Personal_Decorator_HrSeparator extends Zend_Form_Decorator_HtmlTag
{
    protected $_placement = self::PREPEND;
    
    protected $_tag = 'hr';
}


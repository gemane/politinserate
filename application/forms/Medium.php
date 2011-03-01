<?php

/**
* Form for selecting printmedium and region.
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

class Application_Form_Medium extends Zend_Dojo_Form
{
    
    public function init()
    {
        $this->setMethod('post')
             ->setAttrib('name', 'mediumForm');
        Zend_Dojo::enableForm($this);
        
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', 
                array('tag' => 'dl', 'class' => 'inserat_form')),
            array('Description', array('placement' => 'prepend')),
            'DijitForm'
        ));
        
        $table_medium = new Application_Model_Printmedium();
        $printmedium['0'] = '(Wähle Printmedium)';
        foreach ($table_medium->getAllMedium() as $key)
            $printmedium[$key['id_printmedium']] = $key['printmedium'];
        
        $table_config = new Application_Model_Config();
        foreach ($table_config->getAllRegion() as $key)
            $region_printmedium_bit[$key['region_abb']] = $key['region'];
        
        $configuration = Zend_Registry::get('configuration');
        $token = new Zend_Form_Element_Hash('token');
        $token->setSalt(md5(uniqid(rand(), TRUE)));
        $token->setTimeout($configuration->auth->timeout);
        
        $id_inserat = new Zend_Form_Element_Hidden('id_inserat');
        
        $this->addElement($id_inserat, 'id_inserat')
             ->addElement($token)
             ->addElement('FilteringSelect','id_printmedium',
                            array(
                           'label'        => 'Printmedium',
                           'required'     => true,
                           'multiOptions' => $printmedium)
                          )
             ->addElement('DateTextBox','print_date',
                            array(
                         'label'          => 'Datum',
                         'required'       => true,
                         'invalidMessage' => 'Ungültiges Datum spezifiziert.',
                         'formatLength'   => 'long')
                         )
             ->addElement(
                        'NumberSpinner',
                        'print_page',
                        array(
                            'label'             => 'Seite',
                            'smallDelta'        => 1,
                            'largeDelta'        => 5,
                            'defaultTimeout'    => 500,
                            'timeoutChangeRate' => 100,
                            'min'               => 1,
                            'max'               => 100,
                            'places'            => 0,
                            'maxlength'         => 3,
                        )
                    );
             foreach ($region_printmedium_bit as $key => $value) {
        $this->addElement('CheckBox', $key,
                array(
                    'label'          => $value,
                    'checkedValue'   => '1',
                    'uncheckedValue' => '0',
                    'checked'        => false)
                );
                 $key_array[] = $key;
             }
        $this->addDisplayGroup(
                    $key_array,
                    'id_region_printmedium_bit', 
                    array('legend' => 'Regionen')
                );
                          
        $this->addElement('SubmitButton','send',
                    array(
                            'required'   => false,
                            'ignore'     => true,
                            'label'      => 'Weiter',
                        )
                    )
                ->addElement('SubmitButton','trash',
                    array(
                            'required'   => false,
                            'ignore'     => true,
                            'label'      => 'Unbrauchbar',
                        )
                    );
        
    }
}
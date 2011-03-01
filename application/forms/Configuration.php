<?php 
/**
* Form for configuring user profile.
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

class Application_Form_Configuration extends Zend_Dojo_Form
{
    public function init()
    {
        $this->setMethod('post')
             ->setAttrib('name', 'configurationForm');
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
        $region['0'] = '(Wähle Region)';
        foreach ($table_config->getAllRegion() as $key)
            $region[$key['id_config']] = $key['region'];
        
        $hidden = new Zend_Form_Element_Hidden('config');
        
        $this->addElement($hidden, 'config')
             ->addElement('ValidationTextBox','user_fullname',
                            array(
                           'label'        => 'Anrede',
                           'filters' => array('StringTrim', 'StripTags'),
                           'promptMessage' => 'Dient zur Anrede, wenn angemeldet.',
                           'invalidMessage' => 'Ungültige Buchstaben.')
                         )
                         // TODO2 Twitteraccount zuordnen
             ->addElement('FilteringSelect','prefered_printmedium',
                            array(
                           'label'        => 'Printmedium',
                           'multiOptions' => $printmedium)
                          )
             ->addElement('FilteringSelect','prefered_region',
                            array(
                           'label'        => 'Region',
                           'multiOptions' => $region)
                          )
        
                ->addElement('SubmitButton','send',
                    array(
                            'required'   => false,
                            'ignore'     => true,
                            'label'      => 'Speichern',
                        )
                    )
                ->addElement('SubmitButton','cancel',
                    array(
                            'required'   => false,
                            'ignore'     => true,
                            'label'      => 'Zurücksetzen',
                        )
                    );
    }
}
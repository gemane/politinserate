<?php
/**
* Form for selecting party and payer.
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

class Application_Form_Party extends Zend_Dojo_Form
{
    
    public function init()
    {
        $this->setMethod('post')
             ->setAttrib('name', 'partyForm');
        Zend_Dojo::enableForm($this);
        
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', 
                array('tag' => 'dl', 'class' => 'inserat_form')),
            array('Description', array('placement' => 'prepend')),
            'DijitForm'
        ));
        
        $table_config = new Application_Model_Config();
        $party['0'] = '(Wähle Partei)';
        foreach ($table_config->getAllParty() as $key)
            $party[$key['id_config']] = $key['party'];
        
        $region['0'] = '(Wähle Region)';
        foreach ($table_config->getAllRegion() as $key)
            $region[$key['id_config']] = $key['region'];

        $government['0'] = 'Partei (siehe oben)';
        foreach ($table_config->getAllGovernment() as $key)
            $government[$key['id_config']] = $key['government'];
        
        $configuration = Zend_Registry::get('configuration');
        $token = new Zend_Form_Element_Hash('token');
        $token->setSalt(md5(uniqid(rand(), TRUE)));
        $token->setTimeout($configuration->auth->timeout);
        
        $hidden = new Zend_Form_Element_Hidden('id_inserat');
        
        $this->addElement($hidden, 'id_inserat')
             ->addElement($token)
             ->addElement('FilteringSelect','id_party',
                            array(
                           'label'        => 'Beworbene Partei',
                           'required'     => true,
                           'multiOptions' => $party)
                          )
             ->addElement('FilteringSelect','id_region_party',
                            array(
                           'label'        => 'Region',
                           'required'     => true,
                           'multiOptions' => $region)
                          )
             ->addElement('FilteringSelect','id_government',
                            array(
                           'label'        => 'Bezahlt von',
                           'required'     => true,
                           'multiOptions' => $government)
                          )
             ->addElement('CheckBox','politician',
                    array(
                        'label'          => 'Politiker sichtbar',
                        'checkedValue'   => '1',
                        'uncheckedValue' => '0',
                        'checked'        => false)
                    )
                          
                ->addElement('SubmitButton','send',
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
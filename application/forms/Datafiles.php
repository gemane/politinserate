<?php
/**
* Form for uploading datafiles and defining parameters.
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

class Application_Form_Datafiles extends Zend_Dojo_Form
{
    
    public function __construct($id_datafile)
    {
        $this->setMethod('post')
             ->setAttrib('name', 'datafileForm')
             ->setAttrib('enctype', 'multipart/form-data');
        Zend_Dojo::enableForm($this);
        
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', 
                array('tag' => 'dl', 'class' => 'datafile_form')),
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
            
        for ($y=2009; $y<=2020; $y++)
            $year[$y] = $y;
        
        $configuration = Zend_Registry::get('configuration');
        if (0 == $id_datafile) {
            $path = new Zend_Form_Element_File('path');
            $path->setLabel('Datei')
                 ->setDescription('Das Datenfile darf maximal 8MB gross sein.')
                 ->setDestination(APPLICATION_PATH .'/../data/tarife/temp')
                 ->addValidator('Extension', false, 'pdf')
                 ->addValidator('Regex', false, array('pattern' => '/^[A-Za-z0-9_\/\-\.]{6,200}$/i')) // Only checks temp-file and not original
                 ->addValidator('Count', false, 2)
                 ->addValidator('Size', false, $configuration->general->filesize)
                 ->setValueDisabled(true)
                 ->setMaxFileSize($configuration->general->filesize);
        }
        
        $token = new Zend_Form_Element_Hash('token');
        $token->setSalt(md5(uniqid(rand(), TRUE)));
        $token->setTimeout($configuration->auth->timeout);
        
        $hidden = new Zend_Form_Element_Hidden('config_datafile');
        
        $this->addElement($hidden, 'config_datafile')
             ->addElement($token)
             ->addElement('FilteringSelect','id_printmedium',
                            array(
                           'label'        => 'Printmedium',
                           'multiOptions' => $printmedium)
                          )
             ->addElement('FilteringSelect','year',
                            array(
                           'label'        => 'Jahr',
                           'multiOptions' => $year)
                          )
                ->addElement('DateTextBox','date_from',
                    array(
                        'label'         => 'Gültig von',
                        'invalidMessage' => 'Ungültiges Datum spezifiziert.',
                        'formatLength'  => 'medium')
                    )
                ->addElement('DateTextBox','date_to',
                    array(
                        'label'         => 'Gültig bis',
                        'invalidMessage' => 'Ungültiges Datum spezifiziert.',
                        'formatLength'  => 'medium')
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
                    array('legend' => 'Region')
                );
        
        if (0 == $id_datafile) 
            $this->addElement($path, 'path');
             
        $this->addElement('SubmitButton','send',
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
                            'label'      => 'Abbrechen',
                        )
                    );
        
        parent::__construct($id_datafile);
    }
}
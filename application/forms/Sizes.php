<?php

/**
* Form for adding tariff from datafile.
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

class Application_Form_Sizes extends Zend_Dojo_Form
{
    
    protected $_standardElementDecorator = array(
                  'ViewHelper',
                  'Description',
                array('HtmlTag', array('tag' => 'dd')),
                  array('Label', array('tag' => 'dt', 'optionalSuffix' => '', 'requiredSuffix' => '*'))
                  );
    
    public function  __construct($id_printmedium)
    {
        $this->setMethod('post')
             ->setAttrib('name', 'sizesForm');
        Zend_Dojo::enableForm($this);
        
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', 
                array('tag' => 'dl', 'class' => 'inserat_form')),
            array('Description', array('placement' => 'prepend')),
            'DijitForm'
        ));
        
        $table_datafile = new Application_Model_Datafile();
        $datafile_list['0'] = '(Wähle Datenfile)';
        foreach ($table_datafile->getAllPath() as $key) {
            if (1 == $key['id_datafile']) {
                $datafile_list[$key['id_datafile']] = 'Choose Datafile';
            } else {
                $datafile_list[$key['id_datafile']] = $key['path'];
            }
        }
        
        $table_medium = new Application_Model_Printmedium();
        $printmedium['0'] = '(Wähle Printmedium)';
        foreach ($table_medium->getAllMedium() as $key)
            $printmedium[$key['id_printmedium']] = $key['printmedium'];
        
        $table_config = new Application_Model_Config();
        foreach ($table_config->getAllSizes() as $key)
            $size_images[$key['id_config']] = '<img src="/images/sizes/' . $key['size_image'] . '_s.jpg" />';
            
        $table_config = new Application_Model_Config();
        foreach ($table_config->getAllHeights() as $key)
            $height_images[$key['id_config']] = '<img src="/images/heights/' . $key['height_image'] . '_s.jpg" />';
        
        $table_types = new Application_Model_PrintmediumTypes();
        foreach ($table_types->getTypesByPrintmedium($id_printmedium) as $key)
            $type[$key['id_printmedium_type']] = $key['printmedium_type_name'];
        
        $configuration = Zend_Registry::get('configuration');
        $token = new Zend_Form_Element_Hash('token');
        $token->setSalt(md5(uniqid(rand(), TRUE)));
        $token->setTimeout($configuration->auth->timeout);
        
        $hidden = new Zend_Form_Element_Hidden('config_tariff');
        
        $this   ->addElement($hidden, 'config_tariff')
                ->addElement($token)
                ->addElement('ValidationTextBox','size',
                    array(
                        'label'         => 'Bezeichnung',
                        'filters' => array('StringTrim', 'StripTags'),
                        'promptMessage' => 'Name entsprechend dem<br />Tarifblatt des Printmediums',
                        'invalidMessage' => 'Ungültige Zeichen.')
                    )
             ->addElement('CheckBox','cover',
                    array(
                        'label'          => 'Titelseite',
                        'checkedValue'   => '1',
                        'uncheckedValue' => '0',
                        'checked'        => false)
                    )
             ->addElement('FilteringSelect','id_printmedium_type',
                    array(
                        'label'          => 'Format',
                        'required'       => true,
                        'multiOptions'   => $type)
                    )
             ->addElement(
                        'NumberSpinner', 'size_height',
                            array(
                                'value'             => '100',
                                'label'             => 'Höhe [mm]',
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
                ->addElement('RadioButton', 'id_height_image',
                    array(
                        'label'         => '',
                        'escape'        => false,
                         'separator'    => '',
                        'multiOptions'  => $height_images)
                    )
                ->addDisplayGroup(
                    array(
                        'id_height_image'),
                        'height_images', 
                        array('legend' => 'Höhe graphisch')
                    )
                ->addElement('CheckBox','Mon',
                    array(
                        'label'          => 'Mo',
                        'checkedValue'   => '1',
                        'uncheckedValue' => '0',
                        'checked'        => true)
                    )
                ->addElement('CheckBox','Tue',
                    array(
                        'label'          => 'Di',
                        'checkedValue'   => '1',
                        'uncheckedValue' => '0',
                        'checked'        => true)
                    )
                ->addElement('CheckBox','Wed',
                    array(
                        'label'          => 'Mi',
                        'checkedValue'   => '1',
                        'uncheckedValue' => '0',
                        'checked'        => true)
                    )
                ->addElement('CheckBox','Thu',
                    array(
                        'label'          => 'Do',
                        'checkedValue'   => '1',
                        'uncheckedValue' => '0',
                        'checked'        => true)
                    )
                ->addElement('CheckBox','Fri',
                    array(
                        'label'          => 'Fr',
                        'checkedValue'   => '1',
                        'uncheckedValue' => '0',
                        'checked'        => true)
                    )
                ->addElement('CheckBox','Sat',
                    array(
                        'label'          => 'Sa',
                        'checkedValue'   => '1',
                        'uncheckedValue' => '0',
                        'checked'        => true)
                    )
                ->addElement('CheckBox','Sun',
                    array(
                        'label'          => 'So', // TODO2 So & Fei
                        'checkedValue'   => '1',
                        'uncheckedValue' => '0',
                        'checked'        => true)
                    )
                ->addDisplayGroup(
                    array(
                        'Mon', 
                        'Tue', 
                        'Wed', 
                        'Thu', 
                        'Fri', 
                        'Sat', 
                        'Sun'),
                        'weekdays', 
                        array('legend' => 'Wochentage')
                    )
                ->addElement('CurrencyTextBox','price',
                    array(
                        'label'         => 'Preis (EUR)',
                        'promptMessage' => 'Preis für die gesamte Breite und pro mm Höhe',
                        'places'        => 0,
                        'invalidMessage' => 'Ungültige Zahl.',
                        'constraints'   => array('min' => 0 , 'max' => 100000))
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
                            'label'      => 'Abbrechen',
                        )
                    );
        
        parent::__construct($id_printmedium);
    }
}
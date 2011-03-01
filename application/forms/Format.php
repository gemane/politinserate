<?php

/**
* Form for selecting format size of inserat.
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

class Application_Form_Format extends Zend_Dojo_Form
{
    
    public function __construct($id_inserat, $cover)
    {
        $this->setMethod('post')
             ->setAttrib('name', 'formatForm');
        Zend_Dojo::enableForm($this);
        
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', 
                array('tag' => 'dl', 'class' => 'inserat_form')),
            array('Description', array('placement' => 'prepend')),
            'DijitForm'
        ));
        $table_inserat = new Application_Model_Inserate();
        $table_tariff = new Application_Model_Tariff();
        $table_types = new Application_Model_PrintmediumTypes();
        $table_printmedium = new Application_Model_Printmedium();
        
        $printmedium_types = $table_tariff->getPrintmediumTypeByInserat($id_inserat);
        
        foreach ($printmedium_types as $type) {
            $heights = $table_tariff->getHeights($id_inserat, $type['printmedium_type_position'], $cover);
            foreach ($heights as $key)
                $height_images[$type['id_printmedium_type']][$key['id_size']] = '<img src="/images/heights/' . $key['height_image'] . '_s.jpg" rel="imgtip[' . $key['id_size'] . ']" />';
            
            $categories[$type['id_printmedium_type']]['printmedium_type_name'] = $type['printmedium_type_name'];
            $categories[$type['id_printmedium_type']]['printmedium'] = $table_printmedium->getPrintmedium($type['id_printmedium']);
            $config = new Application_Model_Config();
            $region_bit = $table_inserat->getRegionPrintmedium($id_inserat);
            $categories[$type['id_printmedium_type']]['region'] = $config->formatRegion($region_bit);
            $day = date('D', strtotime($table_inserat->getPrintDate($id_inserat)));
            $table_weekdays = new Application_Model_Weekdays();
            $categories[$type['id_printmedium_type']]['weekday'] = $table_weekdays->getWeekdayName($day);
        }
        
        $configuration = Zend_Registry::get('configuration');
        $token = new Zend_Form_Element_Hash('token');
        $token->setSalt(md5(uniqid(rand(), TRUE)));
        $token->setTimeout($configuration->auth->timeout);
        
        $hidden = new Zend_Form_Element_Hidden('id_inserat');
        $format_empty = new Zend_Form_Element_Hidden('format_empty');
        $cover_status = new Zend_Form_Element_Hidden('cover');
        
        $this   ->addElement($hidden, 'id_inserat')
                ->addElement($token)
                ->addElement($format_empty, 'format_empty')
                ->addElement($cover_status, 'cover');
        
        if (empty($height_images)) {
            $format_empty->setValue('true');
        } else {
            $format_empty->setValue('false');
            
            $cover_status->setValue($cover);
            if (0 == $cover) {
        $this   ->addElement('SubmitButton','yes_cover',
                    array(
                            'required'   => false,
                            'ignore'     => true,
                            'label'      => '-> Titelblatt',
                        )
                    );
            }
            if (1 == $cover) {
        $this   ->addElement('SubmitButton','no_cover',
                    array(
                            'required'   => false,
                            'ignore'     => true,
                            'label'      => '-> Kein Titelblatt',
                        )
                    );
            }
            
            //foreach ($printmedium_types as $type) {
            $type = $printmedium_types[0];
                $max_columns = $table_types->getColumnsByInserat($id_inserat, $type['printmedium_type_position']);
                
                $description = 'Auswahl für das Printmedium ' . 
                    $categories[$type['id_printmedium_type']]['printmedium'] . '/' . 
                    $categories[$type['id_printmedium_type']]['printmedium_type_name'] . ' in ' . 
                    $categories[$type['id_printmedium_type']]['region'] . ' am ' . 
                    $categories[$type['id_printmedium_type']]['weekday'];
                $description = '';
                
        $this   ->addElement('NumberSpinner', 'pages', // TODO1 benötigt index
                                array(
                                    'value'             => '1',
                                    'label'             => 'Anzahl Seiten',
                                    'smallDelta'        => 1,
                                    'largeDelta'        => 10,
                                    'defaultTimeout'    => 500,
                                    'timeoutChangeRate' => 100,
                                    'min'               => 1,
                                    'max'               => 99,
                                    'places'            => 0,
                                    'maxlength'         => 2
                                )
                            )
              ->addElement('NumberSpinner', 'inserat_columns',
                                array(
                                    'value'             => $max_columns,
                                    'label'             => 'Spalten (=Breite)',
                                    'smallDelta'        => 1,
                                    'largeDelta'        => 1,
                                    'defaultTimeout'    => 500,
                                    'timeoutChangeRate' => 100,
                                    'min'               => 1,
                                    'max'               => $max_columns,
                                    'places'            => 1,
                                    'maxlength'         => 3
                                )
                            )
                ->addElement('RadioButton', 'id_size',
                    array(
                        'label'         => $description,
                        'escape'        => false,
                        'separator'    => '',
                        'multiOptions'  => $height_images[$type['id_printmedium_type']])
                    )
                ->addDisplayGroup(
                    array('id_size'),
                        'size_images', 
                        array('legend' => 'Höhe: ' . $type['printmedium_type_name'])
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
            //}
        }
        if (empty($height_images)) {
            $format_empty->setValue('true');
        } else {
            $format_empty->setValue('false');
        }
        
        parent::__construct($id_inserat, $cover);
    }
    
}

<?php
/**
* Form for adding printmedium.
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

class Application_Form_Printmedium extends Zend_Dojo_Form
{
    
    public function init()
    {
        $this->setMethod('post')
             ->setAttrib('name', 'printmediumForm');
        Zend_Dojo::enableForm($this);
        
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', 
                array('tag' => 'dl', 'class' => 'inserat_form')),
            array('Description', array('placement' => 'prepend')),
            'DijitForm'
        ));
        
        $configuration = Zend_Registry::get('configuration');
        $token = new Zend_Form_Element_Hash('token');
        $token->setSalt(md5(uniqid(rand(), TRUE)));
        $token->setTimeout($configuration->auth->timeout);
        
        $hidden = new Zend_Form_Element_Hidden('config_printmedium');
        $id = new Zend_Form_Element_Hidden('id');
        $id->setValue (1);
        
        $this->addElement($hidden, 'config_printmedium')
             ->addElement($id, 'id')
             ->addElement($token)
             ->addElement('ValidationTextBox','printmedium',
                            array(
                                'label'             => 'Printmedium',
                                'propercase'        => true,
                                'filters'           => array('StringTrim', 'StripTags'),
                                'promptMessage'     => 'Titel des Printmediums',
                                'invalidMessage'    => 'Ungültige Zeichen.',
                                'order'             => 3,
                            )
                         )
             /*->addElement('ValidationTextBox','keywords_printmedium',
                            array(
                                'label'             => 'Synonyme',
                                'filters'           => array('StringTrim', 'StripTags'),
                                'stringtolower'     => true,
                                'regExp'            => '^[A-Za-z0-9\,\;\ ]{4,}$',
                                'promptMessage'     => 'Schlagwörter die zur Suche in Kurznachrichten<br />(z.B. Twitter) verwendet werden.',
                                'invalidMessage'    => 'Ungültige Zeichen.',
                                'order'             => 4,
                           )
                         )
             ->addElement('ValidationTextBox','color_printmedium',
                            array(
                                'label'             => 'Farbe',
                                'trim'              => true,
                                'stringtolower'     => true,
                                'regExp'            => '^#?([a-f]|[A-F]|[0-9]){3}(([a-f]|[A-F]|[0-9]){3})?$',
                                'promptMessage'     => 'Farbe zur Darstellung des<br />entsprechenden Balkens in Diagrammen.',
                                'invalidMessage'    => 'Nur 6 stelliger hexadezimal Code erlaubt.',
                                'order'             => 5,)
                         )*/
                         
             ->addElement('ValidationTextBox','printmedium_type_name',
                            array(
                                'value'             => 'Tageszeitung',
                                'label'             => 'Typ',
                                'propercase'        => true,
                                'filters'           => array('StringTrim', 'StripTags'),
                                'promptMessage'     => 'Art des Printmediums (Tageszeitung, Magazin,.. )',
                                'invalidMessage'    => 'Ungültige Zeichen.',
                                'order'             => 10)
                         )
             ->addElement(
                        'NumberSpinner', 'printmedium_columns_width',
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
                                'maxlength'         => 3,
                                'order'             => 11
                            )
                        )
             ->addElement(
                        'NumberSpinner', 'printmedium_width',
                            array(
                                'value'             => '100',
                                'label'             => 'Satz Breite [mm]',
                                'smallDelta'        => 1,
                                'largeDelta'        => 10,
                                'defaultTimeout'    => 500,
                                'timeoutChangeRate' => 100,
                                'min'               => 1,
                                'max'               => 999,
                                'places'            => 0,
                                'maxlength'         => 3,
                                'order'             => 12
                            )
                        )
             ->addElement(
                        'NumberSpinner', 'printmedium_height',
                            array(
                                'value'             => '100',
                                'label'             => 'Satz Höhe [mm]',
                                'smallDelta'        => 1,
                                'largeDelta'        => 10,
                                'defaultTimeout'    => 500,
                                'timeoutChangeRate' => 100,
                                'min'               => 1,
                                'max'               => 999,
                                'places'            => 0,
                                'maxlength'         => 3,
                                'order'             => 13
                            )
                        )
            
            ->addElement('Button', 'addElement', 
                    array(
                            'label'     => 'Weiteres Format hinzufügen',
                            'order'     => 91,
                        )
                    )
            ->addElement('Button', 'removeElement', 
                    array(
                            'label'     => 'Letztes Format entfernen',
                            'order'     => 92,
                        )
                    )
            ->addDisplayGroup(
                    array('printmedium_type_name', 'printmedium_columns_width', 'printmedium_width', 'printmedium_height', 'addElement'),
                    'printmedium_class', 
                    array(  'legend'    => 'Format(e)',
                            'order'     => 6)
                )
                    
                ->addElement('SubmitButton','send',
                    array(
                            'required'   => false,
                            'ignore'     => true,
                            'label'      => 'Speichern',
                            'order'     => 93,
                        )
                    )
                ->addElement('SubmitButton','cancel',
                    array(
                            'required'   => false,
                            'ignore'     => true,
                            'label'      => 'Abbrechen',
                            'order'     => 94,
                        )
                    );
        
    }
    
    /**
    * After post, pre validation hook
    * 
    * Finds all fields where name includes 'printmedium_type' and uses addNewField to add
    * them to the form object
    * 
    * @param array $data $_GET or $_POST
    */
    public function preValidation(array $data) 
    {
        // array_filter callback
        function findFields($field) {
            // return field names that include 'newName'
            if (strpos($field, 'newName') !== false) {
                return $field;
            }
        }
        
        // Search $data for dynamically added fields using findFields callback
        $newFields = array_filter(array_keys($data), 'findFields');
        
        foreach ($newFields as $fieldName) {
            // strip the id number off of the field name and use it to set new order
            $id = ltrim($fieldName, 'newName');
            $order = 6 * $id + 20 - 6;
            $this->addNewField($id, $data[$fieldName], $order);
        }
    }
    
    /**
    * Adds new fields to form
    *
    * @param string $name
    * @param string $value
    * @param int    $order
    */
    public function addNewField($id, $value, $order) 
    {
        $this->addElement('ValidationTextBox', 'printmedium_type_name' . $id,
                            array(
                                'value'             => 'Magazin',
                                'label'             => 'Typ',
                                'propercase'        => true,
                                'filters'           => array('StringTrim', 'StripTags'),
                                'promptMessage'     => 'Art des Printmediums (Tageszeitung, Magazin,.. )',
                                'invalidMessage'    => 'Ungültige Zeichen.',
                                'order'             => $order)
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
                                'maxlength'         => 3,
                                'order'             => $order+1
                            )
                        )
             ->addElement(
                        'NumberSpinner', 'printmedium_width' . $id,
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
                                'maxlength'         => 3,
                                'order'             => $order + 2
                            )
                        )
             ->addElement(
                        'NumberSpinner', 'printmedium_height' . $id,
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
                                'maxlength'         => 3,
                                'order'             => $order +3 
                          )
                      );
             }

}


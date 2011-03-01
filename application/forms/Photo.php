<?php
/**
* Form for uploading photos.
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

class Application_Form_Photo extends Zend_Dojo_Form
{
    
    public function init()
    {
        $this->setMethod('post')
             ->setAttrib('name', 'photoForm');
        Zend_Dojo::enableForm($this);
        
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', 
                array('tag' => 'dl', 'class' => 'inserat_form')),
            array('Description', array('placement' => 'prepend')),
            'DijitForm'
        ));
        
        $configuration = Zend_Registry::get('configuration');
        
        $url_image = new Zend_Form_Element_File('url_image');
        $url_image->setLabel('Datei')
                  ->setDescription('Das Inserat darf maximal 8MB gross sein und sollte wegen Lesbarkeit eine Auflösung von mindestens 700x700 Bildpunkte haben. Mögliche Bildformate sind "jpg", "gif" und "png".')
                  ->setDestination(APPLICATION_PATH .'/../data/uploads/images/original/')
                  ->setRequired(true)
                  ->addValidator('Count', false, 1)
                  ->addValidator('Size', false, $configuration->general->filesize) // max 8 MB
                  ->addValidator('Extension', false, 'jpg,png,gif')
                  ->addValidator('MimeType', false, 'image')
                  ->addValidator('IsImage', false)
                  ->setValueDisabled(true)
                  ->setMaxFileSize($configuration->general->filesize);
        
        $token = new Zend_Form_Element_Hash('token');
        $token->setSalt(md5(uniqid(rand(), TRUE)));
        $token->setTimeout($configuration->auth->timeout);
        
        $hidden = new Zend_Form_Element_Hidden('id_inserat');
        
        $this   ->addElement($hidden, 'id_inserat')
                ->addElement($token)
                ->addElement($url_image, 'url_image')
             
                ->addElement('SubmitButton','send',
                    array(
                            'required'   => false,
                            'ignore'     => true,
                            'label'      => 'Hochladen',
                        )
                    )
                ->addElement('SubmitButton','cancel',
                    array(
                            'required'   => false,
                            'ignore'     => true,
                            'label'      => 'Abbrechen',
                        )
                    );
    }
}
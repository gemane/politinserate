<?php

/**
* Form for uploading photos on the front page.
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

class Application_Form_Upload extends Zend_Dojo_Form
{
    
    public function init()
    {
        $config = new Application_Model_Config();
        
        $this->setAction('/eingabe/foto')
             ->setMethod('post')
             ->setAttrib('name', 'photoForm');
        Zend_Dojo::enableForm($this);
        
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', 
                array('tag' => 'dl', 'class' => 'upload_form')),
            array('Description', array('placement' => 'prepend')),
            'DijitForm'
        ));
        
        $configuration = Zend_Registry::get('configuration');
        
        $url_image = new Zend_Form_Element_File('url_image_front');
        $url_image->setLabel('Inserat hochladen')
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
        $token->setTimeout(900); // Front-Page can be there for a long time
        
        $hidden = new Zend_Form_Element_Hidden('id_inserat_front');
        
        $this->addElement($hidden, 'id_inserat_front')
             ->addElement($token)
             ->addElement($url_image, 'url_image_front')
             ->addElement('submit', 'send_front', 
                            array('label' => 'hochladen')
                          );
             
    }
}
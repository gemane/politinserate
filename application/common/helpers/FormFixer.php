<?php

/**
* Fixing hidden form elements.
*
* http://blog.motane.lu/2009/02/17/zend-framework-and-hidden-fields/
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

class Zend_View_Helper_FormFixer extends Zend_View_Helper_Abstract
{
    public function formFixer( Zend_Form $_form ) 
    {
        foreach( $_form->getElements() as $element ) {
            if( $element instanceof Zend_Form_Element_Hidden ) {
                $label = $element->getLabel();
                if( empty( $label ) ) {
                    $element->setLabel( '&nbsp;' );
                }
                foreach( $element->getDecorators() as $decorator ) {
                    $decorator->setOption( 'class', 'hidden' );
                }
            }
        }
        
        return $_form;
    }
}
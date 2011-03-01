<?php

/**
* Calculate full expenses for presenting at layout
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

class Application_Plugin_GetPayments extends Zend_Controller_Plugin_Abstract
{

    public function preDispatch() 
    {
        $expenses_file = APPLICATION_PATH . '/../data/cache/expenses.txt';
        if (!file_exists($expenses_file)) {
            $this->table_inserat = new Application_Model_Inserate();
            $expense = $this->table_inserat->getPayment();
            file_put_contents($expenses_file, $expense, LOCK_EX);
        } else {
            $expense = file_get_contents($expenses_file);
        }
        
        Zend_Registry::set('expense', $expense);
    }
    
}

<?php

/**
* Calculate numbers for layout (will only be calculated by changes)
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
        $this->configuration = Zend_Registry::get('configuration');
        $file = APPLICATION_PATH . '/../data/cache/expenses.txt';
        if (!$this->configuration->general->cache || !file_exists($file)) {
            
            // Complete payments
            $table_inserat = new Application_Model_Inserate();
            $values['expense'] = $table_inserat->getPayment();
            
            // Uploaded advertisment and payments in a month
            $months = array(
                    1 => "Januar",
                    2 => "Februar",
                    3 => "M&auml;rz",
                    4 => "April",
                    5 => "Mai",
                    6 => "Juni",
                    7 => "Juli",
                    8 => "August",
                    9 => "September",
                    10 => "Oktober",
                    11 => "November",
                    12 => "Dezember");
            
            $months_back = 0;
            $last_month = mktime(0, 0, 0, date("m")-$months_back, date("d"),   date("Y"));
            $values['last_year'] = date('Y', $last_month);
            $month = date('n', $last_month);
            $values['last_month'] = $months[$month];
            
            $values['expense_month'] = $table_inserat->getPayment($values['last_year'], $month);
            $values['num_tagged_month'] = $table_inserat->getNewInserate(false, $values['last_year'], $month);
            
            // Months with tagged advertisments
            $year_from = $this->configuration->general->year;
            $year_to = date('Y', time());
            for ($year = $year_from; $year <= $year_to; $year++) {
                $list_month = array();
                for ($month = 1; $month <= 12; $month++) {
                    $num_tagged = $table_inserat->getNumInserate(1, $year, $month);
                    if (0 < $num_tagged) {
                        $list_month[] = $month;
                    }
                    if (!empty($list_month))
                        $list_year[$year] = $list_month;
                }
            }
            $values['list_months'] = $list_year;
            
            // Last changes in the project
            $lastEdited_application = $this->filemtime_r(APPLICATION_PATH);
            $lastEdited_css = $this->filemtime_r(APPLICATION_PATH . '/../public/css');
            $lastEdited_js = $this->filemtime_r(APPLICATION_PATH . '/../public/js/intern');
             
            $values['last_edited'] = max($lastEdited_application, $lastEdited_css, $lastEdited_js);
            
            // Write values to file
            file_put_contents($file,serialize($values), LOCK_EX);
        } else {
            $file_contents = file_get_contents($file);
            $values = unserialize($file_contents);
        }
        
        Zend_Registry::set('expense', $values['expense']);
        Zend_Registry::set('expense_month', $values['expense_month']);
        Zend_Registry::set('num_tagged_month', $values['num_tagged_month']);
        Zend_Registry::set('last_month', $values['last_month']);
        Zend_Registry::set('last_year', $values['last_year']);
        Zend_Registry::set('list_months', $values['list_months']);
        Zend_Registry::set('last_edited', $values['last_edited']);
    }
    
    protected function filemtime_r($path)
    {
        $allowedExtensions = array(
            'php',
            'phtml',
            'css',
            'js'
        );
        
        if (!file_exists($path))
            return 0;
        
        $extension = end(explode(".", $path));
        if (is_file($path) && in_array($extension, $allowedExtensions))
            return filemtime($path);
        $ret = 0;
        
        foreach (glob($path."/*") as $fn)
        {
            if ($this->filemtime_r($fn) > $ret)
                $ret = $this->filemtime_r($fn);
        }
        return $ret;
    }
    
}

<?php
/**
* Library for calculating Christian Holidays.
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

class Holidays_Christian
{
    public function Austria($year, $format = 'Y-m-d')
    {
        $holidays_fix = array(
            'Neujahr'             => date($format, strtotime($year . '-01-01')),
            'Heilige Drei Könige' => date($format, strtotime($year . '-01-06')),
            'Staatsfeiertag'      => date($format, strtotime($year . '-05-01')),
            'Mariä Himmelfahrt'   => date($format, strtotime($year . '-08-15')),
            'Nationalfeiertag'    => date($format, strtotime($year . '-10-26')),
            'Allerheiligen'       => date($format, strtotime($year . '-11-01')),
            'Mariä Empfängnis'    => date($format, strtotime($year . '-12-08')),
            'Heiliger Abend'      => date($format, strtotime($year . '-12-24')),
            'Christtag'           => date($format, strtotime($year . '-12-25')),
            'Stefanitag'          => date($format, strtotime($year . '-12-26')),
            'Silvester'           => date($format, strtotime($year . '-12-31')),
            );
        
        $easter = $this->westEaster($year);
        $one_day = 24 * 60 * 60;
        $holidays_flexible = array(
            'Karfreitag'          => date($format, $easter - 2 * $one_day), 
            'Ostersonntag'        => date($format, $easter - 0 * $one_day), 
            'Ostermontag'         => date($format, $easter + 1 * $one_day),
            'Christi Himmelfahrt' => date($format, $easter + 39 *$one_day),
            'Pfingstmontag'       => date($format, $easter + 50 *$one_day),
            'Fronleichnam'        => date($format, $easter + 60 *$one_day)
            );
            
        $holidays = array_merge($holidays_fix, $holidays_flexible);
        asort($holidays);
        
        return $holidays;
    }
    
    public function westEaster($year)
    {
        $easter = array(
            2009 => '2010-04-12',
            2010 => '2010-04-04', 
            2011 => '2011-04-24',
            2012 => '2012-04-08',
            2013 => '2013-03-31',
            2014 => '2014-04-20',
            2015 => '2015-04-05',
            2016 => '2016-03-27',
            2017 => '2017-04-16',
            2018 => '2018-04-01',
            2019 => '2019-04-21',
            2020 => '2020-04-12',
            );
        
        return strtotime($easter[$year]);
    }
    
    public function eastEaster($year)
    {
        
    }
    
}
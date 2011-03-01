<?php
/**
* Model for statistical data.
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

class Application_Model_Statistics
{
    
    public function __construct()
    {
        $this->table_config = new Application_Model_Config();
        $this->table_inserat = new Application_Model_Inserate();
    }
    
    /**
    * Retrieving data from database for view (partial).
    *
    * @return array Data of all tagged advertisments.
    */
    public function getTableMedium($year, $id_party = 0, $payer = 'all_payed', $id_region = 0)
    {
        if (!isset($this->data_medium)) {
            $this->setDataMedium($year, $id_party, $payer, $id_region);
        }
        
        return $this->data_medium;
    }
    
    public function getPlotDataMedium($year, $id_party = 0, $payer = 'all_payed', $id_region = 0)
    {
        if (!isset($this->data_medium)) {
            $this->setDataMedium($year, $id_party, $payer, $id_region);
        }
        
        return $this->data_medium;
    }
    
    public function setDataMedium($year, $id_party = 0, $payer = 'all_payed', $id_region = 0)
    {
        $this->table_medium = new Application_Model_Printmedium();
        $printmedien = $this->table_medium->getAllMedium();
        
        $dataPaymentsMedium = '';
        $dataColorsMedium = '';
        $dataLegendMedium = '';
        $dataLabelMedium = '{value: 0, text: ""}, ';
        $i = 1;
        $i_max = 9;
        foreach ($printmedien as $printmedium) {
            $payments = 
            $this->data_medium[$printmedium['id_printmedium']]['all'] = 
                $this->table_inserat->getPayment($year, false, $printmedium['id_printmedium'], $id_party, $payer, $id_region);
            for ($month = 1; $month <= 12; $month++)
                $this->data_medium[$printmedium['id_printmedium']][$month] = 
                    $this->table_inserat->getPayment($year, $month, $printmedium['id_printmedium'], $id_party, $payer, $id_region);
            $legend = 
                $printmedium['printmedium'];
            if ($i < $i_max) {
                $dataPaymentsMedium .= $payments . ', ';
                $dataColorsMedium .= '"#' . $this->HSV_TO_RGB($i/$i_max, 1, 0.7) . '", ';
                $dataLegendMedium .= '"' . $legend . '", ';
                $dataLabelMedium .= '{value: ' . $i++ . ', text: ""}, ';
            }
            
        }
        $this->data_medium['printmedien'] = $printmedien;
        
        $this->data_medium['dataPaymentsMedium'] = 
            'var dataPaymentsMedium = new Array(' . substr($dataPaymentsMedium, 0, -2) . ');';
        $this->data_medium['dataColorsMedium'] = 
            'var dataColorsMedium = new Array(' . substr($dataColorsMedium, 0, -2) . ');';
        $this->data_medium['dataLegendMedium'] = 
            'var dataLegendMedium = new Array(' . substr($dataLegendMedium, 0, -2) . ');';
        $this->data_medium['dataLabelMedium'] = 
            'var dataLabelMedium = new Array(' . substr($dataLabelMedium, 0, -2) . ');';
    }
    
    /**
    * Retrieving data from database for view (partial).
    *
    * @return array Data of all tagged advertisments.
    */
    public function getTableRegion($year, $id_printmedium = 0, $id_party = 0, $payer = 'all_payed')
    {
        if (!isset($this->data_region)) {
            $this->setDataRegion($year, $id_printmedium, $id_party, $payer);
        }
        
        return $this->data_region;
    }
    
    public function getPlotDataRegion($year, $id_printmedium = 0, $id_party = 0, $payer = 'all_payed')
    {
        if (!isset($this->data_region)) {
            $this->setDataRegion($year, $id_printmedium, $id_party, $payer);
        }
        
        return $this->data_region;
    }
    
    public function setDataRegion($year, $id_printmedium = 0, $id_party = 0, $payer = 'all_payed')
    {
        $this->table_config = new Application_Model_Config();
        $regions = $this->table_config->getAllRegion();
        
        $dataPaymentsRegion = '';
        $dataColorsRegion = '';
        $dataLegendRegion = '';
        $dataLabelRegion = '{value: 0, text: ""}, ';
        $i = 1;
        foreach ($regions as $region) {
            $payments = 
            $this->data_region[$region['id_config']]['all'] = 
                $this->table_inserat->getPayment($year, false, $id_printmedium, $id_party, $payer, $region['id_config']);
            for ($month = 1; $month <= 12; $month++)
                $this->data_region[$region['id_config']][$month] = 
                    $this->table_inserat->getPayment($year, $month, $id_printmedium, $id_party, $payer, $region['id_config']);
            $color = 
                strtoupper($this->table_config->getColorRegion($region['id_config']));
            $legend = 
                $region['region'];
            if ($i < 9) {
                $dataPaymentsRegion .= $payments . ', ';
                $dataColorsRegion .= '"#' . $color . '", ';
                $dataLegendRegion .= '"' . $legend . '", ';
                $dataLabelRegion .= '{value: ' . $i++ . ', text: ""}, ';
            }
        }
        $this->data_region['regions'] = $regions;
        
        $this->data_region['dataPaymentsRegion'] = 
            'var dataPaymentsRegion = new Array(' . substr($dataPaymentsRegion, 0, -2) . ');';
        $this->data_region['dataColorsRegion'] = 
            'var dataColorsRegion = new Array(' . substr($dataColorsRegion, 0, -2) . ');';
        $this->data_region['dataLegendRegion'] = 
            'var dataLegendRegion = new Array(' . substr($dataLegendRegion, 0, -2) . ');';
        $this->data_region['dataLabelRegion'] = 
            'var dataLabelRegion = new Array(' . substr($dataLabelRegion, 0, -2) . ');';
    }
    
    /**
    * Retrieving data from database for view (partial).
    *
    * @return array Data of all tagged advertisments.
    */
    public function getTableParty($year, $id_printmedium = 0, $payer = 'all_payed', $id_region = 0)
    {
        if (!isset($this->data_party)) {
            $this->setDataParty($year, $id_printmedium, $payer, $id_region);
        }
        
        return $this->data_party;
    }
    
    public function getPlotDataParty($year, $id_printmedium = 0, $payer = 'all_payed', $id_region = 0)
    {
        if (!isset($this->data_party)) {
            $this->setDataParty($year, $id_printmedium, $payer, $id_region);
        }
        
        return $this->data_party;
    }
    
    public function setDataParty($year, $id_printmedium = 0, $id_region = 0)
    {
        $this->table_config = new Application_Model_Config();
        $parties = $this->table_config->getAllParty();
        
        $dataPaymentsParty = '';
        $dataPaymentsGovernment = '';
        $dataColorsParty = '';
        $dataColorsGovernment = '';
        $dataLegendParty = '';
        $dataLegendGovernment = '';
        $dataLabelParty = '{value: 0, text: ""}, ';
        $i = 1; $max_y = 0;
        foreach ($parties as $party) {
            $paymentsParty = 
            $this->data_party[$party['id_config']]['all']['party_payed'] = 
                $this->table_inserat->getPayment($year, false, $id_printmedium, $party['id_config'], 'party_payed', $id_region);
            $paymentsGoverment = 
            $this->data_party[$party['id_config']]['all']['government_payed'] = 
                $this->table_inserat->getPayment($year, false, $id_printmedium, $party['id_config'], 'government_payed', $id_region);
            for ($month = 1; $month <= 12; $month++) {
                $this->data_party[$party['id_config']][$month]['party_payed'] = 
                    $this->table_inserat->getPayment($year, $month, $id_printmedium, $party['id_config'], 'party_payed', $id_region);
                $this->data_party[$party['id_config']][$month]['government_payed'] = 
                    $this->table_inserat->getPayment($year, $month, $id_printmedium, $party['id_config'], 'government_payed', $id_region);
            }
            $colorParty = 
                strtoupper($this->table_config->getColorParty($party['id_config']));
            $colorGoverment = strtoupper($this->makeBrighter($colorParty));
            $legend = 
                $party['party'];
            if ($i < 9) {
                $dataPaymentsParty .= $paymentsParty . ', ';
                $dataPaymentsGovernment .= $paymentsGoverment . ', ';
                $dataColorsParty .= '"#' . $colorParty . '", ';
                $dataColorsGovernment .= '"#' . $colorGoverment . '", ';
                $dataLegendParty .= '"' . $legend . ' (Partei)", ';
                $dataLegendGovernment .= '"' . $legend . ' (Regierung)", ';
                $dataLabelParty .= '{value: ' . $i++ . ', text: ""}, ';
            }
            $max_y = max($max_y, $paymentsParty + $paymentsGoverment);
        }
        $this->data_party['parties'] = $parties;
        
        $this->data_party['dataPaymentsParty'] = 
            'var dataPaymentsParty = new Array(' . substr($dataPaymentsParty, 0, -2) . ');';
        $this->data_party['dataPaymentsGovernment'] = 
            'var dataPaymentsGovernment = new Array(' . substr($dataPaymentsGovernment, 0, -2) . ');';
        $this->data_party['dataColorsParty'] = 
            'var dataColorsParty = new Array(' . substr($dataColorsParty, 0, -2) . ');';
        $this->data_party['dataColorsGovernment'] = 
            'var dataColorsGovernment = new Array(' . substr($dataColorsGovernment, 0, -2) . ');';
        $this->data_party['dataLegendParty'] = 
            'var dataLegendParty = new Array(' . substr($dataLegendParty, 0, -2) . ');';
        $this->data_party['dataLegendGovernment'] = 
            'var dataLegendGovernment = new Array(' . substr($dataLegendGovernment, 0, -2) . ');';
        $this->data_party['dataLabelParty'] = 
            'var dataLabelParty = new Array(' . substr($dataLabelParty, 0, -2) . ');';
        
        $this->data_party['yaxis'] = 'var yaxis = ' . ceil($max_y/100000)*100000;
    }
    
     protected function makeBrighter($hex)
     {
        $adjust = 300;
        
        $red   = hexdec( $hex[0] . $hex[1] );
        $green = hexdec( $hex[2] . $hex[3] );
        $blue  = hexdec( $hex[4] . $hex[5] );
    
        $cb = $red + $green + $blue;
    
        if ( $cb > $adjust ) {
            $db = ( $cb - $adjust ) % 255;
    
            $red -= $db; $green -= $db; $blue -= $db;
            if ( $red < 0 ) $red = 0;
            if ( $green < 0 ) $green = 0;
            if ( $blue < 0 ) $blue = 0;
        } else {
            $db = ( $adjust - $cb ) % 255;
    
            $red += $db; $green += $db; $blue += $db;
            if ( $red > 255 ) $red = 255;
            if ( $green > 255 ) $green = 255;
            if ( $blue > 255 ) $blue = 255;
        }
        
        return str_pad( dechex( $red ), 2, '0', 0 )
             . str_pad( dechex( $green ), 2, '0', 0 )
             . str_pad( dechex( $blue ), 2, '0', 0 );
     }
    
    protected function HSV_TO_RGB ($H, $S, $V) // HSV Values:Number 0-1
    { // RGB Results:Number 0-255
        $RGB = array();
        
        if ($S == 0) {
            $R = $G = $B = $V * 255;
        } else {
            $var_H = $H * 6;
            $var_i = floor( $var_H );
            $var_1 = $V * ( 1 - $S );
            $var_2 = $V * ( 1 - $S * ( $var_H - $var_i ) );
            $var_3 = $V * ( 1 - $S * (1 - ( $var_H - $var_i ) ) );
            
            if ($var_i == 0) { $var_R = $V ; $var_G = $var_3 ; $var_B = $var_1 ; }
            else if ($var_i == 1) { $var_R = $var_2 ; $var_G = $V ; $var_B = $var_1 ; }
            else if ($var_i == 2) { $var_R = $var_1 ; $var_G = $V ; $var_B = $var_3 ; }
            else if ($var_i == 3) { $var_R = $var_1 ; $var_G = $var_2 ; $var_B = $V ; }
            else if ($var_i == 4) { $var_R = $var_3 ; $var_G = $var_1 ; $var_B = $V ; }
            else { $var_R = $V ; $var_G = $var_1 ; $var_B = $var_2 ; }
            
            $R = $var_R * 255;
            $G = $var_G * 255;
            $B = $var_B * 255;
        }
        
        return str_pad( dechex( $R ), 2, '0', 0 )
             . str_pad( dechex( $G ), 2, '0', 0 )
             . str_pad( dechex( $B ), 2, '0', 0 );
    }
    
}
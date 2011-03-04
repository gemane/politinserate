<?php
/**
* Check if user is browsing from a mobile phone
*
* http://www.web-punk.com/2010/03/zend-framework-applications-for-iphone-blackberry-co/ 
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

class Application_Plugin_CheckMobile extends Zend_Controller_Plugin_Abstract
{
    // instead of defining all these parameters here,
    // you could also put them into your application.ini
    
    // if user is inactive vor X minutes and surfs to
    // www.example.com, we'll ask him again if he wants
    // to user mobile or desktop version
    private $ask_again_after_x_minutes = 10;
    
    // used to test your mobile layout. Set this
    // to 'true' to emulate a mobile device
    // private $test_mobile = true;

    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        // did we already ask the user?
        if (isset($_SESSION['mobileasked'])) {
            // is mobile session still valid?
            if (time() < $_SESSION['mobileasked']) {
                // update session
                $_SESSION['mobileasked'] = time() + $this->ask_again_after_x_minutes * 60;
                // continue with requested page
                return $request;
            }
        }
        
        $configuration = Zend_Registry::get('configuration');
        $this->test_mobile = $configuration->general->mobiletesting;

        // otherwise, check if user is using a mobile device
        // or if we are in test mode.
        if ($this->checkmobile() || ($this->test_mobile)) {
            // if requested page != MOBILE.example.com
            if (!(Zend_Registry::isRegistered('directmobile') && Zend_Registry::get('directmobile') == '1')) {
                // set mobile session
                $_SESSION['mobileasked'] = time() + $this->ask_again_after_x_minutes * 60;
                // ask user if he wants to use mobile or desktop version
                $request->setControllerName('index')
                        ->setActionName('askmobile')
                        ->setParam('format', 'mobile')
                        ->setParams($request->getParams())
                        ->setDispatched(false);
            }
        }
        return $request;
    }

    /**
    * This function returns true if user is using a mobile device. False otherwise.
    * (c) by http://www.brainhandles.com/techno-thoughts/detecting-mobile-browsers
    */
    
    private function checkmobile()
    {
        if(isset($_SERVER["HTTP_X_WAP_PROFILE"])) return true;
        if(preg_match("/wap\.|\.wap/i",$_SERVER["HTTP_ACCEPT"])) return true;
        if(isset($_SERVER["HTTP_USER_AGENT"])){
            // Quick Array to kill out matches in the user agent
            // that might cause false positives
            $badmatches = array("OfficeLiveConnector","MSIE\  8\.0","OptimizedIE8","MSN\ Optimized","Creative\ AutoUpdate","Swapper");
        
            foreach($badmatches as $badstring){
                if(preg_match("/".$badstring."/i",$_SERVER["HTTP_USER_AGENT"])) return  false;
            }
        
            // Now we'll go for positive matches
            $uamatches = array("midp", "j2me", "avantg", "docomo", "novarra",  "palmos", "palmsource", "240x320", "opwv", "chtml", "pda", "windows\  ce", "mmp\/", "blackberry", "mib\/", "symbian", "wireless", "nokia",  "hand", "mobi", "phone", "cdm", "up\.b", "audio", "SIE\-", "SEC\-",  "samsung", "HTC", "mot\-", "mitsu", "sagem", "sony", "alcatel", "lg",  "erics", "vx", "NEC", "philips", "mmm", "xx", "panasonic", "sharp",  "wap", "sch", "rover", "pocket", "benq", "java", "pt", "pg", "vox",  "amoi", "bird", "compal", "kg", "voda", "sany", "kdd", "dbt", "sendo",  "sgh", "gradi", "jb", "\d\d\di", "moto","webos");
        
            foreach($uamatches as $uastring){
                if(preg_match("/".$uastring."/i",$_SERVER["HTTP_USER_AGENT"])) return  true;
            }
        }
        return false;
    }
}

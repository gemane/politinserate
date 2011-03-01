<?php
/**
* Library for collecting Twitter messages with links to images.
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

class Service_Twitter
{

    public function __construct()
    {
        $this->configuration = Zend_Registry::get('configuration');
    }

    public function getTwitterLinks()
    {
        $tweets = $this->getTweets();
        
        $image_list = array();
        $pattern = '#(^|[^\"=]{1})(http://[^\s<>]+)([\s\n<>]|$)#sm';
        foreach ($tweets as $value) {
            preg_match_all($pattern, $value['text'], $str);
            foreach ($str[0] as $link) {
                if (!empty($link)) {
                    $image_list = $this->getTwitterImage($link, $value, $image_list);
                }
            }
        }
        return $image_list;
    }
    
    protected function getTweets() // TODO2 Try -> Catch
    {
        if($this->waitTwitter()) {
            $twitter  = new Zend_Service_Twitter($this->configuration->twitter->login, $this->configuration->twitter->password);
            $tweets = array();
            // Get all replies
            $response = $twitter->status->replies();
            $tweets = $this->addTweets($tweets, $response);
            // Get all direct messages
            $response = $twitter->directMessage->messages();
            if (!empty($response))
                $tweets = $this->addTweets($tweets, $response);
            // Search for bot
            $twitter_search = new Zend_Service_Twitter_Search('atom'); // TODO2 funktioniert das?
            $response = $twitter_search->search('@' . $this->configuration->twitter->login);
            if (!empty($response))
                $tweets = $this->addTweets($tweets, $response);
            // TODO2 Suche nach einem Tag für die Seite, ...
            
            return $tweets;
        } else
            return array();
    }
    
    protected function waitTwitter()
    {
        $cache_file = APPLICATION_PATH . '/../data/cache/lock_twitter';
        $cache_life = $this->configuration->twitter->life; // Twitter update after $cache_life seconds
        
        $filemtime = @filemtime($cache_file);  // returns FALSE if file does not exist
        if (!$filemtime || (time() - $filemtime >= $cache_life)) {
            file_put_contents($cache_file, time());
            return true;
        } else {
            return false;
        }
    }
    
    protected function addTweets($tweets, $response)
    {
        foreach ($response as $value) {
            $tweets[] = array(
                'upload_time'   => date('Y-m-d H:i:s', strtotime((string) $value->created_at)),
                'id_twitter'    => (string) $value->id,
                'text'          => (string) $value->text,
                'user_id'       => (string) $value->user->id,
                'name'          => (string) $value->user->name,
                'screen_name'   => (string) $value->user->screen_name
                );
        }
        
        return $tweets;
    }
    
    public function getTwitterImage($link, $value = '', $image_list = array())
    {
        // TODO2 Dürfen Bilder von diesen Seiten legal geladen werden?
        // -> http://twitpic.com
        if (false != strstr($link, 'http://twitpic.com')) {
            $link_full = $link . '/full';
            $client = new Zend_Http_Client(trim($link_full));
            $response = $client->request(); 
            $html = $response->getBody();
            $dom = new Zend_Dom_Query($html);
            $images = $dom->query('img');
            $i = 0;
            foreach ($images as $img) {
                $link_image = $img->getAttribute("src");
                if (0 < $i++) {
                    $image_list[] = $this->getValuesTweet($link, $link_image, true, $value);
                }
            }
        }
        // -> http://tweetphoto.com // TODO2 Größeres Bild laden
        // TODO1 Heißt jetzt Plixi
        if (false != strstr($link, 'http://tweetphoto.com')) {
            $client = new Zend_Http_Client(trim($link));
            $response = $client->request();
            $html = $response->getBody();
            $dom = new Zend_Dom_Query($html);
            $images = $dom->query('#photo > img');
            foreach ($images as $img) {
                $link_image = $img->getAttribute("src");
                $image_list[] = $this->getValuesTweet($link, $link_image, true, $value);
            }
        }
        // -> http://yfrog.com
        if (false != strstr($link, $url = 'http://yfrog.com')) {
            $link_full = $url . '/f' . substr($link, strlen($url));
            $client = new Zend_Http_Client(trim($link));
            $response = $client->request(); 
            $html = $response->getBody();
            $dom = new Zend_Dom_Query($html);
            $images = $dom->query('link');
            $i = 0;
            foreach ($images as $img) {
                $link_image = $img->getAttribute("href");
                if (0 == $i++) {
                    $image_list[] = $this->getValuesTweet($link, $link_image, true, $value);
                }
            }
        }
        // -> http://mobypicture.com
        if (false != strstr($link, 'mobypicture.com')) {
            $link_full = $link . '/sizes/full';
            $client = new Zend_Http_Client(trim($link_full));
            $response = $client->request(); 
            $html = $response->getBody();
            $dom = new Zend_Dom_Query($html);
            $images = $dom->query('#sizes_choose_full');
            foreach ($images as $img) {
                $link_image = $img->getAttribute("href");
                if (0 == $i++) {
                    $image_list[] = $this->getValuesTweet($link, $link_image, true, $value);
                }
            }
            
        }
        
        return $image_list;
    }
    
    protected function getValuesTweet($link, $link_image, $checked = false, $value = '')
    {
        if (empty($value)) {
            $image_tweet = array(
                'source'        => $link,
                'image_remote'  => $link_image
                );
        } else {
            $image_tweet = array(
                'checked'       => $checked,
                'id_source'     => 2,  // Upload via Twitter
                'id_twitter'    => $value['id_twitter'],
                'text'          => $value['text'],
                'source'        => $link,
                'image_remote'  => $link_image,
                'user_id'       => $value['user_id'],
                'name'          => $value['name'],
                'username'      => $value['screen_name'],
                'upload_time'   => $value['upload_time'],
                'keywords'      => $this->getKeywords($value['text'])
                );
        }
        return $image_tweet;
    }
    
    protected function getKeywords($text)
    {
        $keywords = array(
            'id_party' => 'party',
            'id_government' => 'government',
            'id_region' => 'region'
        );
        $inserat_keywords = array();
        $this->table_config = new Application_Model_Config();
        $configs = $this->table_config->getAll();
        foreach ($keywords as $key => $value) {
            foreach ($configs as $config) {
                // stristr does not work for Umlaute
                if (!empty($config[$value]) && false != stristr($text, $config[$value])) {
                    $inserat_keywords[$key] = $config['id_config'];}
            }
        }
        
        $medium_table = new Application_Model_Printmedium();
        $medien = $medium_table->getAllMedium();
        foreach ($medien as $medium) {
            if (false != stristr($text, $medium['printmedium'])) {
                $inserat_keywords['id_printmedium'] = $medium['id_printmedium'];
            } else if (!empty($medium['keywords_printmedium'])) {
                // Check also for alias of printmedium
                foreach (explode(" ", $medium['keywords_printmedium']) as $keyword) {
                    if (false != stristr($text, $keyword)) 
                        $inserat_keywords['id_printmedium'] = $medium['id_printmedium'];
                }
            }
        }
        
        preg_match('/[\d]{0,2}+[\.]+[\d]{0,2}+[\.]+[\d]{2,4}/', $text, $str);
        if (!empty($str))
            $inserat_keywords['print_date'] = date('Y-m-d', strtotime($str[0]));
            // TODO2 Oder Datum des hochladens (gepostet) auf Twitpic verwenden
        
        // TODO2 Mit "Neu" markieren
        
        return $inserat_keywords;
    }
    
}
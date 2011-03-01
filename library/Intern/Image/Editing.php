<?php
/**
* Library for editing images like changing size or rotate.
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

class Image_Editing
{

     public function getImageOriginal($id_inserat)
     {
        $path = '/download/original/';
        $file = 'inserat_' . sprintf('%06d', $id_inserat) . '_o.jpg';
        $image = $path . $file;
        
        return $image;
     }
     
     public function orientationImagebyLinkDefault($link)
     {
        $width = 750;
        $height = 600;
        
        $image = $this->orientationImage($link, $width, $height);
        $image['image'] = $link;
        if ($image['width'] > $image['height']) {
            $width_d = $width;
            $height_d = $image['height'] * $width / $image['width'];
        } else {
            $height_d = $height;
            $width_d = $image['width'] * $height / $image['height'];
        }
        $image['height'] = $height_d;
        $image['width'] = $width_d;
        
        return $image;
     }
     
     public function orientationImageDefault($id_inserat)
     {
        $width = 750;
        $height = 600;
        
        $path = '/images/uploads/default/';
        $file = 'inserat_' . sprintf('%06d', $id_inserat) . '_d.jpg';
        $image = $this->orientationImage(APPLICATION_PATH .'/../public' . $path . $file, $width, $height);
        $image['image'] = $path . $file;
        
        return $image;
     }
     
     public function orientationImageThumbnail($id_inserat)
     {
        $width = 120;
        $height = 90;
        
        $path = '/images/uploads/thumbnail/';
        $file = 'inserat_' . sprintf('%06d', $id_inserat) . '_t.jpg';
        $image = $this->orientationImage(APPLICATION_PATH .'/../public' . $path . $file, $width, $height);
        $image['image'] = $path . $file;
        
        return $image;
     }
     
     public function orientationImage($path, $width, $height)
     {
        
        if (file_exists($path)  || (false != stristr($path, 'http://') && is_array(get_headers($path))) ) {
            list($width, $height, $type, $attr) = getimagesize($path);
            $image['width'] = $width;
            $image['height'] = $height;
        } else {
            $image['width'] = $height;
            $image['height'] = $width;
        }
        return $image;
     }
     
     public function getImageDatebyID($id_inserat)
     {
        $path = APPLICATION_PATH .'/../data/uploads/images/original/';
        $file = 'inserat_' . sprintf('%06d', $id_inserat) . '_o.jpg';
        
        return $this->getImageDate($path . $file);
     }
     
     public function getImageDate($link)
     {
        if (file_exists($link) || (false != stristr($link, 'http://') && is_array(get_headers($link)))) {
            $exif = exif_read_data($link, 'IFD0');
            if (isset($exif['DateTimeOriginal'])) {
                $date = $exif['DateTimeOriginal'];
                return date('Y-m-d', strtotime($date));
            } else {
                return false;
            }
        } else {
            return false;
        }
     }
     
     public function rotateImages($id_inserat, $direction)
     {
        $this->rotateImageThumbnail($id_inserat, $direction);
        $this->rotateImageDefault($id_inserat, $direction);
     }
     
     public function rotateImageThumbnail($id_inserat, $degrees)
     {
        $path_t = APPLICATION_PATH .'/../public/images/uploads/thumbnail/';
        $file_t = 'inserat_' . sprintf('%06d', $id_inserat) . '_t.jpg';
        $this->rotateImage($path_t . $file_t, $path_t . $file_t, $degrees);
     }
     
     public function rotateImageDefault($id_inserat, $degrees)
     {
        $path_d = APPLICATION_PATH .'/../public/images/uploads/default/';
        $file_d = 'inserat_' . sprintf('%06d', $id_inserat) . '_d.jpg';
        $this->rotateImage($path_d . $file_d, $path_d . $file_d, $degrees);
     }
     
    /**
    * Rotates image specified number of degrees using
    *   http://wiki.github.com/masterexploder/PHPThumb/
    * 
    * @author    Ian Selby <ian@gen-x-design.com>
    * @param     string    Path of source image
    * @param     string    Destination path of thumbnail
    * @param     int       $degrees
    */
    private function rotateImage($sourcePath, $destPath, $degrees)
    {
        include_once('Extern/PHPThumb/ThumbLib.inc.php');
        $thumb = PhpThumbFactory::create($sourcePath);
        $thumb->rotateImageNDegrees($degrees);
        $thumb->save($destPath);
    }
    
    /**
    * Create thumbnail of an image using 
    *   http://wiki.github.com/masterexploder/PHPThumb/
    * 
    * @author    Ian Selby <ian@gen-x-design.com>
    * @param     string    Path of source image
    * @param     string    Destination path of thumbnail
    * @param     number    Width of thumbnail
    * @param     number    Height of thumbnail
    */
    public function createThumbnail($sourcePath, $destPath, $w, $h)
    {
        include_once('Extern/PHPThumb/ThumbLib.inc.php');
        $thumb = PhpThumbFactory::create($sourcePath);
        $thumb->resize($w, $h);
        $thumb->save($destPath, 'jpg');
    }
    
}

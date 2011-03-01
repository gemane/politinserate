<?php
/**
* Database Model for User data.
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

class Application_Model_Users extends Zend_Db_Table_Abstract
{
    protected $_name = 'acd_inserate_user';
    protected $_primary = 'id_user';
    protected $array = array('id_user', 'username', 'password', 'password_salt', 'date_register', 'last_access', 'user_email', 'user_activated', 'user_fullname', 'user_show', 'prefered_region', 'prefered_printmedium', 'max_uploads');
    
    public function getArray()
    {
        return $this->array;
    }
    
    public function getUserByID($id_user)
    {
        $select = $this->select()->from(array('acd_inserate_user'))
                                 ->where('id_user = ?', $id_user);
        $result = $this->fetchAll($select)->toArray();
        
        return $result;
    }
    
    public function getUsernameByID($id_user)
    {
        $select = $this->select()->from(array('acd_inserate_user'),
                                        array('username'))
                                 ->where('id_user = ?', $id_user);
        $result = $this->fetchAll($select);
        
        return $result[0]['username'];
    }
    
    public function getUsernameByEmail($email)
    {
        $select = $this->select()->from(array('acd_inserate_user'),
                                        array('username'))
                                 ->where('user_email = ?', $email);
        $result = $this->fetchAll($select);
        
        return $result[0]['username'];
    }
    
    public function getUserId($username)
    {
        $select = $this->select()->from(array('acd_inserate_user'),
                                        array('id_user'))
                                 ->where('username = ?', $username);
        $result = $this->fetchAll($select);
        
        return $result[0]['id_user'];
    }
    
    public function getEmail($username)
    {
        $select = $this->select()->from(array('acd_inserate_user'),
                                        array('user_email'))
                                 ->where('username = ?', $username);
        $result = $this->fetchAll($select);
        
        return $result[0]['user_email'];
    }
    
    public function getFullname($username)
    {
        $select = $this->select()->from(array('acd_inserate_user'),
                                        array('user_fullname'))
                                 ->where('username = ?', $username);
        $result = $this->fetchAll($select);
        
        return $result[0]['user_fullname'];
    }
    
    public function getName($username)
    {
        $select = $this->select()->from(array('acd_inserate_user'),
                                        array('user_fullname', 'username'))
                                 ->where('username = ?', $username);
        $result = $this->fetchAll($select);
        
        if ('' != $result[0]['user_fullname'])
            return $result[0]['user_fullname'];
        else
            return $result[0]['username'];
    }
    
    public function getPreferedPrintmedium($username)
    {
        $select = $this->select()->from(array('acd_inserate_user'),
                                        array('prefered_printmedium'))
                                 ->where('username = ?', $username);
        $result = $this->fetchAll($select);
        
        return $result[0]['prefered_printmedium'];
    }
    
    public function getPreferedRegion($username)
    {
        $select = $this->select()->from(array('acd_inserate_user'),
                                        array('prefered_region'))
                                 ->where('username = ?', $username);
        $result = $this->fetchAll($select);
        
        return $result[0]['prefered_region'];
    }
    
    public function getConfiguration($username)
    {
        $select = $this->select()->from(array('acd_inserate_user'),
                                        array('user_fullname', 'prefered_region', 'prefered_printmedium'))
                                 ->where('username = ?', $username);
        $result = $this->fetchAll($select)->toArray();
        
        return $result;
    }
    
    public function updateConfiguration($username, $values)
    {
        $values_configuration['user_fullname'] = $values['user_fullname'];
        if ('' != $values['user_fullname']) {
            $values_configuration['user_show'] = $values['user_show'];
        } else {
            $values_configuration['user_show'] = 0;
        }
        $values_configuration['prefered_region'] = $values['prefered_region'];
        $values_configuration['prefered_printmedium'] = $values['prefered_printmedium'];
    
        $where = $this->getAdapter()->quoteInto('username = ?', $username);
        $this->update($values_configuration, $where);
    }
    
    public function getMaxUploadsbyUsername($username)
    {
        $select = $this->select()->from(array('acd_inserate_user'),
                                        array('max_uploads'))
                                 ->where('username = ?', $username);
        $result = $this->fetchAll($select);
        
        return $result[0]['max_uploads'];
    }
    
    public function checkUsername($username)
    {
        $select = $this->select()->from(array('acd_inserate_user'),
                                        array('username'))
                                 ->where('username = ?', $username);
        $result = $this->fetchAll($select)->toArray();
        
        if (empty($result)) {
            return false;
        } else {
            return true;
        }
    }
    
    public function checkPassword($password)
    {
        $select = $this->select()->from(array('acd_inserate_user'),
                                        array('password'))
                                 ->where('password = ?', $password);
        $result = $this->fetchAll($select)->toArray();
        
        if (empty($result)) {
            return false;
        } else {
            return true;
        }
    }
    
    public function checkEmail($email)
    {
        $select = $this->select()->from(array('acd_inserate_user'),
                                        array('user_email'))
                                 ->where('user_email = ?', $email);
        $result = $this->fetchAll($select)->toArray();
        
        if (empty($result)) {
            return false;
        } else {
            return true;
        }
    }
    
    public function userActive($username)
    {
        if (!$this->checkUsername($username))
            return false;
        
        $select = $this->select()->from(array('acd_inserate_user'),
                                        array('username', 'user_activated'))
                                 ->where('username = ?', $username);
        $result = $this->fetchAll($select)->toArray();
        
        if (1 == $result[0]['user_activated']) {
            return true;
        } else {
            return false;
        }
    }
    
    public function insertUser($values, $staticSalt)
    {
        $values_user['username'] = $values['username'];
        $values_user['user_email'] = $values['user_email'];
        $values_user['password'] = sha1($staticSalt . $values['password1']);
        $values_user['date_register'] = date('Y-m-d H:i:s', time());
        $values_user['last_access'] = date('Y-m-d H:i:s', time());
        $values_user['user_activated'] = 0;
        $values_user['user_activationcode'] = '';
        $values_user['user_fullname'] = '';
        $values_user['prefered_region'] = 0;
        $values_user['prefered_printmedium'] = 0;
        $values_user['max_uploads'] = 10;
        
        $this->insert($values_user);
        return $this->getAdapter()->lastInsertId();
    }
    
    public function updateUser($id_user, $values)
    {
        $where = $this->getAdapter()->quoteInto('id_user = ?', $id_user);
        $this->update($values, $where);
    }
    
    public function updatePassword($id_user, $password, $staticSalt)
    {
        $where = $this->getAdapter()->quoteInto('id_user = ?', $id_user);
        $values['password'] = sha1($staticSalt . $password);
        
        $this->update($values, $where);
    }
    
    public function updateLastAccess($username)
    {
        $data = array('last_access' => date('Y-m-d H:i:s'));
        $where = $this->getAdapter()->quoteInto('username = ?', $username);
        if (!$this->update($data, $where)) {
            throw new Zend_Exception('Error beim Update von last_access.');
        }
    }
    
    public function deleteOldUser($days = 8)
    {
        $sevenDays = date('Y-m-d H:i:s', time() - $days*24*60*60);
        $where = 'date_register < "' . $sevenDays . '" AND user_activated = 0 AND id_user != 1';
        $this->delete($where);
    }
     
}
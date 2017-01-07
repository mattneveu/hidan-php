<?php
/**
* Hidan-PHP Framework
*
* This library is free software; you can redistribute it and/or
* modify it under the terms of the GNU Lesser General Public
* License as published by the Free Software Foundation; either
* version 2.1 of the License, or (at your option) any later version.
*
* This library is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
* Lesser General Public License for more details.
*
* @package hidan-php
* @link https://github.com/mattneveu/hidan-php
* @author Matthieu Neveu
* @version 1.0
*/

class Index extends _Hidan_Controller
{

  public function __construct()
  {
    parent::__construct();
  }

  /**
  * show() is the default action method called
  */
  public function index()
  {
    // to get a parameter (get, post):
    // $var = $this->_getParam('name', 'default_value');

    $this->_getTemplate()->assign_vars(array('toto' => 'Toto says hello'));
    $this->_getTemplate()->show('index/index.tpl');
  }

  public function toto()
  {
    echo 'toto';
    echo $this->_getParam('param1', 'default');
  }

  public function testdb()
  {
    $db = _Hidan_Database::_getDb(0);
    $sql = "SELECT * FROM %%users%% WHERE id=:id";
    $myUser = $db->selectSingle($sql, array(':id' => 1));
    $this->_getTemplate()->assign_vars(array('user' => $myUser));
    $this->_getTemplate()->show('index/testdb.tpl');
  }

}

?>

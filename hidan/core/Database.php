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

class _Hidan_Database
{
  public static $dbInstance = array();

  public function __construct($config)
  {
    new PDO();
  }

  public static function _getDb($num = 0)
  {
    if(self::$dbInstance[$num] == NULL)
      self::$dbInstance[$num] = new _Hidan_Database();

    return self::$dbInstance[$num];
  }

}

?>

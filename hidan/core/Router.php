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

class _Hidan_Router
{

  /**
  * Path to controllers folder
  * @var string
  */
  private $controllersPath;

  /**
  * constructor
  */
  public function __construct()
  {
    $this->controllersPath = 'application/controllers';
  }

  /**
  * Set controllers path
  * @var string
  */
  public function _setControllerPath(String $path)
  {
    $this->controllersPath = $path;
  }

  /**
  * Get controllers path
  * @var string
  */
  public function _getControllerPath()
  {
    return $this->controllersPath;
  }



}

?>
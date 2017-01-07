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

  private $controllersPath;
  private $defaultAction;
  private $defaultController;
  private $errorController;
  private $errorAction;
  private $controller;
  private $action;
  private $params = array();
  private $file;

  /**
  * constructor
  */
  public function __construct()
  {
    $this->controllersPath = 'application'.DIRECTORY_SEPARATOR.
                             'controllers'.DIRECTORY_SEPARATOR;
    $this->defaultAction = 'index';
    $this->defaultController = 'index';
    $this->errorController = 'hidanError';
    $this->errorAction = 'show404';
  }

  /**
  * load router
  */
  public function load()
  {
    $url        = $_SERVER['REQUEST_URI'];
    $script     = $_SERVER['SCRIPT_NAME'];
    $tabUrl     = self::formatUrl($url, $script);

    self::clear_empty_value($tabUrl);

    $this->getRoute($tabUrl);

    $this->controller   = (!empty($this->controller)) ? $this->controller : $this->defaultController;
    $this->action       = (!empty($this->action)) ? $this->action : $this->defaultAction;
    $ctrlPath           = str_replace('_', DIRECTORY_SEPARATOR, $this->controller);
    $this->file         = realpath($this->controllersPath) . DIRECTORY_SEPARATOR . $ctrlPath . '.php';

    if(!is_file($this->file))
    {
      HTTP::sendHeader('Status', '404 Not Found');
      $this->controller   = $this->errorController;
      $this->action       = $this->errorAction;
      $this->file         = $this->controllersPath . $this->controller . '.php';
    }

    include_once $this->file;

    $class      = ucfirst($this->controller);
    $controller = new $class();
    $controller->_addParams($this->params);

    if (!is_callable(array($controller, $this->action)))
      $action = $this->defaultAction;
    else
      $action = $this->action;

    // call for the action
    $controller->$action();
  }

  private function getRoute($url)
  {
    $items = $url;
    if (!empty($items))
    {
      $this->controller = array_shift($items);
      $this->action = array_shift($items);
      $size = count($items);
      if($size >= 2)
        for($i = 0; $i < $size; $i += 2)
        {
          $key	= (isset($items[$i])) ? $items[$i] : $i;
          $value	= (isset($items[$i+1])) ? $items[$i+1] : null;
          $this->params[$key] = $value;
        }
      else
        $this->params = $items;
    }
  }

  private static function formatUrl($url, $script)
  {
    $tabUrl     = explode('/', $url);
    $tabScript  = explode('/', $script);
    $size       = count($tabScript);

    for ($i = 0; $i < $size; $i++)
      if ($tabScript[$i] == $tabUrl[$i])
        unset($tabUrl[$i]);

    return array_values($tabUrl);
  }

  /**
  * Clear empty elements of an array
  */
  private static function clear_empty_value(&$array)
  {
    foreach ($array as $key => $value)
    {
      if (empty($value))
        unset($array[$key]);
    }
    $array = array_values($array);
  }

  /**
  * Set controllers path
  * @param string
  */
  public function _setControllerPath($path)
  {
    $this->controllersPath = $path;
  }

  /**
  * Get controllers path
  * @return string
  */
  public function _getControllerPath()
  {
    return $this->controllersPath;
  }

}

?>

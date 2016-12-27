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

class _Hidan_Lang implements ArrayAccess
{
  private $container = array();
  private $language = array();
  static private $allLanguages = array('en', 'fr', 'de', 'it', 'es');

  static function getAllowedLangs($OnlyKey = true)
  {
    if($OnlyKey)
    {
      return array_keys(self::$allLanguages);
    }
    else
    {
      return self::$allLanguages;
    }
  }

  public function getUserAgentLanguage()
  {
    if (isset($_REQUEST['lang']) && in_array($_REQUEST['lang'], self::getAllowedLangs()))
    {
      HTTP::sendCookie('lang', $_REQUEST['lang'], 2147483647);
      $this->setLanguage($_REQUEST['lang']);
      return true;
    }

    if (isset($_COOKIE['lang']) && in_array($_COOKIE['lang'], self::getAllowedLangs()))
    {
      $this->setLanguage($_COOKIE['lang']);
      return true;
    }

    if (empty($_SERVER['HTTP_ACCEPT_LANGUAGE']))
    {
      return false;
    }

    $accepted_languages = preg_split('/,\s*/', $_SERVER['HTTP_ACCEPT_LANGUAGE']);

    $language = $this->getLanguage();

    foreach ($accepted_languages as $accepted_language)
    {
      $isValid = preg_match('!^([a-z]{1,8}(?:-[a-z]{1,8})*)(?:;\s*q=(0(?:\.[0-9]{1,3})?|1(?:\.0{1,3})?))?$!i', $accepted_language, $matches);

      if ($isValid !== 1)
        continue;

      list($code)	= explode('-', strtolower($matches[1]));

      if(in_array($code, self::getAllowedLangs()))
      {
        $language	= $code;
        break;
      }
    }

    HTTP::sendCookie('lang', $language, 2147483647);
    $this->setLanguage($language);

    return $language;
  }

  public function __construct($language = NULL)
  {
    $this->setLanguage($language);
  }

  public function setLanguage($language)
  {
    if(!is_null($language) && in_array($language, self::getAllowedLangs()))
    {
      $this->language = $language;
    }
    else
    {
      $this->language	= DEFAULT_LANG;
    }
  }

  public function addData($data)
  {
    $this->container = array_replace_recursive($this->container, $data);
  }

  public function getLanguage()
  {
    return $this->language;
  }

  public function includeData($files)
  {
    ob_start();
    $LNG	= array();

    $path	= 'language/'.$this->getLanguage().'/';

        foreach($files as $file) {
      $filePath	= $path.$file.'.php';
      if(file_exists($filePath))
      {
        require $filePath;
      }
    }

    $filePath	= $path.'CUSTOM.php';
    require $filePath;
    ob_end_clean();

    $this->addData($LNG);
  }

  public function offsetSet($offset, $value)
  {
      if (is_null($offset)) {
          $this->container[] = $value;
      } else {
          $this->container[$offset] = $value;
      }
  }

  public function offsetExists($offset)
  {
      return isset($this->container[$offset]);
  }

  public function offsetUnset($offset)
  {
      unset($this->container[$offset]);
  }

  public function offsetGet($offset)
  {
      return isset($this->container[$offset]) ? $this->container[$offset] : $offset;
  }
}

?>

<?php

namespace moxie\core;


class server extends \moxie\core\instantiatable implements \ArrayAccess, \Countable, \IteratorAggregate {

  protected $aProps = [];

  public function __construct(array $aProps = null) {
    // Import $_SERVER Properties to System
    $this->aProps = $aProps ?? $_SERVER;

    // Configure Path Info
    $this['PATH_INFO'] = rtrim('/' . ltrim(str_replace('?' . $this['QUERY_STRING'], '', substr_replace($this['REQUEST_URI'], '', 0, strlen($this['SCRIPT_NAME']))), '/'), '/');

    var_dump($this['PATH_INFO']);
  }

  public function __get(string $sKey) {
    return $this->aProps[$sKey] ?? false;
  }
  public function __set(string $sKey, $xValue) {
    $this->aProps[$sKey] = $xValue;
  }
  public function __isset(string $sKey) : bool {
    return array_key_exists($this->aProps, $sKey);
  }
  public function __unset(string $sKey) : bool {
    return unset($this->aProps[$sKey]);
  }


  /** ArrayAccess **/
  public function offsetGet($sKey) {
    return (isset($this->aProps[$sKey]) ? $this->aProps[$sKey] : null);
  }
  public function offsetSet($sKey, $xValue) {
    $this->aProps[$sKey] = $xValue;
  }
  public function offsetExists($sKey) {
    return isset($this->aProps[$sKey]);
  }
  public function offsetUnset($sKey) {
    unset($this->aProps[$sKey]);
  }

  public function count() {
    return count($this->aStorage);
  }

  public function getIterator() {
    return new \ArrayIterator($this->aProps);
  }
}

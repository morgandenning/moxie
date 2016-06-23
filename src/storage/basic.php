<?php

namespace moxie\storage;

class basic implements \ArrayAccess, \Countable, \IteratorAggregate {

  protected $aStorage = [];

  public function __construct(array $aItems) {
    $this->replace(($aItems ?? []));
  }

  public function __get(string $sKey) {
    return $this->get($sKey) ?? null;
  }
  public function __set(string $sKey, $xValue) {
    $this->set($sKey, $xValue);
  }
  public function __isset(string $sKey) : bool {
    return $this->isset($sKey) ?? false;
  }
  public function __unset(string $sKey) : bool {
    return $this->unset($sKey) ?? false;
  }

  public function instance(string $sKey, $fCallback) {
    $this->set($sKey, function($x) use ($fCallback) {
      static $oObject;

      if (null === $oObject)
      $oObject = $fCallback($x);

      return $oObject;
    });
  }

  public function get(string $sKey, $fDefault = null) {
    if ($this->isset($sKey)) {
      return (is_object($this->aStorage[$sKey]) && method_exists($this->aStorage[$sKey], '__invoke')) ? $this->aStorage[$sKey]($this) : $this->aStorage[$sKey];
    }

    return $fDefault;
  }
  public function set(string $sKey = null, $xValue = null) {
    if (!is_null($xValue)) {
      $this->aStorage[$sKey] = $xValue;
    }
  }
  public function isset(string $sKey) : bool {
    return array_key_exists($sKey, $this->aStorage);
  }

  public function replace(array $aItems) {
    array_walk($aItems, function($v, $k) {
      $this->set($k, $v);
    });
  }
  public function remove(string $sKey = null) {
    if (!is_null($sKey)) {
      unset($this->aStorage[$sKey]);
    }
  }


  /** ArrayAccess **/
  public function offsetGet($sKey) {
    return $this->get($sKey);
  }
  public function offsetSet($sKey, $xValue) {
    $this->set($sKey, $xValue);
  }
  public function offsetExists($sKey) {
    return $this->isset($sKey);
  }
  public function offsetUnset($sKey) {
    $this->remove($sKey);
  }

  public function count() {
    return count($this->aStorage);
  }

  public function getIterator() {
    return new \ArrayIterator($this->aStorage);
  }
}

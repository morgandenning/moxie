<?php

namespace moxie\core;

abstract class instantiatable {

  protected static $oInstance = null;
  protected $aProps = [];

  public static function instance(bool $bCreateNew = false) : \moxie\core\instantiatable {
    if (is_null(static::$oInstance) || $bOverride)
      return (static::$oInstance = new static());
  }

}

<?php

namespace moxie\http\response;

class headers extends \moxie\storage\basic {



  public static function start() {
    ob_start();
  }

}

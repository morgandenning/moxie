<?php

namespace moxie\http;

class request {

  public static function head() {
    return \moxie\core\base::instance()->oServer['REQUEST_METHOD'] === request\methods::HEAD;
  }

}

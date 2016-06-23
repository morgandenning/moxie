<?php

namespace moxie\routing;


class router {
  protected $aRoutes = [],
            $aCurrentRoutes = [];

  public function __construct() {
    return $this;
  }

  public function add(\moxie\routing\route $oRoute) {
    $this->aRoutes[] = $oRoute;
  }


  public function matches(string $sHttpMethod, string $sUri, bool $bReload = false) : array {
    if ($bReload || empty($this->aCurrentRoutes)) {
      foreach ($this->aRoutes as $oRoute) {
        if (!$oRoute->supportsHttpMethod($sHttpMethod) && !$oRoute->supportsHttpMethod('ANY')) {
          continue;
        }

        if ($oRoute->matches($sUri)) {
          $this->aCurrentRoutes[] = $oRoute;
        }
      }
    }

    return $this->aCurrentRoutes;
  }

}

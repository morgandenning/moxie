<?php

namespace moxie\core;

class base {

  public $aSystem = [];

  protected static $oMoxie = null;

  protected $aEvents = [
              'moxie.prepend' => [[]],
              'moxie.prepend.router' => [[]],
              'moxie.prepend.dispatch' => [[]],
              'moxie.append.dispatch' => [[]],
              'moxie.append.router' => [[]],
              'moxie.append' => [[]]
            ],
            $aDefaults = [
              'html.path' => './pub/html',
              'view.model' => '\moxie\output\view'
            ];


  public function __construct(array $aSettings = []) {
    $this->aSystem = new \moxie\storage\basic(['settings' => array_merge($this->aDefaults, $aSettings)]);

    $this->aSystem->instance('oServer', function($x) : \moxie\core\instantiatable {
      return \moxie\core\server::instance();
    });

    $this->aSystem->instance('oRouter', function($x) : \moxie\routing\router {
      return new \moxie\routing\router;
    });

    $this->aSystem->instance('oRequest', function($x) : \moxie\http\request {
      return new \moxie\http\request;
    });

    $this->aSystem->instance('oResponse', function($x) : \moxie\http\response {
      return new \moxie\http\response;
    });

    $this->aSystem->instance('oView', function($x) : \moxie\output\view {
      return (($this->aSystem->settings['view.model'] instanceof \moxie\output\view) ? $this->aSystem->settings['view.model'] : new  $this->aSystem->settings['view.model'])->setHtmlPath($this->aSystem->settings['html.path']);
    });


    self::$oMoxie = $this;
  }


  public function __get(string $sKey) {
    return $this->aSystem->{$sKey} ?? null;
  }
  public function __set(string $sKey, $xValue) {
    $this->aSystem->{$sKey} = $xValue;
  }
  public function __isset(string $sKey) : bool {
    return isset($this->aSystem->{$sKey});
  }
  public function __unset(string $sKey) {
    unset($this->aSystem->{$sKey});
  }

  static public function __callStatic(string $sMethod, $aParams) {
    return self::$oMoxie;
  }

  //

  public static function oSystem() : \moxie\core\base {
    return $this;
  }

  public function oServer() : \moxie\core\server {
    return $this->oServer;
  }
  public function oRouter() : \moxie\routing\router {
    return $this->oRouter;
  }
  public function oRequest() : \moxie\http\request {
    return $this->oRequest;
  }
  public function oResponse() : \moxie\http\response {
    return $this->oResponse;
  }

  /** Primary Functions **/


  /** Routing **/
  public function any(...$aArgs) {
    //return $this->addRoute(...$aArgs)->via('ANY');
    return \moxie\routing\route::add(...$aArgs)->via(\moxie\http\request\methods::ANY);
  }
  public function get(...$aArgs) {
    return \moxie\routing\route::add(...$aArgs)->via(\moxie\http\request\methods::GET);
  }
  public function post(...$aArgs) {
    return \moxie\routing\route::add(...$aArgs)->via(\moxie\http\request\methods::POST);
  }

  /** Execute Routes **/
  public function execute() {
    try {
      \moxie\http\response\headers::start();

      $bRouteExecuted = false;
      $aRoutes = $this->oRouter->matches($this->oServer['REQUEST_METHOD'], ($this->oServer['PATH_INFO'] ?? $this->oServer['REQUEST_URI']));

      foreach ($aRoutes as $oRoute) {
        try {
          if (($bRouteExecuted = $oRoute->execute()) !== false) {
            break;
          }
        } catch (\Exception $e){}
      }

      if (!$bRouteExecuted) {
        $this->notFound();
      }

    } catch (\Exception $e){}


    if (headers_sent() === false) {
      $this->oResponse->setHeaders();
    }

    if (!\moxie\http\request::head()) {
      echo $this->oResponse->content();
    }
  }

  /** Error Handling **/

  public function notFound() {
    //
  }
}

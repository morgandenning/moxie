<?php

namespace moxie\core;

class base {

  public $aSystem = [];

  protected static $oMoxie = null;

  protected $aDefaults = [
              'paths' => [
                'html' => './pub/html',
                'js' => './pub/js',
                'css' => './pub/css'
              ],
              'namespaces' => [
                'response' => '\moxie\http\response',
                'view' => '\moxie\output\view'
              ]
            ];

  public function __construct(array $aSettings = []) {
    $this->aSystem = new \moxie\storage\basic(['settings' => array_merge($this->aDefaults, $aSettings)]);

    $this->aSystem->instance('oServer', function($x) : \moxie\core\server {
      return \moxie\core\server::instance();
    });

    $this->aSystem->instance('oRouter', function($x) : \moxie\routing\router {
      return new \moxie\routing\router;
    });

    $this->aSystem->instance('oRequest', function($x) : \moxie\http\request {
      return new \moxie\http\request;
    });

    $this->aSystem->instance('oResponse', function($x) : \moxie\http\response {
      return (($this->aSystem->settings['namespaces']['response'] instanceof \moxie\output\response) ? $this->aSystem->settings['namespaces']['response'] : (new  $this->aSystem->settings['namespaces']['response']));
      //return new \moxie\http\response;
    });

    $this->aSystem->instance('oView', function($x) : \moxie\output\view {
      return (($this->aSystem->settings['namespaces']['view'] instanceof \moxie\output\view) ? $this->aSystem->settings['namespaces']['view'] : new  $this->aSystem->settings['namespaces']['view'])->setHtmlPath($this->aSystem->settings['namespaces']['view']);
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
      \moxie\http\responses\headers::start();

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

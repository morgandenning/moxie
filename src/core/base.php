<?php

namespace moxie\core;

class base {

  public $aDependencies = [];

  protected $aApps = [];
  protected $aDispatchers = [
    'moxie.prepend' => [[]],
    'moxie.prepend.router' => [[]],
    'moxie.prepend.dispatch' => [[]],
    'moxie.append.dispatch' => [[]],
    'moxie.append.router' => [[]],
    'moxie.append' => [[]]
  ];
  protected $aDefaults = [
    'mode' => 'dev',
    'debug' => true,
    'html.path' => './pub/html',
    'view.class' => '\moxie\output\view'
  ];


  public function __construct(array $aSettings = []) {
    $this->aDependencies = new \moxie\storage\basic(['settings' => array_merge($this->aDefaults, $aSettings)]);

    $this->aDependencies->get('oSsytem', function($x) {
      return \moxie\core\system::get();
    });

    //
  }

  public function __get(string $sKey) {
    return $this->aDependencies[$sKey];
  }
  public function __set(string $sKey, $xValue) {
    $this->aDependencies[$sKey] = $xValue;
  }
  public function __isset(string $sKey) : bool {
    return isset($this->aDependencies[$sKey]);
  }
  public function __unset(string $sKey) {
    unset($this->aDependencies[$sKey]);
  }

  //

  public function oSystem() : \moxie\core\system {
    return $this->oSystem;
  }
  public function oRouter() : \moxie\core\router {
    return $this->oRouter;
  }
  public function oRequest() : \moxie\http\request {
    return $this->oRequest;
  }
  public function oResponse() : \moxie\http\response {
    return $this->oResponse;
  }

  /** Primary Functions **/

  public function
  public function dispatch(){}
  public function halt(){}
}

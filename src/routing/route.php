<?php

namespace moxie\routing;


class route extends \moxie\core\instantiatable {
  protected $sPattern,
            $fCallback,
            $aParams = [],
            $aParamsParsed = [],
            $aParamsOptionals = [],
            $aRequestMethods = [],
            $aConstraints = [];

  public function __construct(string $sPattern, $xCallback = null) {
    $this->sPattern = $sPattern;
    $this->fCallback = $this->setCallback($xCallback);

    \moxie\core\base::instance()->oRouter()->add($this);
  }

  public static function add(...$aArgs) {
    return new self(...$aArgs);
  }


  public function via(...$aArgs) {
    $this->aRequestMethods = array_merge($this->aRequestMethods ?? [], $aArgs);
    return $this;
  }

  public function constrain(array $constraints) {
    $this->aConstraints += $constraints;
  }

  public function matches(string $sUri) : bool {
    var_dump('sPattern');
    var_dump($this->sPattern);

    var_dump('preg_replace');
    var_dump(preg_replace('#(\)(?!.*\)))#', ')?', $this->sPattern));

    $regex = preg_replace_callback('#:([A-Za-z_]+)\+?#', function(array $aMatches) {
      $this->aParamsParsed[] = $aMatches[1];

      var_dump($this->aParamsParsed);
      var_dump('aMatches');
      var_dump($aMatches);

      if (substr($aMatches[0], -1) === '+') {
        echo 'optionals';
        $this->aParamsOptionals[$aMatches[1]] = true;
        return "(?<{$aMatches[1]}>.+)";
      }

      return ("(?<{$aMatches[1]}>" . ($this->aConstraints[$aMatches[1]] ?? '[^/].+') . ')');

    }, preg_replace('#(\)(?!.*\)))#', ')?', $this->sPattern));

    var_dump('regex');
    var_dump($regex);


    if (!preg_match('#^' . $regex . (substr($this->sPattern, -1) === '/' ? '?' : '') . '$#', $sUri, $aParamValues)) {
      echo 'return false';
      return false;
    }
    var_dump('paramValues');
    var_dump($aParamValues);
    var_dump($this->aParamsParsed);
    var_dump($this->aParamsOptionals);

    foreach ($this->aParamsParsed as $sName) {
      $this->aParams[$sName] = (isset($this->aParamsOptionals[$sName])) ? explode('/', urldecode($aParamValues[$sName] ?? null)) : urldecode($aParamValues[$sName] ?? null);
    }
    return true;
  }

  public function getCallback() {
    return $this->fCallback;
  }
  public function setCallback($xCallback) {

    if (is_string($xCallback) && preg_match('#^([^\:]+)\:(\:)?([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)$#', $xCallback, $aMatches)) {
      $xCallback = function(...$aArgs) use ($aMatches) {
        return (new $aMatches[1])->{$aMatches[3]}(...$aArgs);
      };
    }

    if (!is_callable($xCallback))
      throw new \InvalidArgumentException('invalid route callback');

    return ($this->fCallback = (\Closure::bind($xCallback, \moxie\core\base::instance())));
  }

  public function execute() {
    var_dump('execute');
    return !(($this->fCallback)(...array_values($this->aParams)) === false);
  }

  public function supportsHttpMethod(string $sMethod) : bool {
    return in_array($sMethod, $this->aRequestMethods);
  }
}

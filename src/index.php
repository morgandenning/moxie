<?php

require dirname(dirname(__FILE__)) . '/vendor/autoload.php';

class test {
  public static function __callStatic(string $sName, array $aArgs) {
    var_dump('test:__callstatic');
  }

  public static function testing(int $iId) {
    echo 'do testing';
    var_dump(func_get_args());
    var_dump($iId);

    var_dump(\moxie\core\base::instance());
  }
}

  ($oMoxie = new \moxie\core\base())->get('/test/:id(/:params+)', function($id, $aParams) {

    var_dump('test route');
    var_dump("id:");
    var_dump($id);
    var_dump('aParams');
    var_dump($aParams);

    var_dump('this');
    var_dump($this);

  });
  $oMoxie->any('/', function() use ($oMoxie) {
    var_dump('null route');
  });


  $oMoxie->any('/testing(/:id)', 'test:testing')->constrain(['id' => '([0-9]{1,})']);

  $oMoxie->execute();

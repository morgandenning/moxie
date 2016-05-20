<?php

require dirname(dirname(__FILE__)) . '/vendor/autoload.php';

  $oMoxie = new \moxie\core\base();


echo $oMoxie->add(1, 2), PHP_EOL;
echo $oMoxie->_tNumber, PHP_EOL;

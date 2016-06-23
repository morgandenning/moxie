<?php

namespace moxie\http\request;

class methods {
  const GET = 'GET';
  const POST = 'POST';
  const PUT = 'PUT';
  const PATCH = 'PATCH';
  const DELETE = 'DELETE';
  const HEAD = 'HEAD';
  const OPTIONS = 'OPTIONS';

  // Any Method
  const ANY = 'ANY';


  public function __get(string $sKey) {
    var_dump('get');
  }

  public function __getStatic(string $sKey) {
    var_dump('get_static');
  }
}

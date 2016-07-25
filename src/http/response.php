<?php

namespace moxie\http;

class response {
  protected $iStatus = 200,
            $iLength = null,
            $aCookies = [],
            $sContent = '',
            $oHeaders;

  /** Status Codes **/

  protected static $aHttpStatusCodes = [
    //Informational 1xx
    100 => "100 Continue",
    101 => "101 Switching Protocols",
    //Successful 2xx
    200 => "200 OK",
    201 => "201 Created",
    202 => "202 Accepted",
    203 => "203 Non-Authoritative Information",
    204 => "204 No Content",
    205 => "205 Reset Content",
    206 => "206 Partial Content",
    //Redirection 3xx
    300 => "300 Multiple Choices",
    301 => "301 Moved Permanently",
    302 => "302 Found",
    303 => "303 See Other",
    304 => "304 Not Modified",
    305 => "305 Use Proxy",
    306 => "306 (Unused)",
    307 => "307 Temporary Redirect",
    //Client Error 4xx
    400 => "400 Bad Request",
    401 => "401 Unauthorized",
    402 => "402 Payment Required",
    403 => "403 Forbidden",
    404 => "404 Not Found",
    405 => "405 Method Not Allowed",
    406 => "406 Not Acceptable",
    407 => "407 Proxy Authentication Required",
    408 => "408 Request Timeout",
    409 => "409 Conflict",
    410 => "410 Gone",
    411 => "411 Length Required",
    412 => "412 Precondition Failed",
    413 => "413 Request Entity Too Large",
    414 => "414 Request-URI Too Long",
    415 => "415 Unsupported Media Type",
    416 => "416 Requested Range Not Satisfiable",
    417 => "417 Expectation Failed",
    418 => "418 I'm a teapot",
    422 => "422 Unprocessable Entity",
    423 => "423 Locked",
    //Server Error 5xx
    500 => "500 Internal Server Error",
    501 => "501 Not Implemented",
    502 => "502 Bad Gateway",
    503 => "503 Service Unavailable",
    504 => "504 Gateway Timeout",
    505 => "505 HTTP Version Not Supported"
  ];
  /** End Status Codes **/

  public function __construct(string $sContent = '', int $iStatus = 200, array $aHeaders = [], string $sContentType = 'text/html') {
    $this->iStatus = $iStatus;
    ($this->oHeaders = new responses\headers(['Content-Type' => $sContentType]))->replace($aHeaders);
    $this->write($sContent);
  }

  public function setHeaders() {

    header((strpos(PHP_SAPI, 'cgi') === 0) ? sprintf('Status %s', self::$aHttpStatusCodes[$this->iStatus]) : sprintf('HTTP/1.1 %s', self::$aHttpStatusCodes[$this->iStatus]) );

    foreach ($this->oHeaders as $sName => $xVal) {
      $aValues = explode("\n", $xVal);
      array_walk($aValues, function($x) use ($sName) {
        header("{$sName}: {$x}");
      });
    }
  }

  public function clearHeaders() {
    //
  }

  public function write($sContent, bool $bReplace = false) : string {
    if ($bReplace) {
      $this->sContent = '';
    }

    $this->sContent .= (string)$sContent;
    $this->iLength = strlen($this->sContent);

    return $this->sContent;
  }


  public function status() {
    return $this->iStatus;
  }
  public function headers() {
    return $this->oHeaders;
  }
  public function content() {
    return $this->sContent;
  }


  public function output() {
    return [$this->iStatus, $this->oHeaders, $this->sContent];
  }

}

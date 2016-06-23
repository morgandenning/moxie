<?php

namespace moxie\output;

class view {
  protected $sHtmlPath;


  public function setHtmlPath(string $sPath) {
    if (!file_exists($sPath)) {
      throw new \moxie\exceptions\FileNotFoundException();
    }

    $this->sHtmlPath = $sPath;
  }
}

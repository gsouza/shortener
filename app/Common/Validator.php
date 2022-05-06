<?php

namespace App\Common;

class Validator
{

  public static function isUrlValid($url) {
    return filter_var($url, FILTER_VALIDATE_URL);
  }
}
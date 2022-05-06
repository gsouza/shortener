<?php

namespace App\Common;

use stdClass;

class Utils
{
  public static function checkRequired(array $exist, $request) {

    foreach($exist as $item) {
      if (!isset($request->{strtolower($item)}) && !isset($request->{strtoupper($item)}) && isset($request->{$item}))
        return false;
    }
    return true;
  }

  public static function ret($arg) {
    
    if (!$arg)
      return self::retFail();
    
    $_arg = (($arg instanceof stdClass || is_array($arg)) ? $arg : null);
    return self::retOK($_arg);
  }

  public static function retFail($err = null) {
    $ret = new stdClass();
    $ret->success = false;
    $ret->err = $err ?? __("Error");
    $ret->msg = $ret->err;
    return $ret;
  }

  public static function retOK($arg = null) {
    $ret = new stdClass();
    $ret->success = true;
    
    $arg = (object)$arg;

    if (isset($arg->msg))
      $ret->msg = $arg->msg;
    else 
      $ret->msg = __('Success');

    if (isset($arg->data))
      $ret->data = $arg->data;

    return $ret;
  }

  public static function retBackWith($ret) {
    
    $ret = self::getObject($ret);

    if (isset($ret->scalar)){
      $ret->success = $ret->scalar;
      unset($ret->scalar);
    }

    if (!$ret || !$ret->success)
      return [ 'msg' => $ret->msg ?? __("Error") ];

    if ($ret->success)
      return [ 'msg' => $ret->msg ?? __('Success') ];
  }

  public static function getObject($obj) {
    if (isset($obj) && !$obj instanceof stdClass) 
      $obj = (object)$obj;

    return $obj;
  }

}
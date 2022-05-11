<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Common\Utils;
use App\Jobs\CrawlerJob;

class Shortener extends Model
{
  use HasFactory;

  protected $table = "link_table";

  public static function storeLink($params) {

    $retLink = null;
    $item = new Shortener;
    $exists = self::checkIfExists($params->link);

    if ($exists != null ) {      
      $item->access++;
      $item->id = $exists->id;
      $item->exists = $ret = true;
      $retLink = $exists->unique_id;
    } 
    else {
      $item = new Shortener;
      $item->access = 1;
      $item->original_link = $params->link;
      $item->unique_id = md5(uniqid(rand(), true));
      $ret = $item->save();
      $retLink = $item->unique_id;

      dispatch(new CrawlerJob(['uid' => $item->unique_id, 'url' => $params->link, 'class'=>'\App\Models\Shortener', 'func' =>'updateUrlTitle']));
    }

    $arg = [
      'success' => $ret ?? false,
      'data' => [
        'link' => $retLink,
      ],
    ];

    return Utils::ret($arg);
  }

  public static function getMoreAccessedLinks() {
    
    $links = Shortener::where('access',"!=",'')
                      ->orderBy('access', 'DESC')
                      ->take(100)
                      ->get(['id','access','original_link as link']);
    $ret = [];
    if ($links) {
      $links = $links->toArray();
      foreach($links as $l)
        array_push($ret, $l['link']);

      return $ret;
    }

    return null;
  }

  public static function getLink($uid) {

    $link = Shortener::where('unique_id','=',$uid)
                    ->get(['id','original_link','access'])
                    ->first();

    if ($link != null) {
      $link->exists = true;
      $link->access++;
      $link->save();

      return (object)['link' => $link->original_link];
    }

    return null;
  }

  public static function updateUrlTitle($params) {
  
    $params = Utils::getObject($params);
    $title = $params->title;
    $uid = $params->uid;

    if (!$short = Shortener::where('unique_id','=',$uid)->get()->first()) 
      return 
    logger('short:'. $short->toArray());

    $short->title = $title;
    $short->exists = 1;
    $ret = $short->save();

    return $ret;
  }
  
  /**PRIVATE METHODS */
  private static function checkIfExists($link) {

    $link = Shortener::where('original_link','=',$link)
        ->get(['access','id','unique_id'])
        ->first();

    return $link == null? $link : Utils::getObject($link);
  }


}

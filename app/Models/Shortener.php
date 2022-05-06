<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Common\Utils;
use Goutte\Client;
use Symfony\Component\HttpClient\HttpClient;

class Shortener extends Model
{
  use HasFactory;

  protected $table = "link_table";

  public static function storeLink($params) {

    $retLink = null;
    $item = new Shortener;
    $exists = self::checkIfExists($params->link);

    if ($exists != null ) {      
      $item->id = $exists->id;
      $item->exists = $ret = true;
      $item->access++;
      $retLink = $exists->unique_id;
    } 
    else {
      $item = new Shortener;
      $item->unique_id = md5(uniqid(rand(), true));
      $item->title = self::getUrlTitle($params->link);
      $item->original_link = $params->link;
      $item->access = 1;
      $ret = $item->save();

      $retLink = $item->unique_id;
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

  /**PRIVATE METHODS */
  private static function checkIfExists($link) {

    $link = Shortener::where('original_link','=',$link)
        ->get(['access','id','unique_id'])
        ->first();

    return $link == null? $link : Utils::getObject($link);
  }


  private static function getUrlTitle($url) {
   
    $client = new Client(HttpClient::create(['timeout' => 60]));

    //event dispatch :: not working for now
    $crawler = $client->request('GET', $url);
    $webTitle = $crawler->filter('title')->each(function ($node) {
      return $node->text();
    });

    return $webTitle?$webTitle[0]:'';
  }

}

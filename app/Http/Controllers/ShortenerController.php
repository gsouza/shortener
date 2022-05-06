<?php

namespace App\Http\Controllers;

use App\Models\Shortener;
use Illuminate\Http\Request;

use App\Common\Utils;
use App\Common\Validator;

class ShortenerController extends Controller
{

  public static function storeLink(Request $req) {

    $args = $req->toArray();
    if (!Utils::checkRequired($args, ['link'])) {
      return back()->with(Utils::retFail('All fields are required'));
    }

    if (!Validator::isUrlValid($req->link)) {
      return back()->with(Utils::retFail('Invalid Url given'));
    }

    $params =(object)[
      'link' => $req->link,
    ];

    $ret = Shortener::storeLink($params);
    if (!$ret || !$ret->success) {
      return redirect()->back()->with(['msg'=>'error']);
    }

    return Utils::ret($ret);
  }

  public static function gotoShortten() {

    $uid = request('uid');
    if (!$uid)  
      return back();

    if (!$ret = Shortener::getLink($uid))
      return back();

    return redirect()->away($ret->link);
  }

  public static function getMoreAccessedLink() {

    $links = Shortener::getMoreAccessedLinks();
    
    if (!$links)
      Utils::retFail();

    return Utils::ret([
      'success' => true, 
      'data' => [
        'links' => $links
      ]
    ]);
  }

  public static function getLinkShortened(Request $req) {

    $args = [request('uid')];
    if (!Utils::checkRequired($args, ['uid'])) {
       return back()->with(Utils::retFail('Uid field are required'));
    }

    if (!$ret = Shortener::getLink($req->uid))
      return Utils::ret([
        'success' => false,
        'msg' => 'Link not found',
      ]);
    
    return Utils::ret([
      'success' => true, 
      'data' =>  [
        'linkOfUid' => $ret->link
      ]
    ]);
  }

}

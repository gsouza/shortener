<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Goutte\Client;
use Symfony\Component\HttpClient\HttpClient;

use App\Common\Utils;

class CrawlerJob implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  private $url = null;
  private $uid = null;
  private $class = null;
  private $func = null;
  /**
   * Create a new job instance.
   *
   * @return void
   */
  public function __construct($arg)
  {
    $arg = Utils::getObject($arg);
    $this->url = $arg->url;
    $this->uid = $arg->uid;
    $this->func = $arg->func;
    $this->class = $arg->class;
  }

  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle()
  {
    $client = new Client(HttpClient::create(['timeout' => 60]));

    $crawler = $client->request('GET', $this->url);
    $webTitle = $crawler->filter('title')->each(function ($node) {
      return $node->text();
    });

    if ($this->class && $this->func) {
      if (!class_exists($this->class))
        return;
      [$this->class, $this->func](['title'=> ($webTitle ? $webTitle[0] : ''), 'uid'=>$this->uid]);
    }

  }
}
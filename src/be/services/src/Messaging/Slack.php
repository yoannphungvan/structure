<?php

namespace PROJECT\Services;

use Frlnc\Slack\Http\SlackResponseFactory;
use Frlnc\Slack\Http\CurlInteractor;
use Frlnc\Slack\Core\Commander;

class Slack {

  private $commander;

  /**
   * contructor
   */
  public function __construct($app)
  {
    $this->slackKey  = $app['slack.key'];
    $interactor = new CurlInteractor;
    $interactor->setResponseFactory(new SlackResponseFactory);
    $this->commander = new Commander($this->slackKey, $interactor);
  }

  public function sendMessageToChannel($channel, $from = null, $message = null, $status = null, $icon = null)
  {
    $params = [];

    if (!empty($channel)) {
      $params['channel'] = $channel;
    } 

    if (!empty($from)) {
      $params['username'] = $from;
    }

    if (!empty($message)) {
      $params['text'] = $message;
    }  
    
    if (!empty($icon)) {
      $params['icon_emoji'] = $icon;
    } 

    if (!empty($status)) {
      $icon = null;
      switch($status) {
        case 'success':
           $icon = ':white_check_mark:';
           break;
        case 'warning':
           $icon = ':warning:';
           break;
        case 'error':
           $icon = ':x:';
           break;
      }

      if (!empty($icon)) {
        $params['icon_emoji'] = $icon;
      }
    } 


    $response = $this->commander->execute('chat.postMessage', $params);
  }
}

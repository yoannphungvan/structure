<?php

namespace PROJECT\Services;

class Firebase {

  private $url;
  private $token;
  private $defaultPath;
  private $firebase;

  public function __construct($url, $token, $defaultPath = '')
  {
      $this->url = $url;
      $this->token = $token;
      $this->defaultPath = $defaultPath;
      $this->firebase = new \Firebase\FirebaseLib($this->url, $this->token);
  }

  /**
   * Store data in Firebase
   * @param string $path  path to save data
   * @param array $data   data to save
   */
  public function set($path, $data, $multibrand = false)
  {

    return $this->firebase->set($this->getDefaultPath($multibrand) . '/' . $path, $data);
  }

  /**
   * Get data from Firebase
   * @param  string $path path
   * @return array        data from firebase
   */
  public function get($path, $multibrand = false)
  {
    return $this->firebase->get($this->getDefaultPath($multibrand) . '/' .$path);
  }

  /**
   * Delete from Firebase
   * @param  string $path path
   * @return array
   */
  public function delete($path, $multibrand = false)
  {
    return $this->firebase->delete($this->getDefaultPath($multibrand) . '/' .$path);
  }

  /**
   * Push to firebase. Generate a new child using a unique key and returns a Firebase reference
   * @param string $path  path to save data
   * @param array $data data to save
   */
  public function push($path, $data = null, $multibrand = false)
  {
    return $this->firebase->push($this->getDefaultPath($multibrand) . '/' .$path, $data);
  }

  /**
   * get Url
   * @return string url
   */
  public function getUrl()
  {
    return $this->url;
  }

  /**
   * get token
   * @return string token
   */
  public function getToken()
  {
    return $this->token;
  }

  /**
   * get default path
   * @return string default path
   */
  public function getDefaultPath()
  {
    return $this->defaultPath;
  }

  public function getTimestamp()
  {
    // Default timestamp in microsecond in case there is an issue with firebase, we use by default php timestamp
    $defaultTimestamp = time() . '000';

    // This date is sent to firebase to get a timestamp. We create a child named server/timestamp to get a timestamp
    $data = [
      'timestamp' => [
        '.sv' => 'timestamp'
      ]
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $this->url . $this->getDefaultPath() .'/server.json' );
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    $output = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($code == 200) {
      $output = json_decode(curl_exec($ch));
      curl_close($ch);
      return $output->timestamp;
    }
    curl_close($ch);
    return $defaultTimestamp;
  }
}

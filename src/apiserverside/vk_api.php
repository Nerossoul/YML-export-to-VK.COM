<?php
require_once ('config.php');

function _vkApi_call($method, $params = array()) {
  $params['v'] = VK_API_VERSION;
  $query = http_build_query($params);
  $url = VK_API_ENDPOINT.$method.'?'.$query;
  $curl = curl_init($url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  $json = curl_exec($curl);
  $error = curl_error($curl);
  if ($error) {
    throw new Exception("Failed {$method} request");
  }
  curl_close($curl);
  $response = json_decode($json, true);
  return $response;
}

function _vkApi_post_call($method, $params = array()) {
  $params['v'] = VK_API_VERSION;
  $postdata = http_build_query($params);
  $opts = array('http' =>
      array(
        'method'  => 'POST',
        'header'  => 'Content-type: application/x-www-form-urlencoded',
        'content' => $postdata
      )
  );
  $url = VK_API_ENDPOINT.$method;
  $context  = stream_context_create($opts);
  $response = file_get_contents($url, false, $context);
  return $response;
}

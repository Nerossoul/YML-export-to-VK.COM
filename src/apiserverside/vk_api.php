<?php

define('VK_API_ENDPOINT', 'https://api.vk.com/method/');

function _vkApi_call($method, $params = array()) {

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

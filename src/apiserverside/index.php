<?php 
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once 'vk_api.php';

function _callback_getEvent() {
  return json_decode(file_get_contents('php://input'), true);
}

function _callback_response($data) {
  echo $data;
  exit();
}

$callObj = _callback_getEvent();
if ($callObj['method'] == 'upload_file') {
  $result = vkApi_upload($callObj['params']['upload_url'], $callObj['params']['file_link']);
} elseif (isset($callObj['method'])) {
  $result = _vkApi_post_call($callObj['method'], $callObj['params']);
} else {
  $result = ['wrong method'];
}
  _callback_response(json_encode($result));
?>
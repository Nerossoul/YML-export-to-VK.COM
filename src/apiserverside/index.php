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
$result = _vkApi_call($callObj['method'], $callObj['params']);
_callback_response(json_encode($result));
?>
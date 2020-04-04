<?php

require_once 'vk_api.php';

function _callback_getEvent()
{
    return json_decode(file_get_contents('php://input'), true);
}

function _callback_response($data)
{
    echo json_encode($data);
    exit();
}

$callObj = _callback_getEvent();
if ($callObj['method'] === 'upload_file') {
    $result = vkApi_upload($callObj['params']['upload_url'], $callObj['params']['file_link']);
} elseif ($callObj['method'] === 'update_photobase') {
    $result = updatePhotoBase($callObj['params']['link'], $callObj['params']['photo_id'], $callObj['params']['productVendorCode']);
} elseif ($callObj['method'] === 'saveLastNumber') {
    $result = saveLastNumber($callObj['params']['lastNumber']);
} elseif (isset($callObj['method'])) {
    $result = _vkApi_post_call($callObj['method'], $callObj['params']);
} else {
    $result = ['wrong method'];
}
_callback_response($result);
<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once 'config.php';

function _vkApi_call($method, $params = array())
{
    $params['v'] = VK_API_VERSION;
    $query = http_build_query($params);
    $url = VK_API_ENDPOINT . $method . '?' . $query;
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

function _vkApi_post_call($method, $params = array())
{
    $params['v'] = VK_API_VERSION;
    $postdata = http_build_query($params);
    $opts = array('http' => array(
        'method' => 'POST',
        'header' => 'Content-type: application/x-www-form-urlencoded',
        'content' => $postdata,
    ),
    );
    $url = VK_API_ENDPOINT . $method;
    $context = stream_context_create($opts);
    $response = file_get_contents($url, false, $context);
    return $response;
}

function vkApi_upload($upload_url, $file_link)
{
    $path = (parse_url($file_link));
    $file_link = $_SERVER['DOCUMENT_ROOT'] . $path['path'];
    if (!file_exists($file_link)) {
        $response = array('error' => 'File not found: ' . $file_link);
        return $response;
    }
    $upload_url = stripslashes($upload_url);
    $upload_url = stripslashes($upload_url);
    $curl = curl_init($upload_url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, array('file' => new CURLfile($file_link)));
    curl_setopt($curl, CURLOPT_PORT, 443);
    $json = curl_exec($curl);
    $error = curl_error($curl);
    if ($error) {
        $response = array(
            'error' => array(
                'error_text' => 'Failed {$upload_url} request',
                'error_full' => $error,
            ),
        );
        return $response;
    }
    curl_close($curl);
    $response = json_decode($json, true);
    if (!$response) {
        $response = array(
            'error' => "Invalid response for {$upload_url} request",
        );
        return $response;
    }

    return $response;
}
function updatePhotoBase($link, $photoId, $productVendorCode)
{
    $photoBaseArray = getPhotoBaseData();
    $photoBaseArray[$productVendorCode][$link] = $photoId;
    savePhotoBase($photoBaseArray);
    return array(
        'response' => 'Photo base is updated',
    );
}

function saveLastNumber($lastNumber)
{
    $fileName = 'lastproduct.json';
    file_put_contents($fileName, $lastNumber, LOCK_EX);
    return array(
        'response' => 'LAST NUMBER SAVED',
    );
}

function getPhotoBaseData()
{
    $fileName = 'photobase.json';
    if (file_exists($fileName)) {
        $baseJsonString = file_get_contents($fileName);
        return json_decode($baseJsonString, true);
    }
    return array();
}
function savePhotoBase($photoBaseArray)
{
    $fileName = 'photobase.json';
    file_put_contents($fileName, json_encode($photoBaseArray), LOCK_EX);
}
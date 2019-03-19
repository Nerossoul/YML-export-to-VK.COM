<?php 
require_once ('config.php');

if (isset($_GET[code])) {
  // Получение access_token Для получения access_token необходимо выполнить запрос с Вашего сервера на https://oauth.vk.com/access_token, передав следующие параметры: 
  $request_params = array(
    'client_id' => APP_ID, //обязательный	Идентификатор Вашего приложения
    'client_secret' => APP_SECRET, //обязательный	Защищенный ключ Вашего приложения (указан в настройках приложения)
    'redirect_uri' => REDIRECT_URI, //обязательный	URL, который использовался при получении code на первом этапе авторизации. Должен быть аналогичен переданному при авторизации.
    'code' => $_GET[code], //Временный код, полученный после прохождения авторизации.
    'v' => VK_API_VERSION, //Версия API
    );
  $get_params = http_build_query($request_params);
  $result = json_decode(file_get_contents('https://oauth.vk.com/access_token?' . $get_params));
  if (isset($result -> access_token)) {
    if (isset($_GET[dev])) { 
      $new_url = DEVELOPER_CLIENT_URI . "?access_token=" . $result -> access_token;
    } else {
      $new_url = CLIENT_URI . "?access_token=" . $result -> access_token;
    }    
    header('Location: ' . $new_url);
  } else {    
    echo '<br> Something goes wrong. Press BACK button and try again<br>';
    
  }
            
} else { 
  echo '<br> No code!!! Press BACK button and try again <br>';
} ?>






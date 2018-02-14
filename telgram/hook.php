<?php
// Load composer
//file_put_contents("ok",json_encode([$_GET,$_SERVER,$_POST]));die;
use Longman\TelegramBot\Request;
require __DIR__ . '/vendor/autoload.php';
require __DIR__. '/set.php';
//phpinfo();die;
$bot_api_key  = '518376306:AAGsQp7cBACvPfUjtWRMVAkVF6JTRjT9MV4';
$bot_username ="bitokbitbot";
$hook_url = 'https://telgram.bitneworld.com/hook.php';

try {
    // Create Telegram API object
    $telegram = new Longman\TelegramBot\Telegram($bot_api_key, $bot_username);


// Define all paths for your custom commands in this array (leave as empty array if not used)
$commands_paths = [
    __DIR__ . '/Commands',
    __DIR__.'/src/Commands'
];
// Add this line inside the try{}
$mysql_credentials = [
   'host'     => 'localhost',
   'user'     => 'jack',
   'password' => '350166483Qp!',
   'database' => 'bitcoin',
];

$telegram->enableMySql($mysql_credentials);
$telegram->addCommandsPaths($commands_paths);

$telegram->handle();
$message=json_decode(stripslashes(trim(file_get_contents("php://input"),chr(239).chr(187).chr(191))),true);
$chat_id=$message['message']['chat']['id'];
$text=$message['message']['text'];
switch ($text) {
  case 'ud83cudf88u53d1u5e03u51fau552eud83dudc49':
    Request::sendMessage(['chat_id' => $chat_id,'text'=> "/inputsell"]);
    break;
  case 'ud83cudf88u53d1u5e03u8d2du4e70ud83dudc48':
    Request::sendMessage(['chat_id' => $chat_id,'text'=> "/inputbuy"]);
    break;
  case 'ud83dudd04u6211u8981u51fau552eud83dudc49':
    Request::sendMessage(['chat_id' => $chat_id,'text'=> "/gosell"]);
    break;
  case 'ud83dudd04u6211u8981u8d2du4e70ud83dudc48':
    Request::sendMessage(['chat_id' => $chat_id,'text'=> "/gobuy"]);
    break;
  case 'ud83dudc71u200du2642ufe0fu4e2au4ebau4e2du5fc3ud83dudc71u200du2642ufe0f':
    Request::sendMessage(['chat_id' => $chat_id,'text'=> "/myinfo"]);
    break;
  default:
    # code...
    break;
}



    // Set webhook
  //  $result = $telegram->setWebhook($hook_url);
  //   $result = $telegram->
   // if ($result->isOk()) {
    //    echo $result->getDescription();
  //  }
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    // log telegram errors
 //   echo $e->getMessage();
}

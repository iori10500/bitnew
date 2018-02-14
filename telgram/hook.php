<?php
// Load composer
//file_put_contents("ok",json_encode([$_GET,$_SERVER,$_POST]));die;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\DB;
use PDO;

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
  case 'ud83cudf88u53d1u5e03u51fau552eud83dudc49':   //inputsell
    Request::sendMessage(windowsinfo($chat_id,'发布出售',[['title'=>'    ','des'=>'请按照格式输入发布订单：                             <strong>/inputsell 数量-单价-支付说明  </strong>(例如：  <em>/inputsell 1.2-55432-支付宝账号 350177483@qq.com,谢谢！</em>)']]));
    break;
  case 'ud83cudf88u53d1u5e03u8d2du4e70ud83dudc48':  //inputbuy
    Request::sendMessage(windowsinfo($chat_id,'发布购买',[['title'=>'    ','des'=>'请按照格式输入发布订单：                             <strong>/inputbuy 数量-单价-支付说明  </strong>(例如：  <em>/inputbuy 1.2-55432-本人只支持支付宝付款，请留意</em>)']]));
    break;
  case 'ud83dudd04u6211u8981u51fau552eud83dudc49':   //gosell
    Request::sendMessage(getorder($chat_id,2,0));
    break;
  case 'ud83dudd04u6211u8981u8d2du4e70ud83dudc48':    //gobuy
    Request::sendMessage(getorder($chat_id,3,0));
    break;
  case 'ud83dudc71u200du2642ufe0fu4e2au4ebau4e2du5fc3ud83dudc71u200du2642ufe0f':
    Request::sendMessage(windowsinfo($chat_id,'个人中心',[],[[['text'=>'接收比特币','callback_data'=>"balance"]],[['text'=>'发送比特币','callback_data'=>"sendbitcoin"]],[['text'=>'订单中心','callback_data'=>"myorder"]],[['text'=>'联系我们','callback_data'=>"contentus"]]]));
    break;
  case 'ud83dude4du9080u8bf7u597du53cbud83dude4d':
  Request::sendMessage(windowsinfo($chat_id,'邀请好友',[['title'=>'    ','des'=>'邀请好友加入,您的下级每发生一笔订单,您将获得0.00001btc奖励']]));        // Send message!
  $time=time(); 
  Request::sendMessage( windowsinfo($chat_id,'邀请链接',[['title'=>$message['message']['chat']['username'],'des'=>"<a href='https://t.me/bitokbitbot?start=$chat_id&time=$time'>电币比特币c2c交易平台</a>"]]));        // Send message!


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
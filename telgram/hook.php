<?php
// Load composer
//file_put_contents("ok",json_encode([$_GET,$_SERVER,$_POST]));die;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\DB;


require __DIR__ . '/vendor/autoload.php';
require __DIR__. '/set.php';
ini_set('date.timezone','Asia/Shanghai');
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

if(!empty($message['message'])){
      $chat_id=$message['message']['chat']['id'];
      $text=$message['message']['text'];
      if(is_numeric($text) && strpos($text,'2018')===0){
        $orderid=substr($text,8);
        $sth = DB::getPdo()->prepare('
                SELECT *
                FROM `' . "bitorder" . '`
                WHERE  id=:orderid');  
        $sth->bindValue(':orderid', $orderid);
        $sth->execute();
        $order = $sth->fetchAll(PDO::FETCH_ASSOC);
        if(!empty($order)){
            $order=$order[0];
            if($order['owner'] == $chat_id){
                Request::sendMessage(getorder($chat_id,1,0,$orderid));
            }else{
                if($order['buy_sell'] == 1){
                    Request::sendMessage(getorder($chat_id,2,0,$orderid));
                }else if ($order['buy_sell'] == 0){
                    Request::sendMessage(getorder($chat_id,3,0,$orderid));
                }
            }
            exit();
        }
      }
      switch ($text) {
        case 'ud83dudd25u4ef7u683cu884cu60c5ud83dudd25':   //inputsell
            $buttoninfo['chat_id']=$chat_id;
            $buttoninfo['photo']='http://telgram.bitneworld.com/pchart/example25.png?token='.md5(date("Y-m-d H:i",time()));
            Request::sendPhoto($buttoninfo);

        break;

        case 'ud83cudf88u53d1u5e03u51fau552eud83dudc49':   //inputsell
          Request::sendMessage(windowsinfo($chat_id,'发布出售',[['title'=>'    ','des'=>'请按照格式输入发布订单'],['title'=>'格式','des'=>'/inputsell 数量-单价'],['title'=>'例如','des'=>'/inputsell 1.2-55432']]));

//          $buttoninfo['chat_id']=$chat_id;
 //         $buttoninfo['photo']='http://telgram.bitneworld.com/app/inputsell.png';
 //         Request::sendPhoto($buttoninfo);        // Send me

          break;
        case 'ud83cudf88u53d1u5e03u8d2du4e70ud83dudc48':  //inputbuy
          Request::sendMessage(windowsinfo($chat_id,'发布购买',[['title'=>'    ','des'=>'请按照格式输入发布订单'],['title'=>'格式','des'=>'/inputbuy 数量-单价'],['title'=>'例如','des'=>'/inputbuy 1.2-55432)']]));

        //   $buttoninfo['chat_id']=$chat_id;
        //  $buttoninfo['photo']='http://telgram.bitneworld.com/app/inputbuy.png';
        //  Request::sendPhoto($buttoninfo);        // Send me
          break;
        case 'ud83dudd04u6211u8981u51fau552eud83dudc49':   //gosell
          Request::sendMessage(getorder($chat_id,2,0));
          break;
        case 'ud83dudd04u6211u8981u8d2du4e70ud83dudc48':    //gobuy
          Request::sendMessage(getorder($chat_id,3,0));
          break;
        case 'ud83dudc71u200du2642ufe0fu4e2au4ebau4e2du5fc3ud83dudc71u200du2642ufe0f':
          Request::sendMessage(windowsinfo($chat_id,'个人中心',[],[[['text'=>'📥接收比特币','callback_data'=>"balance"]],[['text'=>'📤发送比特币','callback_data'=>"sendbitcoin"]],[['text'=>'🎯订单中心','callback_data'=>"myorder"]],[['text'=>'🔻下级订单','callback_data'=>"suborder"]],[['text'=>'💸设置收款','callback_data'=>"setcollections"]],[['text'=>'📧联系我们','callback_data'=>"contentus"]],[['text'=>'❔交易说明','callback_data'=>"jiaoyimark"]]]));
          break;
        case 'ud83dude4du9080u8bf7u597du53cbud83dude4d':
         $sth = DB::getPdo()->prepare('SELECT `first_name` FROM `' . TB_USER . '` WHERE `id` ='.$chat_id);
         $sth->execute();
         $username = $sth->fetchColumn();

          Request::sendMessage(windowsinfo($chat_id,'邀请好友',[['title'=>'    ','des'=>'邀请好友加入,您的下级每发生一笔订单,您将获得0.0001BTC奖励']]));        // Send message!
          $time=time(); 
          if($chat_id == 468426414){
              Request::sendMessage( windowsinfo($chat_id,'邀请链接',[['title'=>$username,'des'=>"<a href='https://t.me/bitokbitbot?start=$chat_id&time=$time'>电报比特币交易专区</a>"],['title'=>'    ','des'=>' @bitokbitbot ']]));
          }else{
             Request::sendMessage( windowsinfo($chat_id,'邀请链接',[['title'=>$username,'des'=>"<a href='https://t.me/bitokbitbot?start=$chat_id&time=$time'>电报比特币交易专区</a>"]]));
          }
                 // Send message!
          break;
        default:
          $pdo  = DB::getPdo();
          $col_flag=$pdo->query('SELECT `col_flag` from user where id='.$chat_id)->fetchColumn();
          if($col_flag){  
            $systemcommde[]="invitenote";
            $systemcommde[]="inputsell";
            $systemcommde[]="inputbuy";
            $systemcommde[]="balance";
            $systemcommde[]="gobuy";
            $systemcommde[]="gosell";
            $systemcommde[]="invitenote";
            $systemcommde[]="myinfo";
            $systemcommde[]="myorder";
            $systemcommde[]="button";
            $systemcommde[]="inorder";
            $systemcommde[]="outorder";
            $systemcommde[]="outorders";
            $systemcommde[]="nextmyorder";
            $systemcommde[]="cancelorder";
            $systemcommde[]="cancelpay";
            $systemcommde[]="finishpay";
            $systemcommde[]="adminorder";
            $systemcommde[]="fangxingorder";
            $systemcommde[]="gotorder";
            $systemcommde[]="canceltemporder";
            $systemcommde[]="canceltemporders";
            $systemcommde[]="sendbitcoin";
            $systemcommde[]="setcollections";
            $systemcommde[]="contentus";
            $systemcommde[]="send";
            $systemcommde[]="start";
            $systemcommde[]="letgo";
            $iscommend=false;
            foreach ($systemcommde as $key => $value) {
              if(strpos(strtolower($text),$value)){
                  $iscommend=true;
                  break;
              }
            }
            if(!$iscommend){
              $text=json_decode(file_get_contents("php://input"),true)['message']['text'];
               $sth = $pdo->prepare('update user set collections_bak=:collections where id=:id ');
                $sth->bindValue(':id', $chat_id);
                $sth->bindValue(':collections', $text);
                $sth->execute();
                Request::sendMessage(windowsinfo($chat_id,'设置收款信息',[['title'=>'    ','des'=>$text]],[[['text'=>'确认','callback_data'=>"setcollections-1"],['text'=>'取消','callback_data'=>"setcollections-0"]]]));
            }
           

          }
          break;
      }
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
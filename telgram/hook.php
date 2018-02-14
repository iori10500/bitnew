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
  case 'ud83cudf88u53d1u5e03u51fau552eud83dudc49':
    Request::sendMessage(inputsell($message));
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



function inputsell($message){
        $chat_id=$message['message']['chat']['id'];
        $text=$message['message']['text'];
        $text=trim(str_replace("/inputsell","",$text));
        if(empty($text)){
            $data=windowsinfo($chat_id,'发布出售',[['title'=>'    ','des'=>'请按照格式输入发布订单：/inputsell 数量-单价-支付说明  (例如：  /inputsell 1.2-55432-支付宝账号 350177483@qq.com,谢谢！)']]);
        }else{
            $text = explode('-',$text);
            if(count($text) >= 3){
                $num = (float)$text[0];
                $price = (float)$text[1];
                $allprice=$num*$price;
                unset($text[0]);unset($text[1]);
                $des="";
                foreach ($text as $key => $value) {
                    $des.=$value;
                }
                $cancel['action']='button';
                $cancel['title']='发布出售';
                $cancel['message']='取消发布';
                $cancel['chat_id']=$chat_id;



                $orderinfo['action']='inputorder';
                $orderinfo['num']=$num;
                $orderinfo['price']=$price;
                $orderinfo['allprice']=$allprice;
                $orderinfo['des']=$des;
                $orderinfo['chat_id']=$chat_id;

                try {
                    //余额检测

                    $sth = DB::getPdo()->prepare('
                        SELECT `walletId`,`socked`,`banlance`
                        FROM `' . TB_USER . '`
                        WHERE `id` = :id 
                        LIMIT 1
                    ');

                    $sth->bindValue(':id', $chat_id);
                    $sth->execute();
                    $tempinfo = $sth->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($tempinfo as $key => $one) {
                        $yueinfo = yue($tempinfo['walletId']);
                        $walletbanlance=$yueinfo['balance']+$one['banlance'];
                        if($walletbanlance >= $num){
                            $sth = DB::getPdo()->prepare('
                                INSERT INTO `' . "bitorder_temp" . '`
                                (`buy_sell`, `seller_id`, `price`, `num`,`state`,`create_time`,`owner`,`des`)
                                VALUES
                                (:buy_sell, :seller_id, :price, :num,:state, :create_time, :owner,:des)
                            ');
                            $sth->bindValue(':buy_sell', '0');
                            $sth->bindValue(':seller_id', $chat_id);
                            $sth->bindValue(':price', $price);
                            $sth->bindValue(':num', $num);
                            $sth->bindValue(':state', '0');
                            $sth->bindValue(':create_time', date("Y-m-d H:i:s",time()));
                            $sth->bindValue(':owner', $chat_id);
                            $sth->bindValue(':des', $des);

                            $sth->execute();

                            $sth = DB::getPdo()->prepare('SELECT LAST_INSERT_ID() as lastid ');
                            $sth->execute();
                            $lastid=$sth->fetchColumn();
                            $data=windowsinfo($chat_id,'发布出售',[['title'=>'单价','des'=>$price],['title'=>'数量','des'=>$num],['title'=>'总价','des'=>$allprice],['title'=>'支付','des'=>$des]],[[['text'=>'确认','callback_data'=>"outorders-$lastid"],['text'=>'取消','callback_data'=>"canceltemporders-$lastid"]]]);
                        }else{
                            $data=windowsinfo($chat_id,'发布出售',[['title'=>'    ','des'=>'销售数量大于账户余额']]);
                        }
                    }
                   
                } catch (Exception $e) {
                    throw new TelegramException($e->getMessage());
                } 
     
               

            }else{
                $data=windowsinfo($chat_id,'发布出售',[['title'=>'    ','des'=>'格式不正确']]);
            }
            
        }
        return $data;
}
<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;
use Longman\TelegramBot\DB;
use PDO;

class InputbuyCommand extends UserCommand
{
    protected $name = 'inputbuy';                      // Your command's name
    protected $description = 'A command for Inputbuy'; // Your command description
    protected $usage = '/inputbuy';                    // Usage of your command
    protected $version = '1.0.0';                  // Version of your command

    public function execute()
    {
        $message = $this->getMessage();            // Get Message object
        $chat_id = $message->getChat()->getId();   // Get the current Chat ID
        $text=json_decode(json_encode($message),true)['text'];
        $text=trim(str_replace("/inputbuy","",$text));
        if(empty($text)){
            $data=windowsinfo($chat_id,'发布购买',[['title'=>'    ','des'=>'请按照格式输入发布订单：/inputbuy 数量-单价  (例如：  /inputbuy 1.2-55432)']]);
        }else{
            $text = explode('-',$text);
            if(count($text) >= 2){
                $num = round((float)$text[0],8);
                $price = round((float)$text[1],2);
                $allprice=round($num*$price,2);
                if($allprice <=0 ){
                    return Request::sendMessage(windowsinfo($chat_id,'发布购买',[['title'=>'    ','des'=>'发布订单价格错误']]));
                }

                unset($text[0]);unset($text[1]);
                $des="";
                foreach ($text as $key => $value) {
                    $des.=$value;
                }
                $cancel['action']='button';
                $cancel['title']='发布购买';
                $cancel['message']='取消发布';
                $cancel['chat_id']=$chat_id;


                $orderinfo['action']='inputorder';
                $orderinfo['num']=$num;
                $orderinfo['price']=$price;
                $orderinfo['allprice']=$allprice;
                $orderinfo['des']=$des;
                $orderinfo['chat_id']=$chat_id;

                $pdo  = DB::getPdo();
                try {

                    $pdo->beginTransaction();
                    $sth = $pdo->prepare('
                        SELECT `id` 
                        FROM `' . "bitorder" . '`
                        WHERE `buyer_id` = :id and state=1 and  :time-start_time<1800 
                        LIMIT 1
                    ');
                    $time=time();
                    $sth->bindValue(':id', $chat_id);
                    $sth->bindValue(':time', $time);
                    $sth->execute();
                    $tempinfo = $sth->fetchAll(PDO::FETCH_ASSOC);
                    if(!empty($tempinfo)){
                        return Request::sendMessage(windowsinfo($chat_id,'购买订单',[['title'=>'    ','des'=>'你存在未支付订单,请支付或者取消订单']]));
                    }

                    $sth = $pdo->prepare('
                        INSERT INTO `' . "bitorder_temp" . '`
                        (`buy_sell`, `buyer_id`, `price`, `num`,`state`,`create_time`,`owner`,`des`)
                        VALUES
                        (:buy_sell, :buyer_id, :price, :num,:state, :create_time, :owner,:des)
                    ');
                    $sth->bindValue(':buy_sell', '1');
                    $sth->bindValue(':buyer_id', $chat_id);
                    $sth->bindValue(':price', $price);
                    $sth->bindValue(':num', $num);
                    $sth->bindValue(':state', '0');
                    $sth->bindValue(':create_time', date("Y-m-d H:i:s",time()));
                    $sth->bindValue(':owner', $chat_id);
                    $sth->bindValue(':des', $des);

                    $sth->execute();

                    $sth = $pdo->prepare('SELECT LAST_INSERT_ID() as lastid ');
                    $sth->execute();
                    $lastid=$sth->fetchColumn();

                    $pdo->commit();   
                } catch (Exception $e) {
                     $pdo->rollBack();   
                    throw new TelegramException($e->getMessage());
                } 
                
                $data=windowsinfo($chat_id,'发布购买',[['title'=>'单价','des'=>$price],['title'=>'数量','des'=>$num],['title'=>'总价','des'=>$allprice],['title'=>'支付','des'=>$des]],[[['text'=>'确认','callback_data'=>"outorder-$lastid"],['text'=>'取消','callback_data'=>"canceltemporder-$lastid"]]]);

            }else{
                $data=windowsinfo($chat_id,'发布购买',[['title'=>'    ','des'=>'格式不正确']]);
            }
            
        }
        
        return Request::sendMessage($data);        // Send message!
    }
}

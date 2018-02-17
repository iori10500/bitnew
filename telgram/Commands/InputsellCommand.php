<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;
use Longman\TelegramBot\DB;
use PDO;

class InputsellCommand extends UserCommand
{
    protected $name = 'inputsell';                      // Your command's name
    protected $description = 'A command for Inputsell'; // Your command description
    protected $usage = '/inputsell';                    // Usage of your command
    protected $version = '1.0.0';                  // Version of your command

    public function execute()
    {
        $message = $this->getMessage();            // Get Message object
        $chat_id = $message->getChat()->getId();   // Get the current Chat ID
        $text=json_decode(json_encode($message),true)['text'];
        $text=trim(str_replace("/inputsell","",$text));
        if(empty($text)){
            $data=windowsinfo($chat_id,'发布出售',[['title'=>'    ','des'=>'请按照格式输入发布订单：/inputsell 数量-单价  (例如：  /inputsell 1.2-55432)']]);
        }else{
            $text = explode('-',$text);
            if(count($text) >= 3){
                $num = round((float)$text[0],8);
                $price = round((float)$text[1],2);
                $allprice=round($num*$price,2);
                if($allprice <=0 ){
                    return Request::sendMessage(windowsinfo($chat_id,'发布出售',[['title'=>'    ','des'=>'发布订单价格错误']]));
                }
                $pdo  = DB::getPdo();
                $collections=$pdo->query('SELECT `collections` from user where id='.$chat_id)->fetchColumn();
                if(!$collections){
                    return Request::sendMessage(windowsinfo($chat_id,'发布出售',[['title'=>'    ','des'=>'发布失败，请先设置收款信息，再发布。个人中心->收款信息']]));
                }
                $des=$collections;
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
                $pdo  = DB::getPdo();
                try {
                    //余额检测
                   
                    $pdo->beginTransaction();
                    $sth = $pdo->prepare('
                        SELECT `id` 
                        FROM `' . "bitorder" . '`
                        WHERE `seller_id` = :id and state=2  
                        LIMIT 1
                    ');

                    $sth->bindValue(':id', $message->getFrom()->getId());
                    $sth->execute();
                    $tempinfo = $sth->fetchAll(PDO::FETCH_ASSOC);
                    if(!empty($tempinfo)){
                        return Request::sendMessage(windowsinfo($chat_id,'发布出售',[['title'=>'    ','des'=>'你存在未放行订单,请放行之后再发布']]));
                    }

                    $sth = $pdo->prepare('
                        SELECT `walletId`,`socked`,`banlance`
                        FROM `' . TB_USER . '`
                        WHERE `id` = :id 
                        LIMIT 1
                    ');

                    $sth->bindValue(':id', $message->getFrom()->getId());
                    $sth->execute();
                    $tempinfo = $sth->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($tempinfo as $key => $one) {
                        $yueinfo = yue($tempinfo['walletId']);
                        $walletbanlance=$yueinfo['balance']+$one['banlance'];
                        if($walletbanlance >= $num){
                            $sth = $pdo->prepare('
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

                            $sth = $pdo->prepare('SELECT LAST_INSERT_ID() as lastid ');
                            $sth->execute();
                            $lastid=$sth->fetchColumn();

                            $data=windowsinfo($chat_id,'发布出售',[['title'=>'单价','des'=>$price],['title'=>'数量','des'=>$num],['title'=>'总价','des'=>$allprice],['title'=>'支付','des'=>$des]],[[['text'=>'确认','callback_data'=>"outorders-$lastid"],['text'=>'取消','callback_data'=>"canceltemporders-$lastid"]]]);
                        }else{
                            $data=windowsinfo($chat_id,'发布出售',[['title'=>'    ','des'=>'销售数量大于账户余额']]);
                        }
                    }
                   
                    $pdo->commit();    
                } catch (Exception $e) {
                    $pdo->rollBack();   
                    throw new TelegramException($e->getMessage());
                } 
     
               

            }else{
                $data=windowsinfo($chat_id,'发布出售',[['title'=>'    ','des'=>'格式不正确']]);
            }
            
        }
        return Request::sendMessage($data);        // Send message!
    }
}

<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;


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
            $data=windowsinfo($chat_id,'发布购买',[['title'=>'    ','des'=>'请按照格式输入发布订单：/inputbuy 数量-单价-支付说明  (例如：  /inputbuy 1.2-55432)']]);
        }else{
            $text = explode('-',$text);
            if(count($text) >= 2){
                $num = (float)$text[0];
                $price = (float)$text[1];
                $allprice=$num*$price;
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
                /*
                orderinfo
                入库

                */
                $orderid=1;
                
                
                $data=windowsinfo($chat_id,'发布购买',[['title'=>'单价','des'=>$price],['title'=>'数量','des'=>$num],['title'=>'总价','des'=>$allprice]],[[['text'=>'确认','callback_data'=>"outorder-$orderid"],['text'=>'取消','callback_data'=>"button-取消发布成功"]]]);

            }else{
                $data=windowsinfo($chat_id,'发布购买',[['title'=>'    ','des'=>'格式不正确']]);
            }
            
        }
        
        return Request::sendMessage($data);        // Send message!
    }
}

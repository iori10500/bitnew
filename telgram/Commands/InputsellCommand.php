<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;


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
                /*
                orderinfo
                入库

                */
                $orderid=1;
                
                
                $data=windowsinfo($chat_id,'发布出售',[['title'=>'单价','des'=>$price],['title'=>'数量','des'=>$num],['title'=>'总价','des'=>$allprice],['title'=>'支付','des'=>$des]],[[['text'=>'确认','callback_data'=>"inorder-$orderid"],['text'=>'取消','callback_data'=>"button-取消发布成功"]]]);

            }else{
                $data=windowsinfo($chat_id,'发布出售',[['title'=>'    ','des'=>'格式不正确']]);
            }
            
        }
        
        return Request::sendMessage($data);        // Send message!
    }
}
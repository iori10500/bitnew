<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;
use Longman\TelegramBot\DB;
use PDO;

class MyorderCommand extends UserCommand
{
    protected $name = 'myorder';                      // Your command's name
    protected $description = 'A command for myorder'; // Your command description
    protected $usage = '/myorder';                    // Usage of your command
    protected $version = '1.0.0';                  // Version of your command

    public function execute()
    {
        $message = $this->getMessage();            // Get Message object
        $chat_id = $message->getChat()->getId();   // Get the current Chat ID
        //我的订单分为   发布订单   市场购买订单非支付等待状态 市场销售订单非支付等待状态 市场购买等待支付30分钟内 市场销售等待支付30分钟内
        $sth = DB::getPdo()->prepare('
                SELECT *
                FROM `' . "myorder" . '`
                WHERE `owner` = :id or (`seller` = :id  and `state` =1 and time()-start-time<1800 ) or (`buyer` = :id  and `state` =1 and time()-start-time<1800  ) or (`seller` = :id  and `state` !=1) or (`buyer` = :id  and `state` !=1)
                LIMIT 1
            ');
        $sth->bindValue(':id', $message->getFrom()->getId());
        $sth->execute();
        $order = $sth->fetchAll(PDO::FETCH_ASSOC);

        foreach ($order as $key => $one) {
            if($one['owner'] == 0){
                //邀请奖励
                $orderinfo['orderclass']='下级返利';
                $orderinfo['num']=123;
                $orderinfo['username']='JJJACK';
                $orderinfo['datetime']=date("Y-m-d H:i:s",time());
                $data=windowsinfo($chat_id,$orderinfo['orderclass'],[['title'=>'返利数量','des'=>$orderinfo['num']],['title'=>'下级名称','des'=>$orderinfo['username']],['title'=>'返利时间','des'=>$orderinfo['datetime']]]);
            }else{
                if($one['buyer'] == 123){
                    $orderinfo['orderid']=123;
                    $orderinfo['orderclass']='购买订单';
                    $orderinfo['price']=123;
                    $orderinfo['num']=123;
                    $orderinfo['allprice']=123;
                    $orderinfo['statedec']='等待交易';
                    $orderinfo['mark']='请用支付宝付款';

                    $orderinfo['create_time']=date("Y-m-d H:i:s",time());
                    $orderinfo['start_buy']=date("Y-m-d H:i:s",time());
                    $orderinfo['remain_time']=(time()-12324234)/60;

                    switch ($one['state']) {
                        case '0':
                            $data=windowsinfo($chat_id,$orderinfo['orderclass'],[['title'=>'单价','des'=>$orderinfo['price']],['title'=>'数量','des'=>$orderinfo['num']],['title'=>'总价','des'=>$orderinfo['allprice']],['title'=>'状态','des'=>$orderinfo['statedec'],['title'=>'支付','des'=>$orderinfo['mark']],['title'=>'建时','des'=>$orderinfo['create_time']]],[[['text'=>'取消订单','callback_data'=>"cancelorder-".$orderinfo['orderid']]],[['text'=>'上一条','callback_data'=>"nextmyorder-".$limit],['text'=>'下一条','callback_data'=>"nextmyorder-".$limit]]]);
                            
                            break;
                        case '1':
                            $data=windowsinfo($chat_id,$orderinfo['orderclass'],[['title'=>'单价','des'=>$orderinfo['price']],['title'=>'数量','des'=>$orderinfo['num']],['title'=>'总价','des'=>$orderinfo['allprice']],['title'=>'状态','des'=>$orderinfo['statedec'],['title'=>'支付','des'=>$orderinfo['mark']],['title'=>'提示','des'=>"请在 ".$orderinfo['remain_time']." 分钟内完成支付"]],[[['text'=>'取消付款','callback_data'=>"cancelpay-".$orderinfo['orderid']],['text'=>'付款完成','callback_data'=>"finishpay-".$orderinfo['orderid']]],[['text'=>'上一条','callback_data'=>"nextmyorder-".$limit],['text'=>'下一条','callback_data'=>"nextmyorder-".$limit]]]);

                            
                            break;
                        case '2':
                            $data=windowsinfo($chat_id,$orderinfo['orderclass'],[['title'=>'单价','des'=>$orderinfo['price']],['title'=>'数量','des'=>$orderinfo['num']],['title'=>'总价','des'=>$orderinfo['allprice']],['title'=>'状态','des'=>$orderinfo['statedec'],['title'=>'支付','des'=>$orderinfo['mark']],['title'=>'提示','des'=>"放行将在 ".$orderinfo['remain_time']." 分钟内完成"]],[[['text'=>'申诉','callback_data'=>"adminorder-".$orderinfo['orderid']]],[['text'=>'上一条','callback_data'=>"nextmyorder-".$limit],['text'=>'下一条','callback_data'=>"nextmyorder-".$limit]]]);
                            
                            break;
                        case '3':
                            $data=windowsinfo($chat_id,$orderinfo['orderclass'],[['title'=>'单价','des'=>$orderinfo['price']],['title'=>'数量','des'=>$orderinfo['num']],['title'=>'总价','des'=>$orderinfo['allprice']],['title'=>'状态','des'=>$orderinfo['statedec'],['title'=>'支付','des'=>$orderinfo['mark']]],[[['text'=>'上一条','callback_data'=>"nextmyorder-".$limit],['text'=>'下一条','callback_data'=>"nextmyorder-".$limit]]]);
                            
                            break;
                        default:
                            
                            break;
                    }
                }else if($one['seller'] == 123){
                    $orderinfo['orderid']=123;
                    $orderinfo['orderclass']='销售订单';
                    $orderinfo['price']=123;
                    $orderinfo['num']=123;
                    $orderinfo['allprice']=123;
                    $orderinfo['statedec']='等待交易';
                    $orderinfo['mark']='请用支付宝付款';

                    $orderinfo['create_time']=date("Y-m-d H:i:s",time());
                    $orderinfo['start_buy']=date("Y-m-d H:i:s",time());
                    $orderinfo['remain_time']=(time()-12324234)/60;

                    switch ($one['state']) {
                        case '0':
                            $data=windowsinfo($chat_id,$orderinfo['orderclass'],[['title'=>'单价','des'=>$orderinfo['price']],['title'=>'数量','des'=>$orderinfo['num']],['title'=>'总价','des'=>$orderinfo['allprice']],['title'=>'状态','des'=>$orderinfo['statedec'],['title'=>'支付','des'=>$orderinfo['mark']],['title'=>'建时','des'=>$orderinfo['create_time']]],[[['text'=>'取消订单','callback_data'=>"cancelorder-".$orderinfo['orderid']]],[['text'=>'上一条','callback_data'=>"nextmyorder-".$limit],['text'=>'下一条','callback_data'=>"nextmyorder-".$limit]]]);
                            
                            break;
                        case '1':
                            $data=windowsinfo($chat_id,$orderinfo['orderclass'],[['title'=>'单价','des'=>$orderinfo['price']],['title'=>'数量','des'=>$orderinfo['num']],['title'=>'总价','des'=>$orderinfo['allprice']],['title'=>'状态','des'=>$orderinfo['statedec'],['title'=>'支付','des'=>$orderinfo['mark']],['title'=>'提示','des'=>"将在 ".$orderinfo['remain_time']." 分钟内完成支付"]],[[['text'=>'上一条','callback_data'=>"nextmyorder-".$limit],['text'=>'下一条','callback_data'=>"nextmyorder-".$limit]]]);

                            
                            break;
                        case '2':
                            $data=windowsinfo($chat_id,$orderinfo['orderclass'],[['title'=>'单价','des'=>$orderinfo['price']],['title'=>'数量','des'=>$orderinfo['num']],['title'=>'总价','des'=>$orderinfo['allprice']],['title'=>'状态','des'=>$orderinfo['statedec'],['title'=>'支付','des'=>$orderinfo['mark']],['title'=>'提示','des'=>"请在 ".$orderinfo['remain_time']." 分钟内完成放行"]],[[['text'=>'申诉','callback_data'=>"adminorder-".$orderinfo['orderid']],['text'=>'放行','callback_data'=>"fangxingorder-".$orderinfo['orderid']] ],[['text'=>'上一条','callback_data'=>"nextmyorder-".$limit],['text'=>'下一条','callback_data'=>"nextmyorder-".$limit]]]);
                            
                            break;
                        case '3':
                            $data=windowsinfo($chat_id,$orderinfo['orderclass'],[['title'=>'单价','des'=>$orderinfo['price']],['title'=>'数量','des'=>$orderinfo['num']],['title'=>'总价','des'=>$orderinfo['allprice']],['title'=>'状态','des'=>$orderinfo['statedec'],['title'=>'支付','des'=>$orderinfo['mark']]],[[['text'=>'上一条','callback_data'=>"nextmyorder-".$limit],['text'=>'下一条','callback_data'=>"nextmyorder-".$limit]]]);
                            
                            break;
                        default:
                            
                            break;
                    }

                }
            }
        }
   
        return Request::sendMessage($data);        // Send message!
    }
}

//myorder=1 buy=2  sell=3
function getorder($chat_id,$whorder,$limit){


}

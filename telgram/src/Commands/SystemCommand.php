<?php
/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Longman\TelegramBot\Commands;

use Longman\TelegramBot\Request;

abstract class SystemCommand extends Command
{
    /**
     * A system command just executes
     *
     * Although system commands should just work and return a successful ServerResponse,
     * each system command can override this method to add custom functionality.
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     */
    public function execute()
    {
        //System command, return empty ServerResponse by default
        return Request::emptyResponse();
    }
}



//myorder=1 buy=2  sell=3
function getorder($chat_id,$whorder,$limit){
     $DESC=[
            1=>"我的订单",
            2=>"购买交易", 
            3=>"销售交易"    
        ];
    if($limit<0){
        return  $data=windowsinfo($chat_id,$DESC[$whorder],[['title'=>'    ','des'=>'到顶啦']]);
    }
    if($whorder == 1){//我的订单
        //我的订单分为   发布订单   市场购买订单非支付等待状态 市场销售订单非支付等待状态 市场购买等待支付30分钟内 市场销售等待支付30分钟内
        $time=time();
        $sth = DB::getPdo()->prepare('
                SELECT *
                FROM `' . "bitorder" . '`
                WHERE `owner` = :id or (`seller_id` = :id  and `state` =1 and :time-start_time<1800 ) or (`buyer_id` = :id  and `state` =1 and :time-start_time<1800  ) or (`seller_id` = :id  and `state` !=1) or (`buyer_id` = :id  and `state` !=1)
                LIMIT 1
            ');
        $sth->bindValue(':time', $time);
        $sth->bindValue(':id', $chat_id);
        $sth->execute();
        $order = $sth->fetchAll(PDO::FETCH_ASSOC);
        if(empty($order)){
             return  $data=windowsinfo($chat_id,$DESC[$whorder],[['title'=>'    ','des'=>'到底啦']]);
        }

        foreach ($order as $key => $one) {
            if($one['owner'] == 0){
                //邀请奖励
                $orderinfo['orderclass']='下级返利';
                $orderinfo['num']=$one['num'];
                $orderinfo['username']=$one['des'];
                $orderinfo['datetime']=$one['create_time'];
                $data=windowsinfo($chat_id,$orderinfo['orderclass'],[['title'=>'返利数量','des'=>$orderinfo['num']],['title'=>'下级名称','des'=>$orderinfo['username']],['title'=>'返利时间','des'=>$orderinfo['datetime']]]);
            }else{
                $orderinfo['orderid']=$one['id'];
                
                $orderinfo['price']=$one['price'];
                $orderinfo['num']=$one['num'];
                $orderinfo['allprice']=$one['price']*$one['num'];
                $orderinfo['statedec']=$DESCREBACTION[$one['state']];
                $orderinfo['mark']=$one['des'];

                $orderinfo['create_time']=date("Y-m-d H:i:s",$one['create_time']);
                $orderinfo['start_buy']=date("Y-m-d H:i:s",$one['start_time']);
                $orderinfo['remain_time']=(time()-$one['start_time'])/60;

                if($one['buyer_id'] == $chat_id){
                    $orderinfo['orderclass']='购买订单'
                    switch ($one['state']) {
                        case '0':
                            $data=windowsinfo($chat_id,$orderinfo['orderclass'],[['title'=>'单价','des'=>$orderinfo['price']],['title'=>'数量','des'=>$orderinfo['num']],['title'=>'总价','des'=>$orderinfo['allprice']],['title'=>'状态','des'=>$orderinfo['statedec'],['title'=>'支付','des'=>$orderinfo['mark']],['title'=>'建时','des'=>$orderinfo['create_time']]],[[['text'=>'取消订单','callback_data'=>"cancelorder-".$orderinfo['orderid']]],[['text'=>'上一条','callback_data'=>"nextmyorder-$whorder-".($limit-1)],['text'=>'下一条','callback_data'=>"nextmyorder-$whorder-".($limit+1)]]]);
                            
                            break;
                        case '1':
                            $data=windowsinfo($chat_id,$orderinfo['orderclass'],[['title'=>'单价','des'=>$orderinfo['price']],['title'=>'数量','des'=>$orderinfo['num']],['title'=>'总价','des'=>$orderinfo['allprice']],['title'=>'状态','des'=>$orderinfo['statedec'],['title'=>'支付','des'=>$orderinfo['mark']],['title'=>'提示','des'=>"请在 ".$orderinfo['remain_time']." 分钟内完成支付"]],[[['text'=>'取消付款','callback_data'=>"cancelpay-".$orderinfo['orderid']],['text'=>'付款完成','callback_data'=>"finishpay-".$orderinfo['orderid']]],[['text'=>'上一条','callback_data'=>"nextmyorder-$whorder-".($limit-1)],['text'=>'下一条','callback_data'=>"nextmyorder-$whorder-".($limit+1)]]]);

                            
                            break;
                        case '2':
                            $data=windowsinfo($chat_id,$orderinfo['orderclass'],[['title'=>'单价','des'=>$orderinfo['price']],['title'=>'数量','des'=>$orderinfo['num']],['title'=>'总价','des'=>$orderinfo['allprice']],['title'=>'状态','des'=>$orderinfo['statedec'],['title'=>'支付','des'=>$orderinfo['mark']],['title'=>'提示','des'=>"放行将在 ".$orderinfo['remain_time']." 分钟内完成"]],[[['text'=>'申诉','callback_data'=>"adminorder-".$orderinfo['orderid']]],[['text'=>'上一条','callback_data'=>"nextmyorder-$whorder-".($limit-1)],['text'=>'下一条','callback_data'=>"nextmyorder-$whorder-".($limit+1)]]]);
                            
                            break;
                        case '3':
                            $data=windowsinfo($chat_id,$orderinfo['orderclass'],[['title'=>'单价','des'=>$orderinfo['price']],['title'=>'数量','des'=>$orderinfo['num']],['title'=>'总价','des'=>$orderinfo['allprice']],['title'=>'状态','des'=>$orderinfo['statedec'],['title'=>'支付','des'=>$orderinfo['mark']]],[[['text'=>'上一条','callback_data'=>"nextmyorder-$whorder-".($limit-1)],['text'=>'下一条','callback_data'=>"nextmyorder-$whorder-".($limit+1)]]]);
                            
                            break;
                        default:
                            
                            break;
                    }
                }else if($one['seller'] == $chat_id){
                    $orderinfo['orderclass']='销售订单';
                    switch ($one['state']) {
                        case '0':
                            $data=windowsinfo($chat_id,$orderinfo['orderclass'],[['title'=>'单价','des'=>$orderinfo['price']],['title'=>'数量','des'=>$orderinfo['num']],['title'=>'总价','des'=>$orderinfo['allprice']],['title'=>'状态','des'=>$orderinfo['statedec'],['title'=>'支付','des'=>$orderinfo['mark']],['title'=>'建时','des'=>$orderinfo['create_time']]],[[['text'=>'取消订单','callback_data'=>"cancelorder-".$orderinfo['orderid']]],[['text'=>'上一条','callback_data'=>"nextmyorder-$whorder-".($limit-1)],['text'=>'下一条','callback_data'=>"nextmyorder-$whorder-".($limit+1)]]]);
                            
                            break;
                        case '1':
                            $data=windowsinfo($chat_id,$orderinfo['orderclass'],[['title'=>'单价','des'=>$orderinfo['price']],['title'=>'数量','des'=>$orderinfo['num']],['title'=>'总价','des'=>$orderinfo['allprice']],['title'=>'状态','des'=>$orderinfo['statedec'],['title'=>'支付','des'=>$orderinfo['mark']],['title'=>'提示','des'=>"将在 ".$orderinfo['remain_time']." 分钟内完成支付"]],[[['text'=>'上一条','callback_data'=>"nextmyorder-$whorder-".($limit-1)],['text'=>'下一条','callback_data'=>"nextmyorder-$whorder-".($limit+1)]]]);

                            
                            break;
                        case '2':
                            $data=windowsinfo($chat_id,$orderinfo['orderclass'],[['title'=>'单价','des'=>$orderinfo['price']],['title'=>'数量','des'=>$orderinfo['num']],['title'=>'总价','des'=>$orderinfo['allprice']],['title'=>'状态','des'=>$orderinfo['statedec'],['title'=>'支付','des'=>$orderinfo['mark']],['title'=>'提示','des'=>"请在 ".$orderinfo['remain_time']." 分钟内完成放行"]],[[['text'=>'申诉','callback_data'=>"adminorder-".$orderinfo['orderid']],['text'=>'放行','callback_data'=>"fangxingorder-".$orderinfo['orderid']] ],[['text'=>'上一条','callback_data'=>"nextmyorder-$whorder-".($limit-1)],['text'=>'下一条','callback_data'=>"nextmyorder-$whorder-".($limit+1)]]]);
                            
                            break;
                        case '3':
                            $data=windowsinfo($chat_id,$orderinfo['orderclass'],[['title'=>'单价','des'=>$orderinfo['price']],['title'=>'数量','des'=>$orderinfo['num']],['title'=>'总价','des'=>$orderinfo['allprice']],['title'=>'状态','des'=>$orderinfo['statedec'],['title'=>'支付','des'=>$orderinfo['mark']]],[[['text'=>'上一条','callback_data'=>"nextmyorder-$whorder-".($limit-1)],['text'=>'下一条','callback_data'=>"nextmyorder-$whorder-".($limit+1)]]]);
                            
                            break;
                        default:
                            
                            break;
                    }

                }
            }
        }

    }else if($whorder == 2){//购买交易
        $time=time();
        $sth = DB::getPdo()->prepare('
                SELECT *
                FROM `' . "bitorder" . '`
                WHERE `state` =0 and buy_sell=1 and :time-start_time>1800 
                LIMIT '.$limit." , 1");
        $sth->bindValue(':time', $time);
        $sth->execute();
        $order = $sth->fetchAll(PDO::FETCH_ASSOC);
        if(empty($order)){
             $data=windowsinfo($chat_id,$DESC[$whorder],[['title'=>'    ','des'=>'到底啦']]);
        }else{
            $orderinfo=$order[0];
            $orderinfo['allprice']=$orderinfo['num']*$orderinfo['price'];
            $data=windowsinfo($chat_id,$DESC[$whorder],[['title'=>'单价','des'=>$orderinfo['price']],['title'=>'数量','des'=>$orderinfo['num']],['title'=>'总价','des'=>$orderinfo['allprice']],['title'=>'状态','des'=>$DESCREBACTION[$orderinfo['state']],['title'=>'支付','des'=>$orderinfo['des']],['title'=>'建时','des'=>$orderinfo['create_time']]],[[['text'=>'下单','callback_data'=>"gotorder-".$orderinfo['id']]],[['text'=>'上一条','callback_data'=>"nextmyorder-$whorder-".($limit-1)],['text'=>'下一条','callback_data'=>"nextmyorder-$whorder-".($limit+1)]]]);

        }


    }else if($whorder == 3){//销售交易
         $time=time();
        $sth = DB::getPdo()->prepare('
                SELECT *
                FROM `' . "bitorder" . '`
                WHERE `state` =0 and buy_sell=0 and :time-start_time>1800 
                LIMIT '.$limit." , 1");
        $sth->bindValue(':time', $time);
        $sth->execute();
        $order = $sth->fetchAll(PDO::FETCH_ASSOC);
        if(empty($order)){
             $data=windowsinfo($chat_id,$DESC[$whorder],[['title'=>'    ','des'=>'到底啦']]);
        }else{
            $orderinfo=$order[0];
            $orderinfo['allprice']=$orderinfo['num']*$orderinfo['price'];
            $data=windowsinfo($chat_id,$DESC[$whorder],[['title'=>'单价','des'=>$orderinfo['price']],['title'=>'数量','des'=>$orderinfo['num']],['title'=>'总价','des'=>$orderinfo['allprice']],['title'=>'状态','des'=>$DESCREBACTION[$orderinfo['state']],['title'=>'支付','des'=>$orderinfo['des']],['title'=>'建时','des'=>$orderinfo['create_time']]],[[['text'=>'下单','callback_data'=>"gotorder-".$orderinfo['id']]],[['text'=>'上一条','callback_data'=>"nextmyorder-$whorder-".($limit-1)],['text'=>'下一条','callback_data'=>"nextmyorder-$whorder-".($limit+1)]]]);

        }

    }
    return $data;

}


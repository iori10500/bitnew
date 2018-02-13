<?php
use Longman\TelegramBot\DB;
use PDO;

function get($url,$postdata){
        $curl = curl_init();  //初始化
        curl_setopt($curl,CURLOPT_URL,$url);  //设置url
        $header=['Authorization: Bearer v2xcf5c31d68b77cce774c02053dc375c6e0fd8ab4ecfe637220ffeedc364320f32','Content-Type:application/json;charset=utf-8','Accept:application/json'];
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl,CURLOPT_HTTPAUTH,CURLAUTH_BASIC);  //设置http验证方法
//      curl_setopt($curl,CURLOPT_HEADER,0);  //设置头信息
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);  //设置curl_exec获取的信息的返回方式
//      curl_setopt($curl,CURLOPT_POST,1);  //设置发送方式为post请求
//      curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($postdata));  //设置post的数据
        curl_setopt($curl, CURLINFO_HEADER_OUT, TRUE);
        $result = curl_exec($curl);
        if($result === false){
            echo curl_errno($curl);
            exit();
        }
        curl_close($curl);
        return $result;
}
function post($url,$postdata){
        $curl = curl_init();  //初始化
        curl_setopt($curl,CURLOPT_URL,$url);  //设置url
        $header=['Authorization: Bearer v2xcf5c31d68b77cce774c02053dc375c6e0fd8ab4ecfe637220ffeedc364320f32','Content-Type:application/json;charset=utf-8','Accept:application/json'];
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl,CURLOPT_HTTPAUTH,CURLAUTH_BASIC);  //设置http验证方法
//      curl_setopt($curl,CURLOPT_HEADER,0);  //设置头信息
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);  //设置curl_exec获取的信息的返回方式
        curl_setopt($curl,CURLOPT_POST,1);  //设置发送方式为post请求
        curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($postdata));  //设置post的数据
        curl_setopt($curl, CURLINFO_HEADER_OUT, TRUE);
        $result = curl_exec($curl);
        if($result === false){
            echo curl_errno($curl);
            exit();
        }
        curl_close($curl);
        return $result;
}



//$temp=post("https://www.bitgo.com/api/v1/wallet",['label'=>'jack','m'=>2,'n'=>3,'keychains'=>[['xpub'=>'xpub661MywAqRbcGJqvexUHEVce9aiRkYYBeAiZnDSjSGZ93jFMfpcSDp36RPgBF5N1W9hFXVJdBaSAwHWHr5zJ6NTQqKKLRzfKg1saoUPmd5T'],['xpub'=>'xpub6GiRC55CSBpR3Lj2GRNGVxBj4r3fionThEEThPpvdMsPEafNArDvnnghKUuEARb1XZatVhc9oj21UddkKzmqbycxbzLsdFoBrw3LuVNzkmL'],['xpub'=>'xpub661MyMwAqRbcFtrRQTDRcQqacpyKwAPEWePfgAHV6BQEXsyvWBAzHA3B1AwzsiXad7S59ienmLCsPYdLKYyNhruxk3CBv3SrRMTR9VeM935']]]);
//$temp=post("https://nanxiao.cba123.cn",['ok'=>'ok']);
//
//print_r($temp);


//print_r(post('https://www.bitgo.com/api/v1/keychain/bitgo',[]));die;

//print_r(get('https://www.bitgo.com/api/v1/keychain',[]));



function newWallet($username){
	$temp=post("https://www.bitgo.com/api/v1/wallet",['label'=>$username,'m'=>2,'n'=>3,'keychains'=>[['xpub'=>'xpub661MyMwAqRbcGJqvexUHEVce9aiRkYYBeAiZnDSjSGZ93jFMfpcSDp36RPgBF5N1W9hFXVJdBaSAwHWHr5zJ6NTQqKKLRzfKg1saoUPmd5T' ],['xpub'=>'xpub6GiRC55CSBpR3Lj2GRNGVxBj4r3fionThEEThPpvdMsPEafNArDvnnghKUuEARb1XZatVhc9oj21UddkKzmqbycxbzLsdFoBrw3LuVNzkmL'],['xpub'=>json_decode(post('https://www.bitgo.com/api/v1/keychain/bitgo',[]),true)['xpub']]]]);
	return json_decode($temp,true)['id'];
}

function yue($walletId){
    $balance = json_decode(get("https://www.bitgo.com/api/v1/wallet/$walletId",[]),true)['balance'];
    $address = json_decode(post("https://www.bitgo.com/api/v1/wallet/$walletId/address/0",[]),true)['address'];
    return ['balance'=>$balance,'address'=>$address];
}
function windowsinfo($chat_id,$title,$data,$button=false){
    $buttoninfo['chat_id']=$chat_id;
    $buttoninfo['parse_mode']='HTML';
    $text="<code style='background-color:#f80;color:#f80;width:100px'>$title                                            </code>";
    foreach($data as $one){
        $flag=empty(trim($one['title']))?"":":";
        $text.=("<b>".$one['title']."</b>".$flag." ".$one['des'].'                                                                                        ');
    }
    $buttoninfo['text']=$text;
    if($button){
       $inline_keyboard=['inline_keyboard'=>$button]; 
        $buttoninfo['reply_markup']=$inline_keyboard;
    }
   
    return $buttoninfo;
    
}
$DESCREBACTION=[
    '0'=>'等待交易',
    '1'=>'等待付款',
    '2'=>'等待放行',
    '3'=>'交易完成',
    '4'=>'投诉处理',
];

//echo newWallet('okok');
//echo get("https://www.bitgo.com/api/v1/wallet",[]);






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




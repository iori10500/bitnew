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
    $text="<code style='color:#ee772b;width:100px'>$title                                            </code>";
    foreach($data as $one){
        $flag=empty(trim($one['title']))?"":":";
        $text.=("<code style='color:#00b5f6'>".$one['title']."</code>".$flag." ".$one['des'].'                                                                                        ');
    }
    $buttoninfo['text']=$text;
    if($button){
       $inline_keyboard=['inline_keyboard'=>$button]; 
        $buttoninfo['reply_markup']=$inline_keyboard;
    }
   
    return $buttoninfo;
    
}
function startwindows($chat_id,$title,$button=false){
    $buttoninfo['chat_id']=$chat_id;
    $buttoninfo['text']="欢迎加入电币c2c交易平台";
    if($button){
        $inline_keyboard=['keyboard'=>$button,'resize_keyboard'=>true]; 
        $buttoninfo['reply_markup']=$inline_keyboard;
    }   
    return $buttoninfo;
    
}

//echo newWallet('okok');
//echo get("https://www.bitgo.com/api/v1/wallet",[]);






//myorder=1 buy=2  sell=3
function getorder($chat_id,$whorder,$limit){
   $DESCREBACTION=[
        '-1'=>'取消订单',
        '0'=>'等待交易',
        '1'=>'等待付款',
        '2'=>'等待放行',
        '3'=>'交易完成',
        '4'=>'投诉处理',
    ];
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
                WHERE `owner` = :id or (`seller_id` = :id  and `state` =1 and :time-start_time<1800 ) or (`buyer_id` = :id  and `state` =1 and :time-start_time<1800  ) or (`seller_id` = :id  and `state` in (0,2,3,4)) or (`buyer_id` = :id  and `state` in (0,2,3,4))
                order by id desc LIMIT '.$limit.' , 1');
        $sth->bindValue(':time', $time);
        $sth->bindValue(':id', $chat_id);
        $sth->execute();
        $order = $sth->fetchAll(PDO::FETCH_ASSOC);
        if(empty($order)){
             return  windowsinfo($chat_id,$DESC[$whorder],[['title'=>'    ','des'=>'到底啦']]);
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
                $orderinfo['allprice']=round($one['price']*$one['num'],2);
                $orderinfo['statedec']=$DESCREBACTION[$one['state']];
                $orderinfo['mark']=$one['des'];

                $orderinfo['create_time']=$one['create_time'];
                $orderinfo['start_buy']=date("Y-m-d H:i:s",$one['start_time']);
                $orderinfo['remain_time']=30-((time()-$one['start_time'])/60);

                if($one['buyer_id'] == $chat_id){
                    $orderinfo['orderclass']='购买订单';
                    switch ($one['state']) {
                        case '0':
                            $data=windowsinfo($chat_id,$orderinfo['orderclass'],[['title'=>'单价','des'=>$orderinfo['price']],['title'=>'数量','des'=>$orderinfo['num']],['title'=>'总价','des'=>$orderinfo['allprice']],['title'=>'状态','des'=>$orderinfo['statedec']],['title'=>'支付','des'=>$orderinfo['mark']],['title'=>'建时','des'=>$orderinfo['create_time']]],[[['text'=>'取消订单','callback_data'=>"cancelorder-".$orderinfo['orderid']]],[['text'=>'上一条','callback_data'=>"nextmyorder-$whorder-".($limit-1)],['text'=>'下一条','callback_data'=>"nextmyorder-$whorder-".($limit+1)]]]);
                            
                            break;
                        case '1':
                            $data=windowsinfo($chat_id,$orderinfo['orderclass'],[['title'=>'单价','des'=>$orderinfo['price']],['title'=>'数量','des'=>$orderinfo['num']],['title'=>'总价','des'=>$orderinfo['allprice']],['title'=>'状态','des'=>$orderinfo['statedec']],['title'=>'支付','des'=>$orderinfo['mark']],['title'=>'提示','des'=>"请在 ".$orderinfo['remain_time']." 分钟内完成支付"]],[[['text'=>'取消付款','callback_data'=>"cancelpay-".$orderinfo['orderid']],['text'=>'付款完成','callback_data'=>"finishpay-".$orderinfo['orderid']]],[['text'=>'上一条','callback_data'=>"nextmyorder-$whorder-".($limit-1)],['text'=>'下一条','callback_data'=>"nextmyorder-$whorder-".($limit+1)]]]);

                            
                            break;
                        case '2':
                            $data=windowsinfo($chat_id,$orderinfo['orderclass'],[['title'=>'单价','des'=>$orderinfo['price']],['title'=>'数量','des'=>$orderinfo['num']],['title'=>'总价','des'=>$orderinfo['allprice']],['title'=>'状态','des'=>$orderinfo['statedec']],['title'=>'支付','des'=>$orderinfo['mark']],['title'=>'提示','des'=>"放行将在 ".$orderinfo['remain_time']." 分钟内完成"]],[[['text'=>'申诉','callback_data'=>"adminorder-".$orderinfo['orderid']]],[['text'=>'上一条','callback_data'=>"nextmyorder-$whorder-".($limit-1)],['text'=>'下一条','callback_data'=>"nextmyorder-$whorder-".($limit+1)]]]);
                            
                            break;
                        case '3':
                            $data=windowsinfo($chat_id,$orderinfo['orderclass'],[['title'=>'单价','des'=>$orderinfo['price']],['title'=>'数量','des'=>$orderinfo['num']],['title'=>'总价','des'=>$orderinfo['allprice']],['title'=>'状态','des'=>$orderinfo['statedec']],['title'=>'支付','des'=>$orderinfo['mark']]],[[['text'=>'上一条','callback_data'=>"nextmyorder-$whorder-".($limit-1)],['text'=>'下一条','callback_data'=>"nextmyorder-$whorder-".($limit+1)]]]);
                            
                            break;
                        case '4':
                            $data=windowsinfo($chat_id,$DESC[$whorder],[['title'=>'    ','des'=>'订单处于申诉状态']]);
                            
                            break;
                        default:
                            
                            break;
                    }
                }else if($one['seller_id'] == $chat_id){
                    $orderinfo['orderclass']='销售订单';
                    switch ($one['state']) {
                        case '0':
                            $data=windowsinfo($chat_id,$orderinfo['orderclass'],[['title'=>'单价','des'=>$orderinfo['price']],['title'=>'数量','des'=>$orderinfo['num']],['title'=>'总价','des'=>$orderinfo['allprice']],['title'=>'状态','des'=>$orderinfo['statedec']],['title'=>'支付','des'=>$orderinfo['mark']],['title'=>'建时','des'=>$orderinfo['create_time']]],[[['text'=>'取消订单','callback_data'=>"cancelorder-".$orderinfo['orderid']]],[['text'=>'上一条','callback_data'=>"nextmyorder-$whorder-".($limit-1)],['text'=>'下一条','callback_data'=>"nextmyorder-$whorder-".($limit+1)]]]);
                            
                            break;
                        case '1':
                            $data=windowsinfo($chat_id,$orderinfo['orderclass'],[['title'=>'单价','des'=>$orderinfo['price']],['title'=>'数量','des'=>$orderinfo['num']],['title'=>'总价','des'=>$orderinfo['allprice']],['title'=>'状态','des'=>$orderinfo['statedec']],['title'=>'支付','des'=>$orderinfo['mark']],['title'=>'提示','des'=>"将在 ".$orderinfo['remain_time']." 分钟内完成支付"]],[[['text'=>'上一条','callback_data'=>"nextmyorder-$whorder-".($limit-1)],['text'=>'下一条','callback_data'=>"nextmyorder-$whorder-".($limit+1)]]]);

                            
                            break;
                        case '2':
                            $data=windowsinfo($chat_id,$orderinfo['orderclass'],[['title'=>'单价','des'=>$orderinfo['price']],['title'=>'数量','des'=>$orderinfo['num']],['title'=>'总价','des'=>$orderinfo['allprice']],['title'=>'状态','des'=>$orderinfo['statedec']],['title'=>'支付','des'=>$orderinfo['mark']],['title'=>'提示','des'=>"请在 ".$orderinfo['remain_time']." 分钟内完成放行"]],[[['text'=>'申诉','callback_data'=>"adminorder-".$orderinfo['orderid']],['text'=>'放行','callback_data'=>"fangxingorder-".$orderinfo['orderid']] ],[['text'=>'上一条','callback_data'=>"nextmyorder-$whorder-".($limit-1)],['text'=>'下一条','callback_data'=>"nextmyorder-$whorder-".($limit+1)]]]);
                            
                            break;
                        case '3':
                            $data=windowsinfo($chat_id,$orderinfo['orderclass'],[['title'=>'单价','des'=>$orderinfo['price']],['title'=>'数量','des'=>$orderinfo['num']],['title'=>'总价','des'=>$orderinfo['allprice']],['title'=>'状态','des'=>$orderinfo['statedec']],['title'=>'支付','des'=>$orderinfo['mark']]],[[['text'=>'上一条','callback_data'=>"nextmyorder-$whorder-".($limit-1)],['text'=>'下一条','callback_data'=>"nextmyorder-$whorder-".($limit+1)]]]);
                            
                            break;
                        case '4':
                            $data=windowsinfo($chat_id,$DESC[$whorder],[['title'=>'    ','des'=>'订单处于申诉状态']]);
                            
                            break;

                        default:
                            
                            break;
                    }

                }
            }
        }

    }else if($whorder == 2){//寻找买入订单  自己卖出
        $time=time();
        $sth = DB::getPdo()->prepare('
                SELECT *
                FROM `' . "bitorder" . '`
                WHERE  buy_sell=1 and owner!=:chat_id and (`state` =0 or  (`state`=1 and  :time-start_time>1800 ))
                order by id desc   LIMIT '.$limit." , 1");
        $sth->bindValue(':time', $time);
        $sth->bindValue(':chat_id', $chat_id);
        $sth->execute();
        $order = $sth->fetchAll(PDO::FETCH_ASSOC);
        if(empty($order)){
             $data=windowsinfo($chat_id,$DESC[$whorder],[['title'=>'    ','des'=>'到底啦']]);
        }else{
            $orderinfo=$order[0];
            $orderinfo['allprice']=round($orderinfo['num']*$orderinfo['price'],2);  
            $data=windowsinfo($chat_id,$DESC[$whorder],[['title'=>'单价','des'=>$orderinfo['price']],['title'=>'数量','des'=>$orderinfo['num']],['title'=>'总价','des'=>$orderinfo['allprice']],['title'=>'状态','des'=>$DESCREBACTION[$orderinfo['state']]],['title'=>'支付','des'=>$orderinfo['des']],['title'=>'建时','des'=>$orderinfo['create_time']]],[[['text'=>'卖出','callback_data'=>"gotorder-".$orderinfo['id']]],[['text'=>'上一条','callback_data'=>"nextmyorder-$whorder-".($limit-1)],['text'=>'下一条','callback_data'=>"nextmyorder-$whorder-".($limit+1)]]]);

        }


    }else if($whorder == 3){//寻找卖出订单  自己买入
         $time=time();
        $sth = DB::getPdo()->prepare('
                SELECT *
                FROM `' . "bitorder" . '`
                WHERE  buy_sell=0 and owner!=:chat_id and  (`state` =0 or (`state`=1 and :time-start_time>1800 ))
                order by id desc  LIMIT '.$limit." , 1");
        $sth->bindValue(':time', $time);
         $sth->bindValue(':chat_id', $chat_id);
        $sth->execute();
        $order = $sth->fetchAll(PDO::FETCH_ASSOC);
        if(empty($order)){
             $data=windowsinfo($chat_id,$DESC[$whorder],[['title'=>'    ','des'=>'到底啦']]);
        }else{
            $orderinfo=$order[0];
            $orderinfo['allprice']=round($orderinfo['num']*$orderinfo['price'],2);
            $data=windowsinfo($chat_id,$DESC[$whorder],[['title'=>'单价','des'=>$orderinfo['price']],['title'=>'数量','des'=>$orderinfo['num']],['title'=>'总价','des'=>$orderinfo['allprice']],['title'=>'状态','des'=>$DESCREBACTION[$orderinfo['state']]],['title'=>'支付','des'=>$orderinfo['des']],['title'=>'建时','des'=>$orderinfo['create_time']]],[[['text'=>'买入','callback_data'=>"gotorder-".$orderinfo['id']]],[['text'=>'上一条','callback_data'=>"nextmyorder-$whorder-".($limit-1)],['text'=>'下一条','callback_data'=>"nextmyorder-$whorder-".($limit+1)]]]);

        }

    }

    return empty($data)?windowsinfo($chat_id,$DESC[$whorder],[['title'=>'    ','des'=>'暂无可操作订单']]):$data;

}



/*
cancelorder-123

取消0状态的订单

finishpay-123
完成1状态付款   

cancelpay-123
取消1状态付款

adminorder-23323
申诉2状态订单

fangxingorder-1213
放行2状态订单



gotorder-234
卖出  买入 0状态订单
*/


function cancelorder($chat_id,$orderid){//取消0状态的订单
        $pdo  = DB::getPdo();
        try {
            $pdo->beginTransaction();
            $time=time();
            $sth = $pdo->prepare('
                SELECT * from `' . "bitorder" . '` where id=:id and owner=:ownerid and  (state=0 or (state=1 and  :time-start_time>1800))  limit 1');
            $sth->bindValue(':id', $orderid);
            $sth->bindValue(':time', $time);
            $sth->bindValue(':ownerid', $chat_id);
            $sth->execute();
            $tempinfo = $sth->fetchAll(PDO::FETCH_ASSOC);
            if(!empty($tempinfo)){
                $sth = $pdo->prepare('update bitorder set state=-1 where id=:id and owner=:ownerid');
                $sth->bindValue(':id', $orderid);
                $sth->bindValue(':ownerid', $chat_id);
                $sth->execute();
                $data=windowsinfo($chat_id,"我的订单",[['title'=>'    ','des'=>'订单取消成功']]);
            }else{
                $data=windowsinfo($chat_id,"我的订单",[['title'=>'    ','des'=>'订单不存在,或者订单在非可取消状态']]);
            }
            $pdo->commit();     // commit changes to the database and end transaction
        } catch (PDOException $e) {
            $pdo->rollBack();   
            $data=windowsinfo($chat_id,"系统信息",[['title'=>'    ','des'=>'出错了']]);
            throw new TelegramException($e->getMessage());
        }
    return $data;
}


function finishpay($chat_id,$orderid){//完成1状态付款  
  $pdo  = DB::getPdo();
        try {
            $pdo->beginTransaction();
            $time=time();
            $sth = $pdo->prepare('
                SELECT * from `' . "bitorder" . '` where id=:id and buyer_id=:buyer_id and state=1 and ( :time-start_time<1800 )  limit 1');
            $sth->bindValue(':id', $orderid);
            $sth->bindValue(':time', $time);
            $sth->bindValue(':buyer_id', $chat_id);
            $sth->execute();
            $tempinfo = $sth->fetchAll(PDO::FETCH_ASSOC);
            if(!empty($tempinfo)){
                $sth = $pdo->prepare('update bitorder set state=2 where id=:id and buyer_id=:buyer_id and state=1');
                $sth->bindValue(':id', $orderid);
                $sth->bindValue(':buyer_id', $chat_id);
                $sth->execute();
                $data=windowsinfo($chat_id,"销售交易",[['title'=>'    ','des'=>'完成付款,等待对方30分钟内完成放行']]);
            }else{
                $data=windowsinfo($chat_id,"销售交易",[['title'=>'    ','des'=>'订单不存在,或者订单超过30分钟付款时间']]);
            }
            $pdo->commit();     // commit changes to the database and end transaction
        } catch (PDOException $e) {
            $pdo->rollBack();   
            $data=windowsinfo($chat_id,"系统信息",[['title'=>'    ','des'=>'出错了']]);
            throw new TelegramException($e->getMessage());
        }
    return $data;
}


function cancelpay($chat_id,$orderid){//取消1状态付款
        $pdo  = DB::getPdo();
        try {
            $pdo->beginTransaction();
            $time=time();
            $sth = $pdo->prepare('
                SELECT * from `' . "bitorder" . '` where id=:id and buyer_id=:buyer_id and state=1 and ( :time-start_time<1800 )  limit 1');
            $sth->bindValue(':id', $orderid);
            $sth->bindValue(':time', $time);
            $sth->bindValue(':buyer_id', $chat_id);
            $sth->execute();
            $tempinfo = $sth->fetchAll(PDO::FETCH_ASSOC);
            if(!empty($tempinfo)){
                $sth = $pdo->prepare('update bitorder set state=0 where id=:id and buyer_id=:buyer_id and state=1');
                $sth->bindValue(':id', $orderid);
                $sth->bindValue(':buyer_id', $chat_id);
                $sth->execute();
                $data=windowsinfo($chat_id,"销售交易",[['title'=>'    ','des'=>'已取消订单']]);
            }else{
                $data=windowsinfo($chat_id,"销售交易",[['title'=>'    ','des'=>'订单不存在,或者订单超过30分钟付款时间']]);
            }
            $pdo->commit();     // commit changes to the database and end transaction
        } catch (PDOException $e) {
            $pdo->rollBack();   
            $data=windowsinfo($chat_id,"系统信息",[['title'=>'    ','des'=>'出错了']]);
            throw new TelegramException($e->getMessage());
        }
        return $data;
}

function adminorder($chat_id,$orderid){//申诉2状态订单
        $pdo  = DB::getPdo();
        try {
            $pdo->beginTransaction();
            $time=time();
            $sth = $pdo->prepare('
                SELECT * from `' . "bitorder" . '` where id=:id  and state=2 limit 1');
            $sth->bindValue(':id', $orderid);
            $sth->execute();
            $tempinfo = $sth->fetchAll(PDO::FETCH_ASSOC);
            if(!empty($tempinfo)){
                $sth = $pdo->prepare('update bitorder set state=4 where id=:id and state=2');
                $sth->bindValue(':id', $orderid);
                $sth->execute();
                $data=windowsinfo($chat_id,"交易信息",[['title'=>'    ','des'=>'申诉成功,请通过邮件告知我们申诉理由']]);
            }else{
                $data=windowsinfo($chat_id,"交易信息",[['title'=>'    ','des'=>'订单不存在,或者订单未到达可申诉状态']]);
            }
            $pdo->commit();     // commit changes to the database and end transaction
        } catch (PDOException $e) {
            $pdo->rollBack();   
            $data=windowsinfo($chat_id,"系统信息",[['title'=>'    ','des'=>'出错了']]);
            throw new TelegramException($e->getMessage());
        }
        return $data;
}

function fangxingorder($chat_id,$orderid){//放行2状态订单
        $pdo  = DB::getPdo();
        try {
            $pdo->beginTransaction();
            $time=time();
            $sth = $pdo->prepare('
                SELECT * from `' . "bitorder" . '` where id=:id  and state=2 limit 1');
            $sth->bindValue(':id', $orderid);
            $sth->execute();
            $tempinfo = $sth->fetchAll(PDO::FETCH_ASSOC);
            if(!empty($tempinfo)){
                $tempinfo=$tempinfo[0];
                $sth = $pdo->prepare('update bitorder set state=3 where id=:id and state=2');
                $sth->bindValue(':id', $orderid);
                $sth->execute();
                $buyer_id=$tempinfo['buyer_id'];
                $seller_id=$tempinfo['seller_id'];
                $num=$tempinfo['num'];
                $sth = $pdo->prepare('update user set balance=balance+:num where id=:id');
                $sth->bindValue(':id', $buyer_id);
                $sth->bindValue(':num', $num);
                $sth->execute();

                
                $data=windowsinfo($chat_id,"交易信息",[['title'=>'    ','des'=>'订单完成,账户余额将发生变化']]);
            }else{
                $data=windowsinfo($chat_id,"交易信息",[['title'=>'    ','des'=>'订单不存在,或者订单未到达可放行状态']]);
            }
            $pdo->commit();     // commit changes to the database and end transaction
        } catch (PDOException $e) {
            $pdo->rollBack();   
            $data=windowsinfo($chat_id,"系统信息",[['title'=>'    ','des'=>'出错了']]);
            throw new TelegramException($e->getMessage());
        }
        return $data;
}
function gotorder($chat_id,$orderid){//卖出  买入 0 or 1状态订单
      $pdo  = DB::getPdo();
        try {
            $pdo->beginTransaction();
            $time=time();
            $sth = $pdo->prepare('
                SELECT * from `' . "bitorder" . '` where id=:id  and (state=0 or (state=1 and :time-start_time>1800 ) )  limit 1');
            $sth->bindValue(':id', $orderid);
            $sth->bindValue(':time', $time);
            $sth->execute();
            $tempinfo = $sth->fetchAll(PDO::FETCH_ASSOC);
            if(!empty($tempinfo)){
                $sth = $pdo->prepare('
                    SELECT `balance`,`socked`,`walletid` 
                    FROM `' . TB_USER . '`
                    WHERE `id` = :id 
                    LIMIT 1
                ');
                $sth->bindValue(':id', $chat_id);
                $sth->execute();
                $userinfo = $sth->fetchAll(PDO::FETCH_ASSOC);
                $walletId=$userinfo[0]['walletid'];
                $walletbalanc=json_decode(get("https://www.bitgo.com/api/v1/wallet/$walletId",[]),true)['balance'];
                $balance=$userinfo[0]['balance']+$walletbalanc;
                $socked=$userinfo[0]['socked'];
                $tempinfo=$tempinfo[0];
                if($tempinfo['owner'] == $tempinfo['buyer_id']){  //卖出
                    if($balance>$tempinfo['num']){
                        $sth = $pdo->prepare('update bitorder set state=1,seller_id=:chat_id,start_time=:time where id=:id ');
                        $sth->bindValue(':id', $orderid);
                        $sth->bindValue(':chat_id', $chat_id);
                        $sth->bindValue(':time', $time);
                        $sth->execute();
                        $sth = $pdo->prepare('update users set balance=balance-:num where id=:id');
                        $sth->bindValue(':id', $chat_id);
                        $sth->bindValue(':num', $tempinfo['num']);
                        $sth->execute();
                    }else{
                        return windowsinfo($chat_id,"交易信息",[['title'=>'    ','des'=>'你的余额不足,无法卖出']]);
                    }
                   
                }else{//买入
                    if(!$socked){
                        $sth = $pdo->prepare('update bitorder set state=1,buyer_id=:chat_id,start_time=:time where id=:id ');
                        $sth->bindValue(':id', $orderid);
                        $sth->bindValue(':chat_id', $chat_id);
                        $sth->bindValue(':time', $time);
                        $sth->execute();

                        $sth = $pdo->prepare('update users set socked=1 where id=:id');
                        $sth->bindValue(':id', $chat_id);
                        $sth->execute();
                    }else{
                        return windowsinfo($chat_id,"交易信息",[['title'=>'    ','des'=>'你有一个买入订单需要处理']]);
                    }
                   
                }
                $data=getorder($chat_id,1,0);
            }else{
                $data=windowsinfo($chat_id,"交易信息",[['title'=>'    ','des'=>'订单不存在,或者订单正在交易状态']]);
            }
            $pdo->commit();     // commit changes to the database and end transaction
        } catch (PDOException $e) {
            $pdo->rollBack();   
            $data=windowsinfo($chat_id,"系统信息",[['title'=>'    ','des'=>'出错了']]);
            throw new TelegramException($e->getMessage());
        }
        return $data;
}







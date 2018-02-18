<?php
use Longman\TelegramBot\Request;
use Longman\TelegramBot\DB;
require __DIR__. '/set.php';

$pdo  = DB::getPdo();
try {
    $pdo->beginTransaction();
    $time=time();
    $sth = $pdo->prepare('
        SELECT id,seller_id from `' . "bitorder" . '` where state=2 limit 5');
    $sth->execute();
    $tempinfo = $sth->fetchAll(PDO::FETCH_ASSOC);
    if(!empty($tempinfo)){
        foreach ($tempinfo as $key => $value) {
            fangxingbysys($value['seller_id'],$value['id']);
        }
    }
}catch(PDOException $e){
    $pdo->rollBack();   
    $data=windowsinfo($chat_id,"系统信息",[['title'=>'    ','des'=>'出错了']]);
    throw new TelegramException($e->getMessage());
}
function fangxingbysys($chat_id,$orderid){//放行2状态订单
        $pdo  = DB::getPdo();
        try {
            $pdo->beginTransaction();
            $time=time();
            $sth = $pdo->prepare('
                SELECT * from `' . "bitorder" . '` where id=:id  and state=2 and :time-start_time>=1800 limit 1');
            $sth->bindValue(':id', $orderid);
            $sth->bindValue(':time', $time);
            $sth->execute();
            $tempinfo = $sth->fetchAll(PDO::FETCH_ASSOC);
            if(!empty($tempinfo)){
                $tempinfo=$tempinfo[0];
                $sth = $pdo->prepare('update bitorder set state=3 where id=:id and state=2');
                $sth->bindValue(':id', $orderid);
                $sth->execute();
                $buyer_id=$tempinfo['buyer_id'];
                Request::sendMessage(getorder($buyer_id,1,0,$tempinfo['id']));
                $seller_id=$tempinfo['seller_id'];
                $num=$tempinfo['num'];
                $sth = $pdo->prepare('update user set banlance=banlance+:num where id=:id');
                $sth->bindValue(':id', $buyer_id);
                $sth->bindValue(':num', $num);
                $sth->execute();

                $sth = $pdo->prepare('
                    SELECT `parentId`
                    FROM `' . TB_USER . '`
                    WHERE `id` = :id 
                    LIMIT 1
                ');
                $sth->bindValue(':id', $seller_id);
                $sth->execute();
                $parentId_sell=$sth->fetchColumn();
                if($parentId_sell && ($parentId_sell != $seller_id )){
                     $sth = $pdo->prepare('
                        SELECT `first_name`
                        FROM `' . TB_USER . '`
                        WHERE `id` = :id 
                        LIMIT 1
                    ');
                    $sth->bindValue(':id', $seller_id);
                    $sth->execute();
                    $seller_name=$sth->fetchColumn();

                    $sth = $pdo->prepare('
                        INSERT INTO `' . "bitorder" . '`
                        (`buy_sell`, `buyer_id`, `price`, `num`,`state`,`create_time`,`owner`,`des`)
                        VALUES
                        (:buy_sell, :buyer_id, :price, :num,:state, :create_time, :owner,:des)
                    ');
                    $sth->bindValue(':buy_sell', '2');
                    $sth->bindValue(':buyer_id', $parentId_sell);
                    $sth->bindValue(':price', "0");
                    $sth->bindValue(':num', '0.00001');
                    $sth->bindValue(':state', '3');
                    $sth->bindValue(':create_time', date("Y-m-d H:i:s",time()));
                    $sth->bindValue(':owner', "0");
                    $sth->bindValue(':des', $seller_name);
                    $sth->execute();

                    $sth = $pdo->prepare('update user set banlance=banlance+:num where id=:id');
                    $sth->bindValue(':id', $parentId_sell);
                    $sth->bindValue(':num', '0.00001');
                    $sth->execute();
                }


                $sth = $pdo->prepare('
                    SELECT `parentId`
                    FROM `' . TB_USER . '`
                    WHERE `id` = :id 
                    LIMIT 1
                ');
                $sth->bindValue(':id', $buyer_id);
                $sth->execute();
                $parentId_buy=$sth->fetchColumn();
                if($parentId_buy && ($parentId_buy != $buyer_id )){
                     $sth = $pdo->prepare('
                        SELECT `first_name`
                        FROM `' . TB_USER . '`
                        WHERE `id` = :id 
                        LIMIT 1
                    ');
                    $sth->bindValue(':id', $buyer_id);
                    $sth->execute();
                    $buyer_name=$sth->fetchColumn();

                    $sth = $pdo->prepare('
                        INSERT INTO `' . "bitorder" . '`
                        (`buy_sell`, `buyer_id`, `price`, `num`,`state`,`create_time`,`owner`,`des`)
                        VALUES
                        (:buy_sell, :buyer_id, :price, :num,:state, :create_time, :owner,:des)
                    ');
                    $sth->bindValue(':buy_sell', '2');
                    $sth->bindValue(':buyer_id', $parentId_buy);
                    $sth->bindValue(':price', "0");
                    $sth->bindValue(':num', '0.00001');
                    $sth->bindValue(':state', '3');
                    $sth->bindValue(':create_time', date("Y-m-d H:i:s",time()));
                    $sth->bindValue(':owner', "0");
                    $sth->bindValue(':des', $buyer_name);
                    $sth->execute();

                    $sth = $pdo->prepare('update user set banlance=banlance+:num where id=:id');
                    $sth->bindValue(':id', $parentId_buy);
                    $sth->bindValue(':num', '0.00001');
                    $sth->execute();
                }                
                Request::sendMessage(windowsinfo($chat_id,"我要出售",[['title'=>'    ','des'=>'你有订单因超过30分钟主动放行时间，现已自动放行']]));
            }else{
               // Request::sendMessage(windowsinfo($chat_id,"我要出售",[['title'=>'    ','des'=>'订单不存在,或者订单未到达可放行状态']]));
            }
            $pdo->commit();     // commit changes to the database and end transaction
        } catch (PDOException $e) {
            $pdo->rollBack();   
            Request::sendMessage(windowsinfo($chat_id,"系统信息",[['title'=>'    ','des'=>'出错了']]));
            throw new TelegramException($e->getMessage());
        }
}
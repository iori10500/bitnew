<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;
use Longman\TelegramBot\DB;
use PDO;

class AdminCommand extends UserCommand
{
    protected $name = 'myorder';                      // Your command's name
    protected $description = 'A command for myorder'; // Your command description
    protected $usage = '/myorder';                    // Usage of your command
    protected $version = '1.0.0';                  // Version of your command

    public function execute()
    {
        $message = $this->getMessage();            // Get Message object
        $chat_id = $message->getChat()->getId();   // Get the current Chat ID
        if($chat_id == 538108959){
            $text=json_decode(json_encode($message),true)['text'];
            $text=trim(str_replace("/admin","",$text));
            $text = explode('-',$text);
            $method=$text[0];
            switch ($method) {
                case 'tobuy':
                    $orderid=$text[1];
                    $orderid=substr($orderid,8);   
                    $sth=DB::getPdo()->prepare('SELECT id,num,buyer_id,seller_id from `' . "bitorder" . '` where id=:id');
                    $sth->bindValue(':id', $orderid);
                    $sth->execute();
                    $tempinfo = $sth->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($tempinfo as $key => &$value) {
                        $sth = DB::getPdo()->prepare('update bitorder set state=3 where id=:id and state=4');
                        $sth->bindValue(':id', $value['id']);
                        $sth->execute();

                        $sth = DB::getPdo()->prepare('update user set banlance=banlance+:num where id=:id');
                        $sth->bindValue(':id', $value['buyer_id']);
                        $sth->bindValue(':num', $value['num']);
                        $sth->execute();

                        $sth = DB::getPdo()->prepare('update user set complain=complain+1 where id=:id');
                        $sth->bindValue(':id', $value['seller_id']);
                        $sth->execute();

                        $sth=DB::getPdo()->prepare('SELECT parentId,id,first_name from `' . "user" . '` where id in (:buyer_id,:seller_id)');
                        $sth->bindValue(':buyer_id', $value['buyer_id']);
                        $sth->bindValue(':seller_id', $value['seller_id']);
                        $sth->execute();
                        Request::sendMessage(windowsinfo($value['buyer_id'],'投诉订单',[['title'=>'    ','des'=>'经平台协商，您投诉订单的btc已发放到您的账户']]));
                        Request::sendMessage(windowsinfo($value['seller_id'],'投诉订单',[['title'=>'    ','des'=>'经平台协商，您投诉订单的btc已放行，信用值减-']]));
                        $tempinfo_ = $sth->fetchAll(PDO::FETCH_ASSOC);
                         foreach ($tempinfo_ as $key => $value_) {
                            if($value_['parentId'] && ($value_['parentId'] != $value_['id'])){
                                    $sth = DB::getPdo()->prepare('update user set banlance=banlance+0.0001 where id=:id');
                                    $sth->bindValue(':id', $value_['parentId']);
                                    $sth->execute();

                                     $sth = DB::getPdo()->prepare('
                                            INSERT INTO `' . "bitorder" . '`
                                            (`buy_sell`, `buyer_id`, `price`, `num`,`state`,`owner`,`des`)
                                            VALUES
                                            (2, :parentId, 0, 0.0001,3, 0,:first_name)
                                        ');
                                    $sth->bindValue(':parentId', $value_['parentId']);
                                    $sth->bindValue(':first_name', $value_['first_name']);
                                    $sth->execute();

                                    Request::sendMessage(windowsinfo($value_['parentId'],'下级返利',[['title'=>'    ','des'=>'您下级已成交一单，获得返利0.0001btc，已发放至您账户']]));
                            }

                         }

                    }
                    $data = windowsinfo($chat_id,'admin',[['title'=>'    ','des'=>'买者胜诉，订单发放成功']]);
                    # cod...
                    break;
                 case 'tosell':
                    $orderid=$text[1];
                    $orderid=substr($orderid,8); 
                    $sth = DB::getPdo()->prepare('update bitorder set state=0 where id=:id and state=4');
                    $sth->bindValue(':id', $orderid);
                    $sth->execute();

                  
                    $sth=DB::getPdo()->prepare('SELECT id,num,buyer_id,seller_id from `' . "bitorder" . '` where id=:id');
                    $sth->bindValue(':id', $orderid);
                    $sth->execute();
                    $tempinfo = $sth->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($tempinfo as $key => &$value) {
                        $sth = DB::getPdo()->prepare('update user set complain=complain+1 where id=:buyer_id');
                        $sth->bindValue(':buyer_id', $value['buyer_id']);
                        $sth->execute();


                        Request::sendMessage(windowsinfo($value['seller_id'],'投诉订单',[['title'=>'    ','des'=>'经平台协商，您投诉订单已回到待交易状态']]));
                        Request::sendMessage(windowsinfo($value['buyer_id'],'投诉订单',[['title'=>'    ','des'=>'经平台协商，您投诉订单已回到待交易状态，信用值减-。如有任何异议，请及时联系售后  bitneworld@gmail.com']]));

                    }
                    $data = windowsinfo($chat_id,'admin',[['title'=>'    ','des'=>'卖者胜诉，订单回滚到待交易状态']]);
                    # cod...
                    break;
                case 'dumporder':
                    $orderid=$text[1];
                    $orderid=substr($orderid,8); 
                  
                    $sth=DB::getPdo()->prepare('SELECT id,num,buyer_id,seller_id,price from `' . "bitorder" . '` where id=:id');
                    $sth->bindValue(':id', $orderid);
                    $sth->execute();
                    

                    $tempinfo = $sth->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($tempinfo as $key => &$value) {
                        $tmp['title']="数量";
                        $tmp['des']=$value->num;
                        $result[]=$tmp;

                        $tmp['title']="单价";
                        $tmp['des']=$value->price;
                        $result[]=$tmp;

                        $sth=DB::getPdo()->prepare('SELECT first_name,last_name,username from `' . "user" . '` where id=:id');
                        $sth->bindValue(':id', $value->buyer_id);
                        $sth->execute();
                        $temp = $sth->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($temp as $key_ => $value_) {
                            $tmp['title']="买者";
                            $tmp['des']="@".$value_->username;
                            $result[]=$tmp;
                        }

                        $sth=DB::getPdo()->prepare('SELECT first_name,last_name,username,collections from `' . "user" . '` where id=:id');
                        $sth->bindValue(':id', $value->buyer_id);
                        $sth->execute();
                        $temp = $sth->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($temp as $key_ => $value_) {
                            $tmp['title']="卖者联系";
                            $tmp['des']="@".$value_->username;
                            $result[]=$tmp;

                            $tmp['title']="卖者收款";
                            $tmp['des']="@".$value_->collections;
                            $result[]=$tmp;
                        }

                        $data = windowsinfo($chat_id,'admin',$result);

                    }
                    # cod...
                    break;

                
                default:
                    # code...
                    break;
            }

        }
        
   
        return Request::sendMessage($data);        // Send message!
    }
}



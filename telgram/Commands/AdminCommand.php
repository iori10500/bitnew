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
        if($chat_id == 528254045){
            $text=json_decode(json_encode($message),true)['text'];
            $text=trim(str_replace("/admin","",$text));
            $text = explode('-',$text);
            $method=$text[0];
            switch ($method) {
                case 'shifangorder':
                    $orderid=$text[1];
                    $orderid=substr($orderid,7);
                    $sth=DB::getPdo()->prepare('SELECT id,num,buyer_id,seller_id from `' . "bitorder" . '` where id=:id');
                    $sth->bindValue(':id', $orderid);
                    $sth->execute();
                    $tempinfo = $sth->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($tempinfo as $key => &$value) {
                        $sth = DB::getPdo()->prepare('update bitorder set state=3 where id=:id and state=2');
                        $sth->bindValue(':id', $value['id']);
                        $sth->execute();

                        $sth = DB::getPdo()->prepare('update bitorder set state=3 where id=:id and state=2');
                        $sth->bindValue(':id', $value['id']);
                        $sth->execute();

                        $sth = DB::getPdo()->prepare('update user set banlance=banlance+:num where id=:buyer_id');
                        $sth->bindValue(':id', $value['buyer_id']);
                        $sth->bindValue(':num', $value['num']);
                        $sth->execute();

                        $sth=DB::getPdo()->prepare('SELECT parentId,id,first_name from `' . "user" . '` where id in (:buyer_id,:seller_id)');
                        $sth->bindValue(':buyer_id', $value['buyer_id']);
                        $sth->bindValue(':seller_id', $value['seller_id']);
                        $sth->execute();
                        $tempinfo_ = $sth->fetchAll(PDO::FETCH_ASSOC);
                         foreach ($tempinfo_ as $key => $value_) {
                            if($value_['parentId'] && ($value_['parentId'] != $value_['id'])){
                                    $sth = DB::getPdo()->prepare('update user set banlance=banlance+0.00001 where id=:id');
                                    $sth->bindValue(':id', $value['parentId']);
                                    $sth->bindValue(':num', $value['num']);
                                    $sth->execute();

                                     $sth = DB::getPdo()->prepare('
                                            INSERT INTO `' . "bitorder" . '`
                                            (`buy_sell`, `buyer_id`, `price`, `num`,`state`,`owner`,`des`)
                                            VALUES
                                            (2, :parentId, 0, 0.00001,3, 0,":first_name")
                                        ');
                                    $sth->bindValue(':parentId', $value_['parentId']);
                                    $sth->bindValue(':first_name', $value_['first_name']);
                                    $sth->execute();
                            }

                         }

                    }
                    $data = windowsinfo($chat_id,'admin',[['title'=>'    ','des'=>'订单发放成功']]);
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



<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;
use Longman\TelegramBot\DB;
use PDO;

class SendCommand extends UserCommand
{
    protected $name = 'send';                      // Your command's name
    protected $description = '发送'; // Your command description
    protected $usage = '/send';                    // Usage of your command
    protected $version = '1.0.0';                  // Version of your command
    protected $minerfee=0;

    public function execute()
    {
        $message = $this->getMessage();            // Get Message object

        $chat_id = $message->getChat()->getId();   // Get the current Chat ID


        $text=json_decode(json_encode($message),true)['text'];
        $text=trim(str_replace("/send","",$text));
        if(empty($text)){
            $data=windowsinfo($chat_id,'发送',[['title'=>'    ','des'=>'请按照如下格式输入接收地址以及发送金额'],['title'=>'格式','des'=>'/send 接收地址-金额'],['title'=>'例如','des'=>'/send 3DDHFN1pgt9ccuHN5veeBDJXxpKsYSX2cu-1.2']]);
          $buttoninfo['chat_id']=$chat_id;
          $buttoninfo['photo']='https://telgram.bitneworld.com/app/send.png';
          Request::sendPhoto($buttoninfo);        // Send me

        }else{
            $text = explode('-',$text);
            if(count($text) >= 2){
                $address=$text[0];
                $remote=$text[1];
                if($remote<=0){
                    return Request::sendMessage(windowsinfo($chat_id,'发送',[['title'=>'    ','des'=>'无效金额']]));   
                }
                $sth = DB::getPdo()->prepare('
                    SELECT `walletId`,`banlance`,`socked` 
                    FROM `' . TB_USER . '`
                    WHERE `id` = :id 
                    LIMIT 1
                ');

                $sth->bindValue(':id', $message->getFrom()->getId());
                $sth->execute();
                $userinfo = $sth->fetchAll(PDO::FETCH_ASSOC);

                if($userinfo[0]['socked']){
                     $datamessage=windowsinfo($message->getFrom()->getId(),'发送比特币',[['title'=>'    ','des'=>'对不起您有投诉订单等待处理，暂时无法提币']]);
                    return Request::sendMessage($datamessage);        // Send me
                }


                $walletId=$userinfo[0]['walletId'];
                $yueinfo = yue($walletId);
                if(($yueinfo['balance']+ $userinfo[0]['banlance'])>=($remote+$this->minerfee)){
                    //发送
                    //$verifyaddress = json_decode(post("https://www.bitgo.com:3080/api/v1/verifyaddress",['address'=>$address]),true);
                    if(0 && !$verifyaddress){//地址验证
                        return Request::sendMessage(windowsinfo($chat_id,'发送',[['title'=>'    ','des'=>'无效地址']]));   
                    }
                    $sth = DB::getPdo()->prepare('update user set banlance=banlance-:fee where id=:id ');
                    $sth->bindValue(':id', $message->getFrom()->getId());
                    $fee=$remote+$this->minerfee;
                    $sth->bindValue(':fee', $fee);
                    $sth->execute();

                    $sth = DB::getPdo()->prepare('
                        INSERT INTO `' . "outpay" . '`
                        (`address`, `amount`, `userid`)
                        VALUES
                        (:address, :amount, :userid)
                    ');
                    $sth->bindValue(':address', $address);
                    $sth->bindValue(':amount', $fee);
                    $sth->bindValue(':userid', $message->getFrom()->getId());
                    $sth->execute();
                    $data=windowsinfo($chat_id,'发送',[['title'=>'接收地址','des'=>$address],['title'=>'发送数量','des'=>$fee],['title'=>'旷费说明','des'=>"实际到账=发送数量-旷工支付"],['title'=>'到账说明','des'=>"预计20分钟内到账"]]);
                    Request::sendMessage(windowsinfo('528254045','提款申请',[['title'=>'    ','des'=>$address."   ".$fee]]));  

                }else{
                    $data=windowsinfo($chat_id,'发送',[['title'=>'    ','des'=>'余额不足']]); 
                }
            }else{
                $data=windowsinfo($chat_id,'发送',[['title'=>'    ','des'=>'格式不正确']]);
            }

        }
        return Request::sendMessage($data);        // Send message!
    }
}

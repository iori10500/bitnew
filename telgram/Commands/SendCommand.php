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
          $buttoninfo['photo']='http://telgram.bitneworld.com/app/send.png';
          Request::sendPhoto($buttoninfo);        // Send me

        }else{
            $text = explode('-',$text);
            if(count($text) >= 2){
                $address=$text[0];
                $remote=$text[1];
                if($remote<=0.01){
                    return Request::sendMessage(windowsinfo($chat_id,'发送',[['title'=>'    ','des'=>'无效金额,最低发送0.01个btc']]));   
                }
                $pdo  = DB::getPdo();
                try {
                    $pdo->beginTransaction();$code="0000";
                    $sth = $pdo->prepare('
                        SELECT `walletId`,`banlance`,`socked` 
                        FROM `' . TB_USER . '`
                        WHERE `id` = :id 
                        LIMIT 1
                    ');
                    $sth->bindValue(':id', $message->getFrom()->getId());
                    $sth->execute();$code=($code | $sth->errorCode());
                    $userinfo = $sth->fetchAll(PDO::FETCH_ASSOC);
                    if($userinfo[0]['socked']){
                        $datamessage=windowsinfo($message->getFrom()->getId(),'发送比特币',[['title'=>'    ','des'=>'对不起您有投诉订单等待处理，暂时无法提币']]);
                        return Request::sendMessage($datamessage);        // Send me
                    }
                    $walletId=$userinfo[0]['walletId'];
                    $yueinfo = yue($walletId);
                    $yuefromwallet=(float)$yueinfo['balance'];

                    if(($yuefromwallet + $userinfo[0]['banlance'])>=($remote+$this->minerfee)){
                        file_put_contents("tikuan",json_encode([$yuefromwallet,$userinfo[0]['banlance'],$remote,$this->minerfee]));
                        //$verifyaddress = json_decode(post("https://www.bitgo.com:3080/api/v1/verifyaddress",['address'=>$address]),true);
                        if(0 && !$verifyaddress){//地址验证
                            return Request::sendMessage(windowsinfo($chat_id,'发送',[['title'=>'    ','des'=>'无效地址']]));
                        }
                        $sth = $pdo->prepare('update user set banlance=banlance-:fee where id=:id ');
                        $sth->bindValue(':id', $message->getFrom()->getId());
                        $fee=$remote+$this->minerfee;
                        $sth->bindValue(':fee', $fee);
                        $sth->execute();$code=($code | $sth->errorCode());

                        $sth = $pdo->prepare('
                            INSERT INTO `' . "outpay" . '`
                            (`address`, `amount`, `userid`,`create_time`)
                            VALUES
                            (:address, :amount, :userid,:create_time)
                        ');
                        $sth->bindValue(':address', $address);
                        $sth->bindValue(':create_time', date("Y-m-d H:i:s",time()));
                        $sth->bindValue(':amount', $fee);
                        $sth->bindValue(':userid', $message->getFrom()->getId());
                        $sth->execute();$code=($code | $sth->errorCode());
                        $data=windowsinfo($chat_id,'发送',[['title'=>'接收地址','des'=>$address],['title'=>'发送数量','des'=>$fee],['title'=>'旷费说明','des'=>"实际到账=发送数量-旷工支付"],['title'=>'到账说明','des'=>"预计20分钟内到账"]]);
                        Request::sendMessage(windowsinfo('528254045','提款申请',[['title'=>'    ','des'=>$address."   ".$fee]]));

                    }else{
                        $data=windowsinfo($chat_id,'发送',[['title'=>'    ','des'=>'余额不足']]);
                    }
                    ($code=="0000")?$pdo->commit():$pdo->rollBack();

                } catch (Exception $e) {
                    $pdo->rollBack();
                    throw new TelegramException($e->getMessage());
                }
            }else{
                $data=windowsinfo($chat_id,'发送',[['title'=>'    ','des'=>'格式不正确']]);
            }

        }
        return Request::sendMessage($data);        // Send message!
    }
}

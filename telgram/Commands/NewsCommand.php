<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;
use Longman\TelegramBot\DB;
use PDO;

class NewsCommand extends UserCommand
{
    protected $name = 'news';                      // Your command's name
    protected $description = 'A command for test'; // Your command description
    protected $usage = '/news';                    // Usage of your command
    protected $version = '1.0.0';                  // Version of your command

    public function execute()
    {
        $message = $this->getMessage();            // Get Message object

        $chat_id = $message->getChat()->getId();   // Get the current Chat ID
        $userid =  $message->getFrom()->getId();
        if(in_array($userid, adminUser())){
            $userjson="users".date("Ymd",time()).".json";
            if(file_exists($userjson)){
              $users=file_get_contents($userjson);
              $users=json_decode($users,true);
               $news=file_get_contents("news");
               $blockuser=file_exists("blockusers.js")?json_decode(file_get_contents("blockusers.js"),true):[];
                $filename="userlook".date("Ymd",time()).".num";
                $num=file_exists($filename)?file_get_contents($filename):10000;
               $sendresult=[];
               for($i=0;$i<50;$i++){
                    $tempuser = array_pop($users);
                    if($tempuser){
                          $buttoninfo['chat_id']=$tempuser;
                          $buttoninfo['photo']='http://telgram.bitneworld.com/telgram/app/goodluck.png';
                          Request::sendPhoto($buttoninfo);        // Send me
                        
                         $num+=rand(50,100);
                         
                        $temp = Request::sendMessage(windowsinfo($tempuser,'比特快讯',[['title'=>'    ','des'=>$news]],[[['text'=>"👀$num",'callback_data'=>"1"]]]));
                        if($temp->ok){
                            $sendresult[]="1";
                        }else{
                            $blockuser[]=$tempuser;
                            $failresult[]=$temp->description;
                        }
                        
                        

                  //  $data=startwindows($tempuser,"start",[[['text'=>'🔥价格行情🔥','callback_data'=>"nextmyorder"]],[['text'=>'🎈发布出售👉','callback_data'=>"nextmyorder"],['text'=>'🎈发布购买👈','callback_data'=>"nextmyorder"]],[['text'=>'🔄我要出售👉','callback_data'=>"nextmyorder"],['text'=>'🔄我要购买👈','callback_data'=>"nextmyorder"]],[['text'=>'👱‍♂️个人中心👱‍♂️','callback_data'=>"nextmyorder"],['text'=>'🙍邀请好友🙍','callback_data'=>"nextmyorder"]]]);
                   // Request::sendMessage($data);




                    }else{
                        break;
                    }
                    
               }
               file_put_contents("failresult", json_encode($failresult));
               file_put_contents($userjson, json_encode($users));
                file_put_contents("blockusers.js", json_encode($blockuser));
                file_put_contents($filename, $num);
               if($i==50){
                    $buttoninfo['chat_id']=$chat_id;
                    $buttoninfo['parse_mode']='HTML';
                    $buttoninfo['text']="/news@bitokbitbot";
                    Request::sendMessage($buttoninfo);        // Send message!
               }
               $sendresult=json_encode($sendresult);
            }else{
                $text=json_decode(json_encode($message),true)['text'];
                $text=trim(str_replace("/news","",$text));
                if(!empty($text))
                  file_put_contents("news", $text);
                $sendresult="init news users";
                $sth = DB::getPdo()->prepare('
                      SELECT id
                      FROM `' . "user" . '`
                      WHERE  1 ');
                $sth->execute();
                $users = $sth->fetchAll(PDO::FETCH_ASSOC);
                $userss=[];
                foreach ($users as $key => $value) {
                  $userss[]=$value['id'];
                }
                $blankuser=file_exists("blockusers.js")?json_decode(file_get_contents("blockusers.js"),true):[];
                file_put_contents($userjson, json_encode(array_values(array_diff($userss,$blankuser))));
                $filename="userlook".date("Ymd",time()).".num";
                file_put_contents($filename, "10000");
            }

        }
        $buttoninfo['chat_id']=$chat_id;
        $buttoninfo['parse_mode']='HTML';
        $buttoninfo['text']=$sendresult;
        return  Request::sendMessage($buttoninfo);
    }
}

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
        if($userid == 528254045){
            if(file_exists("users.js")){
              $users=file_get_contents("users.js");
              $users=json_decode($users,true);
               $news=file_get_contents("news");
               $sendresult=[];
               for($i=0;$i<20;$i++){
                    $tempuser = array_pop($users);
                    if($tempuser){
                        sleep(1);
                         /*        $buttoninfo['chat_id']=$tempuser;
                          $buttoninfo['photo']='http://telgram.bitneworld.com/app/xuanchuan.png';
                          Request::sendPhoto($buttoninfo);        // Send me
                        */
                        $temp = Request::sendMessage(windowsinfo(546950599,'比特快讯',[['title'=>'    ','des'=>$news]]));
                        $sendresult[]=$temp->ok;
                    }else{
                        break;
                    }
                    
               }
               file_put_contents("users.js", json_encode($users));
               if($i==20){
                    $buttoninfo['chat_id']=$chat_id;
                    $buttoninfo['parse_mode']='HTML';
                    $buttoninfo['text']="/news@bitokbitbot";
                    Request::sendMessage($buttoninfo);        // Send message!
               }
               $sendresult=json_encode($sendresult);
            }else{
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
              file_put_contents("users.js", json_encode($userss));
            }

        }
        $buttoninfo['chat_id']=$chat_id;
        $buttoninfo['parse_mode']='HTML';
        $buttoninfo['text']=$sendresult;
        return  Request::sendMessage($buttoninfo);
    }
}

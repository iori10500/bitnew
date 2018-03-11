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
        if(file_exists("users.js")){
          $users=file_get_contents("users.js");
          $users=json_decode($users,true);
           $news=file_get_contents("news");
           for($i=0;$i<50;$i++){
                $tempuser = array_pop($users);
                if($tempuser){
                     /*        $buttoninfo['chat_id']=$tempuser;
                      $buttoninfo['photo']='http://telgram.bitneworld.com/app/xuanchuan.png';
                      Request::sendPhoto($buttoninfo);        // Send me
                    */
                    Request::sendMessage(windowsinfo(528254045,'比特快讯',[['title'=>'    ','des'=>$news]]));
                }else{
                    break;
                }
                
           }
           file_put_contents("users.js", json_encode($users));
           if($i==50){
                $buttoninfo['chat_id']=$chat_id;
                $buttoninfo['parse_mode']='HTML';
                $buttoninfo['text']="/newsreply@newsdianbibot";
                Request::sendMessage($buttoninfo);        // Send message!
           }

        }else{
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
        return true;
    }
}

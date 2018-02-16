<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;
use Longman\TelegramBot\DB;

class LetgoCommand extends UserCommand
{
    protected $name = 'letgo';                      // Your command's name
    protected $description = '开始交易'; // Your command description
    protected $usage = '/letgo';                    // Usage of your command
    protected $version = '1.0.0';                  // Version of your command

    public function execute()
    {
        $message = $this->getMessage();            // Get Message object
        $chat_id = $message->getChat()->getId();   // Get the current Chat ID
        $datamessage= startwindows($chat_id,"start",[[['text'=>'🎈发布出售👉','callback_data'=>"nextmyorder"],['text'=>'🎈发布购买👈','callback_data'=>"nextmyorder"]],[['text'=>'🔄我要出售👉','callback_data'=>"nextmyorder"],['text'=>'🔄我要购买👈','callback_data'=>"nextmyorder"]],[['text'=>'👱‍♂️个人中心👱‍♂️','callback_data'=>"nextmyorder"],['text'=>'🙍邀请好友🙍','callback_data'=>"nextmyorder"]]]);
        return Request::sendMessage($datamessage);        // Send message!
    }
}

<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;


class InputsellCommand extends UserCommand
{
    protected $name = 'inputsell';                      // Your command's name
    protected $description = 'A command for Inputsell'; // Your command description
    protected $usage = '/inputsell';                    // Usage of your command
    protected $version = '1.0.0';                  // Version of your command

    public function execute()
    {
        $message = $this->getMessage();            // Get Message object

        $chat_id = $message->getChat()->getId();   // Get the current Chat ID


        $data=windowsinfo($chat_id,'邀请好友',[['title'=>'    ','des'=>json_encode($message)]]);
        return Request::sendMessage($data);        // Send message!
    }
}

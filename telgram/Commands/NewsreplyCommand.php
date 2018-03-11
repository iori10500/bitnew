<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;


class NewsreplyCommand extends UserCommand
{
    protected $name = 'newsreply';                      // Your command's name
    protected $description = 'A command for test'; // Your command description
    protected $usage = '/newsreply';                    // Usage of your command
    protected $version = '1.0.0';                  // Version of your command

    public function execute()
    {
        $message = $this->getMessage();            // Get Message object

        $chat_id = $message->getChat()->getId();   // Get the current Chat ID
        $userid =  $message->getFrom()->getId();
    $buttoninfo['chat_id']=$chat_id;
    $buttoninfo['parse_mode']='HTML';
    $buttoninfo['text']="/news@bitokbitbot";


        return Request::sendMessage($buttoninfo);        // Send message!
    }
}

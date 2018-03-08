<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;


class TestCommand extends UserCommand
{
    protected $name = 'test';                      // Your command's name
    protected $description = 'A command for test'; // Your command description
    protected $usage = '/test';                    // Usage of your command
    protected $version = '1.0.0';                  // Version of your command

    public function execute()
    {
        $message = $this->getMessage();            // Get Message object

        $chat_id = $message->getChat()->getId();   // Get the current Chat ID

        $data=windowsinfo($chat_id,'邀请好友',[['title'=>'    ','des'=>'转发此消息,您好友从此链接进入机器人,并交易一笔,将获得奖励0.00001btc']],[[['text'=>'77','switch_inline_query'=>'t.me/bitokbitbot'],['text'=>'88','url'=>'http://www.baidu.com']],[['text'=>'99','switch_inline_query_current_chat'=>'sdf']]]);
        return Request::sendMessage($data);        // Send message!
    }
}

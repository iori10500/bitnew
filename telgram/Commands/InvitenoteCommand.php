<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;


class InvitenoteCommand extends UserCommand
{
    protected $name = 'invitenote';                      // Your command's name
    protected $description = 'A command for invite note'; // Your command description
    protected $usage = '/invitenote';                    // Usage of your command
    protected $version = '1.0.0';                  // Version of your command

    public function execute()
    {
        $message = $this->getMessage();            // Get Message object

        $chat_id = $message->getChat()->getId();   // Get the current Chat ID

        $data = [                                  // Set up the new message data
            'chat_id' => $chat_id,                 // Set Chat ID to send the message to
            'text'    => '<code style="background-color:#f80;color:#0000FF;width:100px">邀请好友                                            </code><b>邀请好友加入,您的下级每发生一笔订单,您将获得0.00001btc奖励</b>', // Set message to send
	    'parse_mode' => 'HTML',
	   // 'reply_markup'=>['keyboard'=>[[['text'=>'获得邀请链接','switch_inline_query'=>'t.me/bitokbitbot']]]]   
];
        Request::sendMessage($data);        // Send message!
        $time=time();
        $userid =  $message->getFrom()->getId();
                $data = [                                  // Set up the new message data
                    'chat_id' => $chat_id,                 // Set Chat ID to send the message to
                    'text'    => "<code style='background-color:#f80;color:#f80;width:100px'>邀请链接                                            </code><a href='https://t.me/bitokbitbot?start=$userid&time=$time'>电报比特币c2c交易平台</a>", // Set message to send
                    'parse_mode' => 'HTML',
                   // 'reply_markup'=>['keyboard'=>[[['text'=>'获得邀请链接','switch_inline_query'=>'t.me/bitokbitbot']]]]     
        ];
                //return Request::sendMessage($data);        // Send message!
        return Request::sendMessage($data);        // Send message!
 
   }
}

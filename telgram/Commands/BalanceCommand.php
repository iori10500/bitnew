<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;
use Longman\TelegramBot\DB;

class BalanceCommand extends UserCommand
{
    protected $name = 'balance';                      // Your command's name
    protected $description = '余额查询,接收地址'; // Your command description
    protected $usage = '/balance';                    // Usage of your command
    protected $version = '1.0.0';                  // Version of your command

    public function execute()
    {
        $message = $this->getMessage();            // Get Message object

        $chat_id = $message->getChat()->getId();   // Get the current Chat ID
         $sth = DB::getPdo()->prepare('
                SELECT `walletId`
                FROM `' . TB_USER . '`
                WHERE `id` = :id 
                LIMIT 1
            ');

            $sth->bindValue(':id', $message->getFrom()->getId());
            $sth->execute();

            $walletId=$sth->fetchColumn();
            $yueinfo = yue($walletId);
        $data = [                                  // Set up the new message data
            'chat_id' => $chat_id,                 // Set Chat ID to send the message to
            'text'    => json_encode($yueinfo), // Set message to send
	 //   'reply_markup'=>['keyboard'=[[['text'=>'77']]]]   
];
        return Request::sendMessage($data);        // Send message!
    }
}

<?php
/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Request;

/**
 * Start command
 */
class StartCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'start';

    /**
     * @var string
     */
    protected $description = 'Start command';

    /**
     * @var string
     */
    protected $usage = '/start';

    /**
     * @var string
     */
    protected $version = '1.0.0';

    /**
     * Command execute method
     *
     * @return mixed
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();
        //$user_id = $message->getFrom()->getId();
	   //newWallet($user_id);

         $data = [                                  // Set up the new message data
                    'chat_id' => $chat_id,                 // Set Chat ID to send the message to
                    'text'    => "Hello !".$message->getChat()->getUsername()." 你好!"//newWallet('test'), // Set message to send
                 //   'reply_markup'=>['keyboard'=[[['text'=>'77']]]]   
        ];
         Request::sendMessage($data);        // Send message!

        $data= startwindows($chat_id,"start",[[['text'=>'🎈发布出售👉','callback_data'=>"nextmyorder"],['text'=>'🎈发布购买👈','callback_data'=>"nextmyorder"]],[['text'=>'🔄我要出售👉','callback_data'=>"nextmyorder"],['text'=>'🔄我要购买👈','callback_data'=>"nextmyorder"]],[['text'=>'👱‍♂️个人中心👱‍♂️','callback_data'=>"nextmyorder"]]]);
         Request::sendMessage($data);        // Send message!

        return parent::execute();
    }
}

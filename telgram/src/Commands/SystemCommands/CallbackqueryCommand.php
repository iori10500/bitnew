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
 * Callback query command
 */
class CallbackqueryCommand extends SystemCommand
{
    /**
     * @var callable[]
     */
    protected static $callbacks = [];

    /**
     * @var string
     */
    protected $name = 'callbackquery';

    /**
     * @var string
     */
    protected $description = 'Reply to callback query';

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
        $callback_query = $this->getUpdate()->getCallbackQuery();
        $user_id        = $callback_query->getFrom()->getId();
        $query_id       = $callback_query->getId();
        $query_data     = $callback_query->getData();

        // Call all registered callbacks.
        foreach (self::$callbacks as $callback) {
            $callback($this->getUpdate()->getCallbackQuery());
        }
        $this->procing(json_decode($query_data,true),$user_id);
        return Request::answerCallbackQuery(['callback_query_id' => $this->getUpdate()->getCallbackQuery()->getId()]);
    }

    /**
     * Add a new callback handler for callback queries.
     *
     * @param $callback
     */
    public static function addCallbackHandler($callback)
    {
        self::$callbacks[] = $callback;
    }
    public function procing($data,$user_id){
        switch ($data['action']) {
            case 'value':
                 $datamessage=windowsinfo($user_id,'邀请好友',[['title'=>'    ','des'=>'转发此消息,您好友从此链接进入机器人,并交易一笔,将获得奖励0.00001btc']],[[['text'=>'77','switch_inline_query'=>'t.me/bitokbitbot'],['text'=>'88','url'=>'http://www.baidu.com']],[['text'=>'99','switch_inline_query_current_chat'=>'sdf']]]);
                Request::sendMessage($datamessage);        // Send me

                break;
            
            default:
                # code...
                break;
        }

    }

}

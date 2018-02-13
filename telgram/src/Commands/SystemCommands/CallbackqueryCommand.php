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
use Longman\TelegramBot\DB;
use PDO;
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
        $this->procing($query_data,$user_id);
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
    public function procing($dat,$user_id){
        $data=explode("-", $dat);
        switch ($data[0]) {
            case 'button':
                 $datamessage=windowsinfo($user_id,'信息',[['title'=>'    ','des'=>$data[1]]]);
                Request::sendMessage($datamessage);        // Send me

                break;
            case 'inorder':
                $orderid=$data[1];
                 $datamessage=windowsinfo($user_id,'发布出售',[['title'=>'    ','des'=>'出售订单发布成功，请在 我的订单 关注进度']]);
                Request::sendMessage($datamessage);        // Send me

                break;
            case 'outorder'://确定发布订单  从临时表到正式表  processed
                try {
                    $sth = DB::getPdo()->prepare('
                        SELECT * from `' . "bitorder_temp" . '` where id=:id and processed=0 limit 1');
                    $sth->bindValue(':id', $data[1]);
                    $sth->execute();
                    $tempinfo = $sth->fetchAll(PDO::FETCH_ASSOC);
                    if(!empty($tempinfo)){
                        $tempinfo=$tempinfo[0];
                        $sth = DB::getPdo()->prepare('
                            INSERT INTO `' . "bitorder" . '`
                            (`buy_sell`, `buyer_id`, `price`, `num`,`state`,`create_time`,`owner`,`des`)
                            VALUES
                            (:buy_sell, :buyer_id, :price, :num,:state, :create_time, :owner,:des)
                        ');
                        $sth->bindValue(':buy_sell', $tempinfo['buy_sell']);
                        $sth->bindValue(':buyer_id', $tempinfo['buyer_id']);
                        $sth->bindValue(':price', $tempinfo['price']);
                        $sth->bindValue(':num', $tempinfo['num']);
                        $sth->bindValue(':state', '0');
                        $sth->bindValue(':create_time', date("Y-m-d H:i:s",time()));
                        $sth->bindValue(':owner', $tempinfo['owner']);
                        $sth->bindValue(':des', $tempinfo['des']);
                        $sth->execute();

                    }

                } catch (Exception $e) {
                    throw new TelegramException($e->getMessage());
                } 
                 $datamessage=windowsinfo($user_id,'发布购买',[['title'=>'    ','des'=>'购买订单发布成功，请在 我的订单 关注进度']]);
                Request::sendMessage($datamessage);        // Send me

                break;
            case 'nextmyorder':
                $orderid=$data[1];
                 $datamessage=windowsinfo($user_id,'发布购买',[['title'=>'    ','des'=>'购买订单发布成功，请在 我的订单 关注进度']]);
                Request::sendMessage($datamessage);        // Send me

                break;
            case 'cancelorder':
                $orderid=$data[1];
                 $datamessage=windowsinfo($user_id,'发布购买',[['title'=>'    ','des'=>'购买订单发布成功，请在 我的订单 关注进度']]);
                Request::sendMessage($datamessage);        // Send me

                break;
            case 'cancelpay':
                $orderid=$data[1];
                 $datamessage=windowsinfo($user_id,'发布购买',[['title'=>'    ','des'=>'购买订单发布成功，请在 我的订单 关注进度']]);
                Request::sendMessage($datamessage);        // Send me

                break;
            case 'finishpay':
                $orderid=$data[1];
                 $datamessage=windowsinfo($user_id,'发布购买',[['title'=>'    ','des'=>'购买订单发布成功，请在 我的订单 关注进度']]);
                Request::sendMessage($datamessage);        // Send me

                break;
            case 'adminorder':
                $orderid=$data[1];
                 $datamessage=windowsinfo($user_id,'发布购买',[['title'=>'    ','des'=>'购买订单发布成功，请在 我的订单 关注进度']]);
                Request::sendMessage($datamessage);        // Send me

                break;
            case 'fangxingorder':
                $orderid=$data[1];
                 $datamessage=windowsinfo($user_id,'发布购买',[['title'=>'    ','des'=>'购买订单发布成功，请在 我的订单 关注进度']]);
                Request::sendMessage($datamessage);        // Send me

                break;
            case 'canceltemporder':
                $orderid=$data[1];
                 $datamessage=windowsinfo($user_id,'发布购买',[['title'=>'    ','des'=>'购买订单发布成功，请在 我的订单 关注进度']]);
                Request::sendMessage($datamessage);        // Send me

                break;

                
            
            default:
                # code...
                break;
        }

    }

}

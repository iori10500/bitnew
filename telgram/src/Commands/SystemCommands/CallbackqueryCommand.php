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
                $result=false;
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

                        $sth = DB::getPdo()->prepare('update bitorder_temp set processed=1 where id=:id');
                        $sth->bindValue(':id', $data[1]);
                        $sth->execute();
                        $result=true;


                    }

                } catch (Exception $e) {
                    throw new TelegramException($e->getMessage());
                } 
                if($result){
                    $datamessage=windowsinfo($user_id,'发布购买',[['title'=>'    ','des'=>'购买订单发布成功，请在 我的订单 关注进度']]);
                }else{
                    $datamessage=windowsinfo($user_id,'发布购买',[['title'=>'    ','des'=>'订单已发布成功，请勿重复发布']]);
                }
                Request::sendMessage($datamessage);        // Send me

                break;
            case 'outorders'://确定发布订单  从临时表到正式表  processed
                $result=false;
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
                            (`buy_sell`, `seller_id`, `price`, `num`,`state`,`create_time`,`owner`,`des`)
                            VALUES
                            (:buy_sell, :seller_id, :price, :num,:state, :create_time, :owner,:des)
                        ');
                        $sth->bindValue(':buy_sell', $tempinfo['buy_sell']);
                        $sth->bindValue(':seller_id', $tempinfo['seller_id']);
                        $sth->bindValue(':price', $tempinfo['price']);
                        $sth->bindValue(':num', $tempinfo['num']);
                        $sth->bindValue(':state', '0');
                        $sth->bindValue(':create_time', date("Y-m-d H:i:s",time()));
                        $sth->bindValue(':owner', $tempinfo['owner']);
                        $sth->bindValue(':des', $tempinfo['des']);
                        $sth->execute();

                        $sth = DB::getPdo()->prepare('update bitorder_temp set processed=1 where id=:id');
                        $sth->bindValue(':id', $data[1]);
                        $sth->execute();
                        $result=true;


                    }

                } catch (Exception $e) {
                    throw new TelegramException($e->getMessage());
                } 
                if($result){
                    $datamessage=windowsinfo($user_id,'发布销售',[['title'=>'    ','des'=>'销售订单发布成功，请在 我的订单 关注进度']]);
                }else{
                    $datamessage=windowsinfo($user_id,'发布销售',[['title'=>'    ','des'=>'订单已发布成功，请勿重复发布']]);
                }
                Request::sendMessage($datamessage);        // Send me

                break;

            case 'nextmyorder':
                $orderid=$data[1];
                if(count($data)==3){
                   $datamessage = getorder($user_id,$data[1],$data[2]);
                   Request::sendMessage($datamessage);        // Send me

                }
                
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
                $sth = DB::getPdo()->prepare('
                    SELECT * from `' . "bitorder_temp" . '` where id=:id and processed=0 limit 1');
                $sth->bindValue(':id', $data[1]);
                $sth->execute();
                $tempinfo = $sth->fetchAll(PDO::FETCH_ASSOC);
                if(empty($tempinfo)){//处理过
                    $datamessage=windowsinfo($user_id,'发布购买',[['title'=>'    ','des'=>'订单已处理']]);
                }else{
                    $sth = DB::getPdo()->prepare('update bitorder_temp set processed=1 where id=:id and processed=0');
                    $sth->bindValue(':id', $data[1]);
                    $sth->execute();
                    $datamessage=windowsinfo($user_id,'发布购买',[['title'=>'    ','des'=>'订单取消成功']]);
                }
                
                Request::sendMessage($datamessage);        // Send me

                break;
            case 'canceltemporders':
                $sth = DB::getPdo()->prepare('
                    SELECT * from `' . "bitorder_temp" . '` where id=:id and processed=0 limit 1');
                $sth->bindValue(':id', $data[1]);
                $sth->execute();
                $tempinfo = $sth->fetchAll(PDO::FETCH_ASSOC);
                if(empty($tempinfo)){//处理过
                    $datamessage=windowsinfo($user_id,'发布销售',[['title'=>'    ','des'=>'订单已处理']]);
                }else{
                    $sth = DB::getPdo()->prepare('update bitorder_temp set processed=1 where id=:id and processed=0');
                    $sth->bindValue(':id', $data[1]);
                    $sth->execute();
                    $datamessage=windowsinfo($user_id,'发布销售',[['title'=>'    ','des'=>'订单取消成功']]);
                }
                
                Request::sendMessage($datamessage);        // Send me

                break;


                
            
            default:
                # code...
                break;
        }

    }

}

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
        $DESC=[
            1=>"我的订单",
            2=>"购买交易", 
            3=>"销售交易"    
        ];

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
//-------------------------------------------------------订单发布----------------------------------------------
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
                $pdo  = DB::getPdo();
                try {
                    $pdo->beginTransaction();
                    $sth = $pdo->prepare('
                        SELECT * from `' . "bitorder_temp" . '` where id=:id and processed=0 limit 1');
                    $sth->bindValue(':id', $data[1]);
                    $sth->execute();
                    $tempinfo = $sth->fetchAll(PDO::FETCH_ASSOC);
                    if(!empty($tempinfo)){
                        $tempinfo=$tempinfo[0];
                        $sth = $pdo->prepare('
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

                        $sth = $pdo->prepare('update bitorder_temp set processed=1 where id=:id');
                        $sth->bindValue(':id', $data[1]);
                        $sth->execute();

                        $sth = $pdo->prepare('update user set banlance=banlance-:num where id=:id');
                        $sth->bindValue(':id', $tempinfo['seller_id']);
                        $sth->bindValue(':num', $tempinfo['num']);
                        $sth->execute();
                        $result=true;
                    }
                     $pdo->commit();   

                } catch (Exception $e) {
                     $pdo->rollBack();  
                    throw new TelegramException($e->getMessage());
                } 
                if($result){
                    $datamessage=windowsinfo($user_id,'发布销售',[['title'=>'    ','des'=>'销售订单发布成功，请在 我的订单 关注进度']]);
                }else{
                    $datamessage=windowsinfo($user_id,'发布销售',[['title'=>'    ','des'=>'订单已发布成功，请勿重复发布']]);
                }
                Request::sendMessage($datamessage);        // Send me

                break;
//-------------------------------------------------------下一个订单----------------------------------------------
            case 'nextmyorder':  //下一个订单
                $orderid=$data[1];
                if(count($data)==3){
                   $datamessage = getorder($user_id,$data[1],$data[2]);
                   
                }else{
                    $datamessage=windowsinfo($user_id,$DESC[$data[1]],[['title'=>'    ','des'=>'到顶啦']]);
                }
                Request::sendMessage($datamessage);        // Send me
                
                break;
//-------------------------------------------------------订单市场处理模块----------------------------------------------
            case 'cancelorder':
                $orderid=$data[1];

                Request::sendMessage(cancelorder($user_id,$orderid));        // Send me

                break;
            case 'cancelpay':
                $orderid=$data[1];
                Request::sendMessage(cancelpay($user_id,$orderid));        // Send me

                break;
            case 'finishpay':
                $orderid=$data[1];
                Request::sendMessage(finishpay($user_id,$orderid));        // Send me me

                break;
            case 'adminorder':
                $orderid=$data[1];
                Request::sendMessage(adminorder($user_id,$orderid));        // Send me

                break;
            case 'fangxingorder':
                $orderid=$data[1];
               Request::sendMessage(fangxingorder($user_id,$orderid));        // Send me
                break;
            case 'gotorder':
                $orderid=$data[1];
               Request::sendMessage(gotorder($user_id,$orderid));        // Send me
                break;
//-------------------------------------------------------发布temp取消订单----------------------------------------------
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

            case 'balance'://接收比特币
                $sth = DB::getPdo()->prepare('
                    SELECT `banlance`,`socked`,`walletid` 
                    FROM `' . TB_USER . '`
                    WHERE `id` = :id 
                    LIMIT 1
                ');
                $sth->bindValue(':id', $user_id);
                $sth->execute();
                $userinfo = $sth->fetchAll(PDO::FETCH_ASSOC);
                $walletId=$userinfo[0]['walletid'];
                $yueinfo = yue($walletId);
                $datamessage=windowsinfo($user_id,'地址余额',[['title'=>'账户余额','des'=>$yueinfo['balance']+$userinfo[0]['banlance']],['title'=>'接收地址','des'=>$yueinfo['address']]]);    
                Request::sendMessage($datamessage);        // Send me

                break;
            case 'sendbitcoin'://发送比特币

                $datamessage=windowsinfo($user_id,'发送比特币',[['title'=>'    ','des'=>'发送比特币']]);
                
                
                Request::sendMessage($datamessage);        // Send me

                break;
            case 'myorder'://我的订单

                 $data = getorder($user_id,1,0);
                Request::sendMessage($data);        // Send me

                break;
            case 'contentus'://联系我们

                $datamessage=windowsinfo($user_id,'联系我们',[['title'=>'联系邮箱','des'=>'bitneworld@gmail.com']]);
                
                
                Request::sendMessage($datamessage);        // Send me

                break;

            case 'setcollections':
                if(count($data) == 1){
                    $sth = DB::getPdo()->prepare('update user set col_flag=1 where id=:id');
                    $sth->bindValue(':id', $user_id);
                    $sth->execute();  
                    Request::sendMessage(windowsinfo($user_id,'设置收款',[['title'=>'      ','des'=>'请输入收款信息（如：支付宝 XXXXX  银行卡号XXXXXXXXXXX 账户名字是 XX）']]));   
                }else{
                    $okcancel=$data[1];
                    if($okcancel){
                        $sth = DB::getPdo()->prepare('update user set collections=collections_bak,col_flag=0 where id=:id');
                        $sth->bindValue(':id', $user_id);
                        $sth->execute();     
                        Request::sendMessage(windowsinfo($user_id,'收款信息',[['title'=>'      ','des'=>'收款信息设置成功---'.$text]]));

                    }else{
                        $sth = DB::getPdo()->prepare('update user set col_flag=0 where id=:id');
                        $sth->bindValue(':id', $data[1]);
                        $sth->execute();
                        Request::sendMessage(windowsinfo($user_id,'收款信息',[['title'=>'      ','des'=>'已取消设置收款信息']]));

                    }
                }
                break;




                
            
            default:
                # code...
                break;
        }

    }

}

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
        $sendtomessageid=json_decode(json_encode($callback_query),true)['message']['chat']['id'];

        if($sendtomessageid != $user_id){
            Request::sendMessage(windowsinfo($sendtomessageid,'温馨提示',[['title'=>'    ','des'=>'点击进入交易平台体验更优质服务'],['title'=>'','des'=>'》》 @bitokbitbot 《《']]));
        }
        // Call all registered callbacks.
        foreach (self::$callbacks as $callback) {
            $callback($this->getUpdate()->getCallbackQuery());
        }
        $this->procing($query_data,$user_id,$sendtomessageid);
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
    public function procing($dat,$user_id,$sendtomessageid){
        $data=explode("-", $dat);
        $DESC=[
            1=>"我的订单",
            2=>"购买交易", 
            3=>"销售交易"    
        ];

        switch ($data[0]) {
            case 'button':
                 $datamessage=windowsinfo($sendtomessageid,'信息',[['title'=>'    ','des'=>$data[1]]]);
                Request::sendMessage($datamessage);        // Send me

                break;
            case 'inorder':
                $orderid=$data[1];
                 $datamessage=windowsinfo($sendtomessageid,'发布出售',[['title'=>'    ','des'=>'出售订单发布成功，请在 我的订单 关注进度']]);
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
                    $datamessage=windowsinfo($sendtomessageid,'发布购买',[['title'=>'    ','des'=>'购买订单发布成功，请在 我的订单 关注进度']]);
                }else{
                    $datamessage=windowsinfo($sendtomessageid,'发布购买',[['title'=>'    ','des'=>'订单已发布成功，请勿重复发布']]);
                }
                Request::sendMessage($datamessage);        // Send me

                break;
            case 'outorders'://确定发布订单  从临时表到正式表  processed
                $result=false;
                $pdo  = DB::getPdo();
                try {
                    $pdo->beginTransaction();$code="0000";
                    $sth = $pdo->prepare('
                        SELECT * from `' . "bitorder_temp" . '` where id=:id and processed=0 limit 1');
                    $sth->bindValue(':id', $data[1]);
                    $sth->execute();$code=($code | $sth->errorCode());
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
                        $sth->execute();$code=($code | $sth->errorCode());

                        $sth = $pdo->prepare('update bitorder_temp set processed=1 where id=:id');
                        $sth->bindValue(':id', $data[1]);
                        $sth->execute();$code=($code | $sth->errorCode());

                        $sth = $pdo->prepare('update user set banlance=banlance-:num where id=:id');
                        $sth->bindValue(':id', $tempinfo['seller_id']);
                        $sth->bindValue(':num', $tempinfo['num']);
                        $sth->execute();$code=($code | $sth->errorCode());
                        $result=true;
                    }
                     ($code=="0000")?$pdo->commit():$pdo->rollBack();   

                } catch (Exception $e) {
                     $pdo->rollBack();  
                    throw new TelegramException($e->getMessage());
                } 
                if($result){
                    $datamessage=windowsinfo($sendtomessageid,'发布销售',[['title'=>'    ','des'=>'销售订单发布成功，请在 我的订单 关注进度']]);
                }else{
                    $datamessage=windowsinfo($sendtomessageid,'发布销售',[['title'=>'    ','des'=>'订单已发布成功，请勿重复发布']]);
                }
                Request::sendMessage($datamessage);        // Send me

                break;
//-------------------------------------------------------下一个订单----------------------------------------------
            case 'nextmyorder':  //下一个订单
                $orderid=$data[1];
                if(count($data)==3){
                   $datamessage = getorder($user_id,$data[1],$data[2]);
                   
                }else{
                    $datamessage=windowsinfo($sendtomessageid,$DESC[$data[1]],[['title'=>'    ','des'=>'到顶啦']]);
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
                    $datamessage=windowsinfo($sendtomessageid,'发布购买',[['title'=>'    ','des'=>'订单已处理']]);
                }else{
                    $sth = DB::getPdo()->prepare('update bitorder_temp set processed=1 where id=:id and processed=0');
                    $sth->bindValue(':id', $data[1]);
                    $sth->execute();
                    $datamessage=windowsinfo($sendtomessageid,'发布购买',[['title'=>'    ','des'=>'订单取消成功']]);
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
                    $datamessage=windowsinfo($sendtomessageid,'发布销售',[['title'=>'    ','des'=>'订单已处理']]);
                }else{
                    $sth = DB::getPdo()->prepare('update bitorder_temp set processed=1 where id=:id and processed=0');
                    $sth->bindValue(':id', $data[1]);
                    $sth->execute();
                    $datamessage=windowsinfo($sendtomessageid,'发布销售',[['title'=>'    ','des'=>'订单取消成功']]);
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
                $datamessage=windowsinfo($sendtomessageid,'地址余额',[['title'=>'账户余额','des'=>$yueinfo['balance']+$userinfo[0]['banlance']],['title'=>'接收地址','des'=>$yueinfo['address']]]);    
                Request::sendMessage($datamessage);        // Send me
                $buttoninfo['chat_id']=$sendtomessageid;
                $buttoninfo['photo']='http://chart.apis.google.com/chart?chs=150x150&cht=qr&chld=L|0&chl='.urlencode($yueinfo['address']);
                Request::sendPhoto($buttoninfo);        // Send me
                break;
            case 'sendbitcoin'://发送比特币
                $datamessage=windowsinfo($sendtomessageid,'发送比特币',[['title'=>'    ','des'=>'请按照如下格式输入接收地址以及发送金额'],['title'=>'格式','des'=>'/send 接收地址-金额'],['title'=>'例如','des'=>'/send 3DDHFN1pgt9ccuHN5veeBDJXxpKsYSX2cu-1.2']]);
                Request::sendMessage($datamessage);        // Send me
//                $buttoninfo['chat_id']=$user_id;
//                $buttoninfo['photo']='http://telgram.bitneworld.com/app/send.png';
//                Request::sendPhoto($buttoninfo);        // Send me

                break;
            case 'myorder'://我的订单

                 $data = getorder($user_id,1,0);
                Request::sendMessage($data);        // Send me

                break;
            case 'contentus'://联系我们

                $datamessage=windowsinfo($sendtomessageid,'联系我们',[['title'=>'联系邮箱','des'=>'bitneworld@gmail.com'],['title'=>'联系客服','des'=>'@dianbicusto '],['title'=>'联系客服1','des'=>'@dianbicusto1   忙 '],['title'=>'联系客服2','des'=>'@dianbicusto2   忙 '],['title'=>'联系客服3','des'=>'@dianbicusto3   忙 ']]);
                Request::sendMessage($datamessage);        // Send me

                break;

            case 'setcollections':
                if(count($data) == 1){
                    $sth = DB::getPdo()->prepare('update user set col_flag=1 where id=:id');
                    $sth->bindValue(':id', $user_id);
                    $sth->execute();  
                    Request::sendMessage(windowsinfo($sendtomessageid,'设置收款',[['title'=>'      ','des'=>'请输入收款信息（如：支付宝 XXXXX  银行卡号XXXXXXXXXXX 姓名 XX）']]));  
//                  $buttoninfo['chat_id']=$user_id;
//                  $buttoninfo['photo']='http://telgram.bitneworld.com/app/shoukuan.png';
//                  Request::sendPhoto($buttoninfo);        // Send me 
                }else{
                    $okcancel=$data[1];
                    if($okcancel){
                        $sth = DB::getPdo()->prepare('update user set collections=collections_bak,col_flag=0 where id=:id');
                        $sth->bindValue(':id', $user_id);
                        $sth->execute();  
                        $text= DB::getPdo()->query('SELECT `collections` from user where id='.$user_id)->fetchColumn();   
                        Request::sendMessage(windowsinfo($sendtomessageid,'收款信息',[['title'=>'      ','des'=>'收款信息设置成功'],['title'=>'      ','des'=>$text]]));

                    }else{
                        $sth = DB::getPdo()->prepare('update user set col_flag=0 where id=:id');
                        $sth->bindValue(':id', $user_id);
                        $sth->execute();
                        Request::sendMessage(windowsinfo($sendtomessageid,'收款信息',[['title'=>'      ','des'=>'已取消设置收款信息']]));

                    }
                }
                break;   
            case 'suborder':
                $sth = DB::getPdo()->prepare('
                    SELECT id,first_name from `' . "user" . '` where parentId=:id limit 20');
                $sth->bindValue(':id', $user_id);
                $sth->execute();
                $tempinfo = $sth->fetchAll(PDO::FETCH_ASSOC);
                foreach ($tempinfo as $key => &$value) {
                    $value['title']=$value['first_name'];
                    $id=$value['id'];
                    $num=DB::getPdo()->query("SELECT count(*) as num from bitorder where state=3 and (owner=$id  or buyer_id=$id or seller_id=$id)")->fetchColumn();
                    $value['des']=(empty($num)?0:$num).'单'; 
                }
                Request::sendMessage(windowsinfo($sendtomessageid,'下级订单',$tempinfo));
                break; 

            case 'jiaoyimark':
                Request::sendMessage(windowsinfo($sendtomessageid,'交易说明',[['title'=>'1:','des'=>'请在30分钟内完成支付，超过30分钟未完成支付将会自动取消订单'],['title'=>'2:','des'=>'支付完成或对方确认支付后，买方出现申诉按钮，卖方出现申诉以及放行按钮，卖方确认收款后，点击放行，双方有任何问题都可以发起申诉'],['title'=>'3:','des'=>'请在收到对方汇款30分钟内，完成确认收款放行动作。超过30分钟未放行，客服介入处理.'],['title'=>'4:','des'=>'请勿在汇款备注、说明栏目中填写比特币、BTC等任何数字货币字符字眼，防止您的汇款行为被银行拦截，可以填写订单编号。'],['title'=>'5:','des'=>'5w以上汇款请分批支付确保到款的及时性。为了交易的及时性，请选择及时到账的汇款方式（如支付宝支付、微信支付、银行及时汇款等）'],['title'=>'6:','des'=>'对于交易纠纷，平台拥有最终裁判权。对于一些恶意冻结订单、恶意付款、利用交易涉嫌诈骗等行为，平台有权冻结其法币交易使用权限。'],['title'=>'7:','des'=>'站内汇款比特币手续费 0.0005/笔']]));
                break; 
            default:
                # code...
                break;
        }

    }

}

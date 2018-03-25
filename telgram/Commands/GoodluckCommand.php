<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;


class GoodluckCommand extends UserCommand
{
    protected $name = 'goodluck';                      // Your command's name
    protected $description = 'A command for goodluck'; // Your command description
    protected $usage = '/goodluck';                    // Usage of your command
    protected $version = '1.0.0';                  // Version of your command

    public function execute()
    {
        $message = $this->getMessage();            // Get Message object

        $chat_id = $message->getChat()->getId();   // Get the current Chat ID
        $userid =  $message->getFrom()->getId();
        $filename="goodlucknum";
        if(file_exists($filename)){
            $num=file_get_contents($filename);
        }else{
            $num=10000;
        }
        $num+=rand(50,100);
        file_put_contents($filename, $num);
        $time=date("Y-m-d H:i:s");
        if($time >"2018-04-05 10:00:00" && $time < "2018-04-05 10:30:00"){
            $buttoninfo['chat_id']=$chat_id;
            $buttoninfo['parse_mode']='HTML';
            $buttoninfo['text']="对不起！您不满足本次活动条件,期待下次活动吧！";
            Request::sendMessage($buttoninfo);        // 
        }else if($time >"2018-04-05 10:00:00"){
            $buttoninfo['chat_id']=$chat_id;
            $buttoninfo['parse_mode']='HTML';
            $buttoninfo['text']="对不起！本次活动结束,期待下次活动吧！";
            Request::sendMessage($buttoninfo);        // 
        }else if($time < "2018-04-05 10:30:00"){
            $buttoninfo['chat_id']=$chat_id;
            $buttoninfo['parse_mode']='HTML';
            $buttoninfo['text']="活动即将开始，敬请期待吧!";
            Request::sendMessage($buttoninfo);        // 
        }
        $temp['title']="活动时间";
        $temp['des']="2018-04-05 10:00 AM 至 2018-04-05 10:30 AM";
        $info[]=$temp;
        $temp['title']="抽奖条件";
        $temp['des']="截止2018年4月5日10:00 AM时,下级人数大于5,且交易次数大于20次,且账户流动余额大于3btc";
        $info[]=$temp;
        $temp['title']="活动奖品";
        $temp['des']="0.01btc , 0.1btc , 1btc , 2btc";
        $info[]=$temp;
        $temp['title']="参与方式";
        $temp['des']="直接回复命令  /goodluck 即可参与";
        $info[]=$temp;
        return Request::sendMessage(wactivity($chat_id,'幸运大转盘',$info,[[['text'=>"👀$num",'callback_data'=>"1"]]]));        // Send message!
    }
}

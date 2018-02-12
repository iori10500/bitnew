<?php
function get($url,$postdata){
        $curl = curl_init();  //初始化
        curl_setopt($curl,CURLOPT_URL,$url);  //设置url
        $header=['Authorization: Bearer v2xcf5c31d68b77cce774c02053dc375c6e0fd8ab4ecfe637220ffeedc364320f32','Content-Type:application/json;charset=utf-8','Accept:application/json'];
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl,CURLOPT_HTTPAUTH,CURLAUTH_BASIC);  //设置http验证方法
//      curl_setopt($curl,CURLOPT_HEADER,0);  //设置头信息
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);  //设置curl_exec获取的信息的返回方式
//      curl_setopt($curl,CURLOPT_POST,1);  //设置发送方式为post请求
//      curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($postdata));  //设置post的数据
        curl_setopt($curl, CURLINFO_HEADER_OUT, TRUE);
        $result = curl_exec($curl);
        if($result === false){
            echo curl_errno($curl);
            exit();
        }
        curl_close($curl);
        return $result;
}
function post($url,$postdata){
        $curl = curl_init();  //初始化
        curl_setopt($curl,CURLOPT_URL,$url);  //设置url
        $header=['Authorization: Bearer v2xcf5c31d68b77cce774c02053dc375c6e0fd8ab4ecfe637220ffeedc364320f32','Content-Type:application/json;charset=utf-8','Accept:application/json'];
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl,CURLOPT_HTTPAUTH,CURLAUTH_BASIC);  //设置http验证方法
//      curl_setopt($curl,CURLOPT_HEADER,0);  //设置头信息
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);  //设置curl_exec获取的信息的返回方式
        curl_setopt($curl,CURLOPT_POST,1);  //设置发送方式为post请求
        curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($postdata));  //设置post的数据
        curl_setopt($curl, CURLINFO_HEADER_OUT, TRUE);
        $result = curl_exec($curl);
        if($result === false){
            echo curl_errno($curl);
            exit();
        }
        curl_close($curl);
        return $result;
}



//$temp=post("https://www.bitgo.com/api/v1/wallet",['label'=>'jack','m'=>2,'n'=>3,'keychains'=>[['xpub'=>'xpub661MywAqRbcGJqvexUHEVce9aiRkYYBeAiZnDSjSGZ93jFMfpcSDp36RPgBF5N1W9hFXVJdBaSAwHWHr5zJ6NTQqKKLRzfKg1saoUPmd5T'],['xpub'=>'xpub6GiRC55CSBpR3Lj2GRNGVxBj4r3fionThEEThPpvdMsPEafNArDvnnghKUuEARb1XZatVhc9oj21UddkKzmqbycxbzLsdFoBrw3LuVNzkmL'],['xpub'=>'xpub661MyMwAqRbcFtrRQTDRcQqacpyKwAPEWePfgAHV6BQEXsyvWBAzHA3B1AwzsiXad7S59ienmLCsPYdLKYyNhruxk3CBv3SrRMTR9VeM935']]]);
//$temp=post("https://nanxiao.cba123.cn",['ok'=>'ok']);
//
//print_r($temp);


//print_r(post('https://www.bitgo.com/api/v1/keychain/bitgo',[]));die;

//print_r(get('https://www.bitgo.com/api/v1/keychain',[]));



function newWallet($username){
	$temp=post("https://www.bitgo.com/api/v1/wallet",['label'=>$username,'m'=>2,'n'=>3,'keychains'=>[['xpub'=>'xpub661MyMwAqRbcGJqvexUHEVce9aiRkYYBeAiZnDSjSGZ93jFMfpcSDp36RPgBF5N1W9hFXVJdBaSAwHWHr5zJ6NTQqKKLRzfKg1saoUPmd5T' ],['xpub'=>'xpub6GiRC55CSBpR3Lj2GRNGVxBj4r3fionThEEThPpvdMsPEafNArDvnnghKUuEARb1XZatVhc9oj21UddkKzmqbycxbzLsdFoBrw3LuVNzkmL'],['xpub'=>json_decode(post('https://www.bitgo.com/api/v1/keychain/bitgo',[]),true)['xpub']]]]);
	return json_decode($temp,true)['id'];
}

function yue($walletId){
    $balance = json_decode(get("https://www.bitgo.com/api/v1/wallet/$walletId",[]),true)['balance'];
    $address = json_decode(post("https://www.bitgo.com/api/v1/wallet/$walletId/address/0",[]),true)['address'];
    return ['balance'=>$balance,'address'=>$address];
}
function windowsinfo($chat_id,$title,$data,$button=false){
    $buttoninfo['chat_id']=$chat_id;
    $buttoninfo['parse_mode']='HTML';
    $buttoninfo['chat_id']=$chat_id;
    $text="<code style='background-color:#f80;color:#f80;width:100px'>$title                                            </code>";
    foreach($data as $one){
        $text.=("<b>".$one['title']."</b>: ".$one['des'].'                                                                                        ';
    }
    if($button){
       $inline_keyboard=['inline_keyboard'=>$button]; 
    }
    
}

//echo newWallet('okok');
//echo get("https://www.bitgo.com/api/v1/wallet",[]);

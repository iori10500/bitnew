<?php
ini_set('date.timezone','Asia/Shanghai');
//header("Content-type:text/html;charset=utf-8");
$temp=file_get_contents("https://www.bdo.com.ph/sites/default/files/forex/forex.htm");
$temp=substr($temp,strpos($temp,'<td>CNY</td>'));
$temp=substr($temp,0,strpos($temp,'<td>EUR</td>'));
$temp=str_replace(['<td>CNY</td>','</tr>','<tr>','<td>',"\n"],'',$temp);
$temp=explode('</td>',$temp);
$temp=$temp[1];
$resultdata['bdorate#fff6bf']=$temp;

$temp=file_get_contents("https://api.coinage.ph/orderbook/asks?count=1");
$temp=json_decode($temp,true);
$temp=1/$temp[0]['rate'];

$resultdata['coinage#00905A']=$temp;

$temp=file_get_contents("https://quote.coins.ph/v1/markets/BTC-PHP");
$json=json_decode($temp,true);
$price2=$json['market']['ask'];

$resultdata['coins#4183c4']=$price2;

$temp=file_get_contents("https://bitbit.cash/api/r2/rates?currencyPair=BTCPHP&mode=buy");
$json=json_decode($temp,true);
$price3=$json['rate'];

$resultdata['bitbit#966aca']=$price3;

echo json_encode($resultdata);

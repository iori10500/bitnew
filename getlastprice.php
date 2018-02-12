<?php

$temp=file_get_contents("https://www.okex.com/future/api/inner/ticker.do?symbol=-1");
$temp=json_decode($temp,true);
if(!empty($temp)){
  $price=$temp['ticker'][0]['buy']*$temp['ticker'][0]['usdCnyRate'];  
  $result['note']=true;
  $result['price']=(float)$price;
}else{
  $result['note']=false;
}
echo json_encode($result);
/*


function trance(){
    var length=4;
    $.get("/getlastprice.php",function(data){
        data=JSON.parse(data);
        if(data.note){
            type=Math.floor(Math.random()*10)%2;
            num=(Math.floor(Math.random()*10)+2)*0.001;
            if(!type){
                type=2;
                price=parseInt(Math.random()*length+10-length, 10)*100+data.price;
            }else{
                price=parseInt(Math.random()*length+0, 10)*100+data.price;
            }
            console.log(price);
            

            $.post("/Trade/upTrade.html","price="+price+"&num="+num+"&paypassword=123457&market=btc_cny&type="+type,function(data){
                console.log(data);
            })
        }
    })
}

setInterval("trance()",1000);


*/
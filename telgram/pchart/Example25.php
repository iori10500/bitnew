<?php
 /*
     Example25 : Playing with shadow
 */

 // Standard inclusions  
 ini_set('date.timezone','Asia/Shanghai'); 
 include("pChart/pData.class");
 include("pChart/pChart.class");
 $timeprice=file_exists("../app/timeprice.dat")?json_decode(file_get_contents("../app/timeprice.dat"),true):["price"=>[],"time"=>[]];
 $min=$timeprice['price'][0];
 $max=$timeprice['price'][0];
 foreach ($timeprice['price'] as $key => $value) {
 	if($value <$min)$min=$value;
 	if($value >$max)$max=$value;
 }
 $min-=1000;
 $max+=1000;
 // Dataset definition 
 $DataSet = new pData;
 $DataSet->AddPoint($timeprice['price'],"Serie1");
 $DataSet->AddPoint($timeprice['time'],"Serie3");
 $DataSet->AddAllSeries();
 $DataSet->RemoveSerie("Serie3");
 $DataSet->SetAbsciseLabelSerie("Serie3");
 $DataSet->SetSerieName("BTC ","Serie1");
 $DataSet->SetYAxisName("BTC 价格");
 $DataSet->SetYAxisUnit("元");
 $DataSet->SetXAxisUnit("");

 // Initialise the graph
 $Test = new pChart(1300,700);
 $Test->drawGraphAreaGradient(90,90,90,90,TARGET_BACKGROUND);
 $Test->setFixedScale($min,$max,20);

 // Graph area setup
 $Test->setFontProperties("Fonts/china.ttf",6);
 $Test->setGraphArea(80,100,1250,620);
 $Test->drawGraphArea(24,27,42,FALSE);
 $Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,200,200,200,TRUE,0,2);
 $Test->drawGraphAreaGradient(24,27,42);
 $Test->drawGrid(4,FALSE,38,42,65,10);

 // Draw the line chart
 $Test->setShadowProperties(0,0,0,255,0,20,10);
 $Test->drawCubicCurve($DataSet->GetData(),$DataSet->GetDataDescription());
 $Test->clearShadow();
 $Test->drawPlotGraph($DataSet->GetData(),$DataSet->GetDataDescription(),3,0,-1,-1,-1,TRUE);

 // Write the title
 $Test->setFontProperties("Fonts/china.ttf",30);
 $Test->setShadowProperties(1,1,0,0,0);
 $Test->drawTitle(600,50,"电币BTC交易实时价格(China)",78,180,229,700,30,TRUE);
 $Test->setFontProperties("Fonts/china.ttf",15);
 $Test->drawTitle(1500,130,date("Y年m月d日 H时i分s秒",time()),163,163,163,700,30,TRUE);
 $Test->setFontProperties("Fonts/china.ttf",10);
 $Test->drawTitle(620,130,"今日成交量:   240 BTC",163,163,163,700,30,TRUE);
 $Test->clearShadow();
 $Test->drawFromPNG("Sample/logo.png",550,250,1);
 // Render the picture
 $Test->Render("example25.png");
?>

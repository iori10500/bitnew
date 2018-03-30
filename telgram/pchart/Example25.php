<?php
 /*
     Example25 : Playing with shadow
 */

 // Standard inclusions   
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
 $Test = new pChart(700,1300);
 $Test->drawGraphAreaGradient(90,90,90,90,TARGET_BACKGROUND);
 $Test->setFixedScale($min,$max,10);

 // Graph area setup
 $Test->setFontProperties("Fonts/china.ttf",6);
 $Test->setGraphArea(60,40,680,1250);
 $Test->drawGraphArea(24,27,42,FALSE);
 $Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,200,200,200,TRUE,0,2);
 $Test->drawGraphAreaGradient(24,27,42);
 $Test->drawGrid(4,FALSE,38,42,65,10);

 // Draw the line chart
 $Test->setShadowProperties(3,3,0,0,0,200,4);
 $Test->drawCubicCurve($DataSet->GetData(),$DataSet->GetDataDescription());
 $Test->clearShadow();
 $Test->drawPlotGraph($DataSet->GetData(),$DataSet->GetDataDescription(),3,0,-1,-1,-1,TRUE);

 // Write the title
 $Test->setFontProperties("Fonts/china.ttf",18);
 $Test->setShadowProperties(1,1,0,0,0);
 $Test->drawTitle(0,0,"电币BTC交易实时价格(China)",0,0,0,700,30,TRUE);
 $Test->clearShadow();

 // Draw the legend
 $Test->setFontProperties("Fonts/china.ttf",8);
 $Test->drawLegend(610,5,$DataSet->GetDataDescription(),0,0,0,0,0,0,0,0,0,FALSE);

 // Render the picture
 $Test->Render("example25.png");
?>
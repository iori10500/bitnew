<?php
 /*
     Example25 : Playing with shadow
 */

 // Standard inclusions   
 include("pChart/pData.class");
 include("pChart/pChart.class");
 $timeprice=file_exists("../timeprice.dat")?json_decode(file_get_contents("../timeprice.dat"),true):["price"=>[],"time"=>[]];
 $min=$timeprice['price'][0];
 $max=$timeprice['price'][0];
 foreach ($timeprice['price'] as $key => $value) {
 	if($value <$min)$min=$value;
 	if($value >$max)$max=$value;
 }
 // Dataset definition 
 $DataSet = new pData;
 $DataSet->AddPoint($timeprice['price'],"Serie1");
 $DataSet->AddPoint($timeprice['time'],"Serie3");
 $DataSet->AddAllSeries();
 $DataSet->RemoveSerie("Serie3");
 $DataSet->SetAbsciseLabelSerie("Serie3");
 $DataSet->SetSerieName("January","Serie1");
 $DataSet->SetYAxisName("Temperature");
 $DataSet->SetYAxisUnit("°C");
 $DataSet->SetXAxisUnit("h");

 // Initialise the graph
 $Test = new pChart(700,230);
 $Test->drawGraphAreaGradient(90,90,90,90,TARGET_BACKGROUND);
 $Test->setFixedScale($min,$max,10);

 // Graph area setup
 $Test->setFontProperties("Fonts/pf_arma_five.ttf",6);
 $Test->setGraphArea(60,40,680,200);
 $Test->drawGraphArea(200,200,200,FALSE);
 $Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,200,200,200,TRUE,0,2);
 $Test->drawGraphAreaGradient(40,40,40,-50);
 $Test->drawGrid(4,TRUE,230,230,230,10);

 // Draw the line chart
 $Test->setShadowProperties(3,3,0,0,0,30,4);
 $Test->drawCubicCurve($DataSet->GetData(),$DataSet->GetDataDescription());
 $Test->clearShadow();
 $Test->drawPlotGraph($DataSet->GetData(),$DataSet->GetDataDescription(),3,0,-1,-1,-1,TRUE);

 // Write the title
 $Test->setFontProperties("Fonts/MankSans.ttf",18);
 $Test->setShadowProperties(1,1,0,0,0);
 $Test->drawTitle(0,0,"Average temperatures",255,255,255,700,30,TRUE);
 $Test->clearShadow();

 // Draw the legend
 $Test->setFontProperties("Fonts/tahoma.ttf",8);
 $Test->drawLegend(610,5,$DataSet->GetDataDescription(),0,0,0,0,0,0,255,255,255,FALSE);

 // Render the picture
 $Test->Render("example25.png");
?>
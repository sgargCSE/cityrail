<?php

function getMain($xml) {
   return $xml->table->tbody->tr;
}

function getXmlRow($xml, $name) {
   $ret = array();
   foreach(getMain($xml) as $row) {
      if($row->td[0]->span == $name) {
         foreach($row->td as $val) {
            $ret[] = $val;
         }
         return $ret;
      }
   }
}

function getTrains($xml, $start, $end, $tnow) {
   //print date("H:i", $tnow) . "\n";
   $arr = array();
   $sXml = getXmlRow($xml, $start);
   $eXml = getXmlRow($xml, $end);

   foreach($sXml as $ind=>$time) {
      $toAdd = array("s"=>strtotime($time), "e"=> strtotime($eXml[$ind]));
      if($toAdd['s'] && $toAdd['e'] && $tnow <= $toAdd['s']){
         $toAdd['sStr'] = date("H:i", $toAdd['s']);
         $toAdd['eStr'] = date("H:i", $toAdd['e']);
         $arr[] = $toAdd;
         //print $toAdd['sStr'] . " - " . $toAdd['eStr'] . "\n";
      }
   }

   return $arr;
}
/*
function sortByQuickest(&$toSort) {
   $tempArray = array();
   foreach(
}
 */

$_GET['start'] = 'Hurstville'; 
$_GET['end'] = 'Martin Place';

foreach ($argv as $arg) {
   $e=explode("=",$arg);
   if(count($e)==2)
      $_GET[$e[0]]=$e[1];
   else    
      $_GET[$e[0]]=0;
}

$str = file_get_contents('cityrail.html');
$xml = simplexml_load_string($str);
$tNow = strtotime('09:00'); //time()
$allTrains = getTrains($xml, $_GET['start'], $_GET['end'], $tNow);
$subset = array_slice($allTrains, 0, 5);
$formatted = array();
foreach($subset as $v) {
   $formatted[] = array('title'=>$v['sStr'], 'value'=> 'Gets there at: '.$v['eStr']); 
}

//var_dump($formatted);

$json = json_encode($formatted);
print $json;

?>



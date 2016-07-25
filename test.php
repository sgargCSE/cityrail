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

   //TODO: Any times before 4pm modify the time to add 12hours to it.
   foreach($sXml as $ind=>$time) {
      $toAdd = array("s"=>strtotime($time), "e"=> strtotime($eXml[$ind]), "raw"=>($time . "-" . $eXml[$ind]));
      if($toAdd['s'] && $toAdd['e'] && $tnow <= $toAdd['s']){
         $toAdd['sStr'] = date("H:i", $toAdd['s']);
         $toAdd['eStr'] = date("H:i", $toAdd['e']);
         $arr[] = $toAdd;
         //print $toAdd['sStr'] . " - " . $toAdd['eStr'] . "\n";
      } else {
         //print $toAdd["raw"] . "\n";
      }
   }

   return $arr;
}

function sortByQuickest(&$toSort) {
   $tempArray = array();
   foreach($toSort as $k=>$v) {
      $tempArray[$k] = $v['e'];
   }
   array_multisort($tempArray, SORT_ASC, $toSort);
}

$_GET['start'] = 'Hurstville'; 
$_GET['end'] = 'Martin Place';
$_GET['time'] = '9:00';

foreach ($argv as $arg) {
   $e=explode("=",$arg);
   if(count($e)==2)
      $_GET[$e[0]]=$e[1];
   else    
      $_GET[$e[0]]=0;
}

$str = file_get_contents('cityrail.html');
$xml = simplexml_load_string($str);
$tNow = strtotime($_GET['time']); //time()
$allTrains = getTrains($xml, $_GET['start'], $_GET['end'], $tNow);

//sortByQuickest($allTrains);

$subset = array_slice($allTrains, 0, 10);
$formatted = array();
foreach($subset as $v) {
   $formatted[] = array('title'=>$v['sStr'], 'value'=> 'Gets there at: '.$v['eStr']); 
}

foreach($subset as $v) {
   print $v['eStr'] . "\n";
}

$json = json_encode($formatted);
print $json;

?>


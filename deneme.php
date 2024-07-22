<?php

$lme = 0;

$url = 'https://www.bloomberght.com/emtia/aliminyum';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$html = curl_exec($ch);
curl_close($ch);
$dom = new DOMDocument();
@$dom->loadHTML($html);
$xpath = new DOMXPath($dom);
$spanList = $xpath->query('//span');
$lmeArray = [];

foreach ($spanList as $index => $item) {
    $content = $item->nodeValue;
    array_push($lmeArray, $content);
}

$number = $lmeArray[108];
$number = str_replace(".", "", $number);
$number = str_replace(",", ".", $number);
$number = floatval($number);
$roundedNumber = intval($number);
$lme1 = $roundedNumber + 1;

$lme = $lme1;

?>

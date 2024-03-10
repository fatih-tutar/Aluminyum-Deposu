<?php
$url = 'https://www.bloomberght.com/emtia/aliminyum3m';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$html = curl_exec($ch);
curl_close($ch);
$dom = new DOMDocument();
@$dom->loadHTML($html);
$xpath = new DOMXPath($dom);
$h1List = $xpath->query('//h1');
foreach ($h1List as $index => $item) {
    if($index == 0){
        $content = $item->nodeValue;
        $lmeArray = explode(" ", $content);
        foreach($lmeArray as $key => $lm){
            echo "Key: ".$key." Value: ".$lm."<br/>";
        }
        $number = $lmeArray[38];
        $number = str_replace(".", "", $number);
        $number = str_replace(",", ".", $number);
        $number = floatval($number);
        $roundedNumber = intval($number);
        $lme = $roundedNumber + 1;
        echo $lme;
    }
}
?>

<?php
    include 'fonksiyonlar/bagla.php'; 
    $query = $db->query("SELECT turunid FROM teklif", PDO::FETCH_ASSOC);
    if($query->rowCount()) {
        foreach($query as $row) {
            $turunId = $row['turunid'];
            $sorgu = $db->query("SELECT urun_id FROM urun WHERE urun_id = '{$turunId}'")->fetch(PDO::FETCH_ASSOC);
            if(!$sorgu) {
                echo $turunId."<br/>";
            }
        }
    }


// $lme = 0;

// $url = 'https://www.bloomberght.com/emtia/aliminyum';
// $ch = curl_init($url);
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// $html = curl_exec($ch);
// curl_close($ch);
// $dom = new DOMDocument();
// @$dom->loadHTML($html);
// $xpath = new DOMXPath($dom);
// $spanList = $xpath->query('//span');
// $lmeArray = [];

// foreach ($spanList as $index => $item) {
//     $content = $item->nodeValue;
//     array_push($lmeArray, $content);
// }

// print_r($lmeArray);

// $number = $lmeArray[22];
// $number = str_replace(".", "", $number);
// $number = str_replace(",", ".", $number);
// $number = floatval($number);
// $roundedNumber = intval($number);
// $lme1 = $roundedNumber + 1;

// $lme = $lme1;

// echo $lme;

?>

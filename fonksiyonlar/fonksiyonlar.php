<?php

	function getUsername($userId){
		global $db;
		$user = $db->query("SELECT uye_adi FROM uyeler WHERE uye_id = '{$userId}' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
		return $user['uye_adi'];
	}

	function getCategoryInfo($categoryId){
		global $db;
		$category = $db->query("SELECT * FROM kategori WHERE kategori_id = '{$categoryId}' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
		return $category;
	}

	function getSatis($urunId){

		$urun_alis = getUrunInfo($urunId)['urun_alis'];
		$urun_satis = getUrunInfo($urunId)['satis'];

		if($urun_satis == 0){

			$categoryId = getUrunInfo($urunId)['kategori_bir'];

			$ust_kategori_kar_yuzdesi = getCategoryInfo($categoryId)['karyuzdesi'];

			$urun_satis = $urun_alis * ($ust_kategori_kar_yuzdesi + 100) / 100;

			$urun_satis = round($urun_satis, 2);
		}

		return $urun_satis;
	}

	function getCategoryShortName($categoryId){
		global $db;
		$category = $db->query("SELECT kategori_adi FROM kategori WHERE kategori_id = '{$categoryId}' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
		$categoryName = $category['kategori_adi'];
		$categoryNameArray = explode(" ",$categoryName);
		$categoryShortName = $categoryNameArray[0];
		return $categoryShortName;
	}

	function getCategoryName($categoryId){
		global $db;
		$category = $db->query("SELECT kategori_adi FROM kategori WHERE kategori_id = '{$categoryId}' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
		$categoryName = $category['kategori_adi'];
		return $categoryName;
	}

	function getUrunInfo($urunId){
		global $db;
		$urunInfo = $db->query("SELECT * FROM urun WHERE urun_id = '{$urunId}' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
		return $urunInfo;
	}

	function getdmY($saniye){
		return date('d.m.Y', $saniye);
	}

	function getFirmaAdi($firmaId){
		global $db;
		$query = $db->query("SELECT firmaadi FROM firmalar WHERE firmaid = '{$firmaId}'")->fetch(PDO::FETCH_ASSOC);
		$firmaAdi = $query['firmaadi'];
		return $firmaAdi;
	}

	function getDolar(){
		$icerik = file_get_contents("https://www.tcmb.gov.tr/kurlar/today.xml");
		
		$baslik = ara("<ForexSelling>", "</ForexSelling>", $icerik);
		
		$dolarsatis = $baslik[0];

		$formatted_dolar = number_format($dolarsatis, 2, '.', '');

		return $formatted_dolar;
	}

	function getLME(){

		//$string = file_get_contents("https://www.lme.com/api/trading-data/fifteen-minutes-metal-block?datasourceIds=48b1eb21-2c1c-4606-a031-2e0e48804557&datasourceIds=30884874-b778-48ec-bdb2-a0a1d98de5ab&datasourceIds=53f6374a-165d-446a-b9f6-b08bbd2e46a3&datasourceIds=9632206e-db22-407f-892c-ac0fb7735b2e&datasourceIds=61f12b51-04e8-4269-987b-3d4516b20f41&datasourceIds=2908ddcb-e514-4265-9ad9-f0d27561cf52");
		
		//$json_a = json_decode($string, true);

		$lme = 0;

		$url = 'https://www.bloomberght.com/emtia/aliminyum';
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
				$number = $lmeArray[72];
				$number = str_replace(".", "", $number);
				$number = str_replace(",", ".", $number);
				$number = floatval($number);
				$roundedNumber = intval($number);
				$lme1 = $roundedNumber + 1;
			}
		}

		$lme = $lme1;

		// $url = 'https://www.bloomberght.com/emtia/aliminyum3m';
		// $ch = curl_init($url);
		// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// $html = curl_exec($ch);
		// curl_close($ch);
		// $dom = new DOMDocument();
		// @$dom->loadHTML($html);
		// $xpath = new DOMXPath($dom);
		// $h1List = $xpath->query('//h1');
		// foreach ($h1List as $index => $item) {
		// 	if($index == 0){
		// 		$content = $item->nodeValue;
		// 		$lmeArray = explode(" ", $content);
		// 		$number = $lmeArray[74];
		// 		$number = str_replace(".", "", $number);
		// 		$number = str_replace(",", ".", $number);
		// 		$number = floatval($number);
		// 		$roundedNumber = intval($number);
		// 		$lme2 = $roundedNumber + 1;
		// 	}
		// }

		// if($lme2 > $lme1){ $lme = $lme2; }else{$lme = $lme1;}

		return $lme;
	}

	function ayAdi($ay){

		switch ($ay) {
			case '01':
				return 'Ocak';
				break;

			case '02':
			return 'Şubat';
			break;

			case '03':
			return 'Mart';
			break;

			case '04':
			return 'Nisan';
			break;

			case '05':
			return 'Mayıs';
			break;

			case '06':
			return 'Haziran';
			break;

			case '07':
			return 'Temmuz';
			break;

			case '08':
			return 'Ağustos';
			break;

			case '09':
			return 'Eylül';
			break;

			case '10':
			return 'Ekim';
			break;

			case '11':
			return 'Kasım';
			break;
			
			default:
				return 'Aralık';
				break;
		}

	}

	function uppercase_tr($string){

		$string = str_replace("ç", "Ç", $string);

		$string = str_replace("ğ", "Ğ", $string);

		$string = str_replace("ı", "I", $string);

		$string = str_replace("i", "İ", $string);

		$string = str_replace("ö", "Ö", $string);

		$string = str_replace("ş", "Ş", $string);

		$string = str_replace("ü", "Ü", $string);

		$string = strtoupper($string);

		return $string;

	}

	function explodeEachChar($x) {
			    $c = array();
			    while (strlen($x) > 0) {
			        $c[] = substr($x,0,1);
			        $x = substr($x,1);
			    }
			    return $c;
			}

	function bosluk_sil($string)
	{
	   $string = preg_replace("/\s+/", " ", $string);
	   $string = trim($string);
	   return $string;
	}	

	function guvenlik($veri){

		global $db;

		$veri = bosluk_sil($veri);

		$veri_patlat = explodeEachChar($veri);

		foreach($veri_patlat as $key => $value)
		{
			$alfabedizi = "abcçdefgğhıijklmnoöqprsştuüvwxyzwqABCÇDEFGĞHIİJKLMNOÖPRSŞTUÜVYZWQ0123456789.,_-:/\@<>*# ";

			if (!preg_match("/[".$value."]/i", $alfabedizi)) {

				unset($veri_patlat[$key]);
				
			}
		}

		$veri_birlestir = implode("", $veri_patlat);

		$veri = $veri_birlestir;

		return $veri;

	}

	function giris($uye_adi, $sifre){

		global $db;

		$query = $db->query("SELECT * FROM uyeler WHERE uye_adi = '{$uye_adi}'")->fetch(PDO::FETCH_ASSOC);

		$uye_id = $query['uye_id'];

		$sorgu = $db->prepare("SELECT COUNT(*) FROM uyeler WHERE uye_adi = '{$uye_adi}' AND uye_sifre = '{$sifre}'");
		$sorgu->execute();
		$say = $sorgu->fetchColumn();

		if ($say == '0') {
			return false;
		}else{
			return $uye_id;
		}
	}

	function giris_yapti_mi(){

		return (isset($_SESSION['uye_id'])) ? true: false;
	}

	function pasifmi($uye_adi){

		global $db;

		$sorgu = $db->prepare("SELECT COUNT(*) FROM uyeler WHERE uye_adi = '{$uye_adi}' AND pasiflik = '1'");
		$sorgu->execute();
		$say = $sorgu->fetchColumn();

		return ($say == '0') ? '0' : '1';
	}

	function tarihvarmi($tarih){

		global $db;

		$sorgu = $db->prepare("SELECT COUNT(*) FROM gelengiden WHERE tarih = '{$tarih}'");
		$sorgu->execute();
		$say = $sorgu->fetchColumn();

		return ($say == '0') ? '0' : '1';
	}

	function uye_id_var_mi($uye_id){

		global $db;

		$sorgu = $db->prepare("SELECT COUNT(*) FROM uyeler WHERE uye_id = '{$uye_id}'");
		$sorgu->execute();
		$say = $sorgu->fetchColumn();

		return ($say == '0') ? '0' : '1';
	}

	function uye_adi_var_mi($uye_adi){

		global $db;

		$sorgu = $db->prepare("SELECT COUNT(*) FROM uyeler WHERE uye_adi = '{$uye_adi}'");
		$sorgu->execute();
		$say = $sorgu->fetchColumn();

		return ($say == '0') ? '0' : '1';
	}

	function kategoridolumu($kategori_id){

		global $db;

		$sorgu = $db->prepare("SELECT COUNT(*) FROM urun WHERE kategori_bir = '{$kategori_id}' || kategori_iki = '{$kategori_id}'");
		$sorgu->execute();
		$say = $sorgu->fetchColumn();

		return ($say == '0') ? '0' : '1';
	}

	function siparisvarmi($fabrika_id){

		global $db;

		$sorgu = $db->prepare("SELECT COUNT(*) FROM siparis WHERE urun_fabrika_id = '{$fabrika_id}'");
		$sorgu->execute();
		$say = $sorgu->fetchColumn();

		return ($say == '0') ? '0' : '1';
	}

	function teklifvarmi($firmaid){

		global $db;

		$sorgu = $db->prepare("SELECT COUNT(*) FROM teklif WHERE tverilenfirma = '{$firmaid}'");
		$sorgu->execute();
		$say = $sorgu->fetchColumn();

		return ($say == '0') ? '0' : '1';
	}

	function yollaf($veri){

		$veri = addslashes($veri);

		$veri = strip_tags($veri);

		return $veri;

	}

	function cekf($veri){

		$veri = stripslashes($veri);

		$veri = strip_tags($veri);

		return $veri;

	}

	function otomatikyedekal($su_an, $uye_sirket, $sayfa){

		global $db;

		$dbhost = "aluminyumdeposu.com"; //Veritabanın bulunduğu host
		$dbuser = "u9022286_depokullanici"; //Veritabanı Kullanıcı Adı
		$dbpass = "Sifrem10"; //Veritabanı Şifresi
		$dbdata = "u9022286_depoveritabani'"; //Veritabanı Adı

		include 'DBBackupRestore.class.php'; //DBBackup.class.php dosyamızı dahil ediyoruz
		$dbBackup = new DBYedek(); // class'imizla $dbBackup nesnemizi olusturduk

			//$kayityeri klasor yolu belirtirken sonunda mutlaka / olmali (klasoradi/) seklinde
			$kayityeri	= "yedekler/";	// ayni dizin için $kayityeri degiskeni bos birakilmali
			$arsiv		= false;	//Yedeği zip arsivi olarak almak için true // .sql olarak almak için false
			$tablosil	= false;		//DROP TABLE IF EXISTS satırı eklemek için true // istenmiyorsa false
			//Veri için kullanılacak sözdizimi:
			$veritipi	= 1; // INSERT INTO tbl_adı VALUES (1,2,3);
			//$veritipi	= 2; // INTO tbl_adı VALUES (1,2,3), (4,5,6), (7,8,9);
			//$veritipi	= 3; // INSERT INTO tbl_adı (sütun_A,sütun_B,sütun_C) VALUES (1,2,3);
			//$veritipi	= 4; // INSERT INTO tbl_adı (col_A,col_B,col_C) VALUES (1,2,3), (4,5,6), (7,8,9);

			$backup = $dbBackup->Disa_Aktar($kayityeri, $arsiv, $tablosil, $veritipi);

			if($backup){ 

				$yedekal = $db->prepare("UPDATE sirketler SET yedekalmasaniye = ? WHERE sirketid = ?"); 

				$yedekguncelle = $yedekal->execute(array($su_an,$uye_sirket));

				echo '<div class="alert alert-warning" style="position:fixed; z-index:1; left:10%; border-width:3px; border-color:#856404;" >
						Veritabanı yedeğiniz sunucuya alındı.<br/>Bilgisayarınıza da indirmek istiyorsanız<br/>aşağıdaki evet butonuna tıklayın.
						<br/><br/>
						<a href="' . $backup . '" download="' . $backup . '">
							<button class="btn btn-warning">Evet, indirmek istiyorum.</button
						</a>
						<a href="' . $sayfa . '">
							<button class="btn btn-warning">Hayır, indirmek istemiyorum.</button
						</a>

					</div>';
			} else {
				echo 'Beklenmedik hata oluştu!';
			}

		$dbBackup->kapat();// $dbBackup nesnemizi kapattik

	}

	function yedekal(){

		include 'Yedekle.class.php';
     
		$bilgiler = array(
	        'src' => 'mysql', 
	        'host' => 'aluminyumdeposu.com',  
	        'kadi' => 'u9022286_depokullanici',  
	        'parola' => 'Sifrem10',
	        'veritabani' => 'u9022286_depoveritabani'
        );
 
        $olustur = new Yedekle($bilgiler);
        $yedekle = $olustur->yedek();
        if(!$yedekle['hata']){
 
            header('Content-type: text/plain');
            header('Content-disposition: attachment; filename=vt_'.uniqid().'.sql');
            echo $yedekle['mesaj'];
 
        } else {
            echo 'İşlem başarısız oldu!';
        }

	}

	function uyeadcek($uye_id){

		global $db;

		$uyeadcek = $db->query("SELECT * FROM uyeler WHERE uye_id = '{$uye_id}'")->fetch(PDO::FETCH_ASSOC);

		$uye_adi = $uyeadcek['uye_adi'];

		return $uye_adi;

	}

	function firmaadcek($firmaid){

		global $db;

		$firmaadcek = $db->query("SELECT * FROM firmalar WHERE firmaid = '{$firmaid}'")->fetch(PDO::FETCH_ASSOC);

		$firmaadi = $firmaadcek['firmaadi'];

		return $firmaadi;

	}

	function fabrikakullanimdami($fabrika_id){

		global $db;

		$sorgu = $db->prepare("SELECT COUNT(*) FROM urun WHERE urun_fabrika = '{$fabrika_id}'");
		$sorgu->execute();
		$usay = $sorgu->fetchColumn();

		$sorgu = $db->prepare("SELECT COUNT(*) FROM siparis WHERE urun_fabrika_id = '{$fabrika_id}'");
		$sorgu->execute();
		$ssay = $sorgu->fetchColumn();

		$sorgu = $db->prepare("SELECT COUNT(*) FROM siparisformlari WHERE fabrikaid = '{$fabrika_id}'");
		$sorgu->execute();
		$sfsay = $sorgu->fetchColumn();

		return ($usay == '0' && $ssay == '0' && $sfsay == '0') ? '0' : '1';

	}

	function firmakullanimdami($firmaid){

		global $db;

		$sorgu = $db->prepare("SELECT COUNT(*) FROM teklif WHERE tverilenfirma = '{$firmaid}'");
		$sorgu->execute();
		$ssay = $sorgu->fetchColumn();

		$sorgu = $db->prepare("SELECT COUNT(*) FROM teklifformlari WHERE firmaid = '{$firmaid}'");
		$sorgu->execute();
		$sfsay = $sorgu->fetchColumn();

		return ($ssay == '0' && $sfsay == '0') ? '0' : '1';

	}

	function ara($bas, $son, $yazi)
    {
        @preg_match_all('/' . preg_quote($bas, '/') .
        '(.*?)'. preg_quote($son, '/').'/i', $yazi, $m);
        return @$m[1];
    }

?>
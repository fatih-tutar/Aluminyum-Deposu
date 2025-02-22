<?php

function controlProductById($productId){
    global $db;
    $product = $db->query("SELECT * FROM urun WHERE urun_id='{$productId}' AND silik='0'");
    if($product->rowCount() > 0){
        return true;
    }else{
        return false;
    }
}
function getFactoryInfos($fabrikaId) {
    global $db;
    $fabrika = $db->query("SELECT * FROM fabrikalar WHERE fabrika_id = '{$fabrikaId}' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    return $fabrika;
}

function izinTarihKontrol($izinBaslangicTarihi, $iseBaslamaTarihi, $ofis) {
    global $db;
    $izin = $db->query("SELECT * FROM izinler WHERE ofis = '{$ofis}' AND silik = '0' AND durum != '2' AND (
		('{$izinBaslangicTarihi}' >= izin_baslangic_tarihi AND '{$izinBaslangicTarihi}' < ise_baslama_tarihi) 
		OR 
		('{$iseBaslamaTarihi}' >= izin_baslangic_tarihi AND '{$iseBaslamaTarihi}' < ise_baslama_tarihi))");
    return $izin->rowCount();
}

function getOfisType($uyeId) {
    global $db;
    $uye = $db->query("SELECT * FROM users WHERE id = '{$uyeId}' AND is_deleted = '0'")->fetch(PDO::FETCH_ASSOC);
    $yetkiArray = explode(",", $uye['permissions']);
    return $yetkiArray[14];
}

function iseGirisTarihiGetir($uyeId) {
    global $db;
    $uye = $db->query("SELECT * FROM users WHERE id = '{$uyeId}' AND is_deleted = '0'")->fetch(PDO::FETCH_ASSOC);
    return $uye['hire_date'];
}

function kullanilanIzinHesapla($uyeId) {
    global $db;
    $yil = date("Y");
    $kullanilanIzin = $db->query("SELECT SUM(gun_sayisi) as toplam_kullanilan FROM izinler WHERE izinli = '{$uyeId}' AND YEAR(izin_baslangic_tarihi) = '{$yil}' AND durum = '1' AND silik = '0'")->fetch(PDO::FETCH_ASSOC);
    return !$kullanilanIzin['toplam_kullanilan'] ? 0 : $kullanilanIzin['toplam_kullanilan'];
}

function yillikIzinHesapla($uyeId) {
    global $db;
    $uye = $db->query("SELECT * FROM users WHERE id = '{$uyeId}' AND is_deleted = '0'")->fetch(PDO::FETCH_ASSOC);
    $iseGirisTarihi = $uye['hire_date'];
    $bugun = new DateTime();
    $baslamaTarihi = new DateTime($iseGirisTarihi);
    $fark = $bugun->diff($baslamaTarihi);
    $yilFarki = $fark->y;
    if ($yilFarki < 1) {
        return 0; // 0-1 yıl arası izin hakkı yok
    } elseif ($yilFarki >= 1 && $yilFarki < 5) {
        return 14; // 1-5 yıl arası 14 gün izin
    } elseif ($yilFarki >= 5 && $yilFarki < 15) {
        return 20; // 5-15 yıl arası 20 gün izin
    } else {
        return 26; // 15 yıldan fazla ise 26 gün izin
    }
}

function getLastLeaveDate($izinli) {
    global $db;
    $lastLeave = $db->query("SELECT * FROM izinler WHERE izinli = '{$izinli}' AND silik = '0' AND durum != '2' ORDER BY izin_baslangic_tarihi DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    return $lastLeave['ise_baslama_tarihi'];
}

function getSevkiyatInfo($sevkiyatID){
    global $db;
    $sevkiyat = $db->query("SELECT * FROM sevkiyat WHERE id = '{$sevkiyatID}' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    return $sevkiyat;
}

function getUsername($userId){
    global $db;
    $user = $db->query("SELECT name FROM users WHERE id = '{$userId}' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    if($user) {
        return $user['name'];
    }else{
        return null;
    }
}

function getCategoryInfo($categoryId){
    global $db;
    $category = $db->query("SELECT * FROM kategori WHERE kategori_id = '{$categoryId}' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    return $category;
}

function getCategoryID($categoryName,$categoryType){
    global $db;
    $query = $db->query("SELECT kategori_id FROM kategori WHERE kategori_adi = '{$categoryName}' AND kategori_tipi = '{$categoryType}'")->fetch(PDO::FETCH_ASSOC);
    $categoryID = $query['kategori_id'];
    return $categoryID;
}

function getSubCategoryID($categoryName,$mainCategory){
    global $db;
    $query = $db->query("SELECT kategori_id FROM kategori WHERE kategori_adi = '{$categoryName}' AND kategori_ust = '{$mainCategory}' AND kategori_tipi = '1'")->fetch(PDO::FETCH_ASSOC);
    $categoryID = $query['kategori_id'];
    return $categoryID;
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
    if($category && isset($category['kategori_adi'])){
        return $category['kategori_adi'];
    }
    return null;
}

function getUrunInfo($urunId){
    global $db;
    $urunInfo = $db->query("SELECT * FROM urun WHERE urun_id = '{$urunId}' LIMIT 1");
    if($urunInfo) {
        $urunInfo = $urunInfo->fetch(PDO::FETCH_ASSOC);
        return $urunInfo;
    }else{
        return false;
    }
}

function getUrunID($urunAdi,$kategori_iki,$kategori_bir){
    global $db;
    $category1ID = getCategoryID($kategori_bir,'0');
    $category2ID = getSubCategoryID($kategori_iki,$category1ID);
    $query = $db->query("SELECT urun_id FROM urun WHERE urun_adi = '{$urunAdi}' AND kategori_iki = '{$category2ID}' AND kategori_bir = '{$category1ID}' ")->fetch(PDO::FETCH_ASSOC);
    $urunId = $query['urun_id'];
    return $urunId;
}

function getHis($saniye){
    return date('H:i:s', $saniye);
}

function getdmY($saniye){
    return date('d.m.Y', $saniye);
}

function getFirmaInfos($firmaId){
    global $db;
    $firmaInfos = $db->query("SELECT * FROM firmalar WHERE firmaid = '{$firmaId}'")->fetch(PDO::FETCH_ASSOC);
    return $firmaInfos;
}

function getFirmaID($firmaAdi){
    global $db;
    $query = $db->query("SELECT firmaid FROM firmalar WHERE firmaadi = '{$firmaAdi}'")->fetch(PDO::FETCH_ASSOC);
    $firmaid = $query['firmaid'];
    return $firmaid;
}
function getFirmaAdi($firmaId){
    global $db;
    $query = $db->query("SELECT firmaadi FROM firmalar WHERE firmaid = '{$firmaId}' AND silik='0'")->fetch(PDO::FETCH_ASSOC);
    if ($query && isset($query['firmaadi'])) {
        return $query['firmaadi'];
    }
    return null;
}

function getDolar(){
    $icerik = @file_get_contents("https://www.tcmb.gov.tr/kurlar/today.xml");
    if($icerik !== false){
        $baslik = ara("<ForexSelling>", "</ForexSelling>", $icerik);
        if (is_array($baslik) && isset($baslik[0])) {
            $dolarsatis = $baslik[0];
            $formatted_dolar = number_format($dolarsatis, 2, '.', '');
            return $formatted_dolar;
        }else{
            return 0;
        }
    }else{
        return 0;
    }
}

function getLME(){
    $lme = 0;

    $url = 'https://www.bloomberght.com/emtia/aliminyum';
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true); // Sadece header bilgisi alınır
    curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // HTTP kodu alınır
    curl_close($ch);

    // Eğer HTTP kodu 200 değilse hata mesajı döndür
    if ($httpCode !== 200) {
        error_log("Hata: URL erişilemez. HTTP Kodu: $httpCode");
        return $lme; // Hata durumunda $lme = 0 döner
    }

    // Sayfa içeriğini çek
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $html = curl_exec($ch);
    curl_close($ch);

    if (empty($html)) {
        error_log("Hata: URL'den alınan içerik boş.");
        return $lme;
    }

    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    $spanList = $xpath->query('//span');
    $lmeArray = [];

    foreach ($spanList as $index => $item) {
        $content = $item->nodeValue;
        array_push($lmeArray, $content);
    }

    if (isset($lmeArray[22])) {
        $number = $lmeArray[22];
        $number = str_replace(".", "", $number);
        $number = str_replace(",", ".", $number);
        $number = floatval($number);
        $roundedNumber = intval($number);
        $lme1 = $roundedNumber + 1;

        $lme = $lme1;
    } else {
        $lme = 1;
    }

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

        case '12':
            return 'Aralık';
            break;

        default:
            return 'Ocak';
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
        if (!preg_match("/[" . preg_quote($value, '/') . "]/i", $alfabedizi)) {
            unset($veri_patlat[$key]);
        }
    }
    $veri_birlestir = implode("", $veri_patlat);
    $veri = $veri_birlestir;
    return $veri;
}

function giris($name, $sifre){
    global $db;
    $query = $db->query("SELECT * FROM users WHERE name = '{$name}'")->fetch(PDO::FETCH_ASSOC);
    if($query && isset($query['id'])) {
        $userId = $query['id'];
    }
    $sorgu = $db->prepare("SELECT COUNT(*) FROM users WHERE name = '{$name}' AND password = '{$sifre}'");
    $sorgu->execute();
    $say = $sorgu->fetchColumn();
    if ($say == '0') {
        return false;
    }else{
        return $userId;
    }
}

function isLoggedIn(){

    return (isset($_SESSION['user_id'])) ? true: false;
}

function pasifmi($name){
    global $db;
    $sorgu = $db->prepare("SELECT COUNT(*) FROM users WHERE name = '{$name}' AND is_passive = '1'");
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

function checkUserById($userId){

    global $db;

    $sorgu = $db->prepare("SELECT COUNT(*) FROM users WHERE id = '{$userId}'");
    $sorgu->execute();
    $say = $sorgu->fetchColumn();

    return ($say == '0') ? '0' : '1';
}

function checkUserByName($name){

    global $db;

    $sorgu = $db->prepare("SELECT COUNT(*) FROM users WHERE name = '{$name}'");
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

function otomatikyedekal($companyId){

    global $db;

    $sayfa = explode('/',$_SERVER['SCRIPT_NAME']);
    $sayfa = end($sayfa);

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

        $yedekal = $db->prepare("UPDATE companies SET backup_time = ? WHERE id = ?");

        $yedekguncelle = $yedekal->execute(array(time(),$companyId));

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

    $aluminyumDeposu = [
        'host' => 'aluminyumdeposu.com',
        'name' => 'u9022286_depoveritabani',
        'user' => 'u9022286_depokullanici',
        'password' => 'Sifrem10'
    ];

    $aluminyumStok = [
        'host' => 'aluminyumstok.com',
        'name' => 'aluminy4_db',
        'user' => 'aluminy4_fatih',
        'password' => 'ZWT3?CR?k}+y'
    ];

    $database = $aluminyumDeposu;

    $bilgiler = [
        'src' => 'mysql',
        'host' => $database['host'],
        'kadi' => $database['user'],
        'parola' => $database['password'],
        'veritabani' => $database['name']
    ];

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

function uyeadcek($userId){

    global $db;

    $uyeadcek = $db->query("SELECT * FROM users WHERE id = '{$userId}'")->fetch(PDO::FETCH_ASSOC);

    $name = $uyeadcek['name'];

    return $name;

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
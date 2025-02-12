<?php
	session_start();
	ob_start();
	$su_an = time();
	$tarih = date("Y-m-d H:i:s", time());
	$bugunformatlitarih = date("d-m-Y", time());
	$bugununsaniyesi = strtotime($bugunformatlitarih);
	$tarihf2 = date("d-m-Y",$su_an);
	$tarihv3 = date("Y-m-d",$su_an);
	$girdi = 0;
	$hata = "";
	setlocale(LC_TIME, "turkish");
	include 'database.php';
	include 'fonksiyonlar.php';
    $dbInstance = new Database();
    $db = $dbInstance->getConnection();
	// Osmanlı Alüminyum İçin
	$fiyatlistesi = $db->query("SELECT * FROM sirketler WHERE sirketid = '2'")->fetch(PDO::FETCH_ASSOC);
	$sirketfiyatlistesi = guvenlik($fiyatlistesi['fiyatlistesi']);
	$site_adresi = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
	if(giris_yapti_mi() === true){
		$uye_session_id = $_SESSION['user_id'];
        $user = $db->query("SELECT * FROM users WHERE id = '$uye_session_id'", PDO::FETCH_OBJ)->fetch();
		$userPermissionKeys = [
            'buying_price','factory','quote','order','editing','transaction','stock_flow','selling_price',
            'total_view','visit','shipment','piece','pallet','alkop','office','vehicle'
        ];
        $userPermissionValues = explode(",", $user->permissions);
        $user->permissions = (object) array_combine($userPermissionKeys, $userPermissionValues);

		//ŞİRKET BİLGİLERİ
		$sirketbilgileri = $db->query("SELECT * FROM sirketler WHERE sirketid = '{$user->company_id}'")->fetch(PDO::FETCH_ASSOC);
		$sirketaciklama = $sirketbilgileri['sirketaciklama'];
		$sirketlogo = $sirketbilgileri['sirketlogo'];
		$sirketyedekalmasaniye = $sirketbilgileri['yedekalmasaniye'];
		$sirketfiyatlistesi = guvenlik($sirketbilgileri['fiyatlistesi']);
		// KURLAR
		$dolar = getDolar();
		$lme = getLME();
	}
	if (giris_yapti_mi() === true ) {
		$girdi = 1;
		if (checkUserById($uye_session_id) == '0') {
			header("Location:cikis.php");
		}
	}else{
		$girdi = 0;
	}
	$sayfa = explode('/',$_SERVER['SCRIPT_NAME']);
	$sayfa = end($sayfa);
?>
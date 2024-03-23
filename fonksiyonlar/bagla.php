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

	include 'baglan.php';

	include 'fonksiyonlar.php';

	$site_adresi = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];

	if(giris_yapti_mi() === true){

		$uye_session_id = $_SESSION['uye_id'];

		$uye_verileri = $db->query("SELECT * FROM uyeler WHERE uye_id = '{$uye_session_id}'")->fetch(PDO::FETCH_ASSOC);	

		$uye_id = $uye_verileri['uye_id'];

		$uye_adi = $uye_verileri['uye_adi'];

		$uye_mail = $uye_verileri['uye_mail'];

		$uye_sifre = $uye_verileri['uye_sifre'];

		$uye_firma = $uye_verileri['uye_firma'];

		$uye_tipi = $uye_verileri['uye_tipi'];

		$uye_sirket = $uye_verileri['uye_firma'];

		$uye_yetkiler = $uye_verileri['uye_yetkiler'];

		$uye_yetkileri_arrayi = explode(",", $uye_yetkiler);

		$uye_alis_yetkisi = $uye_yetkileri_arrayi[0];

		$uye_fabrika_yetkisi = $uye_yetkileri_arrayi[1];

		$uye_teklif_yetkisi = $uye_yetkileri_arrayi[2];

		$uye_siparis_yetkisi = $uye_yetkileri_arrayi[3];

		$uye_duzenleme_yetkisi = $uye_yetkileri_arrayi[4];

		$uye_islemleri_gorme_yetkisi = $uye_yetkileri_arrayi[5];

		$uye_gelen_giden_yetkisi = $uye_yetkileri_arrayi[6];

		$uye_satis_yetkisi = $uye_yetkileri_arrayi[7];
 
		$uye_toplam_gorme_yetkisi = $uye_yetkileri_arrayi[8];

		$uye_ziyaret_yetkisi = $uye_yetkileri_arrayi[9];

		$uye_sevkiyat_yetkisi = $uye_yetkileri_arrayi[10];
 
		//ŞİRKET BİLGİLERİ

		$sirketbilgileri = $db->query("SELECT * FROM sirketler WHERE sirketid = '{$uye_sirket}'")->fetch(PDO::FETCH_ASSOC);

		$sirketaciklama = $sirketbilgileri['sirketaciklama'];

		$sirketlogo = $sirketbilgileri['sirketlogo'];

		$sirketyedekalmasaniye = $sirketbilgileri['yedekalmasaniye'];

	}

	if (giris_yapti_mi() === true ) {

		$girdi = 1;

		if (uye_id_var_mi($uye_session_id) == '0') {
	
			header("Location:cikis.php");

		}

	}else{

		$girdi = 0;
	}

	$sayfa = explode('/',$_SERVER['SCRIPT_NAME']);

	$sayfa = end($sayfa);	

	
?>
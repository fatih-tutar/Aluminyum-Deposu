<?php 

	include 'functions/init.php';

	if (!isLoggedIn()) {
		header("Location:login.php");
		exit();
	}else if(!isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id'])){
		header("Location:index.php");
		exit();
	}else{

		$categoryId = guvenlik($_GET['id']);

		$ustkategoriyicek = $db->query("SELECT * FROM kategori WHERE kategori_id = '{$categoryId}' AND silik = '0' AND sirketid = '{$user->company_id}'")->fetch(PDO::FETCH_ASSOC);

		if(!$ustkategoriyicek) {
			header("Location:index.php");
			exit();
		}
		
		$ust_kategori_id = guvenlik($ustkategoriyicek['kategori_ust']);

		$ustbilgicek = $db->query("SELECT * FROM kategori WHERE kategori_id = '{$ust_kategori_id}' AND sirketid = '{$user->company_id}'")->fetch(PDO::FETCH_ASSOC);

		$ust_kategori_adi = guvenlik($ustbilgicek['kategori_adi']);

		$ust_kategori_kar_yuzdesi = guvenlik($ustbilgicek['karyuzdesi']);

		$ust_kategori_sutunlar = guvenlik($ustbilgicek['sutunlar']);

		$sutunlaripatlat = explode(",", $ust_kategori_sutunlar);

		$sutunadetizni = $sutunlaripatlat[0];

		$sutunbirimkgizni = $sutunlaripatlat[1];

		$sutuntoplamizni = $sutunlaripatlat[2];

		$sutunalisizni = $sutunlaripatlat[3];

		$sutunsatisizni = $sutunlaripatlat[4];

		$sutunfabrikaizni = $sutunlaripatlat[5];

		$sutunteklifbutonuizni = $sutunlaripatlat[6];

		$sutunsiparisbutonuizni = $sutunlaripatlat[7];

		$sutunduzenlebutonuizni = $sutunlaripatlat[8];

		$sutunsiparisadediizni = $sutunlaripatlat[9];

		$sutunuyariadediizni = $sutunlaripatlat[10];

		$sutunsipariskiloizni = $sutunlaripatlat[11];

		$sutunboyolcusuizni = $sutunlaripatlat[12];

		$sutunmusteriismiizni = $sutunlaripatlat[13];

		$sutuntarihizni = $sutunlaripatlat[14];

		$sutunterminizni = $sutunlaripatlat[15];

		$sutunmanuelsatisizni = $sutunlaripatlat[16];

		$sutunurunkoduizni = $sutunlaripatlat[17];

		$sutundepoadetizni = $sutunlaripatlat[18];

		$sutundepouyariadediizni = $sutunlaripatlat[19];

		$sutunrafizni = $sutunlaripatlat[20];

		$sutunsevkiyatbutonuizni = $sutunlaripatlat[21];

		$sutunpaletizni = $sutunlaripatlat[22];

		if($user->type != '3'){

			if (isset($_POST['urunsil'])) {
				
				$urun_id = guvenlik($_POST['urun_id']);

				$urun_adet = guvenlik($_POST['urun_adet']);

				$urun_palet = guvenlik($_POST['urun_palet']);

				$urun_depo_adet = guvenlik($_POST['urun_depo_adet']);

				$urun_sira = guvenlik($_POST['urun_sira']);

				if ($urun_adet != 0 || $urun_depo_adet != 0 || $urun_palet != 0) {

					header("Location:urunler.php?id=".$categoryId."&u=".$urun_id."&urunsilinemez");

					exit();

				}elseif ($urun_adet == 0 && $urun_depo_adet == 0 && $urun_palet == 0) {

					$siralar = $db->query("SELECT * FROM urun WHERE urun_sira > '{$urun_sira}' AND kategori_iki = '{$categoryId}' AND sirketid = '{$user->company_id}' ORDER BY urun_sira ASC", PDO::FETCH_ASSOC);

					if ( $siralar->rowCount() ){

						foreach( $siralar as $sc ){

							$sira_urun_id = $sc['urun_id'];

							$urun_sira = $sc['urun_sira'];

							$yenisira = $urun_sira - 1;

							$query = $db->prepare("UPDATE urun SET urun_sira = ? WHERE urun_id = ?"); 

							$guncelle = $query->execute(array($yenisira,$sira_urun_id));

						}

					}
					
					$sil = $db->prepare("UPDATE urun SET silik = ? WHERE urun_id = ?");

					$delete = $sil->execute(array('1',$urun_id));

					header("Location:urunler.php?id=".$categoryId."&urunsilindi#".$urun_id);

					exit();

				}

			}

			if (isset($_POST['teklifkaydet'])) {

				$turunid = guvenlik($_POST['turunid']);
				
				$tekliffirma = guvenlik($_POST['tekliffirma']);

				$firmaidcek = $db->query("SELECT * FROM clients WHERE name = '{$tekliffirma}' AND company_id = '{$user->company_id}'")->fetch(PDO::FETCH_ASSOC);

				$tekliffirma = $firmaidcek[ 'id']; 

				$teklifadet =  guvenlik($_POST['teklifadet']);

				$teklifsatisfiyat = guvenlik($_POST['teklifsatisfiyat']);

				$query = $db->prepare("INSERT INTO teklif SET turunid = ?, tverilenfirma = ?, tadet = ?, tsatisfiyati = ?, tsaniye = ?, formda = ?, sirketid = ?, silik = ?");

				$insert = $query->execute(array($turunid,$tekliffirma,$teklifadet,$teklifsatisfiyat,time(),'0',$user->company_id,'0'));

				header("Location:urunler.php?id=".$categoryId."&u=".$turunid."&teklifeklendi#".$turunid);

				exit();

			}

			if (isset($_POST['siparisformu'])) {

				$siparisboy = guvenlik($_POST['siparisboy']);
				
				$hazirlayankisi = guvenlik($_POST['hazirlayankisi']);

				$urun_fabrika = guvenlik($_POST['urun_fabrika']);

				$ilgilikisi = guvenlik($_POST['ilgilikisi']);

				$urun_stok = guvenlik($_POST['urun_stok']);

				$urun_id = guvenlik($_POST['urun_id']);

				$termin = guvenlik($_POST['termin']);

				$terminsaniye = strtotime($termin);

				$uruninfo = $db->query("SELECT * FROM urun WHERE urun_id = '{$urun_id}' AND sirketid = '{$user->company_id}'")->fetch(PDO::FETCH_ASSOC);

				$urun_adi = $uruninfo['urun_adi'];

				$query = $db->prepare("INSERT INTO siparis SET terminsaniye = ?, siparisboy = ?, hazirlayankisi = ?, urun_fabrika_id = ?, ilgilikisi = ?, urun_id = ?, urun_adi = ?, urun_siparis_aded = ?, taslak = ?, siparissaniye = ?, formda = ?, sirketid = ?, silik = ?");

				$insert = $query->execute(array($terminsaniye,$siparisboy,$hazirlayankisi,$urun_fabrika,$ilgilikisi,$urun_id,$urun_adi,$urun_stok,'1',time(),'0',$user->company_id,'0'));

				header("Location:urunler.php?id=".$categoryId."&u=".$urun_id."&sipariseklendi#".$urun_id);

				exit();

			}

			if (isset($_POST['sevkiyatkaydet'])) {
                $urunId = $_POST['urun_id'];
                $adet = guvenlik($_POST['adet']);
                $fiyat = guvenlik($_POST['fiyat']);
                $arac_id =  guvenlik($_POST['arac_id']);
                $sevkTipi = guvenlik($_POST['sevk_tipi']);
                $aciklama = guvenlik($_POST['aciklama']);
                $firma = guvenlik($_POST['firma']);
                if(empty($firma)){
                    $error = '<br/><div class="alert alert-danger" role="alert">Müşteri sipariş formu için lütfen bir firma seçiniz.</div>';
                }else if(empty($adet)){
                    $error = '<br/><div class="alert alert-danger" role="alert">Müşteri sipariş formu için lütfen bir adet belirtiniz.</div>';
                }else if(empty($fiyat)){
                    $error = '<br/><div class="alert alert-danger" role="alert">Müşteri sipariş formu için lütfen bir fiyat yazınız.</div>';
                }else if($sevkTipi === "null") {
                    $error = '<br/><div class="alert alert-danger" role="alert">Sevk tipi : '.$sevkTipi.' Müşteri sipariş formu için lütfen bir sevk tipi seçiniz.</div>';
                }else {
                    $firmaId = getFirmaID($firma);
                    $sevkiyatList = $db->query("SELECT * FROM sevkiyat WHERE firma_id = '{$firmaId}' AND durum = '0' AND manuel = '0' AND silik = '0' AND sirket_id = '{$user->company_id}' ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
                    if ($sevkiyatList) {
                        $urunler = guvenlik($sevkiyatList['urunler']);
                        $adetler = guvenlik($sevkiyatList['adetler']);
                        $fiyatlar = guvenlik($sevkiyatList['fiyatlar']);
                        $urunler = $urunler . "," . $urunId;
                        $adetler = $adetler . "," . $adet;
                        $fiyatlar = $fiyatlar . "-" . $fiyat;
                        $query = $db->prepare("UPDATE sevkiyat SET urunler = ?, adetler = ?, fiyatlar = ? WHERE firma_id = ? AND durum = ? AND silik = ? AND sirket_id = ?");
                        $update = $query->execute(array($urunler, $adetler, $fiyatlar, $firmaId, '0', '0', $user->company_id));
                    } else {
                        $query = $db->prepare("INSERT INTO sevkiyat SET urunler = ?, firma_id = ?, adetler = ?, kilolar = ?, fiyatlar = ?, olusturan = ?, hazirlayan = ?, sevk_tipi = ?, arac_id = ?, aciklama = ?, durum = ?, silik = ?, saniye = ?, sirket_id = ?");
                        $insert = $query->execute(array($urunId, $firmaId, $adet, '', $fiyat, $user->id, '', $sevkTipi, $arac_id, $aciklama, '0', '0', time(), $user->company_id));
                    }
                    header("Location:urunler.php?id=" . $categoryId . "&u=" . $urunId . "&sevkiyateklendi#" . $urunId);
                    exit();
                }
			}

			if (isset($_POST['guncellemeformu'])) {

				$urun_id = guvenlik($_POST['urun_id']);

				$urun_kodu = guvenlik($_POST['urun_kodu'] ?? null);

				$urun_adi = guvenlik($_POST['urun_adi']);

				$urun_adet = guvenlik($_POST['urun_adet'] ?? null);

				$urun_palet = guvenlik($_POST['urun_palet'] ?? null);

				$urun_depo_adet = guvenlik($_POST['urun_depo_adet'] ?? null);

				$urun_raf = guvenlik($_POST['urun_raf']);

				$eskiadeticek = $db->query("SELECT * FROM urun WHERE urun_id = '{$urun_id}'")->fetch(PDO::FETCH_ASSOC); 

				$eskiadet = $eskiadeticek['urun_adet'];

				$eskipalet = $eskiadeticek['urun_palet'];

				$eskidepoadet = $eskiadeticek['urun_depo_adet'];
				
				$eskiurunalis = $eskiadeticek['urun_alis'];

				if(!isset($_POST['urun_adet'])){
					$urun_adet = $eskiadet;
				}

				if(!isset($_POST['urun_palet'])){
					$urun_palet = $eskipalet;
				}

				if(!isset($_POST['urun_depo_adet'])){
					$urun_depo_adet = $eskidepoadet;
				}

				$urun_birimkg = guvenlik($_POST['urun_birimkg']);

				$urun_boy_olcusu = guvenlik($_POST['urun_boy_olcusu'] ?? null);

				$urun_alis = guvenlik($_POST['urun_alis'] ?? null);

				if(!isset($_POST['urun_alis'])){
					$urun_alis = $eskiurunalis;
				}

				$satis = guvenlik($_POST['satis'] ?? null);

				$urun_fabrika = guvenlik($_POST['urun_fabrika']);

				$urun_aciklama = guvenlik($_POST['urun_aciklama']);

				$urun_stok = guvenlik($_POST['urun_stok']);

				$musteri_ismi = guvenlik($_POST['musteri_ismi'] ?? null);

				$tarih = guvenlik($_POST['tarih'] ?? null);

				$termin = guvenlik($_POST['termin'] ?? null);

				$urun_uyari_stok_adedi = guvenlik($_POST['urun_uyari_stok_adedi']);

				$urun_depo_uyari_adet = guvenlik($_POST['urun_depo_uyari_adet']);

				$urun_eski_sira = guvenlik($_POST['urun_eski_sira']);

				$urun_yeni_sira = guvenlik($_POST['urun_yeni_sira']);

				// SIRALAMA AYARI BURADA GÜNCELLEME İLE ALAKALI SIRALAMADAN BAŞKA BİR ŞEY YOK

				if ($urun_eski_sira < $urun_yeni_sira) {
					
					$kayacakurunsayisi = $urun_yeni_sira - $urun_eski_sira;

					$kaycaklaricek = $db->query("SELECT * FROM urun WHERE kategori_iki = '{$categoryId}' AND sirketid = '{$user->company_id}' ORDER BY urun_sira ASC LIMIT $urun_eski_sira,$kayacakurunsayisi", PDO::FETCH_ASSOC);

					if ( $kaycaklaricek->rowCount() ){

						foreach( $kaycaklaricek as $kuc ){

							$kayan_urun_id = $kuc['urun_id'];

							$kayan_urun_sira = $kuc['urun_sira'];

							$kayan_urun_sira--;

							$sira_guncelle = $db->prepare("UPDATE urun SET urun_sira = ? WHERE urun_id = ?"); 

							$kaymaguncelle = $sira_guncelle->execute(array($kayan_urun_sira,$kayan_urun_id));

						}

					}

				}elseif ($urun_eski_sira > $urun_yeni_sira) {
					
					$kayacakurunsayisi = $urun_eski_sira - $urun_yeni_sira; 

					$bionce = $urun_yeni_sira - 1;

					$kaycaklaricek = $db->query("SELECT * FROM urun WHERE kategori_iki = '{$categoryId}' AND sirketid = '{$user->company_id}' ORDER BY urun_sira ASC LIMIT $bionce,$kayacakurunsayisi", PDO::FETCH_ASSOC);

					if ( $kaycaklaricek->rowCount() ){

						foreach( $kaycaklaricek as $kuc ){

							$kayan_urun_id = $kuc['urun_id'];

							$kayan_urun_sira = $kuc['urun_sira'];

							$kayan_urun_sira++;

							$sadece = $db->prepare("UPDATE urun SET urun_sira = ? WHERE urun_id = ?"); 

							$benzeme = $sadece->execute(array($kayan_urun_sira,$kayan_urun_id));

						}

					}

				}

				// SIRALAMA AYARI BİTİŞ

				$query = $db->query("SELECT * FROM clients WHERE company_id = '{$user->company_id}' ORDER BY name ASC", PDO::FETCH_ASSOC);

				if ( $query->rowCount() ){

					foreach( $query as $row ){

						$firmaid = $row[ 'id']; 

					}

				}

				// BÜTÜN BİLGİLER BURADA GÜNCELLENİYOR

				$query = $db->prepare("UPDATE urun SET urun_kodu = ?, urun_adi = ?, urun_adet = ?, urun_palet = ?, urun_depo_adet = ?, urun_raf = ?, urun_birimkg = ?, urun_boy_olcusu = ?, urun_alis = ?, satis = ?, urun_fabrika = ?, urun_aciklama = ?, urun_stok = ?, musteri_ismi = ?,urun_uyari_stok_adedi = ?, urun_depo_uyari_adet = ?, urun_sira = ?, tarih = ?, termin = ? WHERE urun_id = ?"); 

				$guncelle = $query->execute(array($urun_kodu,$urun_adi,$urun_adet,$urun_palet,$urun_depo_adet,$urun_raf,$urun_birimkg,$urun_boy_olcusu,$urun_alis,$satis,$urun_fabrika,$urun_aciklama,$urun_stok,$musteri_ismi,$urun_uyari_stok_adedi,$urun_depo_uyari_adet,$urun_yeni_sira,$tarih,$termin,$urun_id));

				if ($urun_adet != $eskiadet) {
					
					$islem = $db->prepare("INSERT INTO islemler SET yapanid = ?, urunid = ?, eskiadet = ?, yeniadet = ?, saniye = ?, islem_tipi = ?, sirketid = ?");

					$islemiekle = $islem->execute(array($user->id,$urun_id,$eskiadet,$urun_adet,time(),'0',$user->company_id));

				}
				
				if ($urun_depo_adet != $eskidepoadet || $urun_palet != $eskipalet) {
					
					$islem = $db->prepare("INSERT INTO islemler SET yapanid = ?, urunid = ?, eskiadet = ?, yeniadet = ?, saniye = ?, islem_tipi = ?, sirketid = ?");

					$islemiekle = $islem->execute(array($user->id,$urun_id,(floatval($eskidepoadet) + floatval($eskipalet)),(floatval($urun_depo_adet) + floatval($urun_palet)),time(),'1',$user->company_id));

				}

				// ALIŞ FİYATI HEPSİNE UYGULANSIN SEÇİLDİYSE İŞLEYECEK GÜNCELLEME KODLARI

				if(isset($_POST['hepsineuygula'])) { // checkbox seçilmişse "on" değeri gönderiliyor

					//echo "girdi"; echo $urun_alis." / ".$ust_kategori_id." / ".$urun_fabrika; exit();
					
					$alisguncelleme = $db->prepare("UPDATE urun SET urun_alis = ? WHERE kategori_bir = ? AND urun_fabrika = ?"); 

					$guncelleme = $alisguncelleme->execute(array($urun_alis,$ust_kategori_id,$urun_fabrika));

				}

				header("Location:urunler.php?id=".$categoryId."&u=".$urun_id."&guncellendi#".$urun_id);

				exit();

			}

			if (isset($_POST['siparisalindi'])) {
				
				$siparis_id = guvenlik($_POST['siparis_id']);

				$urun_id = guvenlik($_POST['urun_id']);

				$eklenenadet = guvenlik($_POST['eklenenadet']);

				$eskiadet = guvenlik($_POST['urun_adet']);

				$urun_adet = $eskiadet + $eklenenadet;

				if ($urun_adet != $eskiadet) {
					
					$islem = $db->prepare("INSERT INTO islemler SET yapanid = ?, urunid = ?, eskiadet = ?, yeniadet = ?, saniye = ?, islem_tipi = ?, sirketid = ?");

					$islemiekle = $islem->execute(array($user->id,$urun_id,$eskiadet,$urun_adet,time(),'0',$user->company_id));

				}

				$query = $db->prepare("UPDATE urun SET urun_adet = ? WHERE urun_id = ?"); 

				$guncelle = $query->execute(array($urun_adet,$urun_id));

				$query = $db->prepare("UPDATE siparis SET taslak = ? WHERE siparis_id = ?"); 

				$guncelle = $query->execute(array('0',$siparis_id));

				header("Location:urunler.php?id=".$categoryId."&u=".$urun_id."&siparisalindi#".$urun_id);

				exit();

			}

			if (isset($_POST['deposiparisalindi'])) {
				
				$siparis_id = guvenlik($_POST['siparis_id']);

				$urun_id = guvenlik($_POST['urun_id']);

				$eklenenadet = guvenlik($_POST['eklenenadet']);

				$eskiadet = guvenlik($_POST['urun_depo_adet']);

				$urun_depo_adet = $eskiadet + $eklenenadet;

				if ($urun_depo_adet != $eskiadet) {
					
					$islem = $db->prepare("INSERT INTO islemler SET yapanid = ?, urunid = ?, eskiadet = ?, yeniadet = ?, saniye = ?, islem_tipi = ?, sirketid = ?");

					$islemiekle = $islem->execute(array($user->id,$urun_id,$eskiadet,$urun_depo_adet,time(),'1',$user->company_id));

				}

				$query = $db->prepare("UPDATE urun SET urun_depo_adet = ? WHERE urun_id = ?"); 

				$guncelle = $query->execute(array($urun_depo_adet,$urun_id));

				$query = $db->prepare("UPDATE siparis SET taslak = ? WHERE siparis_id = ?"); 

				$guncelle = $query->execute(array('0',$siparis_id));

				header("Location:urunler.php?id=".$categoryId."&u=".$urun_id."&siparisalindi#".$urun_id);

				exit();

			}

            if (isset($_POST['paletsiparisalindi'])) {

                $siparis_id = guvenlik($_POST['siparis_id']);

                $urun_id = guvenlik($_POST['urun_id']);

                $eklenenadet = guvenlik($_POST['eklenenadet']);

                $eskiadet = guvenlik($_POST['urun_palet']);

                $urun_palet = $eskiadet + $eklenenadet;

                if ($urun_palet != $eskiadet) {

                    $islem = $db->prepare("INSERT INTO islemler SET yapanid = ?, urunid = ?, eskiadet = ?, yeniadet = ?, saniye = ?, islem_tipi = ?, sirketid = ?");

                    $islemiekle = $islem->execute(array($user->id,$urun_id,$eskiadet,$urun_palet,time(),'2',$user->company_id));

                }

                $query = $db->prepare("UPDATE urun SET urun_palet = ? WHERE urun_id = ?");

                $guncelle = $query->execute(array($urun_palet,$urun_id));

                $query = $db->prepare("UPDATE siparis SET taslak = ? WHERE siparis_id = ?");

                $guncelle = $query->execute(array('0',$siparis_id));

                header("Location:urunler.php?id=".$categoryId."&u=".$urun_id."&siparisalindi#".$urun_id);

                exit();

            }

			if (isset($_POST['add_inventory'])) {

				$code = guvenlik($_POST['code']);

                $dimension_1 = guvenlik($_POST['dimension_1']);

                $dimension_2 = guvenlik($_POST['dimension_2']);

				$dimension_3 = guvenlik($_POST['dimension_3']);

				$density = guvenlik($_POST['density']);

                $factory_name = guvenlik($_POST['factory_name']);

				$query = $db->prepare("INSERT INTO inventory SET category_id = ?, code = ?, dimension_1 = ?, dimension_2 = ?, dimension_3 = ?, density = ?, factory_name = ?, is_deleted = ?, company_id = ?");

				$insert = $query->execute(array($categoryId,$code,$dimension_1,$dimension_2,$dimension_3,$density,$factory_name,'0',$user->company_id));

				header("Location:urunler.php?id=".$categoryId."&inventory_added");

				exit();

			}

			if (isset($_POST['update_inventory'])) {
				
				$id = guvenlik($_POST['id']);

				$code = guvenlik($_POST['code']);

				$dimension_1 = guvenlik($_POST['dimension_1']);

				$dimension_2 = guvenlik($_POST['dimension_2']);

				$dimension_3 = guvenlik($_POST['dimension_3']);

				$density = guvenlik($_POST['density']);

				$factory_name = guvenlik($_POST['factory_name']);

				$query = $db->prepare("UPDATE inventories SET code = ?, dimension_1 = ?, dimension_2 = ?, dimension_3 = ?, density = ?, factory_name = ? WHERE id = ?");

				$update = $query->execute(array($code,$dimension_1,$dimension_2,$dimension_3,$density,$factory_name,$id));

                if($update) {
                    header("Location:urunler.php?id=".$categoryId."&inventory_updated");
                    exit();
                }else{
                    $error = '<br/><div class="alert alert-danger" role="alert">Güncelleme işlemi gerçekleştirilemedi.</div>';
                }
			}

			if (isset($_GET['guncellendi'])) {
				
				$error = '<br/><div class="alert alert-success" role="alert">İlgili ürüne ait bilgiler başarıyla güncellendi.</a></div>';

			}

			if (isset($_GET['sipariseklendi'])) {
				
				$error = '<br/><div class="alert alert-success" role="alert"><a href="factory.php" target="m_blank">Siparişin başarıyla eklendi. Buraya tıklayarak yönetim sayfasından sipariş formlarına ulaşabilirsin.</a></div>';

			}

			if (isset($_GET['teklifeklendi'])) {
				
				$error = '<br/><div class="alert alert-success" role="alert"><a href="client.php" target="m_blank">Teklifin başarıyla eklendi. Buraya tıklayarak yönetim sayfasından teklif formlarına ulaşabilirsin.</a></div>';

			}

			if (isset($_GET['siparisalindi'])) {
				
				$error = '<br/><div class="alert alert-success" role="alert"><a href="factory.php" target="m_blank">Sipariş alındı ve sipariş kaydı geçmiş siparişlere eklendi.</a></div>';

			}

			if (isset($_GET['urunsilinemez'])) {
				
				$error = '<br/><div class="alert alert-danger" role="alert">İlgili ürüne ait stokda malzeme bulunduğudan kaydını silemezsiniz.</a></div>';

			}

			if (isset($_GET['urunsilindi'])) {
				
				$error = '<br/><div class="alert alert-success" role="alert">Ürünün stokta kaydı olmadığından emin olunmuş ve ürün başarıyla silinmiştir.</div>';

			}

			if (isset($_GET['inventory_added'])) {
				
				$error = '<br/><div class="alert alert-success" role="alert">Hazır kalıp listesine ekleme yapıldı.</div>';

			}

			if (isset($_GET['inventory_updated'])) {
				
				$error = '<br/><div class="alert alert-success" role="alert">İlgili ürünün hazır kalıp bilgisi güncellendi.</div>';

			}

		}

        $araclar = $db->query("SELECT * FROM vehicles WHERE is_deleted = '0'")->fetchAll(PDO::FETCH_OBJ);

        $inventories = $db->query("SELECT * FROM inventories WHERE category_id = '$categoryId' AND company_id = '{$user->company_id}' AND is_deleted = '0'")->fetchAll(PDO::FETCH_OBJ);

    }

?>

<!DOCTYPE html>

<html>

  <head>

    <title>Stok Programım</title>

    <?php include 'template/head.php'; ?>

  </head>

  <body>

    <?php include 'template/banner.php' ?>

    <div class="container-fluid" style="padding: 0px;">

        <?php if(isset($error)){ ?>
            <div class="row">
                <div class="col-md-12">
                    <?= $error; ?>
                </div>
            </div>
        <?php } ?>

    	<div style="background-color: white; padding: 15px 15px 50px 15px; margin: 0px;">

    		<div class="d-block d-sm-none">

    			<div class="row">
    				
    				<div class="col-6">
    					
    					<a href="kategori.php?id=<?= $ust_kategori_id; ?>" style="color: white;">
    			
			    			<button class="btn btn-info btn-sm btn-block">
			    		
					    		<i class="fas fa-backward"></i>&nbsp;&nbsp;&nbsp;<?= $ust_kategori_adi; ?>

					    	</button>

				    	</a>

    				</div>

    				<div class="col-6">
    					
    					<a href="#" onclick="return false" onmousedown="javascript:ackapa('inventory-div');">
						
							<button class="btn btn-primary btn-sm btn-block">
	    		
					    		HAZIR KALIPLAR

					    	</button>

				    	</a>

    				</div>

    			</div>    			

    		</div>
    	
	    	<div class="d-none d-sm-block">

				<div class="row">

					<?php if($sutunurunkoduizni == '1'){?><div class="col-md-1 col-1" style="text-align: center;"><b>Ürün Kodu</b></div><?php } ?>
					
					<div class="col-md-2 col-2"><b>Ürün Adı</b></div>
					<div class="col-md-4 col-4">
						<div class="row">
							<?php if($sutunadetizni == '1'){?><div class="col-md-2 col-2" style="text-align: center;"><b>Adet</b></div><?php } ?>
							<?php if($sutunpaletizni == '1'){?><div class="col-2" style="text-align: center;"><b>Palet</b></div><?php } ?>
							<?php if($sutundepoadetizni == '1'){?><div class="col-md-2 col-2" style="text-align: center;"><b>Alkop</b></div><?php } ?>
							<?php if($sutunrafizni == '1'){?><div class="col-md-2 col-2" style="text-align: center;"><b>Raf</b></div><?php } ?>
							<?php if($sutunbirimkgizni == '1'){?><div class="col-md-2 col-2 px-0" style="text-align: center;"><b>Birim Kg</b></div><?php } ?>
							<?php if($sutunsipariskiloizni == '1'){?><div class="col-md-2 col-2" style="text-align: center;"><b>Sipariş Kilo</b></div><?php } ?>
							<?php if($sutunboyolcusuizni == '1'){?><div class="col-md-2 col-2" style="text-align: center;"><b>Boy Ölçüsü</b></div><?php } ?>
							<?php if($sutuntoplamizni == '1'){?><div class="col-md-2 col-2" style="text-align: center;"><b>Toplam</b></div><?php } ?>
						</div>
					</div>

					<?php if($sutunalisizni == '1' && $user->permissions->buying_price == '1'){?><div class="col-md-1 col-1" style="text-align:center;"><b>Alış</b></div><?php  } ?>

					<?php if($sutunsatisizni == '1' && $user->permissions->selling_price == '1' || $user->type == '2'){?><div class="col-md-1 col-1" style="text-align:center;"><b>Satış</b></div><?php } ?>

					<?php if($sutunfabrikaizni == '1' && $user->permissions->factory == '1'){?><div class="col-md-1 col-1" style="text-align:center;"><b>Fabrika</b></div><?php } ?>	

					<?php if($sutunmusteriismiizni == '1'){?><div class="col-md-1 col-1"><b>Müşteri</b></div><?php } ?>	

					<div class="col-md-3 col-3" style="text-align: right;">

						<div class="row">
							
							<div class="col-6">
								
								<a href="kategori.php?id=<?= $ust_kategori_id; ?>" style="color: white;">
						
									<button class="btn btn-info btn-sm btn-block">
			    		
							    		<i class="fas fa-backward"></i>&nbsp;&nbsp;&nbsp;<?= $ust_kategori_adi; ?>

							    	</button>

						    	</a>

							</div>

							<div class="col-6">
								
								<a href="#" onclick="return false" onmousedown="javascript:ackapa('inventory-div');">
						
									<button class="btn btn-primary btn-sm btn-block">
			    		
							    		HAZIR KALIPLAR

							    	</button>

						    	</a>
								
							</div>

						</div>

					</div>						

				</div>

			</div>

		<?php if (isset($_GET['inventory_updated']) || isset($_GET['inventory_added'])) { ?>

			<div id="inventory-div">
			
		<?php }else{ ?>

			<div style="display: none;" id="inventory-div">

		<?php } ?>

				<hr style="border: 1px solid black;" />

				<div class="row">
								
					<div class="col-md-1 col-2"><b>Kod</b></div>

					<div class="col-md-6 col-4" style="padding: 0px;">
										
						<div class="row" style="margin: 0px; padding: 0px;">
							
							<div class="col-4" style="padding: 0px;"><b>A</b></div>

							<div class="col-4" style="padding: 0px;"><b>B</b></div>

							<div class="col-4" style="padding: 0px;"><b>C</b></div>

						</div>

					</div>		

					<div class="col-md-2 col-2" style="padding: 0px; text-align: center;"><b>Gramaj</b></div>

					<div class="col-md-2 col-2" style="padding: 0px; text-align: center;"><b>Fabrika</b></div>

				</div>

				<form action="" method="POST">

					<div class="row" style="margin-bottom: 3px;">
						
						<div class="col-md-1 col-2" style="padding: 0px;"><input type="text" name="code" class="form-control form-control-sm" placeholder="KOD NO."></div>

						<div class="col-md-6 col-4" style="padding: 0px;">
										
							<div class="row" style="margin: 0px; padding: 0px;">
								
								<div class="col-4" style="padding: 0px;"><input type="text" name="dimension_1" class="form-control form-control-sm" placeholder="(mm)"></div>

								<div class="col-4" style="padding: 0px;"><input type="text" name="dimension_2" class="form-control form-control-sm" placeholder="(mm)"></div>

								<div class="col-4" style="padding: 0px;"><input type="text" name="dimension_3" class="form-control form-control-sm" placeholder="(mm)"></div>

							</div>

						</div>						

						<div class="col-md-2 col-2" style="padding: 0px;"><input type="text" name="density" class="form-control form-control-sm" placeholder="(KG/M)"></div>

						<div class="col-md-2 col-3" style="padding: 0px;"><input type="text" name="factory_name" class="form-control form-control-sm" placeholder="FABRİKA"></div>

						<div class="col-md-1 col-1" style="padding: 0px;"><button type="submit" name="add_inventory" class="btn btn-block btn-warning btn-sm" ><i class="fas fa-plus"></i></button></div>

					</div>

				</form>

				<?php foreach($inventories as $inventory ){ ?>

                    <form action="" method="POST">

                        <input type="hidden" name="id" value="<?= $inventory->id; ?>">

                        <div class="row" style="margin-bottom: 3px;">

                            <div class="col-md-1 col-2" style="padding: 0px;"><input type="text" name="code" class="form-control form-control-sm" value="<?= $inventory->code; ?>"></div>

                            <div class="col-md-6 col-4" style="padding: 0px;">

                                <div class="row" style="margin: 0px; padding: 0px;">

                                    <div class="col-4" style="padding: 0px;"><input type="text" name="dimension_1" class="form-control form-control-sm" value="<?= $inventory->dimension_1; ?>"></div>

                                    <div class="col-4" style="padding: 0px;"><input type="text" name="dimension_2" class="form-control form-control-sm" value="<?= $inventory->dimension_2; ?>"></div>

                                    <div class="col-4" style="padding: 0px;"><input type="text" name="dimension_3" class="form-control form-control-sm" value="<?= $inventory->dimension_3; ?>"></div>

                                </div>

                            </div>

                            <div class="col-md-2 col-2" style="padding: 0px;"><input type="text" name="density" class="form-control form-control-sm" value="<?= $inventory->density; ?>"></div>

                            <div class="col-md-2 col-3" style="padding: 0px;"><input type="text" name="factory_name" class="form-control form-control-sm" value="<?= $inventory->factory_name; ?>"></div>

                            <div class="col-md-1 col-1" style="padding: 0px;"><button type="submit" name="update_inventory" class="btn btn-block btn-primary btn-sm"><i class="fas fa-check"></i></button></div>

                        </div>

                    </form>

				<?php } ?>

			</div>

			<hr style="border: 1px solid black;" />

			<?php

				$toplam_urun_kg = 0;

				$urunlistesira = 0;

				$urun = $db->query("SELECT * FROM urun WHERE kategori_iki = '{$categoryId}' AND sirketid = '{$user->company_id}' AND silik = '0' ORDER BY urun_sira ASC", PDO::FETCH_ASSOC);

				if ( $urun->rowCount() ){

					foreach( $urun as $orw ){

						$urunlistesira++;

						$terminigecikmismi = 0;

						$urun_id = $orw['urun_id'];	

						$terminlericekme = $db->query("SELECT * FROM siparis WHERE urun_id = '{$urun_id}' AND taslak = '1' AND silik = '0'", PDO::FETCH_ASSOC);

						if ( $terminlericekme->rowCount() ){

							foreach( $terminlericekme as $row ){

								$terminsaniye = guvenlik($row['terminsaniye']);

								if($bugununsaniyesi > $terminsaniye && $terminsaniye != 0){

									$terminigecikmismi = 1;

								}

							}

						}

						$urun_kategori_bir = $orw['kategori_bir'];		

						$urun_kodu = $orw['urun_kodu'];																

						$urun_adi = $orw['urun_adi'];

						$urun_adet = $orw['urun_adet'];

						$urun_palet = $orw['urun_palet'];

						$urun_depo_adet = $orw['urun_depo_adet'];

						$urun_raf = $orw['urun_raf'];
 
						$urun_birimkg = $orw['urun_birimkg'];

						$urun_boy_olcusu = $orw['urun_boy_olcusu'];

						$urun_alis = $orw['urun_alis'];

						$urun_satis = $urun_alis * ($ust_kategori_kar_yuzdesi + 100) / 100;

						$urun_satis = round($urun_satis, 2);

						$satis = $orw['satis'];

						if($sutunmanuelsatisizni == '1'){ $urun_satis = $satis; }

						$urun_fabrika = $orw['urun_fabrika'];

						$u_fabrika = $db->query("SELECT * FROM factories WHERE id = '{$urun_fabrika}' AND company_id = '{$user->company_id}'")->fetch(PDO::FETCH_ASSOC);

						$urun_fabrika_adi = $u_fabrika['name'] ?? null;

						$urun_aciklama = $orw['urun_aciklama'];

						$carpim = ($urun_adet + $urun_palet + $urun_depo_adet)  * $urun_birimkg;

						$toplam_urun_kg = $toplam_urun_kg + $carpim;

						$urun_stok = $orw['urun_stok'];

						$urun_uyari_stok_adedi = $orw['urun_uyari_stok_adedi'];

						$urun_depo_uyari_adet = $orw['urun_depo_uyari_adet'];

						$urun_sira = $orw['urun_sira'];

						$musteri_ismi = $orw['musteri_ismi'];

						$tarih = $orw['tarih'];

						$termin = $orw['termin'];

						$urunterminsaniye = strtotime($termin);

						if (empty($tarih)) {$tarih = $tarihf2;}

						if (empty($termin)) {$termin = $tarihf2;}

			?>

						<div class="row">

							<?php if($sutunurunkoduizni == '1'){?>								

							<div class="col-4 d-block d-sm-none">Ürün Kodu : </div>

							<div class="col-md-1 col-8" style="text-align: center;"><?= $urun_kodu; ?></div>

							<?php } ?>

							<?php if($sutunterminizni == 1 && $urunterminsaniye < time()){ ?>

								<div class="col-4 d-block d-sm-none"><b style="color: red;">Ürün Adı :</b></div>

								<div class="col-md-2 col-8">

									<a id="<?= $urun_id; ?>" href="islemler.php?id=<?= $urun_id; ?>" target="_blank"><b style="color: red;"><?= "<small>".$urunlistesira.".</small> ".$urun_adi; ?></b></a>

								</div>

							<?php }elseif($urun_adet < $urun_uyari_stok_adedi || ($urun_depo_adet + $urun_palet) < $urun_depo_uyari_adet){ ?>

								<div class="col-4 d-block d-sm-none"><b style="color: red;">Ürün Adı :</b></div>

								<div class="col-md-2 col-8">

									<a id="<?= $urun_id; ?>" href="islemler.php?id=<?= $urun_id; ?>" target="_blank"><b style="color: red;"><?= "<small>".$urunlistesira.".</small> ".$urun_adi; ?></b></a>

								</div>

							<?php }else{ ?>

								<div class="col-4 d-block d-sm-none">Ürün Adı : </div>

								<div class="col-md-2 col-8">

									<a id="<?= $urun_id; ?>" href="islemler.php?id=<?= $urun_id; ?>" target="_blank"><b><?= "<small>".$urunlistesira.".</small> ".$urun_adi; ?></b></a>

								</div>

							<?php } ?>		

							<div class="col-md-4 col-12">

								<div class="row">

									<?php if($sutunadetizni == '1'){?>								

									<div class="col-4 d-block d-sm-none">Adet : </div>

									<div class="col-md-2 col-8" style="text-align: left;"><button class="btn btn-warning btn-sm btn-block"><b><?= $urun_adet; ?></b></button></div>

									<?php } ?>

									<?php if($sutunpaletizni == '1'){?>								

									<div class="col-4 d-block d-sm-none">Palet : </div>

									<div class="col-md-2 col-8" style="text-align: left;"><button class="btn btn-dark btn-sm btn-block"><b><?= $urun_palet; ?></b></button></div>

									<?php } ?>

									<?php if($sutundepoadetizni == '1'){?>								

									<div class="col-4 d-block d-sm-none">Alkop Adet : </div>

									<div class="col-md-2 col-8" style="text-align: left;"><button class="btn btn-info btn-sm btn-block"><b><?= $urun_depo_adet; ?></b></button></div>

									<?php } ?>

									<?php if($sutunrafizni == '1'){?>								

									<div class="col-4 d-block d-sm-none">Raf : </div>

									<div class="col-md-2 col-8" style="text-align: left;"><b><?= $urun_raf; ?></b></div>

									<?php } ?>

									<?php if($sutunbirimkgizni == '1'){?>

									<div class="col-4 d-block d-sm-none">Birim Kg : </div>

									<div class="col-md-2 col-8" style="text-align:center;"><small><?= $urun_birimkg." kg"; ?></small></div>

									<?php } ?>

									<?php if($sutunsipariskiloizni == '1'){ ?>

									<div class="col-4 d-block d-sm-none">Sipariş Kilo : </div>

									<div class="col-md-2 col-8" style="text-align: center;"><button class="btn btn-danger btn-sm btn-block"><b><?= $urun_birimkg." kg"; ?></b></button></div>

									<?php } ?>

									<?php if($sutunboyolcusuizni == '1'){ ?>

									<div class="col-4 d-block d-sm-none">Boy Ölçüsü : </div>

									<div class="col-md-2 col-8"><?= $urun_boy_olcusu; ?></div>

									<?php } ?>

									<?php if($sutuntoplamizni == '1'){?>

									<div class="col-4 d-block d-sm-none">Toplam : </div>

									<div class="col-md-2 col-8 px-0" style="text-align:center;"><small><?= $carpim." kg"; ?></small></div>

									<?php } ?>

								</div>
							
							</div>

							<?php if($sutunalisizni == '1' && $user->permissions->buying_price == '1'){ ?>

								<div class="col-4 d-block d-sm-none">Alış : </div>

								<div class="col-md-1 col-8" style="text-align:center;"><b><?= $urun_alis." TL"; ?></b></div>

							<?php } ?>

							<?php if($sutunsatisizni == '1' && $user->permissions->selling_price == '1' || $user->type == '2'){?>

							<div class="col-4 d-block d-sm-none">Satış : </div>

							<div class="col-md-1 col-8" style="text-align:center;"><b><?= $urun_satis." TL"; ?></b></div>

							<?php } ?>

							<?php if($sutunfabrikaizni == '1' && $user->permissions->factory == '1'){?>

								<div class="col-4 d-block d-sm-none">Fabrika : </div>

								<div class="col-md-1 col-8" style="text-align:center;"><b><?= $urun_fabrika_adi; ?></b></div>

							<?php } ?>

							<?php if($sutunmusteriismiizni == '1'){?>

								<div class="col-4 d-block d-sm-none">Müşteri : </div>

								<div class="col-md-1 col-8"><b><?= $musteri_ismi; ?></b></div>

							<?php } ?>

							<div class="col-md-3 col-12">

								<div class="row">

									<?php if($sutunteklifbutonuizni == '1' && $user->permissions->quote == '1'){ ?>

										<div class="col-md-3 col-3 p-1">

											<a href="#" onclick="return false" onmousedown="javascript:ackapa4('teklifdivi<?= $urun_id; ?>','siparisdiv<?= $urun_id; ?>','editdiv<?= $urun_id; ?>','sevkiyatdiv<?= $urun_id; ?>');"><button class="btn btn-warning btn-sm btn-block">Teklif</button></a>

										</div>

									<?php } ?>

									<?php if($sutunsiparisbutonuizni == '1' && $user->permissions->order == '1'){ ?>

										<div class="col-md-3 col-3 p-1">
											
											<a href="#" id="btn1" onclick="return false" onmousedown="javascript:ackapa4('siparisdiv<?= $urun_id; ?>','teklifdivi<?= $urun_id; ?>','editdiv<?= $urun_id; ?>','sevkiyatdiv<?= $urun_id; ?>');">

												<?php if($terminigecikmismi == 0){ ?>

													<button class="btn btn-info btn-sm btn-block">

														<b>Sipariş</b></button>

												<?php }else{ ?>

													<button class="btn btn-danger btn-sm btn-block">

														<b>Sipariş</b></button>

												<?php } ?>

											</a>

										</div>

									<?php } ?>

									<?php if($sutunsevkiyatbutonuizni == '1' && $user->permissions->shipment == '1'){ ?>

										<div class="col-md-3 col-3 p-1">

											<a href="#" id="btn1" onclick="return false" onmousedown="javascript:ackapa4('sevkiyatdiv<?= $urun_id; ?>','editdiv<?= $urun_id; ?>','siparisdiv<?= $urun_id; ?>','teklifdivi<?= $urun_id; ?>');"><button class="btn btn-dark btn-sm btn-block"><b>Sevkiyat</b></button></a>

										</div>

									<?php } ?> 

									<?php if($sutunduzenlebutonuizni == '1' && $user->permissions->editing == '1'){ ?>

										<div class="col-md-3 col-3 p-1">

											<a href="#" id="btn1" onclick="return false" onmousedown="javascript:ackapa4('editdiv<?= $urun_id; ?>','siparisdiv<?= $urun_id; ?>','teklifdivi<?= $urun_id; ?>','sevkiyatdiv<?= $urun_id; ?>');"><button class="btn btn-success btn-sm btn-block"><b>Düzenle</b></button></a>

										</div>

									<?php } ?>  
								
								</div>

							</div>

						</div>

						<?php if($user->permissions->quote == '1'){?>

							<?php if (isset($_GET['teklifeklendi']) && $_GET['u'] == $urun_id) { ?>

								<div id="teklifdivi<?= $urun_id; ?>" class="div2">
								
							<?php }else{ ?>

								<div id="teklifdivi<?= $urun_id; ?>" style="display: none;" class="div2">

							<?php } ?>

									<div class="alert alert-warning">

										<h5><b style="line-height: 40px;">Teklif Formu</b></h5>
									
										<form action="" method="POST">
											
											<div class="row">

												<div class="col-md-3 col-12 search-box">

													<b>Teklif Verilen Firma</b>
													
													<input autofocus="autofocus" name="tekliffirma" id="firmainputu" type="text" class="form-control" autocomplete="off" placeholder="Firma Adı"/>
				
													<ul class="list-group liveresult" id="firmasonuc" style="position: absolute; z-index: 1;"></ul>

												</div>
												
												<div class="col-md-2 col-12">

													<b>Adet</b>
													
													<input type="text" class="form-control" name="teklifadet" placeholder="Teklif Adeti Giriniz.">

												</div>

												<div class="col-md-2 col-12">

													<b>Kg Satış Fiyatı</b> (TL)
													
													<input type="text" class="form-control" name="teklifsatisfiyat" value="<?= $urun_satis; ?>">

												</div>

												<div class="col-md-2 col-12">

													<br/>

													<input type="hidden" name="turunid" value="<?= $urun_id; ?>">
													
													<button class="btn btn-warning" name="teklifkaydet">Teklif Formuna Ekle</button>

												</div>

											</div>

										</form>

									</div>

									<hr/>

									<div class="alert alert-success">
										
										<h5><b style="line-height: 40px;">Teklif Listesi</b></h5>

										<div class="d-none d-sm-block">

											<div class="row">
												
												<div class="col-3"><b>Firma Adı</b></div>

												<div class="col-1"><b>Adet</b></div>

												<div class="col-2"><b>Satış Fiyatı</b></div>

												<div class="col-2"><b>Toplam Fiyat</b></div>

												<div class="col-1"><b>Tarih</b></div>

											</div>

										</div>

										<?php

											$tekliflersiralamasi = 0;

											$tklfcek = $db->query("SELECT * FROM teklif WHERE turunid = '{$urun_id}' AND sirketid = '{$user->company_id}' AND silik = '0' ORDER BY teklifid DESC LIMIT 10", PDO::FETCH_ASSOC);

											if ( $tklfcek->rowCount() ){

												foreach( $tklfcek as $tklfrow ){

													$tekliflersiralamasi++;

													$tverilenfirmaid = $tklfrow['tverilenfirma'];

													$firmabilgi = $db->query("SELECT * FROM clients WHERE id = '{$tverilenfirmaid}'")->fetch(PDO::FETCH_ASSOC);

													$tverilenfirmaadi = $firmabilgi['name']; 

													$tadet = $tklfrow['tadet'];

													$tsatisfiyati = $tklfrow['tsatisfiyati'];

													$tsaniye = $tklfrow['tsaniye'];

													$ttarih = date("d-m-Y",$tsaniye);

													$toplam_fiyat = $tadet * $urun_birimkg * $tsatisfiyati;

										?>

													<div class="row">
														
														<div class="col-4 d-block d-sm-none">Firma Adı : </div>

														<div class="col-md-3 col-8"><?= $tekliflersiralamasi.". ".$tverilenfirmaadi; ?></div>

														<div class="col-4 d-block d-sm-none">Adet : </div>
														
														<div class="col-md-1 col-8"><?= $tadet; ?></div>

														<div class="col-4 d-block d-sm-none">Satış Fiyatı : </div>

														<div class="col-md-2 col-8"><?= $tsatisfiyati." TL"; ?></div>

														<div class="col-4 d-block d-sm-none">Toplam Fiyat : </div>

														<div class="col-md-2 col-8"><?= $toplam_fiyat." TL"; ?></div>

														<div class="col-4 d-block d-sm-none">Tarih : </div>

														<div class="col-md-2 col-8"><?= $ttarih; ?></div>

													</div>

										<?php

												}

											}

										?>

									</div>

								</div>

						<?php } ?>

						<?php if($user->permissions->order == '1'){ ?>

							<?php if ((isset($_GET['siparisalindi']) || isset($_GET['sipariseklendi'])) && $_GET['u'] == $urun_id) { ?>

								<div id="siparisdiv<?= $urun_id; ?>" class="div2">
								
							<?php }else{ ?>

								<div style="display: none;" id="siparisdiv<?= $urun_id; ?>" class="div2">

							<?php } ?>

									<form action="" method="POST">

										<div class="alert alert-info">

											<h5><b style="line-height: 40px;">Sipariş Formu</b></h5>

											<div class="row">

												<input type="hidden" name="urun_id" value="<?= $urun_id; ?>">

												<div class="col-md-2 col-12">

													<b>Hazırlayan Kişi</b><br/>

													<select name="hazirlayankisi" class="form-control">

														<option selected>Hazırlayan Kişiyi Seçiniz</option>
														
														<?php

															$calisanlaricek = $db->query("SELECT * FROM users WHERE company_id = '{$user->company_id}' ORDER BY name ASC", PDO::FETCH_ASSOC);

															if ( $calisanlaricek->rowCount() ){

																foreach( $calisanlaricek as $cc ){

																	$hazirlayanadi = $cc['name'];

														?>

																	<option value="<?= $hazirlayanadi; ?>"><?= $hazirlayanadi; ?></option>

														<?php

																}

															}

														?>

													</select>

												</div>
											
												<div class="col-md-2 col-12">

													<b>Talep Edilen Fabrika</b><br/>
													
													<select class="form-control" id="exampleFormControlSelect1" name="urun_fabrika">

														<?php

														if ($urun_fabrika == 0) {
															
															echo "<option selected value='0'>Fabrika Seçiniz</option>";

														}else{

															echo "<option selected value='".$urun_fabrika."'>".$urun_fabrika_adi."</option>";

														}

														$fabrika = $db->query("SELECT * FROM factories WHERE company_id = '{$user->company_id}'", PDO::FETCH_ASSOC);

														if ( $fabrika->rowCount() ){

															foreach( $fabrika as $fbrk ){

																$fabrika_id = $fbrk['id'];

																$fabrika_adi = $fbrk['name'];

																echo "<option value='".$fabrika_id."'>".$fabrika_adi."</option>";

															}

														}

														?>
												
													</select>

												</div>

												<div class="col-md-2 col-12"><b>İlgili Kişi</b><br/><input type="text" class="form-control" name="ilgilikisi" placeholder="İlgili Kişinin İsmini Yazınız"></div>

												<div class="col-md-1 col-12"><b>Miktar</b><br/><input type="text" class="form-control" name="urun_stok" value="<?= $urun_stok; ?>"></div>

												<div class="col-md-1 col-12"><b>Boy</b><br/><input type="text" name="siparisboy" value="6 metre" class="form-control"></div>

												<div class="col-md-4 col-12">
													
													<div class="row">
														
														<div class="col-md-5 col-12"><b>Termin</b><br/><input type="text" name="termin" value="<?= $tarihf2; ?>" id="tarih<?= $urunlistesira; ?>" class="form-control"></div>

														<div class="col-md-7 col-12" style="padding-top: 25px;"><button type="submit" class="btn btn-info btn-sm" name="siparisformu">Sipariş Listesine Ekle</button></div>

													</div>

												</div>									

											</div>

										</div>

									</form>	

									<hr/>

									<div class="alert alert-danger">

										<h5><b style="line-height: 40px;">Sipariş Listesi</b></h5>

										<div class="d-none d-sm-block">

											<div class="row">
															
												<div class="col-2"><b>Hazırlayan Kişi</b></div>

												<div class="col-2"><b>Talep Edilen Fabrika</b></div>

												<div class="col-2"><b>İlgili Kişi</b></div>

												<div class="col-1"><b>Miktar</b></div>

												<div class="col-1"><b>Tarih</b></div>

												<div class="col-1"><b>Termin</b></div>

											</div>

										</div>
									
										<?php

											$sipariscek = $db->query("SELECT * FROM siparis WHERE urun_id = '{$urun_id}' AND taslak = '1' AND sirketid = '{$user->company_id}' AND silik = '0' LIMIT 10", PDO::FETCH_ASSOC);

											if ( $sipariscek->rowCount() ){

												foreach( $sipariscek as $row ){

													$siparis_id = $row['siparis_id'];

													$hazirlayankisi = $row['hazirlayankisi'];

													$urun_siparis_aded = $row['urun_siparis_aded'];

													$urun_fabrika_id = $row['urun_fabrika_id'];

													$ilgilikisi = $row['ilgilikisi'];

													$siparissaniye = $row['siparissaniye'];

													$terminsaniye = $row['terminsaniye'];

													$siparistarih = date("d-m-Y", $siparissaniye);

													$termintarih = date("d-m-Y", $terminsaniye);

													$fabrikaadcek = $db->query("SELECT * FROM factories WHERE id = '{$urun_fabrika_id}' AND company_id = '{$user->company_id}'")->fetch(PDO::FETCH_ASSOC);

													$urun_fabrika_adi = $fabrikaadcek['name'];

										?>

													<form action="" method="POST">

														<div class="row" style="margin: 2px;">

															<div class="col-4 d-block d-sm-none">Hazırlayan :</div>
															
															<div class="col-md-2 col-8"><?= $hazirlayankisi; ?></div>

															<div class="col-4 d-block d-sm-none">Fabrika :</div>

															<div class="col-md-2 col-8"><?= $urun_fabrika_adi; ?></div>

															<div class="col-4 d-block d-sm-none">İlgili Kişi :</div>

															<div class="col-md-2 col-8"><?= $ilgilikisi; ?></div>

															<div class="col-4 d-block d-sm-none">Miktar :</div>

															<div class="col-md-1 col-8"><input type="text" name="eklenenadet" class="form-control form-control-sm" value="<?= $urun_siparis_aded; ?>"></div>

															<div class="col-4 d-block d-sm-none">Tarih :</div>

															<div class="col-md-1 col-8"><?= $siparistarih; ?></div>

															<div class="col-4 d-block d-sm-none">Termin :</div>

															<div class="col-md-1 col-8"><?= $termintarih; ?></div>

															<div class="col-md-1 col-4">
																
																<input type="hidden" name="siparis_id" value="<?= $siparis_id; ?>">

																<input type="hidden" name="urun_id" value="<?= $urun_id; ?>">

																<input type="hidden" name="urun_adet" value="<?= $urun_adet; ?>">
																
																<button type="submit" name="siparisalindi" class="btn btn-primary btn-sm">Mağazaya Gönderildi</button>

															</div>

															<div class="col-md-1 col-4">

																<input type="hidden" name="urun_depo_adet" value="<?= $urun_depo_adet; ?>">
																
																<button type="submit" name="deposiparisalindi" class="btn btn-secondary btn-sm">Depoya Gönderildi</button>

															</div>

                                                            <div class="col-md-1 col-4">

                                                                <input type="hidden" name="urun_palet" value="<?= $urun_palet; ?>">

                                                                <button type="submit" name="paletsiparisalindi" class="btn btn-success btn-sm">Palete Çekildi</button>

                                                            </div>

														</div>

													</form>

										<?php

												}

											}

										?>

									</div>

									<hr/>

									<div class="alert alert-success">

										<h5><b style="line-height: 40px;">Geçmiş Siparişler</b></h5>

										<div class="d-none d-sm-block">

											<div class="row">
														
												<div class="col-2"><b>Hazırlayan Kişi</b></div>

												<div class="col-2"><b>Talep Edilen Fabrika</b></div>

												<div class="col-2"><b>İlgili Kişi</b></div>

												<div class="col-2"><b>Miktar</b></div>

												<div class="col-2"><b>Tarih</b></div>

											</div>

										</div>
									
										<?php

											$sipariscek = $db->query("SELECT * FROM siparis WHERE urun_id = '{$urun_id}' AND taslak = '0' AND sirketid = '{$user->company_id}' AND silik = '0' ORDER BY siparissaniye DESC LIMIT 10", PDO::FETCH_ASSOC);

											if ( $sipariscek->rowCount() ){

												foreach( $sipariscek as $row ){

													$hazirlayankisi = $row['hazirlayankisi'];

													$urun_siparis_aded = $row['urun_siparis_aded'];

													$urun_fabrika_id = $row['urun_fabrika_id'];

													$ilgilikisi = $row['ilgilikisi'];

													$siparissaniye = $row['siparissaniye'];

													$siparistarih = date("d-m-Y", $siparissaniye);

													$fabrikaadcek = $db->query("SELECT * FROM factories WHERE id = '{$urun_fabrika_id}' AND company_id = '{$user->company_id}'")->fetch(PDO::FETCH_ASSOC);

													$urun_fabrika_adi = $fabrikaadcek['name'];

										?>

													<div class="row">

														<div class="col-4 d-block d-sm-none">Hazırlayan</div>
														
														<div class="col-md-2 col-8"><?= $hazirlayankisi; ?></div>

														<div class="col-4 d-block d-sm-none">Fabrika</div>

														<div class="col-md-2 col-8"><?= $urun_fabrika_adi; ?></div>

														<div class="col-4 d-block d-sm-none">İlgili Kişi</div>

														<div class="col-md-2 col-8"><?= $ilgilikisi; ?></div>

														<div class="col-4 d-block d-sm-none">Adet</div>

														<div class="col-md-2 col-8"><?= $urun_siparis_aded; ?></div>

														<div class="col-4 d-block d-sm-none">Tarih</div>

														<div class="col-md-2 col-8"><?= $siparistarih; ?></div>

													</div>

										<?php

												}

											}

										?>

									</div>

								</div>

						<?php } ?>

						<?php if($user->permissions->shipment == '1'){?>

							<?php if (isset($_GET['sevkiyateklendi']) && $_GET['u'] == $urun_id) { ?>

								<div id="sevkiyatdiv<?= $urun_id; ?>" class="div2">
								
							<?php }else{ ?>

								<div id="sevkiyatdiv<?= $urun_id; ?>" style="display: none;" class="div2">

							<?php } ?>

									<div class="alert alert-dark">

										<h5><b style="line-height: 40px;">Sevkiyat Formu</b></h5>
									
										<form action="" method="POST">
											
											<div class="row">

												<div class="col-md-2 col-12 search-box">

													<b>Firma</b>
													
													<input autofocus="autofocus" name="firma" id="firmainputu" type="text" class="form-control" autocomplete="off" placeholder="Firma Adı"/>

													<ul class="list-group liveresult" id="firmasonuc" style="position: absolute; z-index: 1;"></ul>

												</div>
												
												<div class="col-md-1 col-12">

													<b>Adet</b>
													
													<input type="text" class="form-control" name="adet" placeholder="(Boy)">

												</div>

												<div class="col-md-2 col-12">

													<b>Sevk Tipi</b>

													<select name="sevk_tipi" id="sevk_tipi" class="form-control">

														<option value="null">Sevk tipi seçiniz.</option>
														<option value="0">Müşteri Çağlayan</option>
														<option value="1">Müşteri Alkop</option>
														<option value="2">Tarafımızca sevk</option>
														<option value="3">Ambara tarafımızca sevk</option>

													</select>
												
												</div>

												<div class="col-md-1 col-12">
													<b>Fiyat</b>
													<input type="text" class="form-control" name="fiyat" placeholder="TL">
												</div>

                                                <div class="col-md-1 col-12">
                                                    <b>Araç</b>

                                                    <select name="arac_id" id="arac_id" class="form-control">

                                                        <option value="null">Araç seçiniz.</option>
                                                        <?php
                                                        foreach( $araclar as $arac ){
                                                            ?>
                                                            <option value="<?= $arac->id ?>"><?= $arac->name ?></option>
                                                            <?php
                                                        }
                                                        ?>

                                                    </select>

                                                </div>

												<div class="col-md-4 col-12">

													<b>Açıklama</b>

													<input type="text" class="form-control" name="aciklama" placeholder="Sevkiyat ile ilgili açıklama yazabilirsiniz.">

												</div>

												<div class="col-md-1 col-12">

													<br/>

													<input type="hidden" name="urun_id" value="<?= $urun_id; ?>">
													
													<button class="btn btn-warning" name="sevkiyatkaydet">Kaydet</button>

												</div>

											</div>

										</form>

									</div>

								</div>

							<?php } ?>

						<?php if($user->permissions->editing == '1'){ ?>

							<?php if ((isset($_GET['guncellendi']) || isset($_GET['urunsilinemez'])) && $_GET['u'] == $urun_id) { ?>

								<div class="div2" id="editdiv<?= $urun_id; ?>" >
							
							<?php }else{ ?>

								<div class="div2" style="display: none;" id="editdiv<?= $urun_id; ?>" >

							<?php } ?>	

									<div class="alert alert-success">

										<h5><b style="line-height: 40px;">Düzenleme Formu</b></h5>

										<form action="" method="POST">

											<div class="row">

												<input type="hidden" name="urun_id" value="<?= $urun_id; ?>">

												<?php if($sutunurunkoduizni == '1'){?><div class="col-md-1 col-12"><b>Ürün Kodu</b><input type="text" class="form-control" name="urun_kodu" value="<?= $urun_kodu; ?>"></div><?php } ?>	
											
												<div class="col-md-2 col-12"><b>Ürün Adı</b><input type="text" class="form-control" name="urun_adi" value="<?= $urun_adi; ?>"></div>
												
												<?php if($sutunadetizni == '1' && $user->permissions->piece == '1'){?><div class="col-md-1 col-12 px-1"><b>Adet</b><input type="text" class="form-control" name="urun_adet" value="<?= $urun_adet; ?>"></div><?php } ?>	

												<?php if($sutunpaletizni == '1' && $user->permissions->pallet == '1'){?><div class="col-md-1 col-12 px-1"><b>Palet</b><input type="text" class="form-control" name="urun_palet" value="<?= $urun_palet; ?>"></div><?php } ?>	
														
												<?php if($sutundepoadetizni == '1' && $user->permissions->alkop == '1'){?><div class="col-md-1 col-12 px-1"><b>Depo</b><input type="text" class="form-control" name="urun_depo_adet" value="<?= $urun_depo_adet; ?>"></div><?php } ?>	
														
												<?php if($sutunrafizni == '1'){?><div class="col-md-1 col-12 px-1"><b>Raf</b><input type="text" class="form-control" name="urun_raf" value="<?= $urun_raf; ?>"></div><?php } ?>	
													
												<?php if($sutunbirimkgizni == '1'){?><div class="col-md-1 col-12"><b>Birim Kg</b><input type="text" class="form-control" name="urun_birimkg" value="<?= $urun_birimkg; ?>"></div><?php } ?>

												<?php if($sutunsipariskiloizni == '1'){?><div class="col-md-1 col-12"><b>Sipariş Kilo</b><input type="text" class="form-control" name="urun_birimkg" value="<?= $urun_birimkg; ?>"></div><?php } ?>

												<?php if($sutunboyolcusuizni == '1'){?><div class="col-md-1 col-12"><b>Boy Ölçüsü</b><input type="text" class="form-control" name="urun_boy_olcusu" value="<?= $urun_boy_olcusu; ?>"></div><?php } ?>			

												<?php if($sutunalisizni == '1' && $user->permissions->buying_price == '1'){ ?>

													<div class="col-md-1 col-12">

														<b>Alış</b>

														<input type="text" class="form-control" name="urun_alis" value="<?= $urun_alis; ?>">

													</div>

													<div class="col-md-1 col-12" style="padding:0px; margin:0px;">

														<input type="checkbox" name="hepsineuygula">&nbsp;Hepsine Uygula

													</div>

												<?php } ?>

												<?php if($sutunmanuelsatisizni == '1' && $user->permissions->selling_price == '1' || $user->type == '2'){?>

													<div class="col-md-1 col-12">

														<b>Satış</b>

														<input type="text" class="form-control" name="satis" value="<?= $urun_satis; ?>">

													</div>

												<?php } ?>

												<?php if($sutunfabrikaizni == '1' && $user->permissions->factory == '1'){?>

												<div class="col-md-1 col-12"><b>Fabrika</b>
													
													<select class="form-control" id="exampleFormControlSelect1" name="urun_fabrika">

														<?php

														if ($urun_fabrika == 0) {
															
															echo "<option selected value='0'>Fabrika Seçiniz</option>";

														}else{

															echo "<option selected value='".$urun_fabrika."'>".$urun_fabrika_adi."</option>";

														}

														$fabrika = $db->query("SELECT * FROM factories WHERE company_id = '{$user->company_id}' ORDER BY name ASC", PDO::FETCH_ASSOC);

														if ( $fabrika->rowCount() ){

															foreach( $fabrika as $fbrk ){

																$fabrika_id = $fbrk['id'];

																$fabrika_adi = $fbrk['name'];

																echo "<option value='".$fabrika_id."'>".$fabrika_adi."</option>";

															}

														}

														?>
												
													</select>

												</div>

												<?php } ?>

												<?php if($sutunsiparisadediizni == '1'){?><div class="col-md-1 col-12"><b><small>Sipariş Adedi</small></b><input type="text" class="form-control" name="urun_stok" value="<?= $urun_stok; ?>"></div><?php } ?>

												<?php if($sutunuyariadediizni == '1'){?><div class="col-md-1 col-12 p-0"><b>Uyarı Adet</b><input type="text" class="form-control" name="urun_uyari_stok_adedi" value="<?= $urun_uyari_stok_adedi; ?>"></div><?php } ?>

												<?php if($sutundepouyariadediizni == '1'){?><div class="col-md-1 col-12 p-0"><b>Depo Uyarı</b><input type="text" class="form-control" name="urun_depo_uyari_adet" value="<?= $urun_depo_uyari_adet; ?>"></div><?php } ?>

												<div class="col-md-1 col-12">

													<b>Liste Sıra</b>

													<input type="hidden" name="urun_eski_sira" value="<?= $urun_sira; ?>">

													<select class="form-control" id="exampleFormControlSelect1" name="urun_yeni_sira">
														<?php

															$sirayicek = $db->query("SELECT * FROM urun WHERE kategori_iki = '{$categoryId}' AND sirketid = '{$user->company_id}' ORDER BY urun_sira ASC", PDO::FETCH_ASSOC);

															if ( $sirayicek->rowCount() ){

																foreach( $sirayicek as $sc ){

																	$siralama_urun_sira = $sc['urun_sira'];

																	if ($urun_sira == $siralama_urun_sira) {

																		echo '<option selected value='.$siralama_urun_sira.'>'.$siralama_urun_sira.'</option>';
																		
																	}else{

																		echo '<option value='.$siralama_urun_sira.'>'.$siralama_urun_sira.'</option>';

																	}

																}

															}

														?>
													</select>

												</div>

												<div class="col-md-10 col-12">
													
													<b>Ürün Açıklama</b>
													
													<input type="text" name="urun_aciklama" value="<?= $urun_aciklama; ?>" placeholder="Ürün açıklaması girebilirsiniz." class="form-control">
												
												</div>

												<div class="col-md-2 col-12"><br/><button type="submit" class="btn btn-info btn-block" name="guncellemeformu">Güncelle</button></div>

											</div>

											<br/>

											<div class="row">

												<?php if($sutunmusteriismiizni == '1'){?><div class="col-md-2 col-12"><b>Müşteri İsmi</b><input type="text" class="form-control" name="musteri_ismi" value="<?= $musteri_ismi; ?>"></div><?php } ?>

												<?php if($sutuntarihizni == '1'){?><div class="col-md-2 col-12"><b>Tarih</b><input type="text" id="tarih<?= ($urunlistesira+500); ?>" name="tarih" value="<?= $tarih; ?>" class="form-control form-control-sm"></div><?php } ?>
												
												<?php if($sutunterminizni == '1'){?><div class="col-md-2 col-12"><b>Termin</b><input type="text" id="tarih<?= $urunlistesira; ?>" name="termin" value="<?= $termin; ?>" class="form-control form-control-sm"></div><?php } ?>
											
											</div>

										</form>

									</div>

									<div style="text-align: right;">

										<form action="" method="POST">

											<input type="hidden" name="urun_id" value="<?= $urun_id; ?>">

											<input type="hidden" name="urun_adet" value="<?= $urun_adet; ?>">

											<input type="hidden" name="urun_palet" value="<?= $urun_palet; ?>">

											<input type="hidden" name="urun_depo_adet" value="<?= $urun_depo_adet; ?>">

											<input type="hidden" name="urun_sira" value="<?= $urun_sira; ?>">
												
											<button type="submit" name="urunsil" class="btn btn-danger">Ürünü Sil</button>

										</form>

									</div>

								</div>

						<?php } ?>		

						<hr style="border: 1px solid black;" />

			<?php

					}

				}

			?>

			<?php if($user->type == '2'){ ?> 

				<div class="row">

					<div class="col-md-12"><b style="font-size: 20px;">Toplam Ürün : <?= $toplam_urun_kg; ?> Kg</b></div>

				</div>

			<?php } ?>

		</div>

    </div>

    <?php include 'template/script.php'; ?>

</body>
</html>
<?php
	include 'functions/init.php';
	if($user->type == '0'){
		header("Location:index.php");
		exit();
	}else{
        $clientId = guvenlik($_GET['id']);
        $withholding = isset($_GET['withholding']) ? guvenlik($_GET['withholding']) : false;
        $print = isset($_GET['print']) ? guvenlik($_GET['print']) : false;
        if (isset($_POST['save'])) {
            $offerList = guvenlik($_POST['offer_list']);
            $offerListArray = explode(",", $offerList);
            $explanation = guvenlik($_POST['explanation']);
            foreach ($offerListArray as $key => $value) {
                $query = $db->prepare("UPDATE teklif SET formda = ? WHERE teklifid = ?");
                $update = $query->execute(array('1', $value));
            }
            $withholding = $withholding ? 1 : 0;
            $query = $db->prepare("INSERT INTO teklifformlari SET tekliflistesi = ?, withholding = ?, explanation = ?, firmaid = ?, saniye = ?, sirketid = ?, silik = ?");
            $insert = $query->execute(array($offerList, $withholding, $explanation, $clientId, time(), $authUser->company_id, '0'));
            header("Location:client.php?id=".$clientId);
            exit();
        }
        if (isset($_POST['update_unit_weight'])) {
            $id = guvenlik($_POST['id']);
            $unitWeight = guvenlik($_POST['unit_weight']);
            $query = $db->prepare("UPDATE teklif SET unit_weight = ? WHERE teklifid = ?");
            $update = $query->execute(array($unitWeight, $id));
        }
        $client = $db->query("SELECT * FROM clients WHERE id = '{$clientId}'")->fetch(PDO::FETCH_OBJ);
        $offers = $db->query("SELECT * FROM teklif WHERE tverilenfirma = '{$clientId}' AND formda = '0' AND silik = '0'")->fetchAll(PDO::FETCH_OBJ);
        if(!$offers){
            header("Location:client.php?id=".$clientId);
            exit();
        }
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Teklif Formu</title>
        <?php include 'template/head.php'; ?>
        </script>
    </head>
    <body>
        <div class="container mt-5" style="background: white;">
            <div class="row">
                <div class="col-md-4" style="text-align: center;"><img src="img/file/<?= $company->photo; ?>" style="width: 370px; height: auto;"></div>
                <div class="col-md-8" style="text-align: center; padding: 10px 30px 0px 30px;">
                    <p style="font-size: 15px;">
                        <?= str_replace("\n", "<br/>", $company->description); ?>
                    </p>
                </div>
            </div>
            <h2 class="text-center my-4"><b>TEKLİF FORMU</b></h2>
            <table class="table table-bordered">
                <tr>
                    <td><b>Firma Adı:</b></td>
                    <td style="word-wrap: break-word; white-space: normal; max-width: 600px;"><?= $client->name ?></td>
                    <td><b>Tarih:</b></td>
                    <td><?= date("d/m/Y",time()) ?></td>
                </tr>
                <tr>
                    <td><b>Firma E-Posta:</b></td>
                    <td><?= $client->email ?></td>
                    <td><b>Teklif No:</b></td>
                    <td><?= $offers[0]->teklifid ? $offers[0]->teklifid : $offerF ?></td>
                </tr>
                <tr>
                    <td><b>Firma Telefon:</b></td>
                    <td><?= $client->phone ?></td>
                    <td><b>Teklif Tipi:</b></td>
                    <td><?= $withholding ? 'Tevkifatlı' : '%20 KDVli' ?></td>
                </tr>
                <tr>
                    <td><b>Teklifi Oluşturan:</b></td>
                    <td><?= $authUser->name ?></td>
                    <td><b>Ödeme Şekli:</b></td>
                    <td>Nakit</td>
                </tr>
            </table>

            <table class="table table-bordered mt-4">
                <thead>
                    <tr>
                        <td><b>Ürün Adı</b></td>
                        <td><b>Adet</b></td>
                        <td><b>Cinsi</b></td>
                        <td><b>Bir Boy (6m) Kg</b></td>
                        <td><b>Toplam</b></td>
                        <td><b>Bir Kg Fiyatı</b></td>
                        <td><b>Tutar</b></td>
                    </tr>
                </thead>
                <tbody>
                <?php
                $totalAmount = 0;
                $offerList = "";
                $totalWeight = 0;
                foreach( $offers as $key => $offer ):
                    $productId = $offer->turunid;
                    $product = getProduct($productId);
                    $unitWeight = $offer->unit_weight == 0 ? $product->urun_birimkg : $offer->unit_weight;
                    $mainCategory = getCategory($product->kategori_bir);
                    $subCategory = getCategory($product->kategori_iki);
                    $offerList = empty($offerList) ? $offer->teklifid : $offerList.",".$offer->teklifid;
                    $weight = $offer->tadet * $unitWeight;
                    $totalWeight += $weight;
                    $amount = $weight * $offer->tsatisfiyati;
                    $totalAmount += $amount;
                    $kdv = $withholding ? $totalAmount * .06 : $totalAmount * .2;
                    $totalAmountWithKDV = $totalAmount + $kdv;
                    ?>
                    <tr>
                        <td style="word-wrap: break-word; white-space: normal; max-width: 400px;"><?= ($key + 1).". ".$product->urun_adi." ".$subCategory->kategori_adi; ?></td>
                        <td><?= $offer->tadet." Boy "; ?></td>
                        <td><?= $mainCategory->kategori_adi; ?></td>
                        <td>
                            <form action="" method="POST">
                                <input type="hidden" name="id" value="<?= $offer->teklifid ?>" />
                                <input type="text" name="unit_weight" value="<?= $unitWeight ?>" style="width:80px; border-style: none" /> Kg
                                <button type="submit" name="update_unit_weight" style="margin-left:5px; border-style: none; background-color: white">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </form>
                        </td>
                        <td><?= $weight." Kg"; ?></td>
                        <td><?= $offer->tsatisfiyati." TL"; ?></td>
                        <td><?= number_format($amount, 2, ',', '.')." TL + KDV"; ?></td>
                    </tr>
                <?php endforeach;?>
                    <tr>
                        <td colspan="3"></td>
                        <td><b>Toplam Kilo:</b></td>
                        <td><?= $totalWeight." KG"; ?></td>
                        <td><b>Mal Hizmet Toplam Tutarı:</b></td>
                        <td><?= (number_format($totalAmount, 2, ',', '.'))." TL"; ?></td>
                    </tr>
                    <tr>
                        <td colspan="5"></td>
                        <td><b>Hesaplanan KDV (<?= $withholding ? '%6' : '%20' ?>):</b></td>
                        <td><?= (number_format($kdv, 2, ',', '.'))." TL"; ?></td>
                    </tr>
                    <tr>
                        <td colspan="5"></td>
                        <td><b>Vergiler Dahil Toplam Tutar:</b></td>
                        <td><?= (number_format($totalAmountWithKDV, 2, ',', '.'))." TL"; ?></td>
                    </tr>
                </tbody>
            </table>
            <div class="d-flex justify-content-between">
                <table class="table table-bordered" style="width:50%; font-size:14px;">
                    <tr>
                        <td style="padding:5px;">VAKIFBANK</td>
                        <td style="padding:5px;">GARANTİ BANKASI</td>
                    </tr>
                    <tr>
                        <td style="padding:5px;">ÇAĞLAYAN ŞUBESİ (454)</td>
                        <td style="padding:5px;">ÇAĞLAYAN ŞUBESİ (403)</td>
                    </tr>
                    <tr>
                        <td style="padding:5px;">OSMANLI ALÜMİNYUM SAN TİC LTD ŞTİ</td>
                        <td style="padding:5px;">OSMANLI ALÜMİNYUM SAN TİC LTD ŞTİ TL HESABI</td>
                    </tr>
                    <tr>
                        <td style="padding:5px;">TR91 0001 5001 5800 7309 8287 73</td>
                        <td style="padding:5px;">TR73 0006 2000 4030 0006 2985 12</td>
                    </tr>
                </table>
                <div class="d-flex justify-content-end">
                <div class="text-center">
                    Müşteri Onay (Kaşe İmza Tarih)
                    <div style="border: 2px dashed black; width: 400px; height: 80px;"></div>
                </div>
            </div>
            </div>
            <!-- Formun dışında explanation input'u -->
            <input type="text" id="explanationInput" class="form-control" name="explanation_display"
                   style="border-style: none;"
                   value="TESLİMAT  BİLGİLERİ : ONAY SONRASI 1 İŞ GÜNÜ TESLİM (TEKLİF GEÇERLİLİK SÜRESİ 5 İŞ GÜNÜDÜR)">
            <div class="row">
                <div class="col-md-12">
                    <p>
                        *   Ürün mt/gr biriminde yaklaşık gramaj alınmış olup, kg' lar +/- tolerans dâhilindedir.<br/>
                        *   Yeni düzenlenen kanuna göre 10.000 TL üzeri alımlarımızda tevkifatlı fatura kesilecektir.<br/>
                        *   Özel  siparişlerinizde  % 20 ön ödeme alınır ( boyalı ürünlerde )<br/>
                        *   Sipariş formlarının teyit edilerek geri gönderilmesi gerekmektedir. Kaşe-imza yoluyla onayı gelmeyen siparişler sevk edilmeyecektir. Onaylanmış siparişlere ait mesuliyet alıcı firmaya aittir.<br/>
                        *   Kesilmiş profiller iade alınmaz. İadesi söz konusu olan malzemeler orijinal ambalajında ve orijinal boyda olmalıdır.<br/>
                        *   Taşıma bedelleri (ambar/kargo/nakliye)alıcı firmaya aittir. Osmanlı  Alüminyum doğacak sorunlardan sorumlu değildir.<br/>
                    </p>
                </div>
            </div>
        </div>
        <div class="container p-0 d-flex justify-content-end">
            <form action="" method="POST" onsubmit="syncExplanation()">
                <input type="hidden" name="offer_list" value="<?= $offerList; ?>">
                <input type="hidden" name="explanation" id="hiddenExplanation">
                <button type="submit" name="save" class="btn btn-primary btn-lg mt-3 mr-3">Formu Kaydet</button>
            </form>
        </div>
        <?php include 'template/script.php'; ?>
        <script>
            function syncExplanation() {
                const visibleInput = document.getElementById('explanationInput');
                const hiddenInput = document.getElementById('hiddenExplanation');
                hiddenInput.value = visibleInput.value;
            }
        </script>
        <br/><br/><br/><br/><br/><br/><br/><br/><br/>
    </body>
</html>
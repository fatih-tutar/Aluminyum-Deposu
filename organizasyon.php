<?php 

	include 'fonksiyonlar/bagla.php'; 

	if ($girdi != '1') {
		
		header("Location:giris.php");

		exit();

	}else{


	}

?>

<!DOCTYPE html>

<html>

  <head>

    <title>Alüminyum Deposu</title>

    <?php include 'template/head.php'; ?>

    <style>
      .ortali {
        display: flex;
        justify-content: center;
        align-items: center;
      }
      .space-between {
        justify-content: space-between;
      }
      .bg1 {
        background-color: #277790;
        text-align: center;
      }
      .bg2 {
        background-color: #276274;
        text-align: center;
      }
      .bg3 {
        background-color: #e7ecf0;
      }
      .white {
        color: white;
      }
      .dik-cubuk {
        height:30px; 
        border-left:3px solid #276274;
      }
      .border-full {
        border:3px solid #276274;
        padding-top:50px; 
      }
      .widthy40 {
        width: 40%;
      }
      .widthy50 {
        width: 50%;
      }
      .org-card {
        width:300px;
      }
      .expand-left {
        transform: translateX(-130px);
      }
      .expand-right {
        transform: translateX(130px); 
      }
      .height54 {
        height: 54px;
      }
      .relative {
        position: relative;
      }
    </style>

  </head>

  <body>

    <?php include 'template/banner.php' ?>
    <br/>
    <div class="ortali">
      <div class="org-card">
        <div class="bg1 white height54 ortali p-2">YÖNETİM KURULU BAŞKANI</div>
        <div class="bg2 white height54 ortali p-2"><b>OSMAN TÜRKYILMAZ</b></div>
      </div>
    </div>
    <div class="ortali">
      <div></div>
      <div class="dik-cubuk"></div>
    </div>
    <div class="ortali">
      <div class="border-full widthy50">
        <div class="ortali relative space-between">
          <div class="org-card expand-left">
            <div class="bg1 white height54 ortali p-2">FİNANS VE OPERASYON KOORDİNATÖRÜ</div>
            <div class="bg2 white height54 ortali p-2"><b>BEKİR TÜRKYILMAZ</b></div>
          </div>
          <div class="org-card expand-right">
            <div class="bg1 white height54 ortali p-2">GENEL KOORDİNATÖR</div>
            <div class="bg2 white height54 ortali p-2"><b>TARIK TÜRKYILMAZ</b></div>
          </div>
        </div>
        <div class="height54"></div>
        <div class="ortali relative space-between">
          <div class="org-card expand-left">
            <div class="bg1 white height54 ortali p-2">MUHASEBE VE FİNANS MÜDÜRÜ</div>
            <div class="bg2 white height54 ortali p-2"><b>ZEYNEP BOZKURT</b></div>
          </div>
          <div class="org-card expand-right">
            <div class="bg1 white height54 ortali p-2">SATIŞ VE PLANLAMA MÜDÜRÜ</div>
            <div class="bg2 white height54 ortali p-2"><b>SADULLAH FURUNCI</b></div>
          </div>
        </div>
        <div class="height54"></div>
        <div class="ortali relative space-between">
          <div class="border-full widthy40 expand-left bg3">
            <div class="ortali relative space-between">
              <div class="org-card expand-left">
                <div class="bg1 white height54 ortali p-2">MUHASEBE VE MALİ RAPORLAMA YETKİLİSİ</div>
                <div class="bg2 white height54 ortali p-2"><b>GÖZDE ERGEN</b></div>
              </div>
              <div class="org-card expand-right">
                <div class="bg1 white height54 ortali p-2">İTHALAT & İHRACAT</div>
                <div class="bg2 white height54 ortali p-2"><b>?</b></div>
              </div>
            </div>
            <div class="height54"></div>
            <div class="ortali relative space-between">
              <div class="org-card expand-left">
                <div class="bg1 white height54 ortali p-2">MUHASEBE ASİSTANI</div>
                <div class="bg2 white height54 ortali p-2"><b>?</b></div>
              </div>
              <div class="org-card expand-right">
                <div class="bg1 white height54 ortali p-2">ALKOP MUHASEBE SORUMLUSU</div>
                <div class="bg2 white height54 ortali p-2"><b>?</b></div>
              </div>
            </div>
          </div>
          <div class="border-full widthy40 expand-right bg3">
            <div class="ortali relative space-between">
              <div class="org-card expand-left">
                <div class="bg1 white height54 ortali p-2">SATIŞ VE TEDARİK ZİNCİRİ SORUMLUSU</div>
                <div class="bg2 white height54 ortali p-2"><b>SUAT USTA</b></div>
              </div>
              <div class="org-card expand-right">
                <div class="bg1 white height54 ortali p-2">SEVKİYAT VE ÜRÜN YÖNETİMİ SORUMLUSU</div>
                <div class="bg2 white height54 ortali p-2"><b>MUSTAFA UĞURLU</b></div>
              </div>
            </div>
            <div class="height54"></div>
            <div class="ortali relative space-between">
              <div class="org-card expand-left">
                <div class="bg1 white height54 ortali p-2">SATIŞ DESTEK VE KARGO OPERASYON SORUMLUSU</div>
                <div class="bg2 white height54 ortali p-2"><b>HASAN AVCI</b></div>
              </div>
              <div class="org-card expand-right">
                <div class="bg1 white height54 ortali p-2">SATIŞ DESTEK VE YAPI GELİŞİMİ SORUMLUSU</div>
                <div class="bg2 white height54 ortali p-2"><b>SEMİH UYGUN</b></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <?php include 'template/script.php'; ?>

</body>
</html>
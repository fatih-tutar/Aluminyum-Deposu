<?php 

	include 'fonksiyonlar/bagla.php'; 

	if ($girdi != '1') {
		
		header("Location:giris.php");

		exit();

	}else{
    $array = [];
    $people = $db->query("SELECT * FROM organizasyon", PDO::FETCH_ASSOC);
    if ( $people->rowCount() ){
      foreach( $people as $key => $person ){
        $array[$key] = [
          'ad' => guvenlik($person['ad']),
          'unvan' => guvenlik($person['unvan'])
        ];
      }
    }

    if(isset($_POST['organizasyonkaydet'])) {
      $organizasyonVerileri = $_POST['organizasyon'];

      foreach ($organizasyonVerileri as $index => $veri) {
          $ad = guvenlik($veri['ad']);
          $unvan = guvenlik($veri['unvan']);
          
          // Veritabanı güncelleme sorgusu
          $update = $db->prepare("UPDATE organizasyon SET ad = ?, unvan = ? WHERE id = ?");
          $update->execute([$ad, $unvan, $index + 1]);  // id'yi $index ile kullanabilirsin
      }
      header("Location:organizasyon.php");
      exit();
    }

    $uye_tipi=1;
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
        border-top:3px solid #276274;
        border-right:3px solid #276274;
        border-left:3px solid #276274;
        padding-top:50px; 
      }
      .widthy40 {
        width: 40%;
      }
      .widthy50 {
        width: 50%;
      }
      .org-card {
        width:250px;
      }
      .expand-left {
        transform: translateX(-60%);
      }
      .expand-right {
        transform: translateX(50%); 
      }
      .expand-right-30 {
        transform: translateX(-40%); 
      }
      .label {
        width:290px;
        height: 54px;
        padding: 10px;
        padding-left: 70px;
      }
      .label-high {
        width:750px;
        height: 54px;
      }
      .relative {
        position: relative;
      }
      .height-480 {
        height: 480px;
      }
      .profile-pic {
        position: absolute;
        top: 20px;
        left: 5px; /* Divin dışına taşması için negatif değer kullanıyoruz */
        width: 70px;
        height: 70px;
        border-radius: 50%; /* Yuvarlak bir profil resmi için */
      }

    </style>

  </head>

  <body>

    <?php include 'template/banner.php' ?>
    <br/>
    <form action="" method="POST">
      <div class="ortali">
        <div class="org-card">
          <div class="bg1 white label ortali relative">
            <img src="img/pp.png" alt="profile picture" class="profile-pic">
            <?php if($uye_tipi == 2) { ?>
              <input type="text" name="organizasyon[0][unvan]" value="<?= $array[0]['unvan'] ?>">
            <?php }else{ ?>
              <?= $array[0]['unvan'] ?>
            <?php } ?>
          </div>
          <div class="bg2 white label ortali">
            <?php if($uye_tipi == 2) { ?>
              <input type="text" name="organizasyon[0][ad]" value="<?= $array[0]['ad'] ?>">
            <?php }else{ ?>
              <b><?= $array[0]['ad'] ?></b>
            <?php } ?>
          </div>
        </div>
      </div>
      <div class="ortali">
        <div></div>
        <div class="dik-cubuk"></div>
      </div>
      <div class="ortali">
        <div class="border-full widthy40">
          <div class="ortali relative space-between">
            <div class="org-card expand-left">
              <div class="bg1 white label ortali">
                <img src="img/pp.png" alt="profile picture" class="profile-pic">
                <?php if($uye_tipi == 2) { ?>
                  <input type="text" name="organizasyon[1][unvan]" value="<?= $array[1]['unvan'] ?>">
                <?php }else{ ?>
                  <?= $array[1]['unvan'] ?>
                <?php } ?>
              </div>
              <div class="bg2 white label ortali">
              <?php if($uye_tipi == 2) { ?>
                <input type="text" name="organizasyon[1][ad]" value="<?= $array[1]['ad'] ?>">
              <?php }else{ ?>
                <b><?= $array[1]['ad'] ?></b>
              <?php } ?>
              </div>
            </div>
            <div class="org-card expand-right">
              <div class="bg1 white label ortali">
                <img src="img/pp.png" alt="profile picture" class="profile-pic">
                <?php if($uye_tipi == 2) { ?>
                  <input type="text" name="organizasyon[2][unvan]" value="<?= $array[2]['unvan'] ?>">
                <?php }else{ ?>
                  <?= $array[2]['unvan'] ?>
                <?php } ?>
              </div>
              <div class="bg2 white label ortali">
                <?php if($uye_tipi == 2) { ?>
                  <input type="text" name="organizasyon[2][ad]" value="<?= $array[2]['ad'] ?>">
                <?php }else{ ?>
                  <b><?= $array[2]['ad'] ?></b>
                <?php } ?>
              </div>
            </div>
          </div>
          <div class="label"></div>
          <div class="ortali relative space-between">
            <div class="org-card expand-left">
              <div class="bg1 white label ortali">
                <img src="img/pp.png" alt="profile picture" class="profile-pic">
                <?php if($uye_tipi == 2) { ?>
                  <input type="text" name="organizasyon[3][unvan]" value="<?= $array[3]['unvan'] ?>">
                <?php }else{ ?>
                  <?= $array[3]['unvan'] ?>
                <?php } ?>
              </div>
              <div class="bg2 white label ortali">
                <?php if($uye_tipi == 2) { ?>
                  <input type="text" name="organizasyon[3][ad]" value="<?= $array[3]['ad'] ?>">
                <?php }else{ ?>
                  <b><?= $array[3]['ad'] ?></b>
                <?php } ?>
              </div>
            </div>
            <div class="org-card expand-right">
              <div class="bg1 white label ortali">
                <img src="img/pp.png" alt="profile picture" class="profile-pic">
                <?php if($uye_tipi == 2) { ?>
                  <input type="text" name="organizasyon[4][unvan]" value="<?= $array[4]['unvan'] ?>">
                <?php }else{ ?>
                  <?= $array[4]['unvan'] ?>
                <?php } ?>
              </div>
              <div class="bg2 white label ortali">
                <?php if($uye_tipi == 2) { ?>
                  <input type="text" name="organizasyon[4][ad]" value="<?= $array[4]['ad'] ?>">
                <?php }else{ ?>
                  <b><?= $array[4]['ad'] ?></b>
                <?php } ?>
              </div>
            </div>
          </div>
          <div class="label"></div>
          <div class="ortali relative space-between">
            <div class="border-full widthy50 height-480 expand-left bg3">
              <div class="ortali relative space-between">
                <div class="org-card expand-left">
                  <div class="bg1 white label ortali">
                    <img src="img/pp.png" alt="profile picture" class="profile-pic">
                    <?php if($uye_tipi == 2) { ?>
                      <input type="text" name="organizasyon[5][unvan]" value="<?= $array[5]['unvan'] ?>">
                    <?php }else{ ?>
                      <?= $array[5]['unvan'] ?>
                    <?php } ?>
                  </div>
                  <div class="bg2 white label ortali">
                    <?php if($uye_tipi == 2) { ?>
                      <input type="text" name="organizasyon[5][ad]" value="<?= $array[5]['ad'] ?>">
                    <?php }else{ ?>
                      <b><?= $array[5]['ad'] ?></b>
                    <?php } ?>
                  </div>
                </div>
                <div class="org-card expand-right-30">
                  <div class="bg1 white label ortali">
                    <img src="img/pp.png" alt="profile picture" class="profile-pic">
                    <?php if($uye_tipi == 2) { ?>
                      <input type="text" name="organizasyon[6][unvan]" value="<?= $array[6]['unvan'] ?>">
                    <?php }else{ ?>
                      <?= $array[6]['unvan'] ?>
                    <?php } ?>
                  </div>
                  <div class="bg2 white label ortali">
                    <?php if($uye_tipi == 2) { ?>
                      <input type="text" name="organizasyon[6][ad]" value="<?= $array[6]['ad'] ?>">
                    <?php }else{ ?>
                      <b><?= $array[6]['ad'] ?></b>
                    <?php } ?>
                  </div>
                </div>
              </div>
              <div class="label"></div>
              <div class="ortali relative space-between">
                <div class="org-card expand-left">
                  <div class="bg1 white label ortali">
                    <img src="img/pp.png" alt="profile picture" class="profile-pic">
                    <?php if($uye_tipi == 2) { ?>
                      <input type="text" name="organizasyon[7][unvan]" value="<?= $array[6]['unvan'] ?>">
                    <?php }else{ ?>
                      <?= $array[7]['unvan'] ?>
                    <?php } ?>
                  </div>
                  <div class="bg2 white label ortali">
                    <?php if($uye_tipi == 2) { ?>
                      <input type="text" name="organizasyon[7][ad]" value="<?= $array[7]['ad'] ?>">
                    <?php }else{ ?>
                      <b><?= $array[7]['ad'] ?></b>
                    <?php } ?>
                  </div>
                </div>
                <div class="org-card expand-right-30">
                  <div class="bg1 white label ortali">
                    <img src="img/pp.png" alt="profile picture" class="profile-pic">
                    <?php if($uye_tipi == 2) { ?>
                      <input type="text" name="organizasyon[8][unvan]" value="<?= $array[8]['unvan'] ?>">
                    <?php }else{ ?>
                      <?= $array[8]['unvan'] ?>
                    <?php } ?>
                  </div>
                  <div class="bg2 white label ortali">
                    <?php if($uye_tipi == 2) { ?>
                      <input type="text" name="organizasyon[8][ad]" value="<?= $array[8]['ad'] ?>">
                    <?php }else{ ?>
                      <b><?= $array[8]['ad'] ?></b>
                    <?php } ?>
                  </div>
                </div>
              </div>
              <div class="label"></div>
              <div class="ortali relative space-between">
                <div class="org-card expand-left">
                  <div class="bg1 white label ortali">
                    <img src="img/pp.png" alt="profile picture" class="profile-pic">
                    <?php if($uye_tipi == 2) { ?>
                      <input type="text" name="organizasyon[9][unvan]" value="<?= $array[9]['unvan'] ?>">
                    <?php }else{ ?>
                      <?= $array[9]['unvan'] ?>
                    <?php } ?>
                  </div>
                  <div class="bg2 white label ortali">
                    <?php if($uye_tipi == 2) { ?>
                      <input type="text" name="organizasyon[9][ad]" value="<?= $array[9]['ad'] ?>">
                    <?php }else{ ?>
                      <b><?= $array[9]['ad'] ?></b>
                    <?php } ?>
                  </div>
                </div>
                <div class="org-card expand-right-30">
                  <div class="bg1 white label ortali">
                    <img src="img/pp.png" alt="profile picture" class="profile-pic">
                    <?php if($uye_tipi == 2) { ?>
                      <input type="text" name="organizasyon[10][unvan]" value="<?= $array[10]['unvan'] ?>">
                    <?php }else{ ?>
                      <?= $array[10]['unvan'] ?>
                    <?php } ?>
                  </div>
                  <div class="bg2 white label ortali">
                    <?php if($uye_tipi == 2) { ?>
                      <input type="text" name="organizasyon[10][ad]" value="<?= $array[10]['ad'] ?>">
                    <?php }else{ ?>
                      <b><?= $array[10]['ad'] ?></b>
                    <?php } ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="border-full widthy50 expand-right bg3">
              <div class="ortali relative space-between">
                <div class="org-card expand-left">
                  <div class="bg1 white label ortali">
                    <img src="img/pp.png" alt="profile picture" class="profile-pic">
                    <?php if($uye_tipi == 2) { ?>
                      <input type="text" name="organizasyon[11][unvan]" value="<?= $array[11]['unvan'] ?>">
                    <?php }else{ ?>
                      <?= $array[11]['unvan'] ?>
                    <?php } ?>
                  </div>
                  <div class="bg2 white label ortali">
                    <?php if($uye_tipi == 2) { ?>
                      <input type="text" name="organizasyon[11][ad]" value="<?= $array[11]['ad'] ?>">
                    <?php }else{ ?>
                      <b><?= $array[11]['ad'] ?></b>
                    <?php } ?>
                  </div>
                </div>
                <div class="org-card expand-right-30">
                  <div class="bg1 white label ortali">
                    <img src="img/pp.png" alt="profile picture" class="profile-pic">
                    <?php if($uye_tipi == 2) { ?>
                      <input type="text" name="organizasyon[12][unvan]" value="<?= $array[12]['unvan'] ?>">
                    <?php }else{ ?>
                      <?= $array[12]['unvan'] ?>
                    <?php } ?>
                  </div>
                  <div class="bg2 white label ortali">
                    <?php if($uye_tipi == 2) { ?>
                      <input type="text" name="organizasyon[12][ad]" value="<?= $array[12]['ad'] ?>">
                    <?php }else{ ?>
                      <b><?= $array[12]['ad'] ?></b>
                    <?php } ?>
                  </div>
                </div>
              </div>
              <div class="label"></div>
              <div class="ortali relative space-between">
                <div class="org-card expand-left">
                  <div class="bg1 white label ortali">
                    <img src="img/pp.png" alt="profile picture" class="profile-pic">
                    <?php if($uye_tipi == 2) { ?>
                      <input type="text" name="organizasyon[13][unvan]" value="<?= $array[13]['unvan'] ?>">
                    <?php }else{ ?>
                      <?= $array[13]['unvan'] ?>
                    <?php } ?>
                  </div>
                  <div class="bg2 white label ortali">
                    <?php if($uye_tipi == 2) { ?>
                      <input type="text" name="organizasyon[13][ad]" value="<?= $array[13]['ad'] ?>">
                    <?php }else{ ?>
                      <b><?= $array[13]['ad'] ?></b>
                    <?php } ?>
                  </div>
                </div>
                <div class="org-card expand-right-30">
                  <div class="bg1 white label ortali">
                    <img src="img/pp.png" alt="profile picture" class="profile-pic">
                    <?php if($uye_tipi == 2) { ?>
                      <input type="text" name="organizasyon[14][unvan]" value="<?= $array[14]['unvan'] ?>">
                    <?php }else{ ?>
                      <?= $array[14]['unvan'] ?>
                    <?php } ?>
                  </div>
                  <div class="bg2 white label ortali">
                    <?php if($uye_tipi == 2) { ?>
                      <input type="text" name="organizasyon[14][ad]" value="<?= $array[14]['ad'] ?>">
                    <?php }else{ ?>
                      <b><?= $array[14]['ad'] ?></b>
                    <?php } ?>
                  </div>
                </div>
              </div>
              <div class="label"></div>
              <div class="ortali relative space-between">
                <div class="org-card expand-left">
                  <div class="bg1 white label ortali">
                    <img src="img/pp.png" alt="profile picture" class="profile-pic">
                    <?php if($uye_tipi == 2) { ?>
                      <input type="text" name="organizasyon[15][unvan]" value="<?= $array[15]['unvan'] ?>">
                    <?php }else{ ?>
                      <?= $array[15]['unvan'] ?>
                    <?php } ?>
                  </div>
                  <div class="bg2 white label ortali">
                    <?php if($uye_tipi == 2) { ?>
                      <input type="text" name="organizasyon[15][ad]" value="<?= $array[15]['ad'] ?>">
                    <?php }else{ ?>
                      <b><?= $array[15]['ad'] ?></b>
                    <?php } ?>
                  </div>
                </div>
                <div class="org-card expand-right-30">
                  <div class="bg1 white label ortali">
                    <img src="img/pp.png" alt="profile picture" class="profile-pic">
                    <?php if($uye_tipi == 2) { ?>
                      <input type="text" name="organizasyon[16][unvan]" value="<?= $array[16]['unvan'] ?>">
                    <?php }else{ ?>
                      <?= $array[16]['unvan'] ?>
                    <?php } ?>
                  </div>
                  <div class="bg2 white label ortali">
                    <?php if($uye_tipi == 2) { ?>
                      <input type="text" name="organizasyon[16][ad]" value="<?= $array[16]['ad'] ?>">
                    <?php }else{ ?>
                      <b><?= $array[16]['ad'] ?></b>
                    <?php } ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="ortali mt-3">
        <?php if($uye_tipi == 2) { ?>
          <button type="submit" class="btn btn-info" name="organizasyonkaydet" style="width:300px; font-size:25px;">Kaydet</button>
        <?php } ?>
      </div>
    </form>
    <br/><br/><br/><br/><br/><br/><br/><br/>

    <?php include 'template/script.php'; ?>

</body>
</html>
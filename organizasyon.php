<?php 

	include 'fonksiyonlar/bagla.php'; 

	if ($girdi != '1') {
		
		header("Location:giris.php");

		exit();

	}else{
    $users = [];
    $people = $db->query("SELECT * FROM organizasyon", PDO::FETCH_ASSOC);
    if ( $people->rowCount() ){
      foreach( $people as $key => $person ){
        $users[$key] = [
          'ad' => guvenlik($person['ad']),
          'unvan' => guvenlik($person['unvan']),
          'foto' => guvenlik($person['foto'])
        ];
      }
    }

    if(isset($_POST['organizasyonkaydet'])) {
      $organizasyonVerileri = $_POST['organizasyon'];
      $fileVerileri = $_FILES['organizasyon'];

      //var_dump($fileVerileri); exit();

      foreach ($organizasyonVerileri as $index => $veri) {
          $ad = guvenlik($veri['ad']);
          $unvan = guvenlik($veri['unvan']);
  
          // Dosya işlemleri
          if (isset($fileVerileri['name'][$index]['uploadfile']) && $fileVerileri['error'][$index]['uploadfile'] === UPLOAD_ERR_OK) {
              $temp = explode(".", $fileVerileri['name'][$index]['uploadfile']);
              $dosyaadi = $temp[0];
              $extension = end($temp);
              $randomsayi = rand(0, 10000);
              $upload_file = $dosyaadi . $randomsayi . "." . $extension;

              // print_r($temp)."<br/>";
              // echo $dosyaadi."<br/>".$extension."<br/>".$randomsayi."<br/>".$upload_file; exit();
              
              // Dosya yükleme işlemi
              move_uploaded_file($fileVerileri['tmp_name'][$index]['uploadfile'], "img/organizasyon/" . $upload_file);
              
              // Veritabanına kaydetme
              $update = $db->prepare("UPDATE organizasyon SET ad = ?, unvan = ?, foto = ? WHERE id = ?");
              $update->execute([$ad, $unvan, $upload_file, $index + 1]);  // id'yi $index ile kullanabilirsin
          } else {
              // Dosya yüklenmedi, sadece ad ve unvan güncellenir
              $update = $db->prepare("UPDATE organizasyon SET ad = ?, unvan = ? WHERE id = ?");
              $update->execute([$ad, $unvan, $index + 1]);  // id'yi $index ile kullanabilirsin
          }
        }
        
        header("Location:organizasyon.php");
        exit();
    }
  
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
        height:54px; 
        border-left:3px solid #276274;
      }
      .border-full {
        border-top:3px solid #276274;
        border-right:3px solid #276274;
        border-left:3px solid #276274;
        padding-top:50px; 
        z-index: 0;
      }
      .widthy40 {
        width: 35%;
      }
      .widthy50 {
        width: 50%;
      }
      .org-card {
        width:250px;
        z-index: 2;
      }
      .expand-left {
        transform: translateX(-60%);
      }
      .expand-right {
        transform: translateX(50%); 
      }
      .expand-right-40 {
        transform: translateX(40%); 
      }
      .expand-right--40 {
        transform: translateX(-40%); 
      }
      .expand-bottom-right {
        transform: translateX(110%); 
        margin-top: 159px;
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
      .upload-button {
        position: absolute;
        bottom: -20px;
        width:120px; 
        font-size:10px;
      }
      .zi-1 {
        z-index: 1;
      }
      .right-side-frame {
        width:465px; 
        height:380px; 
        position:absolute; 
        right:-465px; 
        top:100px;
      }
    </style>

  </head>

  <body>

    <?php include 'template/banner.php' ?>
    <br/>
    <form action="" method="POST" enctype="multipart/form-data" style="margin-left:-220px;">
      <div class="ortali mb-4">
        <h2><b>ORGANİZASYON ŞEMASI</b></h2>
      </div>
      <div class="ortali" style="margin-left:-45px;">
        <div class="org-card relative">
          <div class="bg1 white label ortali relative">
              <img src="img/<?= empty($users[0]['foto']) ? 'pp.png' : 'organizasyon/'.$users[0]['foto'] ?>" alt="profile picture" class="profile-pic">
            <?php if($uye_tipi == 2) { ?>
              <input type="text" name="organizasyon[0][unvan]" value="<?= $users[0]['unvan'] ?>">
            <?php }else{ ?>
              <?= $users[0]['unvan'] ?>
            <?php } ?>
          </div>
          <div class="bg2 white label ortali">
            <?php if($uye_tipi == 2) { ?>
              <input type="text" name="organizasyon[0][ad]" value="<?= $users[0]['ad'] ?>">
            <?php }else{ ?>
              <b><?= $users[0]['ad'] ?></b>
            <?php } ?>
          </div>
          <?php if($uye_tipi == 2) { ?>
            <input type="file" name="organizasyon[0][uploadfile]" class="upload-button">
          <?php } ?>
        </div>
      </div>
      <div class="ortali">
        <div></div>
        <div class="dik-cubuk"></div>
      </div>
      <div class="ortali">
        <div class="border-full relative widthy40">
          <div class="ortali relative space-between">
            <div class="org-card expand-left relative">
              <div class="bg1 white label ortali">
                <img src="img/<?= empty($users[1]['foto']) ? 'pp.png' : 'organizasyon/'.$users[1]['foto'] ?>" alt="profile picture" class="profile-pic">
                <?php if($uye_tipi == 2) { ?>
                  <input type="text" name="organizasyon[1][unvan]" value="<?= $users[1]['unvan'] ?>">
                <?php }else{ ?>
                  <?= $users[1]['unvan'] ?>
                <?php } ?>
              </div>
              <div class="bg2 white label ortali">
              <?php if($uye_tipi == 2) { ?>
                <input type="text" name="organizasyon[1][ad]" value="<?= $users[1]['ad'] ?>">
              <?php }else{ ?>
                <b><?= $users[1]['ad'] ?></b>
              <?php } ?>
              </div>
              <?php if($uye_tipi == 2) { ?>
                <input type="file" name="organizasyon[1][uploadfile]" class="upload-button">
              <?php } ?>
            </div>
            <div class="org-card expand-right-40 relative">
              <div class="bg1 white label ortali">
                <img src="img/<?= empty($users[2]['foto']) ? 'pp.png' : 'organizasyon/'.$users[2]['foto'] ?>" alt="profile picture" class="profile-pic">
                <?php if($uye_tipi == 2) { ?>
                  <input type="text" name="organizasyon[2][unvan]" value="<?= $users[2]['unvan'] ?>">
                <?php }else{ ?>
                  <?= $users[2]['unvan'] ?>
                <?php } ?>
              </div>
              <div class="bg2 white label ortali">
                <?php if($uye_tipi == 2) { ?>
                  <input type="text" name="organizasyon[2][ad]" value="<?= $users[2]['ad'] ?>">
                <?php }else{ ?>
                  <b><?= $users[2]['ad'] ?></b>
                <?php } ?>
              </div>
              <?php if($uye_tipi == 2) { ?>
                <input type="file" name="organizasyon[2][uploadfile]" class="upload-button">
              <?php } ?>
            </div>
          </div>
          <div class="label"></div>
          <div class="ortali relative space-between">
            <div class="org-card expand-left relative">
              <div class="bg1 white label ortali">
                <img src="img/<?= empty($users[3]['foto']) ? 'pp.png' : 'organizasyon/'.$users[3]['foto'] ?>" alt="profile picture" class="profile-pic">
                <?php if($uye_tipi == 2) { ?>
                  <input type="text" name="organizasyon[3][unvan]" value="<?= $users[3]['unvan'] ?>">
                <?php }else{ ?>
                  <?= $users[3]['unvan'] ?>
                <?php } ?>
              </div>
              <div class="bg2 white label ortali">
                <?php if($uye_tipi == 2) { ?>
                  <input type="text" name="organizasyon[3][ad]" value="<?= $users[3]['ad'] ?>">
                <?php }else{ ?>
                  <b><?= $users[3]['ad'] ?></b>
                <?php } ?>
              </div>
              <?php if($uye_tipi == 2) { ?>
                <input type="file" name="organizasyon[3][uploadfile]" class="upload-button">
              <?php } ?>
            </div>
            <div class="org-card expand-right-40 relative">
              <div class="bg1 white label ortali">
                <img src="img/<?= empty($users[4]['foto']) ? 'pp.png' : 'organizasyon/'.$users[4]['foto'] ?>" alt="profile picture" class="profile-pic">
                <?php if($uye_tipi == 2) { ?>
                  <input type="text" name="organizasyon[4][unvan]" value="<?= $users[4]['unvan'] ?>">
                <?php }else{ ?>
                  <?= $users[4]['unvan'] ?>
                <?php } ?>
              </div>
              <div class="bg2 white label ortali">
                <?php if($uye_tipi == 2) { ?>
                  <input type="text" name="organizasyon[4][ad]" value="<?= $users[4]['ad'] ?>">
                <?php }else{ ?>
                  <b><?= $users[4]['ad'] ?></b>
                <?php } ?>
              </div>
              <?php if($uye_tipi == 2) { ?>
                <input type="file" name="organizasyon[4][uploadfile]" class="upload-button">
              <?php } ?>
            </div>
          </div>
          <div class="label"></div>
          <div class="ortali relative space-between">
            <div class="border-full widthy50 height-480 expand-left bg3 zi-1">
              <div class="ortali relative space-between">
                <div class="org-card expand-left relative">
                  <div class="bg1 white label ortali">
                    <img src="img/<?= empty($users[5]['foto']) ? 'pp.png' : 'organizasyon/'.$users[5]['foto'] ?>" alt="profile picture" class="profile-pic">
                    <?php if($uye_tipi == 2) { ?>
                      <input type="text" name="organizasyon[5][unvan]" value="<?= $users[5]['unvan'] ?>">
                    <?php }else{ ?>
                      <?= $users[5]['unvan'] ?>
                    <?php } ?>
                  </div>
                  <div class="bg2 white label ortali">
                    <?php if($uye_tipi == 2) { ?>
                      <input type="text" name="organizasyon[5][ad]" value="<?= $users[5]['ad'] ?>">
                    <?php }else{ ?>
                      <b><?= $users[5]['ad'] ?></b>
                    <?php } ?>
                  </div>
                  <?php if($uye_tipi == 2) { ?>
                    <input type="file" name="organizasyon[5][uploadfile]" class="upload-button">
                  <?php } ?>
                </div>
                <div class="org-card expand-right--40 relative">
                  <div class="bg1 white label ortali">
                    <img src="img/<?= empty($users[6]['foto']) ? 'pp.png' : 'organizasyon/'.$users[6]['foto'] ?>" alt="profile picture" class="profile-pic">
                    <?php if($uye_tipi == 2) { ?>
                      <input type="text" name="organizasyon[6][unvan]" value="<?= $users[6]['unvan'] ?>">
                    <?php }else{ ?>
                      <?= $users[6]['unvan'] ?>
                    <?php } ?>
                  </div>
                  <div class="bg2 white label ortali">
                    <?php if($uye_tipi == 2) { ?>
                      <input type="text" name="organizasyon[6][ad]" value="<?= $users[6]['ad'] ?>">
                    <?php }else{ ?>
                      <b><?= $users[6]['ad'] ?></b>
                    <?php } ?>
                  </div>
                  <?php if($uye_tipi == 2) { ?>
                    <input type="file" name="organizasyon[6][uploadfile]" class="upload-button">
                  <?php } ?>
                </div>
              </div>
              <div class="label"></div>
              <div class="ortali relative space-between">
                <div class="org-card expand-left relative">
                  <div class="bg1 white label ortali">
                    <img src="img/<?= empty($users[7]['foto']) ? 'pp.png' : 'organizasyon/'.$users[7]['foto'] ?>" alt="profile picture" class="profile-pic">
                    <?php if($uye_tipi == 2) { ?>
                      <input type="text" name="organizasyon[7][unvan]" value="<?= $users[6]['unvan'] ?>">
                    <?php }else{ ?>
                      <?= $users[7]['unvan'] ?>
                    <?php } ?>
                  </div>
                  <div class="bg2 white label ortali">
                    <?php if($uye_tipi == 2) { ?>
                      <input type="text" name="organizasyon[7][ad]" value="<?= $users[7]['ad'] ?>">
                    <?php }else{ ?>
                      <b><?= $users[7]['ad'] ?></b>
                    <?php } ?>
                  </div>
                  <?php if($uye_tipi == 2) { ?>
                    <input type="file" name="organizasyon[7][uploadfile]" class="upload-button">
                  <?php } ?>
                </div>
                <div class="org-card expand-right--40 relative">
                  <div class="bg1 white label ortali">
                    <img src="img/<?= empty($users[8]['foto']) ? 'pp.png' : 'organizasyon/'.$users[8]['foto'] ?>" alt="profile picture" class="profile-pic">
                    <?php if($uye_tipi == 2) { ?>
                      <input type="text" name="organizasyon[8][unvan]" value="<?= $users[8]['unvan'] ?>">
                    <?php }else{ ?>
                      <?= $users[8]['unvan'] ?>
                    <?php } ?>
                  </div>
                  <div class="bg2 white label ortali">
                    <?php if($uye_tipi == 2) { ?>
                      <input type="text" name="organizasyon[8][ad]" value="<?= $users[8]['ad'] ?>">
                    <?php }else{ ?>
                      <b><?= $users[8]['ad'] ?></b>
                    <?php } ?>
                  </div>
                  <?php if($uye_tipi == 2) { ?>
                    <input type="file" name="organizasyon[8][uploadfile]" class="upload-button">
                  <?php } ?>
                </div>
              </div>
              <div class="label"></div>
              <div class="ortali relative space-between">
                <div class="org-card expand-left relative">
                  <div class="bg1 white label ortali">
                    <img src="img/<?= empty($users[9]['foto']) ? 'pp.png' : 'organizasyon/'.$users[9]['foto'] ?>" alt="profile picture" class="profile-pic">
                    <?php if($uye_tipi == 2) { ?>
                      <input type="text" name="organizasyon[9][unvan]" value="<?= $users[9]['unvan'] ?>">
                    <?php }else{ ?>
                      <?= $users[9]['unvan'] ?>
                    <?php } ?>
                  </div>
                  <div class="bg2 white label ortali">
                    <?php if($uye_tipi == 2) { ?>
                      <input type="text" name="organizasyon[9][ad]" value="<?= $users[9]['ad'] ?>">
                    <?php }else{ ?>
                      <b><?= $users[9]['ad'] ?></b>
                    <?php } ?>
                  </div>
                  <?php if($uye_tipi == 2) { ?>
                    <input type="file" name="organizasyon[9][uploadfile]" class="upload-button">
                  <?php } ?>
                </div>
                <div class="org-card expand-right--40 relative">
                  <div class="bg1 white label ortali">
                    <img src="img/<?= empty($users[10]['foto']) ? 'pp.png' : 'organizasyon/'.$users[10]['foto'] ?>" alt="profile picture" class="profile-pic">
                    <?php if($uye_tipi == 2) { ?>
                      <input type="text" name="organizasyon[10][unvan]" value="<?= $users[10]['unvan'] ?>">
                    <?php }else{ ?>
                      <?= $users[10]['unvan'] ?>
                    <?php } ?>
                  </div>
                  <div class="bg2 white label ortali">
                    <?php if($uye_tipi == 2) { ?>
                      <input type="text" name="organizasyon[10][ad]" value="<?= $users[10]['ad'] ?>">
                    <?php }else{ ?>
                      <b><?= $users[10]['ad'] ?></b>
                    <?php } ?>
                  </div>
                  <?php if($uye_tipi == 2) { ?>
                    <input type="file" name="organizasyon[10][uploadfile]" class="upload-button">
                  <?php } ?>
                </div>
              </div>
            </div>
            <div class="border-full widthy50 expand-right bg3 zi-1">
              <div class="ortali relative space-between">
                <div class="org-card expand-left relative">
                  <div class="bg1 white label ortali">
                    <img src="img/<?= empty($users[11]['foto']) ? 'pp.png' : 'organizasyon/'.$users[11]['foto'] ?>" alt="profile picture" class="profile-pic">
                    <?php if($uye_tipi == 2) { ?>
                      <input type="text" name="organizasyon[11][unvan]" value="<?= $users[11]['unvan'] ?>">
                    <?php }else{ ?>
                      <?= $users[11]['unvan'] ?>
                    <?php } ?>
                  </div>
                  <div class="bg2 white label ortali">
                    <?php if($uye_tipi == 2) { ?>
                      <input type="text" name="organizasyon[11][ad]" value="<?= $users[11]['ad'] ?>">
                    <?php }else{ ?>
                      <b><?= $users[11]['ad'] ?></b>
                    <?php } ?>
                  </div>
                  <?php if($uye_tipi == 2) { ?>
                    <input type="file" name="organizasyon[11][uploadfile]" class="upload-button">
                  <?php } ?>
                </div>
                <div class="org-card expand-right--40 relative">
                  <div class="bg1 white label ortali">
                    <img src="img/<?= empty($users[12]['foto']) ? 'pp.png' : 'organizasyon/'.$users[12]['foto'] ?>" alt="profile picture" class="profile-pic">
                    <?php if($uye_tipi == 2) { ?>
                      <input type="text" name="organizasyon[12][unvan]" value="<?= $users[12]['unvan'] ?>">
                    <?php }else{ ?>
                      <?= $users[12]['unvan'] ?>
                    <?php } ?>
                  </div>
                  <div class="bg2 white label ortali">
                    <?php if($uye_tipi == 2) { ?>
                      <input type="text" name="organizasyon[12][ad]" value="<?= $users[12]['ad'] ?>">
                    <?php }else{ ?>
                      <b><?= $users[12]['ad'] ?></b>
                    <?php } ?>
                  </div>
                  <?php if($uye_tipi == 2) { ?>
                    <input type="file" name="organizasyon[12][uploadfile]" class="upload-button">
                  <?php } ?>
                </div>
              </div>
              <div class="label"></div>
              <div class="ortali relative space-between">
                <div class="org-card expand-left relative">
                  <div class="bg1 white label ortali">
                    <img src="img/<?= empty($users[13]['foto']) ? 'pp.png' : 'organizasyon/'.$users[13]['foto'] ?>" alt="profile picture" class="profile-pic">
                    <?php if($uye_tipi == 2) { ?>
                      <input type="text" name="organizasyon[13][unvan]" value="<?= $users[13]['unvan'] ?>">
                    <?php }else{ ?>
                      <?= $users[13]['unvan'] ?>
                    <?php } ?>
                  </div>
                  <div class="bg2 white label ortali">
                    <?php if($uye_tipi == 2) { ?>
                      <input type="text" name="organizasyon[13][ad]" value="<?= $users[13]['ad'] ?>">
                    <?php }else{ ?>
                      <b><?= $users[13]['ad'] ?></b>
                    <?php } ?>
                  </div>
                  <?php if($uye_tipi == 2) { ?>
                    <input type="file" name="organizasyon[13][uploadfile]" class="upload-button">
                  <?php } ?>
                </div>
                <div class="org-card expand-right--40 relative">
                  <div class="bg1 white label ortali">
                    <img src="img/<?= empty($users[14]['foto']) ? 'pp.png' : 'organizasyon/'.$users[14]['foto'] ?>" alt="profile picture" class="profile-pic">
                    <?php if($uye_tipi == 2) { ?>
                      <input type="text" name="organizasyon[14][unvan]" value="<?= $users[14]['unvan'] ?>">
                    <?php }else{ ?>
                      <?= $users[14]['unvan'] ?>
                    <?php } ?>
                  </div>
                  <div class="bg2 white label ortali">
                    <?php if($uye_tipi == 2) { ?>
                      <input type="text" name="organizasyon[14][ad]" value="<?= $users[14]['ad'] ?>">
                    <?php }else{ ?>
                      <b><?= $users[14]['ad'] ?></b>
                    <?php } ?>
                  </div>
                  <?php if($uye_tipi == 2) { ?>
                    <input type="file" name="organizasyon[14][uploadfile]" class="upload-button">
                  <?php } ?>
                </div>
              </div>
              <div class="label"></div>
              <div class="ortali relative space-between">
                <div class="org-card expand-left relative">
                  <div class="bg1 white label ortali">
                    <img src="img/<?= empty($users[15]['foto']) ? 'pp.png' : 'organizasyon/'.$users[15]['foto'] ?>" alt="profile picture" class="profile-pic">
                    <?php if($uye_tipi == 2) { ?>
                      <input type="text" name="organizasyon[15][unvan]" value="<?= $users[15]['unvan'] ?>">
                    <?php }else{ ?>
                      <?= $users[15]['unvan'] ?>
                    <?php } ?>
                  </div>
                  <div class="bg2 white label ortali">
                    <?php if($uye_tipi == 2) { ?>
                      <input type="text" name="organizasyon[15][ad]" value="<?= $users[15]['ad'] ?>">
                    <?php }else{ ?>
                      <b><?= $users[15]['ad'] ?></b>
                    <?php } ?>
                  </div>
                  <?php if($uye_tipi == 2) { ?>
                    <input type="file" name="organizasyon[15][uploadfile]" class="upload-button">
                  <?php } ?>
                </div>
                <div class="org-card expand-right--40 relative">
                  <div class="bg1 white label ortali">
                    <img src="img/<?= empty($users[16]['foto']) ? 'pp.png' : 'organizasyon/'.$users[16]['foto'] ?>" alt="profile picture" class="profile-pic">
                    <?php if($uye_tipi == 2) { ?>
                      <input type="text" name="organizasyon[16][unvan]" value="<?= $users[16]['unvan'] ?>">
                    <?php }else{ ?>
                      <?= $users[16]['unvan'] ?>
                    <?php } ?>
                  </div>
                  <div class="bg2 white label ortali">
                    <?php if($uye_tipi == 2) { ?>
                      <input type="text" name="organizasyon[16][ad]" value="<?= $users[16]['ad'] ?>">
                    <?php }else{ ?>
                      <b><?= $users[16]['ad'] ?></b>
                    <?php } ?>
                  </div>
                  <?php if($uye_tipi == 2) { ?>
                    <input type="file" name="organizasyon[16][uploadfile]" class="upload-button">
                  <?php } ?>
                </div>
              </div>
            </div>
          </div>
          <div class="border-full right-side-frame">
            <div style="border-bottom:3px solid #276274; height:115px;"></div>
            <div class="expand-bottom-right relative" style="width:290px;"> 
              <div class="org-card">
                <div class="bg1 white label ortali">
                  <img src="img/<?= empty($users[17]['foto']) ? 'pp.png' : 'organizasyon/'.$users[17]['foto'] ?>" alt="profile picture" class="profile-pic">
                  <?php if($uye_tipi == 2) { ?>
                    <input type="text" name="organizasyon[17][unvan]" value="<?= $users[17]['unvan'] ?>">
                  <?php }else{ ?>
                    <?= $users[11]['unvan'] ?>
                  <?php } ?>
                </div>
                <div class="bg2 white label ortali">
                  <?php if($uye_tipi == 2) { ?>
                    <input type="text" name="organizasyon[17][ad]" value="<?= $users[17]['ad'] ?>">
                  <?php }else{ ?>
                    <b><?= $users[11]['ad'] ?></b>
                  <?php } ?>
                </div>
                <?php if($uye_tipi == 2) { ?>
                  <input type="file" name="organizasyon[17][uploadfile]" class="upload-button">
                <?php } ?>
              </div>
              <div class="ortali">
                <div></div>
                <div class="dik-cubuk"></div>
              </div>
              <div class="org-card">
                <div class="bg1 white label ortali relative">
                  <img src="img/pp.png" alt="profile picture" class="profile-pic">
                  <?php if($uye_tipi == 2) { ?>
                    <input type="text" name="organizasyon[18][unvan]" value="<?= $users[18]['unvan'] ?>">
                  <?php }else{ ?>
                    <?= $users[18]['unvan'] ?>
                  <?php } ?>
                </div>
                <div class="bg2 white label ortali">
                  <?php if($uye_tipi == 2) { ?>
                    <input type="text" name="organizasyon[18][ad]" value="<?= $users[18]['ad'] ?>">
                  <?php }else{ ?>
                    <b><?= $users[18]['ad'] ?></b>
                  <?php } ?>
                </div>
                <?php if($uye_tipi == 2) { ?>
                  <input type="file" name="organizasyon[18][uploadfile]" class="upload-button">
                <?php } ?>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="ortali mt-5">
        <?php if($uye_tipi == 2) { ?>
          <button type="submit" class="btn btn-info" name="organizasyonkaydet" style="width:300px; font-size:25px;">Kaydet</button>
        <?php } ?>
      </div>
    </form>
    <br/><br/><br/><br/><br/><br/><br/><br/>

    <?php include 'template/script.php'; ?>

</body>
</html>
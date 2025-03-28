<?php
/* Database Bağlantısı */

    include 'functions/init.php';

/* Database Bağlantısı */

    if (!isLoggedIn()) {
        header("Location:login.php");
        exit();
    }else{
    
        if (isset($_REQUEST['term'])) { // Bir terim gelip gelmediğini kontrol ediyoruz.
        
            $term = guvenlik($_REQUEST['term']); // Gelen terimi değişkene atıyoruz.
        
            /* Gelen terim ile eşleşen kayıt olup olmadığını sorguluyoruz. */

            $sorgu = $db->prepare("SELECT COUNT(*) FROM urun WHERE urun_adi LIKE '%$term%' AND sirketid = '{$user->company_id}' ORDER BY urun_adi ASC");
            
            $sorgu->execute();
            
            $say = $sorgu->fetchColumn();

            /* Gelen terim ile eşleşen kayıt olup olmadığını sorguluyoruz. */

            if ($say != 0) { // Sorgulama sonucu dolu olursa eğer sonuçları ekrana basıyoruz.

                $query = $db->query("SELECT * FROM urun WHERE urun_adi LIKE '%$term%' AND sirketid = '{$user->company_id}' ORDER BY urun_adi ASC LIMIT 10", PDO::FETCH_ASSOC);

                if($query->rowCount()){

                    $p = 1;

                    foreach ($query as $row) {

                        $urun_id = guvenlik($row['urun_id']);
                        
                        $urun_adi = guvenlik($row['urun_adi']);

                        $kategori_bir = guvenlik($row['kategori_bir']);

                        $kategori_iki = guvenlik($row['kategori_iki']);

                        $urun_kategori_bir = getCategoryName($kategori_bir);
                        
                        $urun_kategori_iki = getCategoryName($kategori_iki);

                        echo '<li id="li'.$p.'" class="list-group-item" style="cursor:pointer;">'.$urun_adi.' / '.$urun_kategori_iki.' / '.$urun_kategori_bir.'</li>';

                        $p++;

                    }

                }

            }else{

                // Eğer eşleşen kayıt yoksa alttaki uyarıyı ekrana basıyoruz.

                echo '<li class="list-group-item">Eşleşen kayıt bulunamadı.</li>';

            }

        }

    }

?>
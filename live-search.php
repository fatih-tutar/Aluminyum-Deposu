<?php
/* Database Bağlantısı */

    include 'fonksiyonlar/bagla.php';

/* Database Bağlantısı */
    
    if (isset($_REQUEST['term'])) { // Bir terim gelip gelmediğini kontrol ediyoruz.
    
        $term = guvenlik($_REQUEST['term']); // Gelen terimi değişkene atıyoruz.
    
        /* Gelen terim ile eşleşen kayıt olup olmadığını sorguluyoruz. */

        $sorgu = $db->prepare("SELECT COUNT(*) FROM firmalar WHERE firmaadi LIKE '%$term%' AND sirketid = '{$uye_sirket}' ORDER BY firmaadi ASC");
        
        $sorgu->execute();
        
        $say = $sorgu->fetchColumn();

        /* Gelen terim ile eşleşen kayıt olup olmadığını sorguluyoruz. */

        if ($say != 0) { // Sorgulama sonucu dolu olursa eğer sonuçları ekrana basıyoruz.

            $query = $db->query("SELECT * FROM firmalar WHERE firmaadi LIKE '%$term%' AND sirketid = '{$uye_sirket}' ORDER BY firmaadi ASC LIMIT 10", PDO::FETCH_ASSOC);

            if($query->rowCount()){

                $p = 1;

                foreach ($query as $row) {

                    $firmaid = $row['firmaid'];
                    
                    $firmaadi = $row['firmaadi'];

                    echo '<li class="list-group-item" id="li'.$p.'">'.$firmaadi.'</li>';

                    $p++;

                }

            }

        }else{

            // Eğer eşleşen kayıt yoksa alttaki uyarıyı ekrana basıyoruz.

            echo '<li class="list-group-item">Eşleşen kayıt bulunamadı.</li>';

        }

    }

?>
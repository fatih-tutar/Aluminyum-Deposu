<?php
/* Database Bağlantısı */

    include 'functions/init.php';

    if (!isLoggedIn()) {
		header("Location:login.php");
		exit();
	}

/* Database Bağlantısı */
    
    if (isset($_REQUEST['term'])) { // Bir terim gelip gelmediğini kontrol ediyoruz.
    
        $term = guvenlik($_REQUEST['term']); // Gelen terimi değişkene atıyoruz.
    
        /* Gelen terim ile eşleşen kayıt olup olmadığını sorguluyoruz. */

        $sorgu = $db->prepare("SELECT COUNT(*) FROM clients WHERE name LIKE '%$term%' AND company_id = '{$user->company_id}' ORDER BY name ASC");
        
        $sorgu->execute();
        
        $say = $sorgu->fetchColumn();

        /* Gelen terim ile eşleşen kayıt olup olmadığını sorguluyoruz. */

        if ($say != 0) { // Sorgulama sonucu dolu olursa eğer sonuçları ekrana basıyoruz.

            $query = $db->query("SELECT * FROM clients WHERE name LIKE '%$term%' AND company_id = '{$user->company_id}' ORDER BY name ASC LIMIT 10", PDO::FETCH_ASSOC);

            if($query->rowCount()){

                $p = 1;

                foreach ($query as $row) {

                    $firmaid = $row[ 'id']; 
                    
                    $firmaadi = $row['name']; 

                    echo '<li id="li'.$p.'" class="list-group-item" style="cursor:pointer;">'.$firmaadi.'</li>';

                    $p++;

                }

            }

        }else{

            // Eğer eşleşen kayıt yoksa alttaki uyarıyı ekrana basıyoruz.

            echo '<li class="list-group-item">Eşleşen kayıt bulunamadı.</li>';

        }

    }

?>
<?php

//TEK ÇEKİM

$query = $db->query("SELECT * FROM users WHERE id = '{$id}'")->fetch(PDO::FETCH_ASSOC);

//TOPLU ÇEKİM

$query = $db->query("SELECT * FROM clients ORDER BY name ASC", PDO::FETCH_ASSOC);

if ( $query->rowCount() ){

	foreach( $query as $row ){

		$clientId = $row[ 'id'];

	}

}

//INSERT

$query = $db->prepare("INSERT INTO siparis SET siparisboy = ?, taslak = ?, siparissaniye = ?");

$insert = $query->execute(array($siparisboy,'1',time()));

//UPDATE

$query = $db->prepare("UPDATE urun SET urun_adet = ? WHERE urun_id = ?"); 

$update = $query->execute(array($urun_adet,$urun_id));

//DELETE

$sil = $db->prepare("DELETE FROM tablo WHERE id = ?");

$delete = $sil->execute(array($id));

?>